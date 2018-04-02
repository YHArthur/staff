<?php
class DBTable {

  // 表相关
  public $db = null;                      // 数据库链接
  public $name = '';                      // 表名（实际名称英文）
  public $comment = '';                   // 表注释（MySql内设置）
  public $add_comment = '';               // 添加注释（程序内设置）

  // 字段相关
  public $fields = '*';                   // 选取字段列表
  public $next_id = 1;                    // 自增长下一个ID
  public $columns = array();              // 全体字段列表
  public $show_columns = array();         // 展示字段列表
  public $format_columns = array();       // 字段转换样式列表
  public $edit_columns = array();         // 可编辑字段列表（默认除关键字外全体）
  public $srh_columns = array();          // 可检索字段列表（默认全体）
  public $sort_columns = array();         // 可排序字段列表（默认全体）
  public $add_columns = array();          // 额外增加的字段列表
  public $key_columns= array();           // 关键字字段列表

  // 分页相关
  public $pagination = true;              // 是否分页
  public $limit = true;                   // 是否分页
  public $rec_counts = 0;                 // 总记录条数
  public $page = 1;                       // 当前页
  public $page_size = Config::PAGESIZE;   // 每页显示记录条数（默认值）
  public $page_counts = 1;                // 总页数

  // 记录集相关
  public $add_able = true;                // 是否可添加记录
  public $upd_able = true;                // 是否可修改记录
  public $del_able = true;                // 是否可单件删除记录
  public $chkall_able = false;            // 是否可批量处理记录
  public $search_able = true;             // 是否可检索记录
  public $detail_able = true;             // 是否显示更多明细字段（首列+）
  public $card_show = false;              // 是否显示卡片形式列表（工具条切换）
  public $recs = array();                 // 记录数据
  public $where = '';                     // 检索条件
  public $orderby= '';                    // 排序

  // 代码相关
  public $json= '';                       // JSON数据
  public $html= '';                       // Html代码
  public $javascript= '';                 // javascript代码
  public $add_javascript = '';            // 额外增加的javascript代码（与额外增加的工具栏按钮或字段对应）

  // 操作相关
  public $add_toolbar= '';                // 额外增加的工具栏代码

  //======================================
  // 函数: __construct($db, $table_name)
  // 功能: 构造函数
  // 参数: $db          数据库连接类
  // 参数: $table_name  表名
  // 说明: 初始化数据库table对象
  //======================================
  public function __construct($db, $table_name)
  {
    // 表名设定
    $this->name = $table_name;
    // 数据库链接
    $this->db = new $db();
  }

  //======================================
  // 函数: analysis()
  // 功能: 根据参数分析表格处理
  // 说明:
  //======================================
  public function analysis() {

    if (!session_id())
      session_start();

    if (!isset($_SESSION['staff_id'])) {
      return '登录失效，请重新登录';
    }

    // 默认返回值
    $rtn_str = '';
    // 解析方式
    if (empty($_GET["m"])) {
      $method = "html";
    } else {
      $method = $_GET["m"];
    }

    header("cache-control:no-cache,must-revalidate");

    // 取得表注释
    $this->get_comment();
    // 取得表所有列信息
    $this->get_columns();

    switch($method)
    {
      // 查询数据
      case 'data':
        // 取得数据
        header("Content-Type:application/json;charset=utf-8");
        $this->get_json();
        $rtn_str = $this->json;
        break;

      // 设置数据
      case 'set':
        $row = array();
        // 循环处理字段列表
        foreach ($this->columns as $column) {
          $col_name = $column['COLUMN_NAME'];
          // 该关键字是否传递过来
          if (isset($_POST[$col_name])) {
            $value = $_POST[$col_name];
            $row[$col_name] = $value;
          }
        }
        $_SESSION['cur_row'] = json_encode($row);
        break;

      // 插入数据
      case 'ins':
        header("Content-Type:text/html;charset=utf-8");
        $rtn_str = '1';
        $where = $this->get_key_search();
        if (empty($where)) {
          $rtn_str = "关键字为空";
        } else {
          $db = $this->db;
          $db->connect();
          $sql = $db->sqlSelect($this->name, $where);
          $db->query($sql);
          if ($db->recordCount() == 0) {
            $sql = $db->sqlInsert($this->name, $this->get_insert_fields());
            $staff_name = $_SESSION['staff_name'];
            Log::INFO($sql . "【{$staff_name}】");
            $result = $db->query($sql);
            if ($result == 0)
              $rtn_str = $db->error;
          } else {
              $rtn_str = "添加失败，关键字重复";
          }
        }
        break;

      // 更新数据
      case 'upd':
        header("Content-Type:text/html;charset=utf-8");
        $rtn_str = '1';
        $where = $this->get_key_search();
        $upd = $this->get_update_fields();
        if (empty($where)) {
          $rtn_str = "更新条件为空";
        } elseif (empty($upd)) {
          $rtn_str = "更新内容为空";
        } else {
          $db = $this->db;
          $db->connect();
          $sql = $db->sqlUpdate($this->name, $upd, $where);
          $staff_name = $_SESSION['staff_name'];
          Log::INFO($sql . "【{$staff_name}】");
          $result = $db->query($sql);
          if ($result == 0)
            $rtn_str = $db->error;
          if ($db->affectedRows() == 0)
            $rtn_str = "没有记录被更新";
        }
        break;

      // 处理多条数据
      case 'all':
        header("Content-Type:text/html;charset=utf-8");
        $rtn_str = '1';
        if (empty($_POST['ids'])) {
          $rtn_str = "提交参数为空";
        } else {
          $db = $this->db;
          $db->connect();
          $where = implode(' OR ', $_POST['ids']);
          // 未提交SQL，默认删除
          if (empty($_POST['pre_sql'])) {
            $sql = $db->sqlDelete($this->name, $where);
          } else {
            $pre_sql = PreSQL::get_sql($_POST['pre_sql']);
            $sql = $pre_sql . ' WHERE ' . $where;
          }
          $staff_name = $_SESSION['staff_name'];
          Log::INFO($sql . "【{$staff_name}】");
          $result = $db->query($sql);
          if ($result == 0)
            $rtn_str = $db->error;
        }
        break;


      // 删除数据
      case 'del':
        header("Content-Type:text/html;charset=utf-8");
        $rtn_str = '1';
        $where = $this->get_key_search();
        if (empty($where)) {
          $rtn_str = "删除条件为空";
        } else {
          $db = $this->db;
          $db->connect();
          $sql = $db->sqlDelete($this->name, $where);
          $staff_name = $_SESSION['staff_name'];
          Log::INFO($sql . "【{$staff_name}】");
          $result = $db->query($sql);
          if ($result == 0)
            $rtn_str = $db->error;
        }
        break;

      // 默认获取HTML构造
      default:
        header("Content-Type:text/html;charset=utf-8");
        $_SESSION['cur_table'] = json_encode($this);
        unset($_SESSION['cur_row']);
        // 取得表HTML代码
        $this->get_html();
        // 取得表javascript代码
        $this->get_javascript();

        $rtn_str = $this->html;
        $rtn_str .= $this->javascript;
        break;
    }

    // 返回数据
    return $rtn_str;

  }

  //======================================
  // 函数: get_comment()
  // 功能: 取得表注释
  // 说明:
  //======================================
  private function get_comment() {
    $db = $this->db;
    $this->db->connect('', '', '', 'Information_schema');
    $sql = "SELECT TABLE_COMMENT FROM TABLES WHERE TABLE_NAME = '" . $this->name . "'";
    $sql .= " AND TABLE_SCHEMA = '" . $db->schema . "'";
    $this->comment = $db->getField($sql, 'TABLE_COMMENT');
  }

  //======================================
  // 函数: get_next_id($col_name)
  // 功能: 取得下一个自增长的ID
  // 说明:
  //======================================
  public function get_next_id($col_name) {
    $db = $this->db;
    $db->connect();
    $sql = "SELECT MAX({$col_name}) AS max_id FROM " . $this->name;
    $max_id = $db->getField($sql, 'max_id');
    if (!empty($max_id))
      $this->next_id = $max_id + 1;
  }

  //======================================
  // 函数: get_rec_counts()
  // 功能: 取得表记录数
  // 说明:
  //======================================
  public function get_rec_counts() {
    $db = $this->db;
    $db->connect();
    $sql = "SELECT COUNT(*) AS rec_counts FROM " . $this->name;
    $sql .= ($this->where ? " WHERE " . $this->where : "");
    $this->rec_counts = $db->getField($sql, 'rec_counts');
  }

  //======================================
  // 函数: get_columns()
  // 功能: 取得表列数据
  // 说明:
  //======================================
  private function get_columns()
  {

    // 是否设置展示字段列表
    $show_empty = empty($this->show_columns) ? true : false;
    // 是否设置可编辑字段
    $edit_empty = empty($this->edit_columns) ? true : false;
    // 是否设置可排序字段
    $sort_empty = empty($this->sort_columns) ? true : false;

    // 未设置展示字段列表的场合，全体字段为展示字段，所以不需要再显示更多明细字段
    if ($show_empty)
      $this->detail_able = false;

    $db = $this->db;
    $this->db->connect('', '', '', 'Information_schema');
    $sql = "SELECT * FROM COLUMNS WHERE TABLE_NAME = '" . $this->name . "'";
    $sql .= " AND TABLE_SCHEMA = '" . $db->schema . "'";
    $db->query($sql);
    $rows = $db->fetchAll();

    // 临时字段列表1(展示字段)
    $tmp_columns1 = array();
    // 临时字段列表2(未展示字段)
    $tmp_columns2 = array();

    // 循环处理字段列表
    foreach ($rows as $row) {

      $col_name = $row['COLUMN_NAME'];

      // 未设置展示字段列表的场合，全体字段为展示字段
      if ($show_empty)
        $this->show_columns[] = $col_name;

      // 是否展示字段
      if (in_array($col_name, $this->show_columns)) {
        $pos = array_keys($this->show_columns, $col_name);
        $tmp_columns1[$pos[0]] = $row;
      } else {
        $tmp_columns2[] = $row;
      }

      // 未设置可排序字段列表的场合，全体字段为可排序字段
      if ($sort_empty)
        $this->sort_columns[] = $col_name;

      if ($row['COLUMN_KEY'] == 'PRI') {
        // 关键字字段列表添加
        $this->key_columns[] = $col_name;
        // 自增长关键字
        if ($row['EXTRA'] == 'auto_increment') {
          // 取得下一个自增长的ID
          $this->get_next_id($col_name);
          // 未指定排序
          if (empty($this->orderby))
            $this->orderby = $col_name . ' desc';
        }
      } else {
        // 未设置可编辑字段列表的场合，非关键字字段为可编辑字段
        if ($edit_empty)
          $this->edit_columns[] = $col_name;
      }
    }

    // 展示字段重排
    ksort($tmp_columns1);
    // 全体字段列表设置
    $this->columns = array_merge($tmp_columns1, $tmp_columns2);

    // 未设置可检索字段列表的场合，展示字段为可检索字段
    if (empty($this->srh_columns))
      $this->srh_columns = $this->show_columns;

  }

  //======================================
  // 函数: get_recs()
  // 功能: 取得表记录数据
  // 说明:
  //======================================
  public function get_recs()
  {
    $db = $this->db;
    $db->connect();
    $sql = "SELECT " . $this->fields . " FROM " . $this->name;
    $sql .= ($this->where ? " WHERE " . $this->where : "");
    $sql .= ($this->orderby ? " ORDER BY " . $this->orderby : "");
    $sql .= ($this->limit ? " limit " . ($this->page - 1) * $this->page_size . ", " . $this->page_size : "");
    $db->query($sql);
    $rows = $db->fetchAll();
    $this->recs = $rows;
  }

  //======================================
  // 函数: get_insert_fields()
  // 功能: 取得添加字段
  // 说明:
  //======================================
  public function get_insert_fields()
  {
    $insert = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      // 该关键字是否传递过来
      if (isset($_POST[$col_name])) {
        $value = addslashes($_POST[$col_name]);
        $insert[$col_name] = $value;
      }
    }

    return $insert;
  }

  //======================================
  // 函数: get_update_fields()
  // 功能: 取得更新字段
  // 说明:
  //======================================
  public function get_update_fields()
  {
    $update = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      $col_type = $column['DATA_TYPE'];
      // 是否在关键字列表中
      if (!in_array($col_name, $this->key_columns)) {
        // 字符型
        if (in_array($col_type, array("text", "tinytext", "mediumtext", "varchar", "char", "timestamp"))) {
          $value = '';
        } else {
          $value = 0;
        }
        // 该关键字是否传递过来
        if (isset($_POST[$col_name]))
          $value = addslashes($_POST[$col_name]);
        $update[$col_name] = $value;
      }
    }

    return $update;
  }

  //======================================
  // 函数: get_key_search()
  // 功能: 取得单件检索条件
  // 说明:
  //======================================
  public function get_key_search()
  {
    $where = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      $col_type = $column['DATA_TYPE'];
      // 是否在关键字列表中
      if (in_array($col_name, $this->key_columns)) {
        // 该关键字是否传递过来
        if (isset($_POST[$col_name])) {
          $keyword = $_POST[$col_name];
          // 字符型
          if (in_array($col_type, array("text", "tinytext", "mediumtext", "varchar", "char", "timestamp"))) {
            $where[] = "$col_name = '$keyword'";
          } else {
            // 数字型
            if (is_numeric($keyword))
              $where[] = "$col_name = $keyword";
          }
        }
      }
    }

    if (empty($where)) {
      return '';
    }

    return implode(' AND ', $where);
  }

  //======================================
  // 函数: get_all_search($keyword)
  // 功能: 取得全体检索条件
  // 说明:
  //======================================
  public function get_all_search($keyword)
  {
    if ($keyword == '')
      return;

    $where = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      $col_type = $column['DATA_TYPE'];
      // 是否在检索字段列表中
      if (in_array($col_name, $this->srh_columns)) {
        // 字符型
        if (in_array($col_type, array("text", "tinytext", "mediumtext", "varchar", "char"))) {
          $where[] = "$col_name like '%$keyword%'";
        } else {
          // 数字型
          if (is_numeric($keyword))
            $where[] = "$col_name = $keyword";
        }
      }
    }

    if (empty($where))
      return;

    if (empty($this->where)) {
      $this->where = implode(' OR ', $where);
    } else {
      $this->where .= ' AND (' . implode(' OR ', $where) . ')';
    }
  }

  //======================================
  // 函数: get_json()
  // 功能: 取得表记录的JSON数据
  // 说明:
  //======================================
  public function get_json()
  {

    // 每页显示记录数取得
    if (!empty($_GET["limit"])) {
      $this->page_size = intval($_GET["limit"]);
    }

    // 当前页数取得
    if (!empty($_GET["offset"])) {
      $this->page = (intval($_GET["offset"]) / $this->page_size) + 1;
    }

    // 检索条件取得
    if (isset($_GET["search"])) {
      $this->get_all_search($_GET["search"]);
    }

    // 排序字段取得
    if (isset($_GET["sort"]) && isset($_GET["order"])) {
      $orderby = $_GET["sort"] . " " . $_GET["order"];
      // 原始排序为空
      if (empty($this->orderby)) {
         $this->orderby = $orderby;
      } else {
         $this->orderby = $orderby . "," . $this->orderby;
      }
    }

    $this->get_rec_counts();
    $this->get_recs();
    $tmp = array();

    $tmp["total"] = $this->rec_counts;
    $tmp["rows"] = $this->recs;

    $this->json = json_encode($tmp);
  }

  //======================================
  // 函数: get_html()
  // 功能: 取得表的HTML代码
  // 说明:
  //======================================
  private function get_html()
  {
    $tablename = $this->comment;
    $comment = $this->add_comment;

    $html_str  = <<<EOF

    <h1>
      $tablename
      <small class="text-muted">$comment</small>
    </h1>

    <div id="toolbar">
EOF;

    if ($this->add_able) {
      // 添加纪录按钮
      $html_str .= <<<EOF
        <button id="create" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加记录
      </button>
EOF;
    }

    // 额外增加的工具栏按钮
    $html_str .= $this->add_toolbar;

    // 关键字字段
    $data_id_field = join(",",$this->key_columns);
    // 获取数据API
    $data_url = $_SERVER['PHP_SELF'] . '?m=data';

    $html_str .= <<<EOF

    </div>

    <table id="table"
      data-locale="zh-CN"
      data-toolbar="#toolbar"
      data-search="true"
      data-show-refresh="true"
      data-show-toggle="true"
      data-show-columns="true"
      data-show-export="true"
EOF;

    if ($this->detail_able) {
      // 显示更多明细字段
      $html_str .= <<<EOF
      data-detail-view="true"
      data-detail-formatter="detailFormatter"
EOF;
    }

    $html_str .= <<<EOF

      data-minimum-count-columns="2"
      data-pagination="true"
      data-classes="table table-hover table-no-bordered"
      data-striped="true"
      data-id-field="$data_id_field"
      data-page-list="[10, 25, 50, 100, 200]"
      data-show-footer="false"
      data-side-pagination="server"
      data-url="$data_url"
      data-response-handler="responseHandler">
    </table>

EOF;

    if ($this->add_able || $this->upd_able) {
      // 添加修改记录模块框
      $html_str .= <<<EOF

    <div id="modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
EOF;

      // 全体字段列表
      $columns = $this->columns;
      $html = new Html(20);
      // 字段列表生成
      foreach ($columns as $column) {
        // 字段名
        $col_name = $column['COLUMN_NAME'];
        $col_comment = $column['COLUMN_COMMENT'];
        $col_type = $column['DATA_TYPE'];
        $col_value = '';
        if (isset($column['COLUMN_DEFAULT'])) {
          $col_value = $column['COLUMN_DEFAULT'];

          // 当前时间默认值处理
          if ($col_value == 'CURRENT_TIMESTAMP')
            $col_value = date('Y-m-d H:i:s');
        }

        $html_str .= $html->out('<div class="form-group">');
        $html->indent(2);
        $html_str .= $html->out("<label>$col_comment</label>");
        switch($col_type)
        {
          // 文本框
          case 'tinytext':
          case 'mediumtext':
          case 'text':
            $html_str .= $html->out('<textarea class="form-control" name="' . $col_name . '" placeholder="' . $col_comment . '" rows="3">' . $col_value . '</textarea>');
            break;
          default:
            $html_str .= $html->out('<input type="text" class="form-control" name="' . $col_name . '" value="' . $col_value . '" placeholder="' . $col_comment . '">');
            break;
        }

        $html->indent(-2);
        $html_str .= $html->out('</div>');
      }

      $html_str .= <<<EOF

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary submit">确认</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
EOF;
    }

    $this->html = $html_str;
  }


  //======================================
  // 函数: get_javascript()
  // 功能: 取得表的javascript代码
  // 说明:
  //======================================
  private function get_javascript()
  {

    $html = new Html(4);

    $js_str = $html->out('<script>');

    // 表格设定
    $js_str .= $this->get_table_js();

    // 可添加或可修改记录
    if ($this->add_able || $this->upd_able || $this->del_able)
      $js_str .= $this->get_modal_js();

    // 可操作记录（修改，删除）
    if ($this->upd_able || $this->del_able)
      $js_str .= $this->get_operate_js();

    // 可批量处理记录
    if ($this->chkall_able)
      $js_str .= $this->get_chkall_js();

    // 额外增加的JS代码
    $js_str .= $this->add_javascript;

    // 共通JS
    $js_str .= $this->get_common_js();

    $js_str .= $html->out('</script>');



    $this->javascript = $js_str;
  }

  //======================================
  // 函数: get_modal_js()
  // 功能: 取得添加，修改记录相关javascript代码
  // 说明:
  //======================================
  private function get_modal_js()
  {
    $html = new Html(12);

    // javascript代码生成
    $js_str  = <<<EOF

    // 添加，修改模态框
    var modal = $('#modal').modal({show: false});

    // 填充模态框内的各个项目
    function changeModal(row) {
        for (var name in row) {
            modal.find('input[name="' + name + '"]').val(row[name]);
            modal.find('textarea[name="' + name + '"]').val(row[name]);
        }
    }

    // 展示模态框（标题，模式）
    function showModal(title, method) {
        modal.data('m', method);
        modal.find('.modal-title').text(title);
        modal.modal('show');
    }

    $(function () {
EOF;

    // 可添加纪录
    if ($this->add_able) {

      $js_str  .= <<<EOF

        // 添加记录按钮点击事件
        $('#create').click(function () {
EOF;

      $js_str  .= $html->out("showModal('{$this->comment}-添加记录', 'ins');");

      $js_str  .= <<<EOF

        });
EOF;
    }

    $js_str  .= <<<EOF

        // 模态框确认按钮点击事件
        modal.find('.submit').click(function () {
            var row = {};

            modal.find('input[name]').each(function () {
                row[$(this).attr('name')] = $(this).val();
            });

            modal.find('textarea[name]').each(function () {
                row[$(this).attr('name')] = $(this).val();
            });

            // 取得记录描述
            var desc = getRowDescriptions(row);
            // alert(JSON.stringify(row));
            var method = modal.data('m');

            method_name = '添加';
            if (method == 'upd')
              method_name = '更新';

            $.ajax({
EOF;
              $html->indent(2);
            $js_str  .= $html->out("url: '{$_SERVER['PHP_SELF']}?m=' + method,");

    $js_str  .= <<<EOF

                type: 'post',
                data: row,
                success:function(msg) {
                  // AJAX正常返回，调用SQL更新数据成功
                  modal.modal('hide');
                  if (msg == '1') {
                    show_OK_msg(table_name + '数据操作成功', desc + ' 的记录已经被' + method_name);
                    table.bootstrapTable('refresh');
                  } else {
                    show_NG_msg(table_name + '数据操作失败', msg);
                  }
                },
                error:function(XMLHttpRequest, textStatus, errorThrown) {
                  // AJAX异常
                  show_NG_msg(textStatus, errorThrown);
                  modal.modal('hide');
                }
            });
        });

    });

EOF;

    return $js_str;
  }

  //======================================
  // 函数: get_operate_js()
  // 功能: 取得可操作记录（修改，删除）相关javascript代码
  // 说明:
  //======================================
  private function get_operate_js()
  {
    $html = new Html(12);
    
    $operate_buttons = array();
    if ($this->upd_able)
      $operate_buttons[] = ' <button class="update btn-info" type="button" aria-label="更新"><i class="glyphicon glyphicon-edit"></i></button> ';
    if ($this->del_able)
      $operate_buttons[] = ' <button class="remove btn-danger" type="button" aria-label="删除"><i class="glyphicon glyphicon-remove"></i></button> ';

    $operate_str = join("",$operate_buttons);

    // javascript代码生成
    $js_str  = <<<EOF

    // 操作
    function operateFormatter(value, row, index) {
        
EOF;

    $js_str  .= $html->out("return '" . $operate_str . "';");

    $js_str  .= <<<EOF
    }

    window.operateEvents = {
        'click .update': function (e, value, row) {
EOF;

    $js_str  .= $html->out("showModal('{$this->comment}-修改记录', 'upd');");

    $js_str  .= <<<EOF

        },
        'click .remove': function (e, value, row) {
          //询问框
          layer.confirm('是否删除 ' + getRowDescriptions(row) + ' 的记录？', {
              icon: 3,
              title: table_name + '操作确认',
              btn: ['确认','取消']
          }, function(){
              $.ajax({
EOF;

              $js_str  .= $html->out("url: '" . $_SERVER['PHP_SELF'] . "?m=del',");

    $js_str  .= <<<EOF

                  type: 'post',
                  data: row,
                  success: function (msg) {
                    if (msg == '1') {
                      layer.msg(getRowDescriptions(row) + ' 的记录已经被删除');
                      table.bootstrapTable('refresh');
                    } else {
                      show_NG_msg(table_name + '数据操作失败', msg);
                    }
                  },
                  error:function(XMLHttpRequest, textStatus, errorThrown) {
                    // AJAX异常
                    show_NG_msg(textStatus, errorThrown);
                  }
              })
          } , function(){
          });
        }
    };

EOF;

    return $js_str;
  }

  //======================================
  // 函数: get_chkall_js()
  // 功能: 取得可批量处理记录相关javascript代码
  // 说明:
  //======================================
  private function get_chkall_js()
  {
    $html = new Html(16);

    // javascript代码生成
    $js_str  = <<<EOF

    // 取得被选中的记录ID列表（逗号分割：例id=1,id=2）
    function getIdSelections() {
        return $.map(table.bootstrapTable('getSelections'), function (row) {
EOF;

    $where = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      $col_type = $column['DATA_TYPE'];
      // 是否在关键字列表中
      if (in_array($col_name, $this->key_columns)) {
        // 字符型
        if (in_array($col_type, array("text", "tinytext", "mediumtext", "varchar", "char", "timestamp"))) {
          $where[] = "\"$col_name='\" + row.$col_name + \"'\"";
        } else {
          // 数字型
          $where[] = "\"$col_name=\" + row.$col_name";
        }
      }
    }

    $rtn_str = '';
    if (!empty($where)) {
      $rtn_str = implode(' + " AND " + ', $where);
    }

    $html->indent(-4);
    $js_str .= $html->out("return $rtn_str;");
    $js_str .= <<<EOF

        });
    }

EOF;

    return $js_str;
  }

  //======================================
  // 函数: get_toolbar_js()
  // 功能: 取得工具栏相关javascript代码
  // 说明:
  //======================================
  private function get_toolbar_js()
  {

    // javascript代码生成
    $js_str  = <<<EOF



EOF;

    return $js_str;
  }

  //======================================
  // 函数: get_table_js()
  // 功能: 取得表格设定相关javascript代码
  // 说明:
  //======================================
  private function get_table_js()
  {
    $html = new Html(4);
    $js_str = $html->out("var table_name = '{$this->comment}';");

    // 表格设定相关javascript代码
    $js_str .= <<<EOF

    var table = $('#table');
    function initTable() {
        table.bootstrapTable({
            height: getHeight(),
            columns: [
EOF;

    if ($this->chkall_able) {
      // 可批量处理记录，首列添加checkbox
      $js_str .= <<<EOF
                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle'
                },
EOF;
    }

    // 全体字段列表
    $columns = $this->columns;
    // 展示字段列表
    $show_columns = $this->show_columns;
    // 可编辑字段列表
    $edit_columns  = $this->edit_columns;
    // 可排序字段列表
    $sort_columns = $this->sort_columns;

    $html = new Html(16);
    // 字段列表生成
    foreach ($columns as $column) {
      // 字段名
      $col_name = $column['COLUMN_NAME'];

      $js_str .= $html->out('{');
      $html->indent(2);
      $js_str .= $html->out("title: '" . $column['COLUMN_COMMENT'] . "',");
      $js_str .= $html->out("field: '" . $col_name . "',");
      // 行对齐
      switch($column['DATA_TYPE'])
      {
        // 居左
        case 'text':
        case "tinytext":
        case "mediumtext":
        case 'varchar':
          $js_str .= $html->out("align: 'left',");
          break;
        // 居中
        case 'char':
        case 'timestamp':
          $js_str .= $html->out("align: 'center',");
          break;
        default:
          if (in_array($col_name, $this->key_columns)) {
            // 默认居中（关键字数字）
            $js_str .= $html->out("align: 'center',");
          } else {
            // 默认居右（一般数字）
            $js_str .= $html->out("align: 'right',");
          }
          break;
      }
      // 不显示
      if (!in_array($col_name, $show_columns))
        $js_str .= $html->out("visible: false,");
      // 可编辑
      // if (in_array($col_name, $edit_columns))
        // $js_str .= $html->out("editable: true,");
      // 可排序
      if (in_array($col_name, $sort_columns)) {
        $js_str .= $html->out("sortable: true,");
        // 自增长字段
        if ($column['EXTRA'] == 'auto_increment')
          $js_str .= $html->out("sortOrder: 'desc',");
      }
      // 字段转换样式处理
      foreach ($this->format_columns as $f_column) {
        if ($f_column['field'] == $col_name)
          $js_str .= $html->out("formatter: {$f_column['formatter']},");
      }

      // 列对齐
      $js_str .= $html->out("valign: 'middle'");

      $html->indent(-2);
      $js_str .= $html->out('},');
    }

    // 额外增加字段列表
    $add_columns = $this->add_columns;
    foreach ($add_columns as $column) {
      $js_str .= $html->out('{');
      $html->indent(2);
      $js_str .= $html->out("title: '" . $column['title'] . "',");
      $js_str .= $html->out("field: '" . $column['field'] . "',");
      $js_str .= $html->out("align: '" . $column['align'] . "',");
      $js_str .= $html->out("valign: '" . $column['valign'] . "',");
      $js_str .= $html->out("events: " . $column['events'] . ",");
      $js_str .= $html->out("formatter: " . $column['formatter'] . "");
      $html->indent(-2);
      $js_str .= $html->out('},');
    }

    // 操作字段
    $js_str .= <<<EOF

                {
                    field: 'operate',
                    title: '操作',
                    align: 'center',
                    valign: 'middle',
                    events: operateEvents,
                    formatter: operateFormatter
                }
            ]
        });

        // sometimes footer render error.
        setTimeout(function () {
            table.bootstrapTable('resetView');
        }, 200);
EOF;

    if ($this->chkall_able) {
      $js_str .= <<<EOF

        // 检查框选择事件
        table.on('check.bs.table uncheck.bs.table ' +
                'check-all.bs.table uncheck-all.bs.table', function () {
            // 是否允许选中相关操作按钮有效
            $('.btn_check').prop('disabled', !table.bootstrapTable('getSelections').length);
        });
EOF;
    }

    // 点击行添加默认值
    if ($this->add_able || $this->upd_able || $this->del_able) {
      $js_str .= <<<EOF

        // 点击行事件
        table.on('click-row.bs.table', function (e, row) {
            // 自动填充Model
            changeModal(row);
            $.ajax({
EOF;

            $js_str  .= $html->out("url: '{$_SERVER['PHP_SELF']}?m=set',");

    $js_str  .= <<<EOF

                type: 'post',
                data: row,
                success:function() {
                },
                error:function(XMLHttpRequest, textStatus, errorThrown) {
                  // AJAX异常
                  show_NG_msg(textStatus, errorThrown);
                  modal.modal('hide');
                }
            });
        });
EOF;
    }

    $js_str .= <<<EOF

        // 窗口尺寸变化事件
        $(window).resize(function () {
            table.bootstrapTable('resetView', {
                height: getHeight()
            });
        });
    }

EOF;

    return $js_str;
  }

  //======================================
  // 函数: get_common_js()
  // 功能: 取得共通javascript代码
  // 说明:
  //======================================
  private function get_common_js()
  {
    $html = new Html(8);
    // javascript代码生成
    $js_str  = <<<EOF

    // 取得记录描述
    function getRowDescriptions(row) {
EOF;

    $where = array();
    // 循环处理字段列表
    foreach ($this->columns as $column) {
      $col_name = $column['COLUMN_NAME'];
      $col_comment = $column['COLUMN_COMMENT'];
      // 是否在关键字列表中
      if (in_array($col_name, $this->key_columns))
        $where[] = "'$col_comment=' + row.$col_name";
    }

    $rtn_str = '';
    if (!empty($where)) {
      $rtn_str = implode(' + " AND " + ', $where);
    }
    $js_str .= $html->out("return $rtn_str;");
    $js_str .= <<<EOF

    }

    var selections = [];

    // 更多明细字段显示
    function detailFormatter(index, row) {
        var html = [];
        $.each(row, function (key, value) {
            html.push('<p><b>' + key + ':</b> ' + value + '</p>');
        });
        return html.join('');
    }

    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            // row.state = $.inArray(row.id, selections) !== -1;
        });
        return res;
    }

    // 链接格式化
    function urlFormatter(value, row, index) {
        if(value) {
          return '<a href="' + value + '" target="_blank">' + value + '</a>';
        }
    }

    // 图片格式化
    function imageFormatter(value, row, index) {
        if(value) {
            return '<a href="' + value + '" target="_blank"><img src="' + value + '" class="avata"></a>';
        }
    }

    // 源图片格式化
    function sourceFormatter(value, row, index) {
        if(value) {
            return '<a href="http://source1.snh48.com' + value + '" target="_blank"><img src="http://source1.snh48.com' + value + '" class="avata"></a>';
        }
    }

    // 获取表格高度
    function getHeight() {
        return $(window).height() - $('h1').outerHeight(true) - $('nav').outerHeight(true);
    }

    $(function () {
        var scripts = [
                location.search.substring(1) || 'js/bootstrap-table.min.js',
                'js/bootstrap-table-zh-CN.min.js',
                'js/bootstrap-table-export.js',
                'js/tableExport.js',
                'js/bootstrap-table-editable.js',
                'js/bootstrap-editable.js'
            ],
            eachSeries = function (arr, iterator, callback) {
                callback = callback || function () {};
                if (!arr.length) {
                    return callback();
                }
                var completed = 0;
                var iterate = function () {
                    iterator(arr[completed], function (err) {
                        if (err) {
                            callback(err);
                            callback = function () {};
                        }
                        else {
                            completed += 1;
                            if (completed >= arr.length) {
                                callback(null);
                            }
                            else {
                                iterate();
                            }
                        }
                    });
                };
                iterate();
            };

        eachSeries(scripts, getScript, initTable);
    });

    function getScript(url, callback) {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.src = url;

        var done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState ||
                    this.readyState == 'loaded' || this.readyState == 'complete')) {
                done = true;
                if (callback)
                    callback();

                // Handle memory leak in IE
                script.onload = script.onreadystatechange = null;
            }
        };

        head.appendChild(script);

        // We handle everything using the script element injection
        return undefined;
    }

EOF;

    return $js_str;
  }
}

?>