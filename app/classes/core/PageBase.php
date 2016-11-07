<?php 
/*
 * 
 * Page基底クラス
 * @author xxxxxxxx (c)2015
 * 
 */

define("SMARTY_MODULE_DIR", dirname(__FILE__).'/../modules/Smarty/libs');

class PageBase{
	private $isMobile;
	private $isSphone;
	private $MobileCarrier;
	private $PcBrowser;
	private $FullUserAgent;
	private $UserAgentVars;
	private $smarty;
	public $pageFlow;
	private $pageTemplateName;
	private $smartyVars;
	private $sessionVars;
	private $transSessionVars;
	public $pageDataParams;
	public $validateHandler;
	public $mailbase;
	public $logbase;
	public $DBforView;
	public $DBforEdit;
	public $pageLayotBase;
	public $pageTitle;
	public $requestURL;
	public $onLoadAlertMsg;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->pageLayotBase = null;
		$this->pageTitle = null;
		$this->onLoadAlertMsg = null;
		/*-- Smarty初期設定--*/
		$this->smartyVars = array();
		$this->setSmartyVars('dummy', 'xxx');
		$this->initSmarty();
		
		/*-- モバイル判定--*/
		$mobileUserAgent = new MobileUserAgent();
		$this->isMobile = $mobileUserAgent->isMobile();
		$this->MobileCarrier =  $mobileUserAgent->getMobileCarrier();
		$this->isSphone = $mobileUserAgent->isSphone();
		$this->PcBrowser = $mobileUserAgent->getPcBrowser();
		$this->FullUserAgent = $mobileUserAgent->getFullUserAgent();
		$this->UserAgentVars = array();
		$this->UserAgentVars['mobile'] = $this->isMobile ;
		$this->UserAgentVars['sphone'] = $this->isSphone;
		$this->UserAgentVars['blowser'] = $this->PcBrowser;
		$this->UserAgentVars['useragent'] = $this->FullUserAgent;
		$this->UserAgentVars['MobileCarrier'] = $this->MobileCarrier;
		
		
		/*-- セッション初期化--*/
		$this->iniSession();
		
		/*-- ページフロー設定--*/
		$this->pageFlow = new PageFlow();
		$this->sessionVars = $this->pageFlow->getPagePassVars();
		$this->transSessionVars = $this->pageFlow->getTransPassVars();
		//セッションの変数は一旦クリアする
		$this->pageFlow->clearPagePassVars();
		
		/*-- ページデータ変数--*/
		$this->pageDataParams = new PageDataParams();
		/*-- バリデーション変数--*/
		$this->validateHandler = new ValidateHandler();
		/*-- メールクラス変数--*/
		$this->mailbase = new MailBase($this->smarty);
		/*-- ログクラス変数--*/
		$this->logbase = new LogBase();
		/*-- データベース変数--*/
		$this->DBforView = new DBBase();
		$this->DBforEdit = new DBBase();
		
		
	}
	public function __destruct(){
	}
	/*************************
	 * コンストラクタ
	 **************************/
	public function setPageLayoutBase($layoutbasefile, $pagetitle) {
		$this->pageLayotBase = $layoutbasefile;
		$this->pageTitle = $pagetitle;
		$this->requestURL = $_SERVER['REQUEST_URI'];
	}
	/*************************
	 * Token関連
	 **************************/
	public function checkPageToken($request){
		$requestToken = null;
		if(isset($request[PageToken])) $requestToken =  $request[PageToken];
		$this->pageFlow->comparePageToken($requestToken);
	}
	public function initializePageToken(){
		$this->pageFlow->initPageToken();
	}
	/*************************
	 * ページ遷移リスト関連
	 **************************/
	public function setPageFlowList($PCArray, $MobileArray = null , $SphoneArray = null){
		if($this->isMobile && $MobileArray){
			$this->pageFlow->setPageFlowList($MobileArray);
		}elseif($this->isSphone && $SphoneArray){
			$this->pageFlow->setPageFlowList($MobileArray);
		}else{
			$this->pageFlow->setPageFlowList($PCArray);
		}
	}
	/*************************
	 * ページ遷移用最終処理関連
	 **************************/
	public function setForInitPage(){
		$this->pageFlow->clearPagePassVars();
		$this->pageFlow->zeroPageNo();
	}
	public function setForEndPage(){
		$this->pageFlow->clearPagePassVars();
		$this->pageFlow->clearPageNo();
	}
	public function setForNextPage(){
		$this->pageFlow->setPagePassVars($this->sessionVars);
		$this->pageFlow->addPageNo();
	}
	public function setForSamePage(){
		$this->pageFlow->setPagePassVars($this->sessionVars);
	}
	public function setForBackPage(){
		$this->pageFlow->setPagePassVars($this->sessionVars);
		$this->pageFlow->subtractPageNo();
	}
	public function clearPageToken(){
		$this->pageFlow->clearPageToken();
	}
	public function setSessionVarsNow(){
		$this->pageFlow->setPagePassVars($this->sessionVars);
	}
	public function setTransSessionVarsNow(){
		$this->pageFlow->setTransPassVars($this->transSessionVars);
	}
	public function cleaTransSessionVarsNow(){
		$this->pageFlow->clearTransPassVars();
	}
	/*************************
	 * Smartyページ表示処理
	 **************************/
	public function displayPage(){
		//Tokenをセット
		$this->setSmartyVars("pagetoken", $this->pageFlow->getPageToken());
		//表示データをアサイン
		$this->smarty->assign("FormData", $this->smartyVars);			
		//デバッグ用データをアサイン
		$this->smarty->assign("UserAgentVars", $this->UserAgentVars);			
		//エラーデータをアサイン
		$this->smarty->assign("Errors", $this->validateHandler->getErrors());		
		
		//ページタイトルをアサイン
		if(isset($this->pageTitle) && $this->pageTitle != null){
			$this->smarty->assign("PAGETITLE", $this->pageTitle);
		}
		
		//URLをアサイン
		if(isset($this->requestURL) && $this->requestURL != null){
			$this->smarty->assign("REQUESTURL", $this->requestURL);
		}
		//ページ読み込み時のJavascriptメッセージをアサイン
		if(isset($this->onLoadAlertMsg) && $this->onLoadAlertMsg != null){
			$this->smarty->assign("OnLoadAlertMsg", $this->onLoadAlertMsg);
		}
		//ログイン用セッション情報をアサイン
		if(defined("SESSION_ADMINAUTH") && isset($_SESSION[SESSION_ADMINAUTH])&& $_SESSION[SESSION_ADMINAUTH] != null){
			$this->smarty->assign("AUTHINFO", $_SESSION[SESSION_ADMINAUTH]);
		}
		
		/****************ページレイアウトが有る場合はセットする***************/
		$pageShow = null;
		if($this->pageLayotBase != null){
			$this->smarty->assign("BODYTEMPLATE", $this->smarty->fetch($this->pageFlow->getPage()));		
			$pageShow = $this->pageLayotBase;
		}else{
			$pageShow = $this->pageFlow->getPage();
		}
		//テンプレート表示
		if($this->isMobile){
			$output = $this->smarty->fetch($pageShow);
			//半角カナにしてパケ代をとりあえず節約。
			$output=mb_convert_kana($output,"k","UTF-8");
			//SJISに変換しましょう。
			$output=mb_convert_encoding($output,"SJIS", "UTF-8");
			if ($this->MobileCarrier == MOBILE_CARRIER_DOCOMO) {
			 	header('Content-Type: application/xhtml+xml');
			}
			echo "<?xml version=\"1.0\" encoding=\"Shift_JIS\" ?>";
			echo $output;
				
		}else{
			$this->smarty->display($pageShow);
		}
	
	}
	/*************************
	 * エラーページ表示
	 **************************/
	public function displayErrorPage($errorpage, $message){
		//エラーデータをアサイン
		$this->smarty->assign("ErrorMessage", $message);

			$pageShow = null;
		if($this->pageLayotBase != null){
			$this->smarty->assign("BODYTEMPLATE", $this->smarty->fetch($errorpage));		
			$pageShow = $this->pageLayotBase;
		}else{
			$pageShow = $errorpage;
		}
		$this->smarty->display($pageShow);
		exit;
	}
	/*************************
	 * ページデータをSmartyページ表示用変数セット
	 **************************/
	public function setVarsFromParamData($request = null){
		foreach((array)$this->pageDataParams->getParamAllKey() as $key){
			$parampage = (int)$this->pageDataParams->getParamPage($key);
			if($this->pageFlow->getPageNo() === $parampage){
				$setValue = "";
				if($request != null && isset($request[$key])) $setValue = $request[$key];
				$this->setSmartyVars($key, $setValue);
				if(strpos($key, "search_") !== false || strpos($key, "trans_") !== false){
					$this->setTransSessionVars($key, $setValue);
				}else{
					$this->setSessionVars($key, $setValue);
				}
			}else{
				if($this->isDefineSmartyVars($key)==false){
					//Smartyエラー回避用
					$this->setSmartyVars($key, null);
				}
			}
		}
	}
	public function copySmartyVarsFromSession(){
		foreach((array)$this->sessionVars as $key=>$setValue){
			$parampage = (int)$this->pageDataParams->getParamPage($key);
			if($this->pageFlow->getPageNo() === $parampage){
				$this->setSmartyVars($key, $setValue);
			}else{
				if($this->isDefineSmartyVars($key)==false){
					//Smartyエラー回避用
					$this->setSmartyVars($key, null);
				}
			}
		}
	}
	public function allCopySmartyVarsFromSession(){
		foreach((array)$this->sessionVars as $key=>$setValue){
				$this->setSmartyVars($key, $setValue);
		}
	}
	/*************************
	 * Smartyページ表示用変数セット
	 **************************/
	public function setSmartyVars($key, $value){
		$this->smartyVars[$key] = $value;
	}
	public function isDefineSmartyVars($key){
		return isset($this->smartyVars[$key]);
	}
	public function getAllSmartyVars(){
		return $this->smartyVars;
	}
	
	/*************************
	 * ページ遷移用セッション変数関連
	 **************************/
	public function setSessionVars($key, $value){
		$this->sessionVars[$key] = $value;
	}
	public function getSessionVars(){
		return $this->sessionVars;
	}
	public function getSessionVarsOne($key){
		if(isset($this->sessionVars[$key])){
			return $this->sessionVars[$key];
		}
		return null;
	}
	/*************************
	 * ページ渡し用セッション変数関連
	 **************************/
	public function setTransSessionVars($key, $value){
		$this->transSessionVars[$key] = $value;
	}
	public function getTransSessionVars(){
		return $this->transSessionVars;
	}
	public function getTransSessionVarsOne($key){
		if(isset($this->transSessionVars[$key])){
			return $this->transSessionVars[$key];
		}
		return null;
	}
	/*************************
	 * Smarty初期化処理
	 **************************/
	private function initSmarty(){
		//Smartyオブジェクト作成
		$this->smarty = new Smarty();
		//Smartyのディレクトリ設定(キャッシュやテンプレート置き場など)
		$this->smarty->template_dir = TEMPLATE_DIR;
		$this->smarty->compile_dir = "/tmp/smarty/templates_c";
		$this->smarty->cache_dir ="/tmp/smarty/cache";
		// 一時間以上経過しているファイルをすべてクリアします
		$this->smarty->clearAllCache(3600);
			
	}
	/*************************
	 * Session初期化処理
	 **************************/
	private function iniSession(){
		//モバイルの場合
		if($this->isMobile && !$this->isSphone){
			ini_set("session.use_cookies", false);
			ini_set("session.use_only_cookies", false);
			ini_set("session.use_trans_sid", true);
     		ini_set('mbstring.http_input', "SJIS");
     	   	ini_set('mbstring.http_output', "SJIS");
	        ini_set('mbstring.internal_encoding', "UTF-8");
	        //htmlからのinputデータを変換
        	$_REQUEST = Util::convertArrayFullWidthKana($_REQUEST, "SJIS");
 	        $_REQUEST = Util::convertArrayEncoding($_REQUEST, "UTF-8", "SJIS");
       		session_start();
			output_add_rewrite_var(session_name(), session_id());
		}else{
			ini_set("session.use_cookies", true);
			ini_set("session.use_only_cookies", true);
			ini_set("session.use_trans_sid", false);
			session_start();
		}
		
	}
	/*************************
	 * モバイル判定、スマフォ判定
	 **************************/
	public function isMobileAgent(){
		return $this->isMobile;
	}
	public function isSphoneAgent(){
		return $this->isSphone;
	}
	
}
