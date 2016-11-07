<?php 
/*
 * 
 * エラー操作クラス
 *  @author  xxxxxxxx (c)2012
 * 
 */

class ErrorHandler{
	public $errors;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->errors = array();
	}
	public function __destruct(){
	}
	/*************************
	 * 値セット
	 **************************/
	public function addErrorMessage($key, $message){
		if(isset($this->errors[$key])){
			$this->errors[$key] .= $message;
		}else{
			$this->errors[$key] = $message;
		}
	}
	/*************************
	 * 値取得
	 **************************/
	public function getAllList(){
		return $this->errors;
	}
}
