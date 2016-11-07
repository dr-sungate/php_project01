<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class StepmailBatch extends BatchBase{
	private $stepmail_setting_id;
	private $taskid;
	private $generateFilePath;
	private $sendListData;
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
	public function init($batchrecord){
		$this->stepmail_setting_id = $batchrecord['stepmail_setting_id'];
		$this->taskid = TaskIDGenerator::generate();
		
	}
	/*************************
	 * メイン処理
	 **************************/
	public function process(){
		$StepmailSettings = new StepmailSettings();
		$stempmailData = $StepmailSettings->getStepmailData($this->stepmail_setting_id);
		if(empty($stempmailData)){
			parent::writeloginfo("[".__METHOD__."]"."No Stempmail Data or Client active /stepmail_setting_ID:".$this->stepmail_setting_id);
			return;
		}
 		$response = null;
		if(!$this->generateSendCompressedFile($stempmailData[0])){
			return;
		}
		if(empty($this->generateFilePath)){
			throw new Exception("Compressing File error!!");
		}
		parent::writelogdebug("[".__METHOD__."]"."start request");
		$response = $this->requestapi();
		if(!empty($response)){
			parent::writelogdebug("[".__METHOD__."]"."Success HTTP");
			$DeliveryLog = new DeliveryLog();
			$DeliveryLog->insertData($this->stepmail_setting_id, $this->taskid, $response, $this->sendListData);
		}else{
			parent::writelogerror("[".__METHOD__."]"."No HTTP response");
		}
		parent::writeloginfo("[".__METHOD__."] END   stepmail_setting_ID:".$this->stepmail_setting_id);
	}
	private function generateSendCompressedFile($stempmailData){
		$MailSendfileGenerator = new MailSendfileGenerator();
		$MailSendfileGenerator->setParam(MAIL_SYNCFILE_DIR.'/'.$stempmailData['sync_dir'], $stempmailData['list_name'], MAIL_CONTSFILE_DIR.'/'.$stempmailData['client_id'].'/'.$stempmailData['conts_name'], $this->taskid, $stempmailData['list_charset']);
		if($MailSendfileGenerator->isTargetExists()){
			parent::writelogdebug("[".__METHOD__."]"."Target File Exists");
			$this->generateFilePath = $MailSendfileGenerator->process();
			$this->sendListData = $MailSendfileGenerator->getListFileData();
			parent::writelogdebug("[".__METHOD__."]"."Compress Success");
			return true;
		}else{
			parent::writeloginfo("[".__METHOD__."]"."Target File Not Exists");
			$DeliveryLog = new DeliveryLog();
			$DeliveryLog->insertData($this->stepmail_setting_id, $this->taskid, "No Target File");
			parent::writelogdebug("[".__METHOD__."]"."insert DB DeliveryLog");
			return false;
		}
	
	}
	private function requestapi(){
		$RequestHandler = new RequestHandler();
		$RequestHandler->setRequestURI(API_URL);
		$ApiV1Dto = new ApiV1Dto();
		$compressedfile = null;
		//for under PHP5.5  
		if(class_exists('CURLFile')){
			$compressedfile = new CURLFile($this->generateFilePath);
		}else{
			$compressedfile = "@".$this->generateFilePath;
		}
		$ApiV1Dto->setParamData($this->taskid, date('Ymd H:i:s'), $compressedfile);
		$paramsArray = get_object_vars($ApiV1Dto);
		foreach((array)$paramsArray as $key=>$value){
			$RequestHandler->setPostdata($key, $value);
		}		
		parent::writelogdebug("[".__METHOD__."]"."start request API");		
		if($RequestHandler->process(RequestHandler::HTTP_POST, RequestHandler::RESPONSE_JSON) && $RequestHandler->isSuccess()){
  			$response = $RequestHandler->getResponseData();
			Logger::debug("[".__METHOD__."]");
  			Logger::debug($response);
			if(!$RequestHandler->isSuccessResponseData("success")){
				parent::writelogerror("[".__METHOD__."]");
				parent::writelogerror($response);
				$DeliveryLog = new DeliveryLog();
				$DeliveryLog->insertData($this->stepmail_setting_id, $this->taskid, $response);
				return null;
			}
  			return $response;
		}else{
			$response = $RequestHandler->getResponseData();
			$errorCode = $RequestHandler->getHttpStatusCode();
			$errorMsg = $RequestHandler->getErrorMsg();
			parent::writelogerror("[".__METHOD__."]");
			parent::writelogerror("[".__METHOD__."]".$errorCode.":".$errorMsg.":::".print_r($response, true));
			$DeliveryLog = new DeliveryLog();
			$DeliveryLog->insertData($this->stepmail_setting_id, $this->taskid, $response);
			return null;
		}
		
	}
}
