<?php
$http = new swoole_http_server("127.0.0.1", 9501);
$GLOBALS['filePool'] = [];
//require('SimpleSHM.class.php');
$http->on('request', function (swoole_http_request $request, swoole_http_response $response) {
	$posts = $request->post;
	if(empty($posts)||!isset($posts['action'])) {
		$response->end(json_encode(['res'=>-1,'data'=>'','msg'=>'']));
		return;
	}
	if($posts['action']=='addFile') {
		$item = [];
		$item['file_path'] = $posts['file_path'];
		$item['file_handle'] = fopen($item['file_path'],'a');
		if($item['file_handle'] === false) {
			$response->end(json_encode(['res'=>-1,'data'=>'','msg'=>'open file error']));
			return;
		}
		fileOp::fput($item);
		$response->end(json_encode(['res'=>0,'data'=>fileOp::getId(),'msg'=>'add file successfully']));
	} else if ($posts['action'] == 'addText') {
		$posts['fileId'] = intval($posts['fileId']);
		$item = fileOp::fget($posts['fileId']);
		if(!isset($posts['fileId'])||empty($item)) {
			echo 'fail';
			$response->end(json_encode(['res'=>-1,'data'=>'','msg'=>'file id not found']));
			return;
		}
		if(empty($posts['content'])) {
			$response->end(json_encode(['res'=>-1,'data'=>'','msg'=>'content empty']));
			return;
		}
		if(!empty($posts['filePool'])) {
			$handle = $item['file_handle'];
			fwrite($handle, $posts['content'] . PHP_EOL);
		} else {
			$file_path = $item['file_path'];
			$res = fopen($file_path,'a');
			fwrite($res, $posts['content'] . PHP_EOL);
			fclose($res);
		}
		$msg = 'write file '.$item['file_path'].' successfully.'.PHP_EOL;
		echo $msg;
		$response->end(json_encode(['res'=>0,'data'=>'','msg'=>$msg]));
	}
});
//$http->on('close', function(){
//	echo 'closed.'.PHP_EOL;
//});

$http->start();

class fileOp {
	static function fput($item)
	{
		if(!isset($GLOBALS['topFileId'])) {
			$GLOBALS['topFileId'] = 0;
		} else {
			$GLOBALS['topFileId'] += 1;
		}
		$GLOBALS['filePool'][$GLOBALS['topFileId']] = $item;
	}
	static function fget($fileId) {
		if(!array_key_exists($fileId,$GLOBALS['filePool'])) {
			return false;
		}
		return $GLOBALS['filePool'][$fileId];
	}
	static function getId() {
		return $GLOBALS['topFileId'];
	}
}