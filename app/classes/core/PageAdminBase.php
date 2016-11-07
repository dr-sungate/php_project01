<?php 
/*
 * 
 * 管理サイトPage基底サブクラス
 * @author xxxxxxxx (c)2015
 * 
 */
define("SESSION_ADMINAUTH", "adminauth");
define("ADMIN_PAGE_LAYOUT_BASE", "bookadmin/layout.tpl");
define("ADMIN_ERROR_PAGE", "bookadmin/error.tpl");

class PageAdminBase extends PageBase{
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct($pagetitle) {
		parent::__construct();
		parent::setPageLayoutBase(ADMIN_PAGE_LAYOUT_BASE, $pagetitle);
	}
	/*************************
	* ログインチェック
	**************************/
	public function checkAuth(){
		if($this->checkSession() == false){
			parent::displayErrorPage(ADMIN_ERROR_PAGE, "ログインエラー");
		}
	}
	private function checkSession(){
		if(isset($_SESSION[SESSION_ADMINAUTH]) && isset($_SESSION[SESSION_ADMINAUTH]['member_id']) && $_SESSION[SESSION_ADMINAUTH]['member_id'] != null && $_SESSION[SESSION_ADMINAUTH]['member_id'] != ""){
			
			$sql = "SELECT * FROM member_info WHERE member_id = :member_id";
			$setParam = array();
			$setParam[':member_id']['value'] = $_SESSION[SESSION_ADMINAUTH]['member_id'];
			$setParam[':member_id']['type'] = PARAM_TYPE_STR;
			Logger::debug($sql);
			Logger::debug($setParam);
			//いったんクリアする
			unset($_SESSION[SESSION_ADMINAUTH]);		
				
			$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
			if($result != null && $this->DBforView->get_Rows() > 0){
				Logger::debug("ログイン済み");
				$_SESSION[SESSION_ADMINAUTH] = array();
				$_SESSION[SESSION_ADMINAUTH]['member_id'] = $result[0]['member_id'];
				$_SESSION[SESSION_ADMINAUTH]['name'] = $result[0]['name'];
				$_SESSION[SESSION_ADMINAUTH]['roll'] = $result[0]['roll'];
				$_SESSION[SESSION_ADMINAUTH]['status'] = $result[0]['status'];
				return true;
			}
		}else{
			return false;
		}
	}
	
	/*************************
	 * ページ遷移用最終処理関連
	 **************************/
	public function initDisplay(){
		parent::setForInitPage();
		parent::displayPage();
	}
	public function initPageVars(){
		parent::setForInitPage();
	}
	public function onlyPageDisplay(){
		parent::displayPage();
	}
	public function endDisplay(){
		parent::setForNextPage();
		parent::displayPage();
		parent::setForEndPage();
		parent::clearPageToken();
	}
	public function confirmDisplay(){
		parent::setForNextPage();
		parent::allCopySmartyVarsFromSession();
		parent::displayPage();
	}
	public function normalDisplay(){
		parent::setForNextPage();
		parent::displayPage();
	}
	public function sameDisplay(){
		parent::setForSamePage();
		parent::displayPage();
	}
	public function backAndDisplay(){
		parent::setForBackPage();
		parent::copySmartyVarsFromSession();
		parent::displayPage();
	}
	/*************************
	* バリデーション実行
	**************************/
	public function validate(){
		$allParamsKeys = $this->pageDataParams->getParamAllKey();
		foreach((array)$allParamsKeys as $key){
			$parampage = (int)$this->pageDataParams->getParamPage($key);
			if($this->pageFlow->getPageNo() === $parampage){
				$inputval = parent::getSessionVarsOne($key);
				$viewname = $this->pageDataParams->getParamName($key);
				$errorMethodList = $this->pageDataParams->getParamErrorMethod($key);
				$maxlen = $this->pageDataParams->getParamMaxlen($key);
				$minlen = $this->pageDataParams->getParamMinlen($key);
				$year = $this->pageDataParams->getParamYear($key);
				$month = $this->pageDataParams->getParamMonth($key);
				$day = $this->pageDataParams->getParamDay($key);
				$comparekey = $this->pageDataParams->getParamComparekey($key);
				$compareval = parent::getSessionVarsOne($comparekey);
				foreach((array)$errorMethodList as $errormethod){
					switch($errormethod){
						case MAX_CHECK :
						case MAXVALUE_CHECK :
							$this->validateHandler->$errormethod($maxlen, $key, $inputval, $viewname);
							break;
						case MIN_CHECK :
						case MINVALUE_CHECK :
							$this->validateHandler->$errormethod($minlen, $key, $inputval, $viewname);
							break;
															
						case DATE_CHECK :
							$this->validateHandler->$errormethod($key, $year, $month, $day, $viewname);
							break;
															
						case EQUAL_CHECK :
							$this->validateHandler->$errormethod($key, $inputval, $compareval, $viewname);
							break;
															
						default:
							$this->validateHandler->$errormethod($key, $inputval, $viewname);
							break;
					}
				}			
			}
		}
	}
	
}

?>