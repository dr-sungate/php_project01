<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class TaskIDGenerator{
	const IPGET_COMMAND= "hostname -I";
	
	public function __construct() {
	}
	public function __destruct(){
	}
	    /**
     * プロセス.
     *
     * @return void
     */
    public static function generate() {
    	//サーバのIPアドレスを接尾辞に付与
		exec(self::IPGET_COMMAND, $output);
		$serverip = $output[0];
		Logger::debug("ServerIP:".$serverip);
		$prefix = self::calcSuffixFromIP($serverip);
		Logger::debug("TaskID prefix:".$prefix);
		return uniqid($prefix.".");
	}
   private static function calcSuffixFromIP( $ipaddress ){
		$return_suffix = 0;
		if(!empty($ipaddress)){
	    	$ipaddress_divide = explode(".", $ipaddress);
	    	$count = count($ipaddress_divide);
//  	    	foreach($ipaddress_divide as $divide){
//  	    		$return_suffix += $divide*pow(256, $count-1);
//  	    		$count--;
//  	    	}
			//桁数オーバーになるので2・3・４番目のみ使用
			if(isset($ipaddress_divide[1])){
				$return_suffix += $ipaddress_divide[1]*254;
			}
			if(isset($ipaddress_divide[2])){
				$return_suffix += $ipaddress_divide[2]*254;
			}
			if(isset($ipaddress_divide[3])){
				$return_suffix += $ipaddress_divide[3];
			}
		}
		return $return_suffix;
	}
}
