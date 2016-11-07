<?php 
/*
 * 
 * ページ遷移制御クラス
 * @author xxxxxxxx (c)2015
 * 
 */

define("PageFlowLIST", "_PAGE_FLOW_LIST_");
define("PagePassVars", "_PAGE_PASS_VARS_");
define("TransPassVars", "_Trans_PASS_VARS_");
define("PageNo", "_PAGE_NO_");
define("PageToken", "_PAGE_TOKEN_");

class PageFlow{
	private $pageFlowList;
	private $pageNo;
	private $pagetoken;
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		if(!isset($_SESSION[PageNo])){
			$this->pageNo = "0";
			$_SESSION[PageNo] = $this->pageNo;
		}else{
			$this->pageNo = $_SESSION[PageNo] ;
		}
		//print_r("_SESSION[PageToken]::".$_SESSION[PageToken]);
		if(!isset($_SESSION[PageToken])){
			$this->initPageToken();
		}else{
			$this->pagetoken = $_SESSION[PageToken] ;
			unset($_SESSION[PageToken] );
		}
	}
	public function __destruct(){
	}
	/*************************
	 * Token新規発行
	 **************************/
	private function getNewPageToken(){
		mt_srand((double)microtime()*1000000);
		$pagetoken = md5((string)mt_rand());
		return $pagetoken;
	}
	/*************************
	 * ページ増減
	 **************************/
	public function addPageNo(){
		$this->pageNo++;
		$_SESSION[PageNo] = $this->pageNo;
	}
	public function subtractPageNo(){
		$this->pageNo--;
		if($this->pageNo < 0){
			$this->pageNo = 0;
		}
		$_SESSION[PageNo] = $this->pageNo;
	}
	public function zeroPageNo(){
		$this->pageNo = 0;
		$_SESSION[PageNo] = $this->pageNo;
	}
	/*************************
	 * Token比較（正常なページ遷移かどうか）
	 **************************/
	public function comparePageToken($requestToken){
		if($requestToken !== $this->pagetoken){
			$this->clearPagePassVars();
			$this->pageNo = -100;
		}
		$this->pagetoken = $this->getNewPageToken();
		$_SESSION[PageToken] = $this->pagetoken;
	}
	/*************************
	 * Token初期化
	 **************************/
	public function initPageToken(){
		$this->pageNo = 0;
		$this->pagetoken = $this->getNewPageToken();
		$_SESSION[PageToken] = $this->pagetoken;
	}
	
	/*************************
	 * セッション格納変数配列セット
	 **************************/
	public function setPageFlowList($flowArray){
		$_SESSION[PageFlowLIST] = serialize($flowArray);
	}
	public function setPagePassVars($vars){
		$_SESSION[PagePassVars] = serialize($vars);
	}
	public function setTransPassVars($vars){
		$_SESSION[TransPassVars] = serialize($vars);
	}
	/*************************
	 * セッション格納変数配列クリア
	 **************************/
	public function clearPagePassVars(){
		unset($_SESSION[PagePassVars]);
	}
	public function clearTransPassVars(){
		unset($_SESSION[TransPassVars]);
	}
	public function clearPageNo(){
		unset($_SESSION[PageNo]);
	}
	public function clearPageToken(){
		unset($_SESSION[PageToken]);
	}
	/*************************
	 * セッション格納変数配列取得
	 **************************/
	public function getPagePassVars(){
		if(isset($_SESSION[PagePassVars]) && $_SESSION[PagePassVars] != null && $_SESSION[PagePassVars] != ""){
			$vars = unserialize($_SESSION[PagePassVars]);
			return $vars;
		}else{
			return null;
		}
	}
	public function getTransPassVars(){
		if(isset($_SESSION[TransPassVars]) && $_SESSION[TransPassVars] != null && $_SESSION[TransPassVars] != ""){
			$vars = unserialize($_SESSION[TransPassVars]);
			return $vars;
		}else{
			return null;
		}
	}
	/*************************
	 * メンバ変数取得
	 **************************/
	public function getPageNo(){
		return (int)$this->pageNo;
	}
	public function getPageToken(){
		return $this->pagetoken;
	}
	/*************************
	 * 現在ページテンプレート取得
	 **************************/
	public function getPage(){
		if(isset($_SESSION[PageFlowLIST])){
			$pageFlowList = unserialize($_SESSION[PageFlowLIST]);
			if($this->pageNo < 0 || $pageFlowList[$this->pageNo] == null || $pageFlowList[$this->pageNo] == ""){
				$this->pageNo = 0;
				$_SESSION[PageNo] = $this->pageNo;
			}
			return $pageFlowList[$this->pageNo];
		}
	}
}

?>