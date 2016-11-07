<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class SyncFromAPP extends BatchBase{
	private $client_id;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();

	}
	public function __destruct(){
	}
	/*************************
	 * 初期処理
	 **************************/
	public function init($batchrecord){
		$this->client_id = $batchrecord['client_id'];
		
	}
	/*************************
	 * メイン処理
	 **************************/
	public function process(){
		$Clients = new Clients();
		if(empty($this->client_id)){
			$clientData = $Clients->getAllActiveClient();
		}else{
			$clientData = $Clients->getClientByID($this->client_id);
		}
		if(empty($clientData)){
			parent::writeloginfo("[".__METHOD__."]"."No Ckient Data or Client active");
			return;
		}
 		$response = null;
 		foreach($clientData as $client){
			$this->syncAPP($client);
 		}
		parent::writeloginfo("[".__METHOD__."] END");
	}
	private function syncAPP($clientData){
		parent::writeloginfo("[".__METHOD__."]"."Sync Start ClientID:".$clientData['id']);
		$ServerSyncFiles = new ServerSyncFiles();
		if($ServerSyncFiles->rsyncFiles($clientData['remote_sync_account'], $clientData['remote_sync_dir'], MAIL_SYNCFILE_DIR.'/'.$clientData['sync_dir'])){
			parent::writeloginfo("[".__METHOD__."]"."rsync SUCCESS");
			return true;
		}else{
			parent::writelogerror("[".__METHOD__."]"."rsync ERROR!!");
			throw new Exception("rsync error!!");
		}
	
	}
}
