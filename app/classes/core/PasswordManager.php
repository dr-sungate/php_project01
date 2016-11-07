<?php 
/*
 * 
 * パスワード制御クラス
 * @author  xxxxxxxx (c)2012
 * 
 */


class PasswordManager{

	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {

	}
	public function __destruct(){
	}
	/*************************
	 * 暗号化したパスワード取得
	 **************************/
	static public function getEncrypedPassword($password){
		if($password != null && $password != ""){
			return crypt($password, CRYPT_MD5);
		}
		return null;
	}
	
}

?>