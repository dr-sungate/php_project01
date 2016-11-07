<?php 
/*
 * 
 * エラー操作クラス
 * @author  xxxxxxxx (c)2012
 * 
 */


define("EXIST_CHECK", "existcheck");
define("SELECT_CHECK", "selectcheck");
define("MIN_CHECK", "minlencheck");
define("MAX_CHECK", "maxlencheck");
define("MINVALUE_CHECK", "minvaluecheck");
define("MAXVALUE_CHECK", "maxvaluecheck");
define("EQUAL_CHECK", "equalcheck");
define("NUM_CHECK", "numcheck");
define("INT_CHECK", "intcheck");
define("INT_HYPHEN_CHECK", "inthyphencheck");
define("INT_CHECK_ARRAY", "intcheckArray");
define("POSTCODE_CHECK", "postcodecheck");
define("ALPHA_CHECK", "alphacheck");
define("ALPHANUM_CHECK", "alphacheck");
define("ALPHANUMSYMBOL_CHECK", "alphanumsymbolcheck");
define("KANA_CHECK", "kanacheck");
define("EMAIL_CHECK", "emailcheck");
define("DATE_CHECK", "datecheck");

class ValidateHandler {
	public $errorHandler;
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->errorHandler = new ErrorHandler();
	}
	public function __destruct(){
	}
	/*************************
	 * 必須チェック
	 **************************/
	public function existcheck($key, $value, $viewname = null){
		//スペースなど除去
		$value = $this->trimValue($value);
		if($value == null || $value == ""){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."を");
			}
			$this->errorHandler->addErrorMessage($key, "入力してください。<br/>");
		}		
	}
	public function selectcheck($key, $value, $viewname = null){
		if($value == null || $value == "" || is_array($value) && count($value) == 0){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."を");
			}
			$this->errorHandler->addErrorMessage($key, "選択してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 文字数チェック
	 **************************/
	public function minlencheck($min, $key, $value, $viewname = null){
		//スペースなど除去
		$value = $this->trimValue($value);
		if($value != null && $value != "" && mb_strlen($value) < $min){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key,  $min."文字以上入力してください。<br/>");
			return false;
		}
		return true;
	}
	public function maxlencheck($max, $key, $value, $viewname = null){
		if($value != null && $value != "" && mb_strlen($value) > $max){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key,  $max."文字以下で入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 数値上限下限チェック
	 **************************/
	public function minvaluecheck($min, $key, $value, $viewname = null){
		//スペースなど除去
		$value = $this->trimValue($value);
		if($value != null && $value != "" && $value < $min){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key,  $min."以上入力してください。<br/>");
			return false;
		}
		return true;
	}
	public function maxvaluecheck($max, $key, $value, $viewname = null){
		if($value != null && $value != "" && $value > $max){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, $max."以下で入力してください。<br/>");
		}		
	}
	/*************************
	 * 同一入力チェック
	 **************************/
	public function equalcheck($key, $value1, $value2, $viewname = null){
		if($value1 != null && $value1 != "" && $value2 != null && $value2 != "" && $value1 !== $value2){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."が");
			}
			$this->errorHandler->addErrorMessage($key, "一致ません。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 数字チェック
	 **************************/
	public function numcheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !is_numeric($value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "数字を入力してください。<br/>");
		}		
	}
	/*************************
	 * 整数チェック
	 **************************/
	public function intcheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !preg_match("/^[0-9]+$/", $value) && !is_int($value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "整数を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 整数ハイフンチェック（電話番号）
	 **************************/
	public function inthyphencheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !preg_match("/^[0-9-+]+$/", $value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "数字およびハイフン/＋を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 郵便番号チェック
	 **************************/
	public function postcodecheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !preg_match("/^\d{3}\-\d{4}$/", $value) ){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "正しい郵便番号の形式を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 整数チェック(配列)
	 **************************/
	public function intcheckArray($key, $valueArray, $viewname = null){
		foreach((array)$valueArray as $value){
			$this->intcheck($key, $value, $viewname);
		}
	}
	/*************************
	 * 英字チェック
	 **************************/
	public function alphacheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !ctype_alpha($value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "半角英字を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 英数字チェック
	 **************************/
	public function alphanumcheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !ctype_alnum($value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "半角英数字を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * 英数字チェック
	 **************************/
	public function alphanumsymbolcheck($key, $value, $viewname = null){
		if($value != null && $value != "" && !preg_match("/^[[:graph:]|[:space:]]+$/i", $value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "半角英数字記号を入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * カタカナチェック
	 **************************/
	public function kanacheck($key, $value, $viewname = null){
		//スペースなど除去
		$value = $this->trimValue($value);
		if($value != null && $value != "" && !preg_match("/^[ア-ヶｦ-ﾟー]+$/u", $value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."は");
			}
			$this->errorHandler->addErrorMessage($key, "カタカナを入力してください。<br/>");
			return false;
		}
		return true;
	}
	/*************************
	 * メールアドレスチェック
	 **************************/
	public function emailcheck($key, $value, $viewname = null){
  
        $wsp           = '[\x20\x09]';
        $vchar         = '[\x21-\x7e]';
        $quoted_pair   = "\\\\(?:$vchar|$wsp)";
        $qtext         = '[\x21\x23-\x5b\x5d-\x7e]';
        $qcontent      = "(?:$qtext|$quoted_pair)";
        $quoted_string = "\"$qcontent*\"";
        $atext         = '[a-zA-Z0-9!#$%&\'*+\-\/\=?^_`{|}~]';
        $dot_atom_text = "$atext+(?:[.]$atext+)*";
        $dot_atom      = $dot_atom_text;
        $local_part    = "(?:$dot_atom|$quoted_string)";
        $domain        = $dot_atom;
        $addr_spec     = "${local_part}[@]$domain";

        $dot_atom_loose   = "$atext+(?:[.]|$atext)*";
        $local_part_loose = "(?:$dot_atom_loose|$quoted_string)";
        $addr_spec_loose  = "${local_part_loose}[@]$domain";

        //if (RFC_COMPLIANT_EMAIL_CHECK) {
        //    $regexp = "/\A${addr_spec}\z/";
        //} else {
            // 携帯メールアドレス用に、..や.@を許容する。
            $regexp = "/\A${addr_spec_loose}\z/";
        //}

		if($value != null && $value != "" && !preg_match($regexp, $value)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."の");
			}
			$this->errorHandler->addErrorMessage($key, "形式が不正です。<br/>");
			return false;
		}		
		// 最大文字数制限の判定 (#871)
		return $this->maxlencheck(256, $key, $value, $viewname);

     }
	/*************************
	 * 日付チェック
	 **************************/
	public function datecheck($key, $year, $mon, $day, $viewname = null){
		if($year != null && $year != ""
			&& $mon != null && $mon != ""
			&& $day != null && $day != "" 
			&& !checkdate($mon, $day, $year)){
			if($viewname != null && $viewname != ""){
				$this->errorHandler->addErrorMessage($key, $viewname."の");
			}
			$this->errorHandler->addErrorMessage($key,  "日付が不正です。<br/>");
			return false;
		}
		return true;
	}
     public function trimValue($value){
		//スペースなど除去
		$value = trim($value);
		$value = $this->mb_trim($value, "　");
		return $value;
	}
	public function getErrors(){
		return $this->errorHandler->getAllList();
	}
	/*
	* マルチバイト文字列用trim
	*/
	public function mb_trim ($str, $chars = "\s　")
	{
		$str = mb_ereg_replace("^[$chars]+", "", $str);
		$str = mb_ereg_replace("[$chars]+$", "", $str);
		return $str;
	}
}
