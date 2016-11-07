<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class StepmailSettings extends DaoBase {
	
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	}
	public function __destruct(){
	}
	public function getStepmailData($stepmail_setting_id){
		$setParam = array();
		$sql = "SELECT stepmail_settings.*, clients.client_name, clients.sync_dir ";
		$sql .= " FROM stepmail_settings INNER JOIN clients ON  stepmail_settings.client_id = clients.id ";
		$sql .= " WHERE clients.status = :client_status AND stepmail_settings.id = :id AND stepmail_settings.status = :stepmail_status";

		$setParam[':client_status']['value'] = 'active';
		$setParam[':client_status']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':id']['value'] = $stepmail_setting_id;
		$setParam[':id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		$setParam[':stepmail_status']['value'] = 'active';
		$setParam[':stepmail_status']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
		
		return $result;
		
	}	
}
