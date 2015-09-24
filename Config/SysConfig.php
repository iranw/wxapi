<?php
namespace Wx\Config;

class SysConfig{
    const AppID = 'wx91ac357ae83d1f68';
    const AppSecret = '4c5fe543abea83c58abee26283032e03';
    const TOKEN = 'rBUY4tyAbdjn7B';
    const EncodingAESKey = 'gTQbYBqwM9TlA8AZ75rCy2WkKE2BLaBhBi7E01B64CC';

    public static $logFile = './logs/msg.log';
    public static $redis = array(
        'host'=>'127.0.0.1',
        'port'=>'6379',
    );
}

