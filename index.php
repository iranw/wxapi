<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

include './Libs/autoload.php';

if(isset($_GET['echostr'])){
    $wechatObj = new \Wx\Libs\WechatCallbackapiTest(\Wx\Config\SysConfig::TOKEN);
    $wechatObj->valid();
}

//初始化GET信息
$signature = isset($_GET['signature'])?$_GET['signature']:'';
$timestamp = isset($_GET['timestamp'])?$_GET['timestamp']:'';
$nonce = isset($_GET['nonce'])?$_GET['nonce']:'';
$encrypt_type = isset($_GET['encrypt_type'])?$_GET['encrypt_type']:'';
$msg_signature = isset($_GET['msg_signature'])?$_GET['msg_signature']:'';

//微信服务器post过来的xml内容
$postStr = file_get_contents('php://input');

$xmlObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
$from_xml = sprintf("<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>", $xmlObj->Encrypt);

$msg = "";//初始化解密后字符串
$pc = new \Wx\Libs\WxBizMsgCrypt(\Wx\Config\SysConfig::TOKEN, \Wx\Config\SysConfig::EncodingAESKey, \Wx\Config\SysConfig::AppID);
$errCode = $pc->decryptMsg($msg_signature, $timestamp, $nonce, $from_xml, $msg);

if($errCode==0){
    $logStr = $msg;
    $xml = simplexml_load_string($msg, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_NOBLANKS);
    // $msgArr = json_decode(json_encode($xml),TRUE);
    $msgArr = (array)$xml;
    putRedis($msgArr);
}else{
    $logStr = "error code:".$errCode;
}

$ntime = date("Y-m-d H:i:s",time());
$logStr = "\n----POST DATA({$ntime})----\n".$logStr."\n";
file_put_contents(\Wx\Config\SysConfig::$logFile,$logStr,FILE_APPEND);

/**
 * 信息注入到redis
 * @param  [type] $msgArr [description]
 * @return [type]         [description]
 */
function putRedis($msgArr){
    $msgArr['IsTask']=0;
    $redis = new Redis();
    $redis->connect(\Wx\Config\SysConfig::$redis['host'], \Wx\Config\SysConfig::$redis['port']);
    $redis->sadd(\Wx\Config\SysConfig::$preMem['init_openid_list'], $msgArr['FromUserName']);//注入到初始化用户集合里面
    $redis->sadd(\Wx\Config\SysConfig::$preMem['session_openid_list'], $msgArr['FromUserName']);//注入到会话用户集合里面 
    $redis->rPush(\Wx\Config\SysConfig::$preMem['session_user'].$msgArr['FromUserName'], json_encode($msgArr));
}