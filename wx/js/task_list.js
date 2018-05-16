// $('.execute_item').click(function(){
//     window.location.href='task_content.php';

// })
// $('.supervise_item').click(function(){
//    window.location.href='task_content_check.php';
     
// })
$('#execute').click(function(){
    my_task();
})

$('#supervise').click(function(){
    my_check();
})
//我执行的
function my_task(){
    $('.execute').css('display','block');
    $('#supervise').removeClass('weui-bar__item_on');
    $('#execute').addClass('weui-bar__item_on');
    $('.supervise').css('display','none');
}
//我监督的
function my_check(){
    $('.execute').css('display','none');
    $('#execute').removeClass('weui-bar__item_on');
    $('#supervise').addClass('weui-bar__item_on');
    $('.supervise').css('display','block');
}
