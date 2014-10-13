<?php
/**
 * 加载sdk包以及错误代码包
 */
require_once '../oss_tool.class.php';

$tool=  new OssTool();

$rs=$tool->upload("d:/缩列图.txt", "test/缩列图.txt");
echo "=============================<br>";
echo $rs . '<br>';
echo "=============================<br>";
echo $tool->sign_url("test/缩列图.txt<br>");

//$rs=$tool->delete("a/b/c.txt");
echo "=============================<br>";
echo $rs . '<br>';
echo "=============================<br>";
