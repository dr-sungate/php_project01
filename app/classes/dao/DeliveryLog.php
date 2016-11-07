<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class DeliveryLog extends DaoBase {
	
	const DELETE_RECORD_TERM = "-3 month";
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	}
	public function __destruct(){
		$this->deleteOld();
	}
	    /**
     * プロセス.
     *
     * @return void
     */
    public function insertData($stepmail_setting_id, $taskid, $results, $sendlistdata = '') {
    	$setParam = array();
		
		$sql = "INSERT INTO delivery_logs(stepmail_setting_id, task_id, process_date, results, sendlist_data) values(:stepmail_setting_id, :task_id, :process_date, :results, :sendlist_data) ";
		
		$setParam[':stepmail_setting_id']['value'] = $stepmail_setting_id;
		$setParam[':stepmail_setting_id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':task_id']['value'] = $taskid;
		$setParam[':task_id']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':process_date']['value'] = date('Y-m-d H:i:s');
		$setParam[':process_date']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':results']['value'] = print_r($results, true);
		$setParam[':results']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':sendlist_data']['value'] = print_r($sendlistdata, true);
		$setParam[':sendlist_data']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforEdit->setPrepareQuery($sql, $setParam, true, true);
		if(!$result){
			$errorMSG = $this->DBforEdit->get_Error();
			Logger::error("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
			exit;
		}
    }
    public function getDailyReport() {
    	$setParam = array();
    	
    	$sql = "SELECT delivery_logs.stepmail_setting_id, delivery_logs.task_id, delivery_logs.process_date, delivery_logs.results, delivery_logs.sendlist_data, stepmail_settings.*, clients.client_name ";
    	$sql .= " FROM delivery_logs  ";
    	$sql .= " LEFT JOIN stepmail_settings ON stepmail_settings.id = delivery_logs.stepmail_setting_id ";
    	$sql .= " LEFT JOIN clients ON clients.id = stepmail_settings.client_id ";
    	$sql .= " WHERE  process_date  >= now() + interval -24 hour AND process_date <= now() ;";
    	    	 
    	Logger::debug($sql);
    	Logger::debug($setParam);
    	
    	$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
    	
    	return $result;
    	 
    }
    public function deleteOld() {
    	$setParam = array();
    	
    	$sql = "DELETE FROM delivery_logs WHERE  process_date < now() +  interval ".self::DELETE_RECORD_TERM."";
     	
    	Logger::debug($sql);
    	Logger::debug($setParam);
    	
    	$result = $this->DBforEdit->setPrepareQuery($sql, $setParam, true, false);
    	if(!$result){
    		$errorMSG = $this->DBforEdit->get_Error();
    		Logger::error("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
    		exit;
    	}
    	 
    }
}
