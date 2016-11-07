<?php 

$res = register_shutdown_function('fatal_shutdown');
function fatal_shutdown()
{
	global $RETURN_ERROR;
	
    if ($error = error_get_last()) {
    	if (isset($error['type']) && ($error['type'] == E_ERROR || $error['type'] == E_CORE_ERROR)) {
			ob_end_clean();
			Logger::error(__METHOD__);
			Logger::error($error['message']);
			//echo $RETURN_ERROR.",".$error['message'];
			exit;
    	}
    }
}
