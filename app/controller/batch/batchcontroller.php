<?php 
	
	try{
		$BatchController = new BatchController();
		$BatchController->process();
 	}catch(PDOException $exception){
 		$errorMSG['errorMsg'] =  $exception->getMessage();
		$errorMSG['errorCode'] =  $exception->getCode();
 		$BatchController->logerrorAndEnd("[DB ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
 		exit;
		
 	}catch(Exception $exception){
 		$errorMSG['errorMsg'] =  $exception->getMessage();
		$errorMSG['errorCode'] =  $exception->getCode();
 		$BatchController->logerrorAndEnd("[ERROR]".$errorMSG['errorCode']."   ". $errorMSG['errorMsg']);
 		exit;
		
	}