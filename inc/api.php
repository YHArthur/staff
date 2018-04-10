<?php
class API {
  // 获得微信用户信息
  public function getWxUserInfo($wxid, $unionid)
  {
    $url = "http://wx.fnying.com/user_info.php?wxid={$wxid}&unionid={$unionid}";
    $res = json_decode($this->httpGet($url), true);
    $user = array();
    if (!isset($res['openid']))
      return $user;
    return $res;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }

}
?>
