<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
header("Access-Control-Allow-Origin:*");

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
require ('../aliyun-oss/oss_tool.class.php');

define('OSS_PATH', 'http://video.c20.org.cn/');
define('FFMPEG', '/usr/local/bin/ffmpeg');

$upload_handler = new UploadHandler(null,false);
$osstool=new OssTool();
$files = $upload_handler->post();

$file = $files['files'][0];

$time = time();
$mp4 = $time.'.mp4';
$jpg = $time.'.jpg';
$targetMp4 = 'uploadfile/video/'.$mp4;
$targetJpg = 'uploadfile/thumb/'.$jpg;
$dir = dirname($file->path);
$mp4 = $dir.'/mp4/'.$mp4;
$jpg = $dir.'/thumb/'.$jpg;

$jpgCmd = FFMPEG.' -i  "' . $file->path . '"  -f  image2  -ss 5 -vframes 1  ' . $jpg;
$mp4Cmd = FFMPEG.' -i  "' . $file->path . '" -c:v libx264 -strict -2 -r 30 ' . $mp4;

// exec($jpgCmd);
// exec($mp4Cmd);

$result = array('thumb'=>'', 'mp4'=>'');
$result['mp4'] = OSS_PATH.$targetMp4;
$result['thumb'] = OSS_PATH.$targetJpg;
echo json_encode($result);die;
if(file_exists($jpg)){
	$rs=$osstool->upload($jpg, $targetJpg);
	if($rs)
		$result['thumb'] = OSS_PATH.$targetJpg;
}
if(file_exists($mp4)){
	$rs=$osstool->upload($mp4, $targetMp4);
	if($rs)
		$result['mp4'] = $targetMp4;
}

echo json_encode($result);

