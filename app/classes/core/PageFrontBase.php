<?php 
/*
 * 
 * フロントPage基底サブクラス
 * @author xxxxxxxx (c)2015
 * 
 */
define("FRONT_ERROR_PAGE", "booking/error.tpl");

class PageFrontBase extends PageBase{
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