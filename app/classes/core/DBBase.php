<?php
//***************************************************************************
//* CLASS DBBase
//* 機能：データベースクラス（フレームワーク組み込み用中間クラス） 
//* 基幹クラス：PdoDBBase 
//* 備考：PHP5.1.x用
//* ※MySQL5.x系はストレージエンジンを「InnoDB」にしないとRollbackなどが動作しないので注意
//* 作成：Created By Magnet Toy (Luxsor Recordings (c) 2009)
//***************************************************************************/

class DBBase extends PdoDBBase
{
	private $db_target		= null;
	private $db_locale			= null;
	private $db_database	= null;
	private $db_userid		= null;
	private $db_password	= null;
	private $db_option		= null;
	private $db_connection	= null;

	/*------------------------------------------------------------------/
	/	■データベース定義&接続■
	/	--DB接続子を返す
	/-----------------------------------------------------------------*/
	public function __construct(){
		/* 定数より値設定 */
		$this->db_target	= DB_TARGETE;
		$this->db_locale	= DB_LOCALE;
		$this->db_database	= DB_NAME;
		$this->db_userid	= DB_USER;
		$this->db_password	= DB_PASS;
		$this->db_option	= DB_OPTION;
		/* 親クラスのコンストラクタ呼び出し */
		parent::__construct($this->db_target, $this->db_locale, $this->db_database, $this->db_userid, $this->db_password);
		/* 親クラスの接続メソッド呼び出し（mysql用オプション設定有り） */
		if($this->db_target == "mysql" &&  !is_null($this->db_option) && $this->db_option != ""){	
			$this->db_connection = $this->connect(false,true,true,$this->db_option);
		}else{
			$this->db_connection = $this->connect(false,true,true);
		}
		/* 接続エラー時ハンドリング */
		if ( !$this->db_connection ) {
			$errorMSG = $this->get_Error();
			$this->execError($errorMSG);
			exit();
		}
		return $this->db_connection;
	}
	/*------------------------------------------------------------------/
	/	■エラーメッセージハンドリング■
	/	--エラー出力及び後処理
	/-----------------------------------------------------------------*/
  	public function execError($error){
	    Logger::error($error['errorCode']);
	    Logger::error($error['errorMsg']);
	    try {
	      	if($this->db_connection != null){
		      $this->rollbackTrans();
		      $this->disconnect();
		}
	    } catch(PDOException $e){
	      Logger::error($e->getMessage());
	    }
	    throw new Exception("DBエラー発生!!");
	    exit;
	}
	/*------------------------------------------------------------------/
	/	■デストラクタ■
	/	--
	/-----------------------------------------------------------------*/
	public function __destruct(){
		$this->disconnect();
	}
}
