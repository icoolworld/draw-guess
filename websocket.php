<?php
$keywords = ['大象','猪' ,'狗', '鸟', '火车', '汽车','飞机','猫'];
//$server = new swoole_websocket_server("0.0.0.0", 8090,SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
$server = new swoole_websocket_server("0.0.0.0", 8090);
$server->set(array(
    //'log_level' => 0,
    'log_file' => './swoole.log',
    //'ssl_cert_file' => 'xxx.pem',
    //'ssl_key_file' => 'xxx.key',
));
$server->keywords = $keywords;
$server->on('open', function (swoole_websocket_server $server, $request) {
    //$keywords = $server->keywords;
    //shuffle($server->keywords);
    //$server->keyword = $server->keywords[0];
    //echo "server: handshake success with fd{$request->fd}\n";
    //$server->push($request->fd, 'keyword:'.$server->keyword);
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    //echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    //$server->push($frame->fd, "this is server");
    
    //开始绘画,下发关键词给绘画端
    if ($frame->data == 'start-draw') {
        shuffle($server->keywords);
        $server->keyword = $server->keywords[0];
        $server->push($frame->fd, 'keyword:'.$server->keyword,true);
    }
    $answer = explode(':', $frame->data);
    $answer = is_array($answer) && isset($answer[1]) ? $answer[1] : '';
    //回答问题,向猜题者输出提示
    if ($answer) {
        $value = ($answer == $server->keyword) ? 'success' : 'error';
        $server->push($frame->fd, $value,true);
    }
    //绘画中,向所有连接的client广播数据
    else{
        foreach($server->connection_list() as $fd) {
            $server->push($fd, $frame->data,true);
            error_log("\nreceived:\n$frame->data\n",3,'/tmp/log');
        }
    }
    //echo 'keyword:'.$server->keyword."\n";

});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
