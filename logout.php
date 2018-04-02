<?php
// 删除SESSION
session_start();
session_destroy();

// 重定向
Header("Location: index.php");
?>
