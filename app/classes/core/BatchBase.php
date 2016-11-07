<?php 
/*
 * 
 * Batch基底クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class BatchBase{
	private $smarty;
	public $mailbase;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		/*-- Smarty初期設定--*/
		$this->smartyVars = array();
		$this->setSmartyVars('dummy', 'xxx');
		$this->initSmarty();
		/*-- メールクラス変数--*/
		$this->mailbase = new MailBase($this->smarty);
		
	}
	public function __destruct(){
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
	 * エラー表示
	 **************************/
	public function logerrorAndEnd($message){
		$dbg = debug_backtrace();
		Logger::error(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		Logger::error($message);
		var_dump(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		var_dump($message);
		exit;
	}
	/*************************
	 * ログ表示
	 **************************/
	public function writelogerror($message){
		$dbg = debug_backtrace();
		Logger::error(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		Logger::error($message);
		var_dump(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		var_dump($message);
	}
	public function writeloginfo($message){
		$dbg = debug_backtrace();
		Logger::info(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		Logger::info($message);
		var_dump(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		var_dump($message);
	}
	public function writelogdebug($message){
		$dbg = debug_backtrace();
		Logger::debug(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		Logger::debug($message);
		var_dump(" at ".$dbg[1]['class']."::".$dbg[1]['function']."--line ".$dbg[1]['line']."");
		var_dump($message);
	}
	
}
