<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class Clients extends DaoBase {
	
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	}
	public function __destruct(){
	}
	    /**
     * プロセス.
     *
     * @return void
     */
    public function getClientByID($clientid) {
		$setParam = array();
		$sql = "SELECT * ";
		$sql .= " FROM clients ";
		$sql .= " WHERE id = :id AND status = :status";

		$setParam[':status']['value'] = 'active';
		$setParam[':status']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':id']['value'] = $clientid;
		$setParam[':id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
		
		return $result;
    }
	    /**
     * プロセス.
     *
     * @return void
     */
    public function getClientByStepmailSettingsID($stepmail_settings_id) {
		$setParam = array();
		$sql = "SELECT * ";
		$sql .= " FROM clients ";
		$sql .= " WHERE clients.id = (SELECT client_id FROM stepmail_settings WHERE stepmail_settings.id = :id) AND status = :status";

		$setParam[':status']['value'] = 'active';
		$setParam[':status']['type'] = PdoDBBase::PARAM_TYPE_STR;
		$setParam[':id']['value'] = $stepmail_settings_id;
		$setParam[':id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
		
		return $result;
    }
	    /**
     * プロセス.
     *
     * @return void
     */
    public function getAllActiveClient() {
		$setParam = array();
		$sql = "SELECT * ";
		$sql .= " FROM clients ";
		$sql .= " WHERE status = :status";

		$setParam[':status']['value'] = 'active';
		$setParam[':status']['type'] = PdoDBBase::PARAM_TYPE_STR;
		
		Logger::debug($sql);
		Logger::debug($setParam);
		
		$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
		
		return $result;
    }
}
