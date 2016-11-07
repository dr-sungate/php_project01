<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class ConvertListdataSettings extends DaoBase {
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
	}
	public function __destruct(){
	}
	public function getSettings($stepmail_setting_id) {
    	$setParam = array();
    	
    	$sql = "SELECT show_column ";
    	$sql .= " FROM convert_listdata_settings  ";
     	$sql .= " WHERE  stepmail_setting_id = :stepmail_setting_id ;";
    	    	 
		$setParam[':stepmail_setting_id']['value'] = $stepmail_setting_id;
		$setParam[':stepmail_setting_id']['type'] = PdoDBBase::PARAM_TYPE_INT;
		
     	Logger::debug($sql);
    	Logger::debug($setParam);
    	
    	$result = $this->DBforView->getPrepareSelectArray($sql, $setParam, true);
    	
    	return $result;
    	 
    }
}
