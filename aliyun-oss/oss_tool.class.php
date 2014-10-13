<?php
//defined('IN_PHPCMS') or exit('No permission resources.'); 
/**
 * 阿里云OSS操作工具类
 */

/**
 * 加载sdk包以及错误代码包
 */
require_once 'sdk.class.php';

class OssTool
{ 
        var $bucket;
        /**
         *@var URL签名有效时间 
         */
        var $stimeout;
        var $debugMode;

        /**
         * 获取本对象实例
         * @staticvar OssTool $object
         * @return OssTool实例的引用
         */
	function &instance() {
		static $object;
		if(empty($object)) {
			$object = new OssTool();
		}
		return $object;
	}

        function __construct() {
		$this->bucket = OSS_DEFAULT_BUCKET;
		$this->stimeout = OSS_SIGN_TIMEOUT;
                $this->debugMode=false;
	}

        /**
         * 上传函数upload，$source为附件本地文件名，$target为远程文件名
         * @param type $source 本地文件名
         * @param type $target 远程文件名
         * @param type $options 可选项
         * @return 成功返回1，否则返回0.
         */
	function upload($source, $target,$options=NULL){
		$obj = new ALIOSS();
                $obj->set_debug_mode($this->debugMode);
		$bucket = $this->bucket;
		$response = $obj->upload_file_by_file($bucket,$target,$source,$options);
		$rt = OssTool::status($response);
		return $rt == '2' ? 1 : 0;
	}

	/**
	 * 清理地址路径中的非法字符
	 */
	function clear($str) {
		return str_replace(array( "\n", "\r", '..'), '', $str);
	}

        /**
         * 获取远程文件的大小。
         * @param type $remote_file 远程文件
         * @return 远程文件的大小，如果不存在返回0.
         */
	function size($remote_file) {
		$obj = new ALIOSS();
                $obj->set_debug_mode($this->debugMode);
		$bucket = $this->bucket;
		$remote_file = OssTool::clear($remote_file);
		$response = $obj->is_object_exist($bucket,$remote_file);
		$rt = OssTool::status($response);
		return $rt == '2' ? $response->header["content-length"] : 0;
	}

        /**
         * 删除远程附件，编辑帖子或者删除附件时，可同步删除OSS上的文件
         * @param type $path
         * @return 成功返回1，否则返回0.
         */
	function delete($path){
		$obj = new ALIOSS();
                $obj->set_debug_mode($this->debugMode);
		$bucket = $this->bucket;
		$path = OssTool::clear($path);
		$path = str_replace($bucket.'/','',$path);
		$response = $obj->delete_object($bucket,$path);
		$rt = OssTool::status($response);
		return $rt == '2' ? 1 : 0;
        }

        /**
         * 签名URL，在后台打开隐藏附件URL时，即自动使用签名．
         * @param type $file
         * @return 签名后的ＵＲＬ
         */
	function sign_url($file){
		$obj = new ALIOSS();
                $obj->set_debug_mode($this->debugMode);
		$obj->set_enable_domain_style();
		$bucket = $this->bucket;
		$file = OssTool::clear($file);
		$timeout = $this->stimeout;
		$response = $obj->get_sign_url($bucket,$file,$timeout);
		return $response;
	}

        /**
         * 下载远程附件
         * @param type $file
         * @param type $path
         * @return 成功返回1，否则返回0.
         */
	function get($file,$path){
		$obj = new ALIOSS();
                $obj->set_debug_mode($this->debugMode);
		$bucket = $this->bucket;
		$file = OssTool::clear($file);
		$path = OssTool::clear($path);
		$options = array(
			ALIOSS::OSS_FILE_DOWNLOAD => $path,
		);
		$response = $obj->get_object($bucket,$file,$options);
		$rt = OssTool::status($response);
		return $rt == '2' ? 1 : 0;
	}
	
	 
        /**
         * 拷贝远程附件，用于改名或者生成临时文件等
         * @param type $fromfile
         * @param type $tofile
         * @return 成功返回1，否则返回0.
         */
	function copy($fromfile,$tofile){
		$obj = new ALIOSS();	
                $obj->set_debug_mode($this->debugMode);
		$bucket = $this->bucket;
		$fromfile = OssTool::clear($fromfile);
		$tofile = OssTool::clear($tofile);
		$response = $obj->copy_object($bucket,$fromfile,$bucket,$tofile);
		$rt = OssTool::status($response);
		return $rt == '2' ? 1 : 0;
	}

        /**
         * 因为阿里云OSS返回状态为2XX时，均代表成功，故取返回值的第一位是否为2判断是否成功
         * @param type $response 阿里云OSS响应对象
         * @return 状态位
         */
	function status($response){
		$rt='0';
		$rstatus=$response->status;
		if ($rstatus > ''){
			$rt=substr($rstatus,0,1);
		}
		return $rt;
	}
}