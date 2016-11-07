<?php 
/*
 * 
 * 汎用関数クラス
 * @author  xxxxxxxx (c)2012
 * 
 */

class Util{

	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {

	}
	public function __destruct(){
	}
	/*************************
	* 配列の半角カナ→全角カナ変換
	**************************/
	static public function convertArrayFullWidthKana($input, $Charset){
		if(is_array($input)){
			$return = array();
			foreach((array)$input as $key=>$val){
				$return[$key] = self::convertArrayFullWidthKana($val, $Charset);
			}
			return $return;
		}else{
			return mb_convert_kana($input,"KV",$Charset); 
		}
	}
	/*************************
	* 配列の全角カナ→半角カナ変換
	**************************/
	static public function convertArrayHarfWidthKana($input, $Charset){
		if(is_array($input)){
			$return = array();
			foreach((array)$input as $key=>$val){
				$return[$key] = self::convertArrayHarfWidthKana($val, $Charset);
			}
			return $return;
		}else{
			return mb_convert_kana($input,"kV",$Charset);
		}
	}
	/*************************
	* 配列の文字コード変換
	**************************/
	static public function convertArrayEncoding($input, $toCharset, $fromCharset = null){
		if(is_array($input)){
			$return = array();
			foreach((array)$input as $key=>$val){
				$return[$key] = self::convertArrayEncoding($val, $toCharset, $fromCharset);
			}
			return $return;
		}else{
			if($fromCharset != null && $fromCharset != ""){
				return mb_convert_encoding($input, $toCharset, $fromCharset);
			}else{
				return mb_convert_encoding($input, $toCharset);
			}
		}
	}
	/*************************
	* 配列の空要素を除去
	**************************/
	static public function deleteNonValueFromArray($inputArray, $nokeyFlg = true){
		$returnArray = array();
		foreach($inputArray as $key=>$value){
			if($value != null && $value != "") {
				if($nokeyFlg){
					$returnArray[] = $value;
				}else{
					$returnArray[$key] = $value;
				}
			}
		}
		return $returnArray;
	}
}
