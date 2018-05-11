// 禁止在微信以外的浏览器里打开
window.onload = function(){
    if(!IsWeiXin()){
        window.location.href="http://www.fnying.com/staff/forbiden.php?code=2";
    }
}

var staff_count = 0;
var staff_pos = 0;

// 展示员工信息
function view_staff_info(my_id) {
  
    var api_url = 'staff_info_list.php';
    CallApi(api_url, {}, function (response) {
        var staff_id, edit_url, staff_sex, staff_status, staff_subsidy, exp_balance;
        var rows = response.rows;
        if (rows.length > 0) {

            rows.forEach(function(row, index, array) {
                staff_id = row.staff_id;
                //得到当前员工位置
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
                if (row.staff_status == 1) {
                    staff_status = "img/online.png";
                } else {
                    staff_status = "img/outline.png";
                }
                
                // 本周补贴
                staff_subsidy = '¥' + parseInt(row.staff_subsidy) + '.00';
                
                // 经费余额
                exp_balance = '¥' + parseInt(row.exp_balance/100) + '.' + row.exp_balance.substr(-2,2);

                info_html = '\
                <div class="swiper-slide">\
                    <div class="weui-cell">\
                        <div class="upload">\
                            <form  id="uploadForm" enctype="multipart/form-data" method="post" action="../../api/upload_image.php">\
                                <img src="'+ row.staff_avata +'" id="image"  class="avata" name="image"/>\
                            </form>\
                            <div class="page__hd" >\
                                <h1 class="page__title staff-name" >'+ row.staff_name + '(' + row.nick_name + ') <a target=_blank href="' + edit_url + '"><img src="' + staff_sex + '" class = "sex"></a></h1>\
                                <h3 class="page__desc staff-number">'+ row.staff_position + ' ' +'<img src="' + staff_status + '" class = "status">' + ' ' + row.staff_cd + '</h3>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="weui-cells__title">' + row.staff_memo + '</div>\
                    <div class="weui-cells__title">个人信息</div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">年龄：</label></div>\
                        <div class="weui-cell__bd bc">'+ row.staff_age + '</div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">星座：</label></div>\
                        <div class="weui-cell__bd bc">'+ row.staff_star_sign +'</div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">性格：</label></div>\
                        <div class="weui-cell__bd bc"><a target="_blank" href="https://baike.baidu.com/item/' + row.staff_mbti + '">' + row.staff_mbti + '</a></div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">加入日期：</label></div>\
                        <div class="weui-cell__bd bc">'+ row.join_date + '</div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">本周补贴：</label></div>\
                        <div class="weui-cell__bd bc"><a target="_blank" href=current_subsidy.php?staff_id="' + staff_id + '">' + staff_subsidy + '</a></div>\
                    </div>\
                    <div class="weui-cell">\
                        <div class="weui-cell__hd bt"><label class="weui-label">经费余额：</label></div>\
                        <div class="weui-cell__bd bc">' + exp_balance + '</div>\
                    </div>\
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
      console.log(response.errmsg);
  });
}
 