<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class MailSendfileGenerator {
	private $tmpDir;
	private $taskID;
	private $targetDir;
	private $listFile;
	private $contFilePath;
	private $fromdir;
	private $generatefile;
	private $clientFileEndode;
	
	const GENERATEFILE_CHASET = "JIS";
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->tmpDir = "/tmp/".uniqid();
	}
	public function __destruct(){
	}
	public function setParam($targetDir, $listFileName, $contFilePath, $taskID, $clientFileEndode) {
		$this->targetDir = $targetDir;
		$this->taskID = $taskID;
		$this->clientFileEndode = $clientFileEndode;
		$this->listFile = $this->replaceGenerateFileName($listFileName);
		$this->contFilePath = $contFilePath;
	}
    public function isTargetExists() {
    	Logger::debug("Check File : ".$this->targetDir."/".$this->listFile);
    	if(file_exists($this->targetDir."/".$this->listFile)){
    		return true;
    	}else{
    			return false;
    	}
    }
	public function process() {
    	mkdir($this->tmpDir."/".$this->taskID,  0777, true);
    	$this->copyFile($this->targetDir."/".$this->listFile, $this->tmpDir."/".$this->taskID."/".$this->taskID.".list");
		if(!empty($this->clientFileEndode) && $this->clientFileEndode != self::GENERATEFILE_CHASET ){
			$this->convertFileChaset($this->tmpDir."/".$this->taskID."/".$this->taskID.".list", $this->clientFileEndode);
		}
    	$this->copyFile($this->contFilePath, $this->tmpDir."/".$this->taskID."/".$this->taskID.".conts");
    	if($this->compress()){
    		return $this->generatefile;
    	}else{
    		return null;
    	}
	}
	public function getListFileData(){
		$filedata= file_get_contents($this->tmpDir."/".$this->taskID."/".$this->taskID.".list");
		$filedata = mb_convert_encoding($filedata, SYSTEM_CHARCODE, self::GENERATEFILE_CHASET);
		Logger::debug($filedata);
		return $filedata;
	}
	private function copyFile($target, $copyfile){
		copy($target, $copyfile);
	}
	private function convertFileChaset($target, $fromchaset){
		$newfile = $target."_new";
		$fhfrom = fopen($target, 'r');
		if ($fhfrom) {
			while($line=fgets($fhfrom)){
				$line = mb_convert_encoding($line, self::GENERATEFILE_CHASET, $fromchaset);
				$fhto= fopen($newfile, 'a');
				fwrite($fhto,$line);
				fclose($fhto);
			}
			fclose($fhfrom);
		}
		copy($newfile, $target);
		unlink($newfile);
		
	}
	private function compress(){
		$this->generatefile = $this->tmpDir."/".$this->taskID.".tar.gz";
		exec( "cd $this->tmpDir ; tar cvfz $this->generatefile $this->taskID 2>&1" , $output, $return_var);
		if($return_var == 0){
			Logger::debug("[".__METHOD__."] Success compress");
			return true;
		}else{
			Logger::debug("[".__METHOD__."] ERROR compress");
			Logger::error("[".__METHOD__."]");
			Logger::error($output, true);
			return false;
		}
	}
	private function replaceGenerateFileName($filename){
		$filename =  str_replace("YYYY", date('Y'), $filename);
		$filename =  str_replace("YY", date('y'), $filename);
		$filename =  str_replace("MM", date('m'), $filename);
		$filename =  str_replace("DD", date('d'), $filename);
		return $filename;
	}
}
