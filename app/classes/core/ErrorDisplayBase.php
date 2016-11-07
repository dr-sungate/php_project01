<?php 
/*
 * 
 * ログ制御クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class ErrorDisplayBase{
	private $logbody;
	private $logfilename;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->logbody = "";
	}
	public function __destruct(){
	}
	public function setlogfile($logfilepath){
		$this->logfilename = $logfilepath;
	}
	public function writelog($param, $logVars){
		$this->logbody = date("Y-m-d H:i;s");
		$this->logbody .= ",";
		$this->logbody .= $_SERVER['REMOTE_ADDR'];
		foreach($param as $key){
			$this->logbody .= ",";
			$this->logbody .= $this->convertForCSV($logVars[$key], "SJIS");
		}
		$this->logbody .= "\n";
		$this->writefile();
	}
	private function convertForCSV($data, $charcode = null){
		if(is_array($data)){
			$data = $this->convertArrayToString($data);
		}
		$data = str_replace(",", " ", $data);
		$data = str_replace("\r\n", " ", $data);
		$data = str_replace("\r", " ", $data);
		$data = str_replace("\n", " ", $data);
		$data = str_replace("\t", " ", $data);
		if($charcode != null){
			$data = mb_convert_encoding($data, $charcode);
		}
		return $data;
	}
	private function convertArrayToString($array){
		$returnVal = "{ ";
		$counter = 0;
		foreach((array)$array as $val){
			if($counter != 0 ) $returnVal .= " | ";
			if(is_array($val)){
				$val = $this->convertArrayToString($val);
			}
			$returnVal .= $val;
			$counter++;
		}
		$returnVal .= " }";
		return $returnVal;
	}
	private function writefile(){
		@file_put_contents($this->logfilename, $this->logbody, FILE_APPEND | LOCK_EX);
	}
	
}

?>