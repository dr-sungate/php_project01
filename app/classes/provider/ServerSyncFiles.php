<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class ServerSyncFiles {
	const SSH_PRIVATEKEY = "id_rsa_stepmailrsync";
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
	}
	public function __destruct(){
	}
	public function rsyncFiles($remotessh, $remotedir, $syncdir){
		if(!file_exists($syncdir)){
			mkdir($syncdir,  0755, true);
		}
		
		$command = "/usr/bin/rsync -avz --no-p --no-o --no-g --delete -e 'ssh -i  ".SSH_KEYDIR."/".self::SSH_PRIVATEKEY."' $remotessh:$remotedir/* $syncdir/ ";
		print_r($command);
		exec( "$command 2>&1" , $output, $return_var);
		if($return_var == 0){
		print_r($output);
			return true;
		}else{
			Logger::error("[".__METHOD__."]");
			Logger::error($output, true);
			return false;
		}
	}
}
