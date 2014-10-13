<?php
	require_once 'aliyun-oss/oss_tool.class.php';
	$osstool=new OssTool();
	$sourceFile = 'soothion.mp4';
	$targetFile = 'uploadfile/video/soothion.mp4';
	$rs=$osstool->upload($sourceFile, $targetFile);
	if($rs)
		echo 'success';
	else echo 'error';
?>