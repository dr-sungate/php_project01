<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class BatchController extends BatchBase{
	
	private $batchloopend;
	private $batchendflg;
	const BATCH_PROCESS_TIME_LIMIT = 12000;
	const BATCH_PROCESS_MEMORY_LIMIT = "1536M";
	
	const BATCH_LOOP_TIME = "+1 minutes";
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		parent::__construct();
		// バッチ処理時間とメモリ設定
		set_time_limit(self::BATCH_PROCESS_TIME_LIMIT);
		ini_set('memory_limit', self::BATCH_PROCESS_MEMORY_LIMIT);
	}
	public function __destruct(){
	}
	    /**
     * プロセス.
     *
     * @return void
     */
    public function process() {
    		$this->batchloopend = strtotime( self::BATCH_LOOP_TIME );
    		$BatchManager = new BatchManager();
    		$this->batchendflg = true;
    		while(time() < $this->batchloopend && $this->batchendflg == true){
    			$batchList = $BatchManager->getCurrentJob();
    			if(!empty($batchList)){
     				$BatchManager->setStartProcessFlg($batchList[0], 1);
    				$this->processBatch($batchList[0]);
    				$BatchManager->setEndProcessFlg($batchList[0]);
    				//$this->batchendflg = false;
    				Logger::debug("Batch Processed");
    				continue;
    			}
    			Logger::debug("No Batch NOW");
    			sleep(18);
    		}
    		$BatchManager->resetErrorProcessFlg();
    		
       }
	private function processBatch($batchrecord){
		$batchclassname = $batchrecord['batch_class'];
		Logger::debug("Start process : ". $batchclassname);
		$batchClass = new $batchclassname();
		$batchClass->init($batchrecord);
		$batchClass->process();
	}
}
