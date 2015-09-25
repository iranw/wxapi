<?php
namespace Wx\Config;

class SysConfig{
    const AppID = 'wx91ac357ae83d1f68';
    const AppSecret = '4c5fe543abea83c58abee26283032e03';
    const TOKEN = 'rBUY4tyAbdjn7B';
    const EncodingAESKey = 'gTQbYBqwM9TlA8AZ75rCy2WkKE2BLaBhBi7E01B64CC';

    public static $logFile = './logs/msg.log';
    public static $redis = array(
        'host'=>'192.168.2.50',
        'port'=>'6379',
        'dbindex'=>2,//the database number to switch to.
    );
    public static $preMem = array(
        'init_openid_list'=>'init_openid_list',
        'session_openid_list'=>'session_openid_list',
        'session_user'=>'session_',
        'session_user_tmp'=>'session_tmp_',
        'sleep_time'=>'wx_sleep_time',
        'openid_bind'=>'wx_bind_',
        'im_queue'=>'wechat_msg_queue',
    );

    public static $sleepTime = array(
        'worktime'=>"",
        'resttime'=>"",
    );

}

