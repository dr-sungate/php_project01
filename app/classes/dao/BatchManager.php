<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class BatchManager extends DaoBase{
	
	
	const BATCH_LOOP_TIME = "+1 minutes";
	const BATCH_INTERVAL_MIN = 3;
	const BATCH_NOTARGET_LASTDATE_MIN =  "-4 minute";
	const RESET_ERROR_PROCESS_TERM = "-3 hours";
	const BATCH_ERROR_PROCESS_INTERVAL =  "-1 hour";
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	}
	public function __destruct(){
	}
	public function getCurrentJob(){
		$setParam = array();
		
		$sql = "SELECT id, client_id, stepmail_setting_id , batch_class";
		$sql .= " FROM batch_manager ";
		$sql .= " WHERE (week IS NULL OR week = :week ) ";
		$sql .= " AND (month IS NULL OR month = :month ) ";
		$sql .= " AND (day IS NULL OR day = :day ) ";
		$sql .= " AND (hour IS NULL OR hour = :hour )";
		$sql .= " AND (minute IS NULL OR  (minute  <= :minute1  AND  minute + ".self::BATCH_INTERVAL_MIN."  >= :minute2 ) OR  (minute  > 60 - ".self::BATCH_INTERVAL_MIN." AND ".self::BATCH_INTERVAL_MIN."  >= :minute3  AND  minute + ".self::BATCH_INTERVAL_MIN."  <= :minute4 )) ";
		$sql .= " AND (proccess_flg IS NULL OR proccess_flg <> :proccess_flg) AND ( last_enddate IS NULL OR last_enddate  < ( now()  + interval ".self::BATCH_NOTARGET_LASTDATE_MIN.")) ORDER  BY  id limit 1";
		
		$setParam[':week']['value'] = (int)0;
		$setParam[':week']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':month']['value'] = date("n");
		$setParam[':month']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':day']['value'] = date("j");
		$setParam[':day']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':hour']['value'] = (int)date("H");
		$setParam[':hour']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':minute1']['value'] = (int)date("i");
		$setParam[':minute1']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':minute2']['value'] = (int)date("i");
		$setParam[':minute2']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':minute3']['value'] = (int)date("i");
		$setParam[':minute3']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':minute4']['value'] = (int)date("i") + 60;
		$setParam[':minute4']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':proccess_flg']['value'] = 1;
		$setParam[':proccess_flg']['type'] = PdoDBBase::PARAM_TYPE_INT;
		
		//Logger::debug($sql);
		//Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);

		return $result;
	}
	public function setStartProcessFlg($batchrecord){
		$setParam = array();
		
		$sql = "UPDATE batch_manager SET proccess_flg = :flg , process_startdate = :process_startdate, update_date = now()  WHERE id=:id ";
		
		$setParam[':flg']['value'] = 1;
		$setParam[':flg']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':id']['value'] = $batchrecord['id'];
		$setParam[':id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':process_startdate']['value'] = date('Y-m-d H:i:s');
		$setParam[':process_startdate']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug("Batch SET START FLAG ***ID:::".$batchrecord['id']);
		
		$this->DBforEdit->setQuery("LOCK TABLES batch_manager  WRITE; ", true, false);
		$this->DBforEdit->beginTrans();
		$result = $this->DBforEdit->setPrepareQuery($sql, $setParam, false, false);
		if($result){
			$this->DBforEdit->commitTrans();
			$this->DBforEdit->setQuery("UNLOCK TABLES; ", true, false);
		}else{
			$this->DBforEdit->rollbackTrans();
			$errorMSG = $this->DBforEdit->get_Error();
			$this->DBforEdit->setQuery("UNLOCK TABLES; ", true, false);
			parent::logerrorAndEnd("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
			exit;
		}
	}
	public function setEndProcessFlg($batchrecord){
		$setParam = array();
		
		$sql = "UPDATE batch_manager SET proccess_flg = null , last_enddate = :last_enddate , update_date = now()  WHERE id=:id ";
		
		$setParam[':id']['value'] = $batchrecord['id'];
		$setParam[':id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':last_enddate']['value'] = date('Y-m-d H:i:s');
		$setParam[':last_enddate']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug("Batch SET END FLAG ***ID:::".$batchrecord['id']);
		
		$this->DBforEdit->setQuery("LOCK TABLES batch_manager  WRITE; ", true, false);
		$this->DBforEdit->beginTrans();
		$result = $this->DBforEdit->setPrepareQuery($sql, $setParam, false, false);
		if($result){
			$this->DBforEdit->commitTrans();
			$this->DBforEdit->setQuery("UNLOCK TABLES; ", true, false);
		}else{
			$this->DBforEdit->rollbackTrans();
			$errorMSG = $this->DBforEdit->get_Error();
			$this->DBforEdit->setQuery("UNLOCK TABLES; ", true, false);
			parent::logerrorAndEnd("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
			exit;
		}
		
	}
	public function resetErrorProcessFlg(){
		$setParam = array();
		
		$sql = "UPDATE batch_manager SET proccess_flg = null , update_date = now() WHERE proccess_flg=:proccess_flg AND process_startdate < :process_startdate; ";
		
		$setParam[':proccess_flg']['value'] = 1;
		$setParam[':proccess_flg']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':process_startdate']['value'] = date("Y-m-d H:i:s", strtotime(self::RESET_ERROR_PROCESS_TERM));
		$setParam[':process_startdate']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug("resetErrorProcessFlg");
		
		$this->DBforEdit->beginTrans();
		$result = $this->DBforEdit->setPrepareQuery($sql, $setParam, false, false);
		if($result){
			$this->DBforEdit->commitTrans();
		}else{
			$this->DBforEdit->rollbackTrans();
			$errorMSG = $this->DBforEdit->get_Error();
			parent::logerrorAndEnd("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
			exit;
		}
	}
	public function getErrorBatchList(){
		$setParam = array();
		
		$sql = "SELECT batch_manager.id as batch_id, batch_manager.process_startdate, batch_manager.batch_class, stepmail_settings.*, clients.client_name ";
		$sql .= " FROM batch_manager  ";
		$sql .= " LEFT JOIN stepmail_settings ON stepmail_settings.id = batch_manager.stepmail_setting_id ";
		$sql .= " LEFT JOIN clients ON clients.id = stepmail_settings.client_id ";
		$sql .= " WHERE  proccess_flg = :proccess_flg ";
		$sql .= " AND process_startdate  < ( now()  + interval ".self::BATCH_ERROR_PROCESS_INTERVAL.")";
		
		$setParam[':proccess_flg']['value'] = 1;
		$setParam[':proccess_flg']['type'] = PdoDBBase::PARAM_TYPE_INT;
			
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);

		return $result;
	}
}
