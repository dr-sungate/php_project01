<?php
//***************************************************************************
//* CLASS PdoDBBase
//* 機能：PDOデータベースクラス
//* 備考：PHP5.1.x用
//* 　　　Prepared Statment関数【getPrepareSelectArray(),getPrepareSelectArray_Seq()
//* 　　　setPrepareQuery(),setPrepareQuery_Seq()】の使用を推奨
//* 　　　●配列定義：$setParam[$key]['type'] --> "bool","null","int","str","lob"
//* 　　　※MySQL5.x系はストレージエンジンを「InnoDB」にしないとRollbackなどが動作しないので注意
//* 作成：Created By Magnet Toy (Luxsor Recordings (c) 2006)
//***************************************************************************/

class PdoDBBase
{
	const PARAM_TYPE_BOOL = "bool";
	const PARAM_TYPE_NULL = null;
	const PARAM_TYPE_INT = "int";
	const PARAM_TYPE_STR = "str";
	const PARAM_TYPE_LOB = "lob";
	
	private $db_dns = "";
	 private $db_userid = "";
	 private $db_password = "";
	 private $dbh = null;
	 private $stmt = null;
	 private $setBindParam = "";
	 private $countRow = 0;
	 private $countColumn = 0;
	 private $error_status = Array();
	 private $DBType = null;
	/*------------------------------------------------------------------/
	/	■データベース定義■
	/	--DB接続変数に読み込み
	/-----------------------------------------------------------------*/
	public function __construct( $db_target, $db_locale, $db_database , $db_userid, $db_password ) {
		/* DNS情報作成・セット */
		$this->DBType =  $db_target;
		if($db_target =="sqlite"){
			$this->db_dns = $db_target.":".$db_database;
		}else{
			$this->db_dns = $db_target.":dbname=".$db_database.";host=".$db_locale;
		}
		$this->db_userid = $db_userid;
		$this->db_password = $db_password;
	}
	/*------------------------------------------------------------------/
	/	■データベース接続■
	/	--DB接続処理
	/	--引数：1>>カラム名大文字(ture,0)・小文字(false,1)
	/	--引数：2>>自動コミットOff(ture,1)
	/	--引数：3>>エラー時にExceptionをthrowさせる(ture,1)
	/-----------------------------------------------------------------*/
	public function connect($AttFlg01 = true, $AttFlg02 = true, $AttFlg03 = true,$queryoption=null){
		try {
			$this->dbh = new PDO($this->db_dns, $this->db_userid, $this->db_password);
			/* カラム名を大文字・小文字で取得する */
			if($AttFlg01){
				$this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER);
			}else{
				$this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
			}
			/* 自動コミットをOff */
			if($AttFlg02){
				$this->dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
			}else{
				$this->dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
			}
			if ($this->dbh->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
				$this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true); 
			}
			/* エラー時にExceptionをthrowさせる */
			if($AttFlg02){
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			/* その他DB固有設定 */   
			if(!is_null($queryoption) && $queryoption!=""){
				$this->setQuery($queryoption); 
			}
		} catch (PDOException $exception) {
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
		} catch (Exception $exception) {
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
		}
		return $this->dbh;
	}
	/*------------------------------------------------------------------/
	/	■データベース切断■
	/	--DB切断処理
	/-----------------------------------------------------------------*/
	public function disconnect(){
		$this->dbh = null;
	}
	/*------------------------------------------------------------------/
	/	■トランザクション開始■
	/	--トランザクション開始処理
	/-----------------------------------------------------------------*/
	public function beginTrans(){
		$this->dbh->beginTransaction();
	}
	/*------------------------------------------------------------------/
	/	■トランザクション終了■
	/	--トランザクション終了処理
	/-----------------------------------------------------------------*/
	public function commitTrans(){
		$this->dbh->commit();
	}
	/*------------------------------------------------------------------/
	/	■トランザクションロールバック■
	/	--トランザクションロールバック処理
	/-----------------------------------------------------------------*/
	public function rollbackTrans(){
		$this->dbh->rollBack();
	}
	/*------------------------------------------------------------------/
	/	■SELECT処理■
	/	--SELECT文実行
	/	--$transFLG :true トランザクションの開始、コミット、ロールバック有り
	/-----------------------------------------------------------------*/
	public function getSelectArray($sql,$transFLG=true){
		/* SQL文をセットしてない時はエラー */
		if ( $sql == "" ||  $sql==null) {
			throw new PDOException("sql is null");
		}
		if($transFLG){
			/* トランザクション開始 */
			$this->beginTrans();
		}
		try{
			/* クエリ実行 */
			$this->stmt = $this->dbh->prepare($sql); 
			$this->stmt->execute();
 			/* 結果取得 */
			$result = $this->stmt->fetchAll();
			/* 取得行数 */
			$this->countRow = count($result);
			/* カラムの数 */
			$this->countColumn = $this->stmt->columnCount();
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->stmt->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				if($transFLG){
					/* ロールバック処理 */
  					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* カーソル開放 */
			$this->stmt->closeCursor();
			/* ステートメントの開放 */
			$this->stmt = null;
			if($transFLG){
				/* コミット処理 */
		  		$this->commitTrans();
			}

			return $result;

		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
  				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
  				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	} 
	/*------------------------------------------------------------------/
	/	■行取得■
	/	--件数取得
	/	--$table：テーブル名　$Where：条件
	/-----------------------------------------------------------------*/
	public function getCountRow($table,$Where){
		/* テーブルが指定していないときエラー */
		if ( $table == "" || $table == null) {
			throw new PDOException("table is null");
		}
		/* カウント用SELECT文作成 */
		$sql = "SELECT count(*) FROM ".$table;
		if($Where!="" && $Where!=null){
			$sql .= " WHERE ".$Where.";";
		}
		/* トランザクション開始 */
		$this->beginTrans();
		try{
			/* クエリ実行 */
			$this->stmt = $this->dbh->prepare($sql); 
			$this->stmt->execute();
			/* 結果取得 */
			$result = $this->stmt->fetchColumn(0);
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->stmt->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				/* ロールバック処理 */
  				$this->rollbackTrans();
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* カーソルの開放 */
			$this->smtp->closeCursor();
			/* ステートメントの開放 */
			$this->stmt = null;
			/* コミット処理 */
	  		$this->commitTrans();

			return $result;

		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
 			$this->rollbackTrans();
			Logger::error($this->error_status);
 			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
 		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
 			$this->rollbackTrans();
			Logger::error($this->error_status);
 			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
 		}
	} 
	/*------------------------------------------------------------------/
	/	■query処理■
	/	--INSERT,UPDATE,DELETEなど実行
	/	--$sql：SQL文
	/	--$transFLG :true トランザクションの開始、コミット、ロールバック有り
	/	--$nonRowFlg：0/false or 1/ture or 省略
	/	--（処理件数0でエラーを出すか出さないか）
	/-----------------------------------------------------------------*/
	public function setQuery($sql,$transFLG=true,$nonRowFlg = true){
		/* SQL文をセットしてない時はエラー */
		if ( $sql == "" ||  $sql==null) {
			throw new PDOException("sql is null");
		}
		if($transFLG){
			/* トランザクション開始 */
			$this->beginTrans();
		}
		try{
			/* クエリ実行 */
			$this->stmt = $this->dbh->prepare($sql); 
			$this->stmt->execute();
			/* 処理した行数 */   
			$this->countRow = $this->stmt->rowCount();
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->stmt->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				if($transFLG){
					/* ロールバック処理 */
					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* 処理件数が0件のとき */
			if($nonRowFlg && $this->countRow==0){
				if($transFLG){
					/* ロールバック処理 */
 					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = "no record was changed.";
				return false;
			}
			/* カーソル開放 */
			$this->stmt->closeCursor();
			/* ステートメントの開放 */
			$this->stmt = null;
			if($transFLG){
				/* コミット処理 */
				$this->commitTrans();
			}

			return true;

		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}

		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	} 
	/*------------------------------------------------------------------/
	/	■Prepared Statment BindParam実行■
	/	--$setBindParam：実データパラメータ
	/	--（$setBindParam[$key]['value'],$setBindParam[$key]['type'])
	/-----------------------------------------------------------------*/
	private function execPreBind(){
		foreach($this->setBindParam as $key => $param){
			switch ($param['type']){
				case self::PARAM_TYPE_BOOL:
					$this->stmt->bindParam($key, $param['value'],PDO::PARAM_BOOL);
					break;
				case self::PARAM_TYPE_NULL:
					$this->stmt->bindParam($key, $param['value'],PDO::PARAM_NULL);
					break;
				case self::PARAM_TYPE_INT:
					$this->stmt->bindParam($key, $param['value'],PDO::PARAM_INT);
					break;
				case self::PARAM_TYPE_STR:
					$this->stmt->bindParam($key, $param['value'],PDO::PARAM_STR);
					break;
				case self::PARAM_TYPE_LOB:
					$this->stmt->bindParam($key, $param['value'],PDO::PARAM_LOB);
					break;
				default:
					break;
			}
		}
	}
	/*------------------------------------------------------------------/
	/	■Prepared Statment SELECT処理■
	/	--SELECT文実行
	/	--$sql：prepared statment用SQL文
	/	--$transFLG :true トランザクションの開始、コミット、ロールバック有り
	/	--$Param：実データパラメータ
	/	--（$setParam[$key]['value'],$setParam[$key]['type'])
	/-----------------------------------------------------------------*/
	public function getPrepareSelectArray($sql,$setParam,$transFLG=true){
		/* SQL文をセットしてない時はエラー */
   		if ( $sql == "" ||  $sql==null ) {
			throw new PDOException("sql is null");
		}
	  	if (!is_array($setParam)) {
			throw new PDOException("setParam is not array");
 		}
		$this->setBindParam = $setParam;
		if($transFLG){
			/* トランザクション開始 */
			$this->beginTrans();
		}
		try{
			/* クエリ実行 */
			$this->stmt = $this->dbh->prepare($sql); 
			$this->execPreBind();
			$this->stmt->execute();
 			/* 結果取得 */
			$result = $this->stmt->fetchAll();
			/* 取得行数 */
			$this->countRow = count($result);
			/* カラムの数 */
			$this->countColumn = $this->stmt->columnCount();
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->stmt->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				if($transFLG){
					/* ロールバック処理 */
					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* カーソル開放 */
			$this->stmt->closeCursor();
			/* ステートメントの開放 */
			$this->stmt = null;
			if($transFLG){
				/* コミット処理 */
				$this->commitTrans();
			}

			return $result;

		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	} 
	/*------------------------------------------------------------------/
	/	■Prepared Statment query処理■
	/	--INSERT,UPDATE,DELETEなど実行
	/	--$sql：prepared statment用SQL文
	/	--$Param：実データパラメータ
	/	--（$setParam[$key]['value'],$setParam[$key]['type'])
	/	--$transFLG :true トランザクションの開始、コミット、ロールバック有り
	/	--$nonRowFlg：0/false or 1/ture or 省略
	/	--（処理件数0でエラーを出すか出さないか）
	/-----------------------------------------------------------------*/
	public function setPrepareQuery($sql,$setParam,$transFLG=true,$nonRowFlg = true){
		/* SQL文をセットしてない時はエラー */
	   	if ( $sql == "" ||  $sql==null ) {
			throw new PDOException("sql is null");
		}
	  	if (!is_array($setParam)) {
			throw new PDOException("setParam is not array");
 		}
		$this->setBindParam = $setParam;
		if($transFLG){
			/* トランザクション開始 */
			$this->beginTrans();
		}
		try{
			/* クエリ実行 */
			$this->stmt = $this->dbh->prepare($sql); 
			$this->execPreBind();
			$this->stmt->execute();
			/* 処理した行数 */   
			$this->countRow = $this->stmt->rowCount();
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->stmt->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				if($transFLG){
					/* ロールバック処理 */
					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* 処理件数が0件のとき */
			if($nonRowFlg && $this->countRow==0){
				if($transFLG){
					/* ロールバック処理 */
 					$this->rollbackTrans();
				}
				$this->error_status['errorMsg'] = "no record was changed.";
				return false;
			}
			/* ステートメントの開放 */
			$this->stmt = null;
			if($transFLG){
				/* コミット処理 */
		 		$this->commitTrans();
			}

			return true;

		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
			if($transFLG){
				$this->rollbackTrans();
			}
			Logger::error($this->error_status);
			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	} 
	/*------------------------------------------------------------------/
	/	■query処理-query()■
	/	--SELECTをquery()で実行
	/-----------------------------------------------------------------*/
	public function getSelectArray_Query($sql,$errorNotThru = true){
		/* SQL文をセットしてない時はエラー */
	   	if ( $sql == "" ||  $sql==null ) {
			throw new PDOException("sql is null");
		}
		/* トランザクション開始 */
		$this->dbh->beginTransaction();
		try{
			/* クエリ実行 */
			$result = $this->dbh->query($sql); 
			/* 取得行数 */
			$this->countRow = count($result);
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->dbh->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				/* ロールバック処理 */
  				$this->rollbackTrans();
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
				
			/* コミット処理 */
	  		$this->commitTrans();
			  
			return $result;
		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
  			$this->rollbackTrans();
			Logger::error($this->error_status);
  			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
  			$this->rollbackTrans();
			Logger::error($this->error_status);
  			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	}
	/*------------------------------------------------------------------/
	/	■query処理-exec()■
	/	--INSERT,UPDATE,DELETEなどをexec()で実行
	/-----------------------------------------------------------------*/
	public function setQuery_Exec($sql,$nonRowFlg = true){
		/* SQL文をセットしてない時はエラー */
	   	if ( $sql == "" ||  $sql==null ) {
			throw new PDOException("sql is null");
		}
		/* トランザクション開始 */
		$this->beginTrans();
		try{
			/* クエリ実行 */
			$result = $this->dbh->exec($sql); 
			/* 処理した行数 */   
			$this->countRow = $result;
			/* DBからのエラーメッセージ */
			$dbErrorMSG = $this->dbh->errorInfo();
			if(isset($dbErrorMSG[2]) && $dbErrorMSG[2]!="" && $dbErrorMSG[2]!=null){
				/* ロールバック処理 */
  				$this->rollbackTrans();
				$this->error_status['errorMsg'] = $dbErrorMSG[2];
				return false;
			}
			/* 処理件数が0件のとき */
			if($nonRowFlg && $this->countRow==0){
				$this->error_status['errorMsg'] = "no record was changed.";
				return false;
			}
			/* コミット処理 */
	  		$this->commitTrans();
			  
			return true;
		}catch(Exception $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
  			$this->rollbackTrans();
			Logger::error($this->error_status);
  			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}catch(PDOException $exception){
			$this->error_status['errorMsg']	= $exception->getMessage();
			$this->error_status['errorCode'] = $exception->getCode();
  			$this->rollbackTrans();
			Logger::error($this->error_status);
  			if(defined('DB_DEBUG_MODE') && DB_DEBUG_MODE == true){
				throw $exception;
			}
		}
	}
	/*------------------------------------------------------------------/
	/	■エラー取得■
	/	--エラーメッセージ、エラーコード取得
	/-----------------------------------------------------------------*/
	public function get_Error(){
		return $this->error_status;
	}
	/*------------------------------------------------------------------/
	/	■処理行数取得■
	/	--処理後行数取得
	/-----------------------------------------------------------------*/
	public function get_Rows(){
		return $this->countRow;
	}
	/*------------------------------------------------------------------/
	/	■処理カラム取得■
	/	--処理後カラム数取得
	/-----------------------------------------------------------------*/
	public function get_Column(){
		return $this->countColumn;
	}

}
