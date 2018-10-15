// 员工总数
var staff_count = 0;
// 当前位置
var staff_pos = 0;

// 展示员工信息
function view_staff_info(my_id) {
    var api_url = 'staff_info_list.php';
    CallApi(api_url, {}, function (response) {
        var staff_id, edit_url, staff_sex, staff_status, staff_subsidy, exp_balance, staff_task;
        var rows = response.rows;
        if (rows.length > 0) {

            rows.forEach(function(row, index, array) {
                staff_id = row.staff_id;
                // 判断是否当前员工
                if(staff_id != my_id){
                    staff_count ++;
                    edit_url = "javascript:void(0)";
                }else{
                    staff_pos = staff_count;
                    edit_url = "info_edit.php";
                   
                }
                
                // 判断员工性别
                if (row.staff_sex == 1) {
                  staff_sex = "img/man.png";
                } else if (row.staff_sex == 2) {
                  staff_sex = "img/woman.png";
                }
                
                // 判断员工在线情况
                if (row.online_status == 1) {
                    staff_status = "img/online.png";
                } else {
                    staff_status = "img/outline.png";
                }
                
                // 本周补贴
                staff_subsidy = '¥' + parseInt(row.staff_subsidy) + '.00';
                
                // 经费余额
                exp_balance = '';
                // 可报销办公经费
                if ((row.free_exp) >= 0)
                  exp_balance = '¥' + parseInt(row.free_exp / 100) + '.' + row.free_exp.substr(-2, 2);
                // 锁定办公经费
                if ((row.lock_exp) > 0) {
                  if (exp_balance != '')
                      exp_balance += '/';
                  exp_balance += '<span class="lock_text">¥' + parseInt(row.lock_exp / 100) + '.' + row.lock_exp.substr(-2, 2) + '<span>';
                }
                
                // 当前任务
                staff_task = '无';
                if ((row.staff_task) > 0)
                  staff_task = '<span class="weui-badge">' + row.staff_task + '</span>';

                info_html = '\
                <div class="swiper-slide">\
                    <div class="weui-cell">\
                        <div class="upload">\
                            <form  id="uploadForm" enctype="multipart/form-data" method="post" action="../../api/upload_image.php">\
                                <img src="'+ row.staff_avata +'" id="image"  class="avata" name="image"/>\
                            </form>\
                            <div class="page__hd" >\
                                <h2 class="page__title" >'+ row.staff_name + '(' + row.nick_name + ') <a target=_blank href="' + edit_url + '"><img src="' + staff_sex + '" class = "sex"></a></h2>\
                                <h3 class="page__desc">'+ row.staff_position + ' ' +'<img src="' + staff_status + '" class = "status">' + ' ' + row.staff_cd + '</h3>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="weui-cells__title">' + row.staff_memo + '</div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd"><label class="weui-label">星座：</label></div>\
                        <div class="weui-cell__bd">'+ row.staff_star_sign +'</div>\
                    </div>\
                    <a class="weui-cell weui-cell_access" href="https://baike.baidu.com/item/' + row.staff_mbti + '">\
                        <div class="weui-cell__hd"><label class="weui-label">性格：</label></div>\
                        <div class="weui-cell__bd">' + row.staff_mbti + '</div>\
                        <div class="weui-cell__ft"></div>\
                    </a>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd"><label class="weui-label">加入日期：</label></div>\
                        <div class="weui-cell__bd">'+ row.join_date + '</div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd"><label class="weui-label">出勤时段：</label></div>\
                        <div class="weui-cell__bd">'+ row.work_period + '</div>\
                    </div>\
                    <a class="weui-cell weui-cell_access" href="week_subsidy.php?staff_id=' + staff_id + '">\
                        <div class="weui-cell__hd"><label class="weui-label">本周补贴：</label></div>\
                        <div class="weui-cell__bd">' + staff_subsidy + '</div>\
                        <div class="weui-cell__ft"></div>\
                    </a>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd"><label class="weui-label">经费余额：</label></div>\
                        <div class="weui-cell__bd">' + exp_balance + '</div>\
                    </div>\
                    <a class="weui-cell weui-cell_access" href="task_list.php?staff_id=' + staff_id + '">\
                        <div class="weui-cell__hd"><label class="weui-label">当前任务：</label></div>\
                        <div class="weui-cell__bd">' + staff_task + '</div>\
                        <div class="weui-cell__ft"></div>\
                    </a>\
                </div>\
                ';
                $("#staff_info").append(info_html);
            });
        }

        var swiper = new Swiper('.swiper-container', {
            initialSlide : staff_pos,
            slidesPerView : 1,
            loop: true,
            observer:true,
            observeParents:true,
            autoplay : 2500,
            autoplayDisableOnInteraction : false
        });

  }, function (response) {
      AlertDialog(response.errmsg);
  });
}
 