<?php 
/*
 * 
 * ページデータ変数クラス
 * @author xxxxxxxx (c)2015
 * 
 */


class PageDataParams{
	private $param;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->param = array();
	}
	public function __destruct(){
	}
	/*************************
	 * 値セット
	 **************************/
// 	public function setParam($key, $value = ""){
// 		$this->param[$key] = $value;
// 	}
	public function setParamName($key, $page, $name = null){
		$this->param[$key]['page'] = $page;
		$this->param[$key]['name'] = $name;
	}
	public function setParamErrorMethod($key, $errorlist){
		foreach((array)$errorlist as $value){
			$this->param[$key]['errormethod'][] = $value;
		}
	}
	public function setParamErrorMaxMinlen($key, $max = 0, $min = 0){
		$this->param[$key]['maxlen'] = $max;
		$this->param[$key]['minlen'] = $min;
	}
	public function setParamErrorDate($key, $year, $mon, $day){
		$this->param[$key]['year'] = $year;
		$this->param[$key]['month'] = $mon;
		$this->param[$key]['day'] = $day;
	}
	public function setParamErrorCompare($key, $targetkey){
		$this->param[$key]['comparekey'] = $targetkey;
	}
	/*************************
	 * 値取得
	 **************************/
	public function getParamList(){
		return $this->param;
	}
	public function getParamAllKey(){
		return array_keys($this->param);
	}
	public function getParamPage($key){
		if(isset($this->param[$key]['page'] )){
			return $this->param[$key]['page'] ;
		}
		return null;
	}
	public function getParamName($key){
		if(isset($this->param[$key]['name'] )){
			return $this->param[$key]['name'] ;
		}
		return null;
	}
	public function getParamErrorMethod($key){
		if(isset($this->param[$key]['errormethod'] )){
			return (array)$this->param[$key]['errormethod'] ;
		}
		return null;
	}
	public function getParamMaxlen($key){
		if(isset($this->param[$key]['maxlen'] )){
			return $this->param[$key]['maxlen'] ;
		}
		return null;
	}
	public function getParamMinlen($key){
		if(isset($this->param[$key]['minlen'] )){
			return $this->param[$key]['minlen'] ;
		}
		return null;
	}
	public function getParamYear($key){
		if(isset($this->param[$key]['year'] )){
			return $this->param[$key]['year'] ;
		}
		return null;
	}
	public function getParamMonth($key){
		if(isset($this->param[$key]['month'] )){
			return $this->param[$key]['month'] ;
		}
		return null;
	}
	public function getParamDay($key){
		if(isset($this->param[$key]['day'] )){
			return $this->param[$key]['day'] ;
		}
		return null;
	}
	public function getParamComparekey($key){
		if(isset($this->param[$key]['comparekey'] )){
			return $this->param[$key]['comparekey'] ;
		}
		return null;
	}
}

?>