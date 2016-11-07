<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class ReportDeliveryLogs extends BatchBase{
	const mailtemplate = "adminmail/reportdeliverylogs.tpl";
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
    	$DeliveryLog = new DeliveryLog();
		$reportList = $DeliveryLog->getDailyReport();
		if(!empty($reportList)){
			$reportList = $this->convertSendListData($reportList);
			$this->sendReportMail($reportList);
		}else{
			parent::writeloginfo("No Delivery Logs");
		}
	}
	private function sendReportMail($reportList){
		$mailVars = $reportList;
		$this->mailbase->setHeader(MAIL_FROM, MAIL_TO, MAIL_CC, MAIL_BCC, MAIL_REPLYTO, "【無料ユーザー　ステップメール ".STEPMAIL_ENV."】　本日の配信レポート");
		$this->mailbase->setBody($mailVars, ReportDeliveryLogs::mailtemplate);
		$this->mailbase->send();
		
	}
	private function convertSendListData($reportList){
		$returnReportList = array();
		foreach($reportList as $report){
			$report['convertlistdata'] = "";
			$settedColumn = $this->getShowColumnList($report);
			
			$sendlistdata = $report['sendlist_data'];
			$sendlistdata = explode("\n", $sendlistdata);
			
			$showPosition = $this->getPositionList($sendlistdata[0], $settedColumn);
			foreach($sendlistdata as $sendlistline){
				$sendlistline = str_getcsv($sendlistline, ',');
				$convertlistdata = array();
				for($i = 0; $i< count($sendlistline); $i++){
					if(in_array($i, $showPosition)){
						$convertlistdata[] = $sendlistline[$i];
					}
				}
				$report['convertlistdata'] .= implode(",", $convertlistdata);
				$report['convertlistdata'] .= "\n";
				unset($convertlistdata);
			}
			$returnReportList[] = $report;
		}
		return $returnReportList;
	}
	private function getShowColumnList($report){
		$settingResult = $this->getConvertSettingList($report);
		$settingColumn = array();
		foreach($settingResult as $result){
			$settingColumn[] = $result['show_column'];
		}
		return $settingColumn;
	}
	private function getConvertSettingList($report){
		$ConvertListdataSettings = new ConvertListdataSettings();
		return $ConvertListdataSettings->getSettings($report['stepmail_setting_id']);		
	}
	private function getPositionList($headerline, $settedColumn){
		$showPosition = array();
		$headerlinecsv = str_getcsv($headerline, ',');
		for($i = 0; $i < count($headerlinecsv); $i++){
			if(in_array($headerlinecsv[$i], $settedColumn)){
				$showPosition[] =$i;
			}
		}
		return $showPosition;
	}
}
