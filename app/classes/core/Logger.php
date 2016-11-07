<?php 
/*
 * 
 * 操作ログ制御クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class Logger{
	static private $logbody;
	static private $logfilename;
	const DELETE_OLD_TERM = "-1 month";
	const DELETE_OLD_TERM_MONTH = "-2 month";
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
	}
	public function __destruct(){
	}
	static public function info($message){
		if(LOGGER_LEVEL >=LOGGER_INFO){
			$dbg = debug_backtrace();
			self::writelog($message, LOGGER_INFO, $dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line'] );
		}
		//ログローテート
		Logger::deleteOld();
	}
	static public function debug($message){
		if(LOGGER_LEVEL >=LOGGER_DEBUG){
			$dbg = debug_backtrace();
			self::writelog($message, LOGGER_DEBUG, $dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line'] );
		}
		//ログローテート
		Logger::deleteOld();
	}
	static public function error($message){
		if(LOGGER_LEVEL >=LOGGER_ERROR){
    		$dbg = debug_backtrace();
 			self::writelog($message, LOGGER_ERROR, $dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line'] );
		}
		//ログローテート
		Logger::deleteOld();
	}
	static private function writelog($message, $level, $calledclassname){
		$now = new DateTime();
		self::$logbody = $now->format("Y-m-d H:i:s.U");
		self::$logbody .= " [".self::getPID()."] ";
		self::$logbody .= " <".$calledclassname."> ";
		switch($level){
			case LOGGER_INFO:
				self::$logbody .= " INFO*** ";
				break;
			case LOGGER_DEBUG:
				self::$logbody .= " DEBU*** ";
				break;
			case LOGGER_ERROR:
				self::$logbody .= " ERROR*** ";
				break;
		}
		self::$logbody .= self::convertForLog($message, "UTF-8");
		self::$logbody .= "\n";
		self::writefile();
	}
	static private function getPID(){
		$pidFuncList = array("session_id", "getmypid", "posix_getppid");
		foreach($pidFuncList as $func){
			$pid = $func();
			if(!empty($pid)){
				return $pid;
			}
		}
		return null;
	}
	static private function convertForLog($data, $charcode = null){
		if(is_array($data)){
			$data = self::convertArrayToString($data);
		}
		//$data = str_replace(",", " ", $data);
		$data = str_replace("\r\n", " ", $data);
		$data = str_replace("\r", " ", $data);
		$data = str_replace("\n", " ", $data);
		$data = str_replace("\t", " ", $data);
// 		$data = str_replace("    ","", $data);		//スペース続き除去
// 		$data = str_replace("   ","", $data);			//スペース続き除去
// 		$data = str_replace("  ","", $data);			//スペース続き除去
		if($charcode != null){
			$data = mb_convert_encoding($data, $charcode);
		}
		return $data;
	}
	static private function convertArrayToString($array){
		$returnVal = "{ ";
		$counter = 0;
		foreach((array)$array as $val){
			if($counter != 0 ) $returnVal .= " | ";
			if(is_array($val)){
				$val = self::convertArrayToString($val);
			}
			$returnVal .= $val;
			$counter++;
		}
		$returnVal .= " }";
		return $returnVal;
	}
	static private function writefile(){
		self::$logfilename = LOG_DIR."/".LOGGER_FILE_NAME.date("Y-m-d").".log";
		@file_put_contents(self::$logfilename, self::$logbody, FILE_APPEND | LOCK_EX);
		@chmod(self::$logfilename, 0777);
	}
	static public function deleteOld(){
		$deletelogfile = LOG_DIR."/".LOGGER_FILE_NAME.date("Y-m-d",strtotime(self::DELETE_OLD_TERM)).".log";
		if(file_exists($deletelogfile)){
			@unlink($deletelogfile);
		}
		$deletelogfile_month = LOG_DIR."/".LOGGER_FILE_NAME.date("Y-m-",strtotime(self::DELETE_OLD_TERM_MONTH))."*.log";
		foreach ( glob($deletelogfile_month) as $filepath ) {
				@unlink($filepath);
		}
	}
	
}
