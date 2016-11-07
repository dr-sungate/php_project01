<?php 

class ApiV1Dto {
	public $Type = API_TYPE;
	public $ClientID = API_CLIENTID;
	public $TaskID;
	public $ToListNum = '1';
	public $Interval = API_INTERVAL;
	public $DeliveryTime;
	public $datafile;
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
	}
	public function __destruct(){
	}
	public function setParamData($TaskID, $DeliveryTime, $datafile) {
		$this->TaskID = $TaskID;
		$this->DeliveryTime = $DeliveryTime;
		$this->datafile = $datafile;
	}
	
}
