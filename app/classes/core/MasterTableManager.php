<?php 
/*
 * 
 * マスタ制御クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class MasterTableManager{

	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {

	}
	public function __destruct(){
	}
	/*************************
	 * マスタ全取得取得
	 **************************/
	static public function getAllMasterArray($tablename, $DBforView){
		$returnList = array();
		if($tablename != null && $tablename != ""){
			$sql = "SELECT * FROM ".$tablename." ORDER BY rank ";
			Logger::debug($sql);
		
			$result = $DBforView->getSelectArray($sql, true);
			if($result != null && $DBforView->get_Rows() > 0){
				for($i = 0; $i < $DBforView->get_Rows(); $i++){
					$returnList[$result[$i]['id']] = $result[$i]['name'];
				}
			}
		}
		return $returnList;
	}
	
}
