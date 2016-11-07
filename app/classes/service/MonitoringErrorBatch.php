<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class MonitoringErrorBatch extends BatchBase{
	const mailtemplate = "adminmail/monitorerrorbatch.tpl";
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	
	}
	public function __destruct(){
	}
	/*************************
	 * 初期処理
	 **************************/
	public function init(){
		
	}
	/*************************
	 * メイン処理
	 **************************/
	public function process(){
    	$BatchManager = new BatchManager();
		$errorList = $BatchManager->getErrorBatchList();
		if(!empty($errorList)){
			$this->sendErrorReportMail($errorList);
		}
	}
	private function sendErrorReportMail($errorList){
		$mailVars = $errorList;
		$this->mailbase->setHeader(MAIL_FROM, MAIL_TO, MAIL_CC, MAIL_BCC, MAIL_REPLYTO, "【無料ユーザー　ステップメール ".STEPMAIL_ENV."】　エラーのあるバッチがあります");
		$this->mailbase->setBody($mailVars, MonitoringErrorBatch::mailtemplate);
		$this->mailbase->send();
		
	}
}
