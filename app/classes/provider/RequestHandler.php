<?php 
/*
 * 
 * 処理クラス
 * @author xxxxxxxx (c)2015
 * 
 */

class RequestHandler {
	private $connecttype;
	private $responsetype;
	private $requestURI;
	private $requestURIParam;
	private $responseJSON;
	private $responseHTML;
	private $responseContentType;
	private $responseHeadersize;
	private $responseData;
	private $responseHTTPStatus;
	private $requesttype;
	private $requestParam;
	private $Controller;
	
	const HTTP_SUCCESS_CODE = "200";
	const HTTP_POST = "httppost";
	const HTTP_GET = "httpget";
	const RESPONSE_HTML = "text/html";
	const RESPONSE_TEXT = "text/plain";
	const RESPONSE_JSON = "application/json";
	
	/*************************
	 * コンストラクタ
	 **************************/
	public function __construct() {
		$this->requestURIParam = array();
	}
	public function __destruct(){
	}
	    /**
     * プロセス.
     *
     * @return void
     */
    public function process($requesttype, $responsetype = null) {
    	$this->responsetype = $responsetype;
    	if(!$this->requestHTTP($requesttype)){
			Logger::debug("[".__METHOD__."]"."error request");		
    		return false;
   		}
       	if($this->responsetype == self::RESPONSE_HTML){
       		$this->analizeHTML();		
       	}elseif($this->responsetype == self::RESPONSE_TEXT){
       		$this->analizeHTML();		
       	}elseif($this->responsetype == self::RESPONSE_JSON){
    		$this->analizeJSON();
    	}
   		return true;
   		
	}
	public function getResponseData() {
		return $this->responseData;
    }
   public function isSuccessResponseData($key) {
		if(isset($this->responseData[$key]) && $this->responseData[$key] == true){
			return true;
		}
		return false;
    }
    public function getErrorMsg() {
      	if(isset($this->responseHTTPStatus['error'])){
			return $this->responseHTTPStatus['error'];
   		}else{
   			return null;
   		}
    }
   public function getHttpStatusCode() {
   		if(isset($this->responseHTTPStatus['code'])){
			return $this->responseHTTPStatus['code'];
   		}else{
   			return null;
   		}
    }
   public function isSuccess() {
		if($this->getHttpStatusCode() == self::HTTP_SUCCESS_CODE){
	   	 	return true;
	   	 }
	   	 return false;
     }
    /**
     * XML解析.
     *
     * @return void
     */
    private function analizeJSON(){
    	$jsonData = substr( $this->responseJSON, $this->responseHeadersize );
   		$this->responseData = json_decode($jsonData, true);
   }
    private function analizeHTML(){
     	$this->responseData = substr( $this->responseHTML, $this->responseHeadersize );
     }
     public function setRequestURI($requestURI){
     	$this->requestURI = $requestURI;
     }
     public function setPostdata($key, $value){
     	$this->postdata[$key] = $value;
     }
	public function setRequestURIParam($key, $param){
		$this->requestURIParam[$key] = $param;
	}
     /**
     * HTTPリクエスト
     *
     * @return void
     */
	private function requestHTTP($connection, $basic_auth = false){
		
 	   	try{
      		$curl_handle = curl_init();
    		curl_setopt( $curl_handle, CURLOPT_URL, $this->requestURI );
    		curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array( "Content-type: multipart/form-data" ) );
    		curl_setopt( $curl_handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    		curl_setopt( $curl_handle, CURLOPT_HEADER, TRUE );
    		curl_setopt( $curl_handle, CURLOPT_TIMEOUT, 120);
    		curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE );  // SSL証明書非検証
   			curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYHOST, 1 );  // ホスト名非検証
    		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );
   			curl_setopt( $curl_handle, CURLOPT_FOLLOWLOCATION, FALSE); // Locationヘッダ追跡
    		curl_setopt( $curl_handle, CURLOPT_MAXREDIRS, 5); //HTTP のリダイレクト先を追いかける最大値
    		if($connection == self::HTTP_POST ){
    			curl_setopt( $curl_handle, CURLOPT_POST, TRUE );
				curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $this->postdata);
    		}elseif($connection == self::HTTP_GET ){
    			curl_setopt( $curl_handle, CURLOPT_HTTPGET, TRUE ); 
   				curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, http_build_query($this->requestURIParam, "", "&"));
    		}
    		if($basic_auth){
    			curl_setopt( $curl_handle, CURLOPT_USERPWD, BnidRequestConfig::BASIC_AUTH_ID.":".BnidRequestConfig::BASIC_AUTH_PASSWD);
    		}
    		curl_setopt( $curl_handle, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
      		
			Logger::debug("[".__METHOD__."]"."exec start");		
     		$result =  curl_exec( $curl_handle ); 
     		Logger::debug($result);
     		 
    		$this->responseContentType = curl_getinfo( $curl_handle, CURLINFO_CONTENT_TYPE);
    		$this->responseHeadersize = curl_getinfo( $curl_handle, CURLINFO_HEADER_SIZE);
     		Logger::debug($this->responseContentType);
    		Logger::debug($this->responseHeadersize );
    		if($this->responsetype == self::RESPONSE_HTML){
    			$this->responseHTML = $result;
    		}elseif($this->responsetype == self::RESPONSE_TEXT){
    			$this->responseHTML = $result;
    		}elseif($this->responsetype == self::RESPONSE_JSON){
    			$this->responseJSON = $result;
    		}elseif(strpos ($this->responseContentType, self::RESPONSE_HTML) !==false){
    			$this->responsetype = self::RESPONSE_HTML;
    			$this->responseHTML = $result;
    		}elseif(strpos ($this->responseContentType, self::RESPONSE_TEXT) !==false){
   				$this->responsetype = self::RESPONSE_TEXT;
    			$this->responseHTML = $result;
    		}elseif(strpos ($this->responseContentType, self::RESPONSE_JSON) !==false){
  				$this->responsetype = self::RESPONSE_JSON;
    			$this->responseJSON = $result;
    		}
     		Logger::debug($this->responseHTML );
     		Logger::debug($this->responseJSON );
     		$this->responseHTTPStatus['code'] = curl_getinfo( $curl_handle, CURLINFO_HTTP_CODE );
   			$this->responseHTTPStatus['error'] = curl_error($curl_handle);
  			
    		curl_close( $curl_handle );
    		    		
    		return true;
   		}catch(ErrorException $e){
   			restore_error_handler();
   		  	$this->responseHTTPStatus['code'] = curl_getinfo( $curl_handle, CURLINFO_HTTP_CODE );
   			$this->responseHTTPStatus['error'] = curl_error($curl_handle);
   			return false;
    	}catch(Exception $e){
    		restore_error_handler();
    	   	$this->responseHTTPStatus['code'] = curl_getinfo( $curl_handle, CURLINFO_HTTP_CODE );
    	   	$this->responseHTTPStatus['error'] = curl_error($curl_handle);
     		return false;
   		} 
	}
}
