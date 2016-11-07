<?php 
	/*-- PHP設定 --*/
	date_default_timezone_set('Asia/Tokyo');
	/*--エラー制御拡張 --*/
	require_once(dirname(__FILE__)."/system_exception.php");
	/*-- 環境変数データ取得 --*/
	$stepmailenv = getenv("STEPMAIL_ENV");
	if(empty($stepmailenv)){
		$stepmailenv = "localdevelopment";
	}
	define("STEPMAIL_ENV", $stepmailenv);
	/*-- システム設定 --*/
	define("CONTROLLER_DIR", dirname(__FILE__)."/controller");
	define("CLASS_DIR", dirname(__FILE__)."/classes");
	define("TEMPLATE_DIR", dirname(__FILE__)."/templates");
	define("MAIL_CONTSFILE_DIR", dirname(dirname(__FILE__))."/mailtemplates");
	if(STEPMAIL_ENV == "production" || STEPMAIL_ENV == "staging"){
		define("SSH_KEYDIR", "/home/salesmanagement/.ssh");		
		define("MAIL_SYNCFILE_DIR",  "/home/salesmanagement/stepmail_works/syncdir");
		define("LOG_DIR",  "/home/salesmanagement/stepmail_works/logs");
	}else{
		define("SSH_KEYDIR", dirname(dirname(__FILE__))."/sshkeys");		
		define("MAIL_SYNCFILE_DIR",  dirname(dirname(__FILE__))."/syncdir");
		define("LOG_DIR",  dirname(dirname(__FILE__))."/logs");
		define("SSH_KEYDIR", dirname(dirname(__FILE__))."/sshkeys");
	}
	define("SYSTEM_ROOT", dirname(APP_DIR));
	

	
	
	define("SYSTEM_CHARCODE", "UTF-8");
	
	/*-- データベース設定 --*/
	if(STEPMAIL_ENV == "production"){
		define("DB_TARGETE"    	, "mysql");
    	define("DB_LOCALE"    	, "localhost");
		define("DB_NAME"    		, "trialuser_stepmail_db");
		define("DB_USER"          	, "stepmail_user");
		define("DB_PASS"          	, "xxxxxx");
	}elseif(STEPMAIL_ENV == "staging"){
		define("DB_TARGETE"    	, "mysql");
    	define("DB_LOCALE"    	, "localhost");
		define("DB_NAME"    		, "trialuser_stepmail_db_dev");
		define("DB_USER"          	, "stepmail_user_dev");
		define("DB_PASS"          	, "xxxxxx");
		//define('DB_DEBUG_MODE', true);
	}else{
		define("DB_TARGETE"    	, "mysql");
		define("DB_LOCALE"    	, "mysqlserver");
		define("DB_NAME"    		, "trialuser_stepmail_db");
		define("DB_USER"          	, "root");
		define("DB_PASS"          	, "stepmaildev");
		define('DB_DEBUG_MODE', true);
	}
		define("DB_OPTION"          	, "SET NAMES utf8");
	
	/*-- メール設定 --*/		
	if(STEPMAIL_ENV == "production"){
		define("MAIL_TO", "xxx@xxx.xx");
		define("MAIL_FROM", "xxx@xxx.xx");
		define("MAIL_CC", "xxx@xxx.xx");
		define("MAIL_BCC", "");
		define("MAIL_REPLYTO", "xxx@xxx.xx");
	}elseif(STEPMAIL_ENV == "staging"){
		define("MAIL_TO", "xxx@xxx.xx");
		define("MAIL_FROM", "xxx@xxx.xx");
		define("MAIL_CC", "xxx@xxx.xx");
		define("MAIL_BCC", "");
		define("MAIL_REPLYTO", "xxx@xxx.xx");
	}else{
		define("MAIL_TO", "xxx@xxx.xx");
		define("MAIL_FROM", "xxx@xxx.xx");
		define("MAIL_CC", "xxx@xxx.xx");
		define("MAIL_BCC", "");
		define("MAIL_REPLYTO", "xxx@xxx.xx");
	}

	/*--API.Mail設定 --*/
	if(STEPMAIL_ENV == "production"){
		define("API_TYPE", 'pc');
		define("API_CLIENTID", 1);
		define("API_INTERVAL", 1);	
		define("API_URL", 'https://xxxxxxxxx');
	}elseif(STEPMAIL_ENV == "staging"){
		define("API_TYPE", 'pc');
		define("API_CLIENTID", 1);
		define("API_INTERVAL", 1);	
		define("API_URL", 'https://xxxxxxxxx');
	}else{
		define("API_TYPE", 'pc');
		define("API_CLIENTID", 1);
		define("API_INTERVAL", 1);	
		define("API_URL", 'https://xxxxxxxxx');
	}
	
	
	/*--ログ設定 --*/
	define("LOGGER_DEBUG", 30);
	define("LOGGER_INFO", 20);
	define("LOGGER_ERROR", 10);
	
	if(STEPMAIL_ENV == "production"){
		define("LOGGER_LEVEL", LOGGER_DEBUG);
	}else{
		define("LOGGER_LEVEL", LOGGER_DEBUG);
	}
	define("LOGGER_FILE_NAME", STEPMAIL_ENV."_");

	define("LIST_ROW", 20);
	
	/*--クラスオートロード --*/
	require_once(dirname(__FILE__)."/class_autoloader.php");
	
	/*--Smarty設定 --*/
	define("SMARTY_MODULE_DIR", dirname(__FILE__).'/modules/Smarty/libs');
	require_once(SMARTY_MODULE_DIR.'/Smarty.class.php');
	
	