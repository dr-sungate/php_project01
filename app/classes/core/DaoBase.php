<?php 
/*
 * 
 * Dao基底クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class DaoBase{
	public $DBforView;
	public $DBforEdit;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		/*-- データベース変数--*/
		$this->DBforView = new DBBase();
		$this->DBforEdit = new DBBase();
	}
	public function __destruct(){
	}
	
}
