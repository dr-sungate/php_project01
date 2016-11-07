<?php 
/*
 * 
 * メール制御クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class MailBase{
	private $smarty;
	private $mailto;
	private $mailfrom;
	private $mailcc;
	private $mailbcc;
	private $mailreplyto;
	private $subject;
	private $body;
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct($smarty) {
		$this->smarty = $smarty;
	}
	public function __destruct(){
	}
	public function setHeader($mailfrom, $mailto, $mailcc, $mailbcc, $mailreplyto, $subject){
		$this->mailto = $mailto;
		$this->mailfrom = $mailfrom;
		$this->mailcc = $mailcc;
		$this->mailbcc = $mailbcc;
		$this->mailreplyto = $mailreplyto;
		$this->subject = $subject;
		
	}
	public function setBody($mailVars, $mailtemplate){
		//表示データをアサイン
		$this->smarty->assign("MailData", $mailVars);
		//テンプレート表示
		$this->body = $this->smarty->fetch($mailtemplate);
		
	}
	public function send(){
		mb_language("japanese");
		mb_internal_encoding(SYSTEM_CHARCODE);
		$header="From: ".$this->mailfrom;
		$header.="\n";
		if($this->mailcc != null){
			$header.="Cc: ".$this->mailcc;
			$header.="\n";
		}
		if($this->mailbcc != null){
			$header.="Bcc: ".$this->mailbcc;
			$header.="\n";
		}
		if($this->mailreplyto != null){
			$header.="Reply-To: ".$this->mailreplyto;
			$header.="\n";
		}
		Logger::debug($this->mailto);
		Logger::debug($this->subject);
		Logger::debug($this->body);
		mb_send_mail($this->mailto,$this->subject,$this->body,$header);
	}
}
