<?php
// HTML代码输出类
class Html {

  public $space = 0;

  //======================================
  // 函数: __construct()
  // 功能: 构造函数
  // 参数: 类初始化
  //======================================
  public function __construct($space = 0) {
    $this->space = $space;
  }

  public function indent($nm = 1) {
    // 缩进控制（默认2空格）
    $this->space += $nm * 2;
  }

  public function out($html_str) {
    // 回车加空格后返回
    return "\n" . str_repeat(' ', $this->space) . $html_str;
  }

  public function outs($html_array) {
    $html_str = '';
    foreach ($html_array as $html) {
      // </ 开头，缩进减少2
      if (substr($html, 0, 2) == '</')
        $this->indent(-2);
      
      // 回车加空格后返回
      $html_str .= "\n" . str_repeat(' ', $this->space) . $html;
      
      // 非</ 开头，只包含一个">"，非"<br,<hr,<input,<img" 开头，缩进增加2
      if ((substr($html, 0, 2) != '</') && (substr_count($html, '>') == 1) && (substr($html, 0, 3) != '<br') && (substr($html, 0, 3) != '<hr') && (substr($html, 0, 4) != '<img') && (substr($html, 0, 6) != '<input'))
        $this->indent(2);
    }
    return $html_str;
  }


}
?>