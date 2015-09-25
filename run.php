#!/usr/bin/php
<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

include './Libs/autoload.php';

$redis = new Redis();
$redis->pconnect(\Wx\Config\SysConfig::$redis['host'], \Wx\Config\SysConfig::$redis['port']);
$redis->select(\Wx\Config\SysConfig::$redis['dbindex']); 

//初始化休息时间
$redis->set(\Wx\Config\SysConfig::$preMem['sleep_time'], 1);

while (true) {
    //从openid_list集合里获取一个openid
    $openid = $redis->sPop(\Wx\Config\SysConfig::$preMem['session_openid_list']);
    // $openid = $redis->sPop('myset');
    if($openid==false){
        goSleep();
        continue;
    }

    //获取用户redis信息
    $openidArr = $redis->hGetAll(\Wx\Config\SysConfig::$preMem['openid_bind'].$openid);
    if( !isset($openidArr['misid']) || empty($openidArr['misid']) ){
        $misid = getMisid($openid);//算法接口
        if($misid==0){
            $redis->sadd(\Wx\Config\SysConfig::$preMem['session_openid_list'], $openid);//将openid重新注入到队列中 
            goSleep();
            continue;
        }
        $openidArr = array('misid' => $misid, 'type' => 1);
        $redis->hMset(\Wx\Config\SysConfig::$preMem['openid_bind'].$openid, $openidArr);
    }

    while ( true ) {
        $msg = $redis->lPop(\Wx\Config\SysConfig::$preMem['session_user'].$openid);
        if($msg==false){
            break;
        }
        $msgArr = json_decode($msg,true);
        $msgArr['ToUserName'] = $openidArr['misid'];
        $redis->rPush(\Wx\Config\SysConfig::$preMem['im_queue'], json_encode($msgArr));
        if( isset($openidArr['type']) && $openidArr['type']==1 ){
            $redis->rPush(\Wx\Config\SysConfig::$preMem['session_user_tmp'].$openid, json_encode($msgArr));
        }
        unset($msg);
        unset($msgArr);
    }
    unset($openidArr);
    unset($openid);
}

unset($redis);
echo "Game Over\n";


function goSleep(){
    global $redis;
    $sleep_time = intval($redis->get(\Wx\Config\SysConfig::$preMem['sleep_time']));
    $nSleep_time = 2*$sleep_time;
    $nSleep_time = $nSleep_time>10?10:$nSleep_time;
    $redis->set(\Wx\Config\SysConfig::$preMem['sleep_time'], $nSleep_time);
    // \Ev::sleep ( $nSleep_time );
    sleep($nSleep_time);
    echo "sleep time:".$nSleep_time."\t".date("Y-m-d H:i:s",time())."\n";
}

/**
 * 算法接口
 * @param  [string] $openid [openid]
 * @return [int]         [misid]
 */
function getMisid($openid){ 
    $misid = 1001;
    return $misid;
}
