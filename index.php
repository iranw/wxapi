<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

include './libs/autoload.php';

if(isset($_GET['echostr'])){
    $wechatObj = new Wx\Libs\WechatCallbackapiTest(Wx\Config\SysConfig::TOKEN);
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
$pc = new Wx\Libs\WxBizMsgCrypt(TOKEN, EncodingAESKey, AppID);
$errCode = $pc->decryptMsg($msg_signature, $timestamp, $nonce, $from_xml, $msg);

if($errCode==0){
    $logStr = $msg;
    $xml = simplexml_load_string($msg, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_NOBLANKS);
    // $msgArr = json_decode(json_encode($xml),TRUE);
    $msgArr = (array)$xml;
}else{
    $logStr = "error code:".$errCode;
}

$ntime = date("Y-m-d H:i:s",time());
$logStr = "\n----POST DATA({$ntime})----\n".$logStr."\n";
file_put_contents("msg.log",$logStr,FILE_APPEND);




