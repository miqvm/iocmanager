<?php

//-----------------------------------------------------------------------------
//ROUTE: /api/quarantine
//-----------------------------------------------------------------------------
Route::add('/api/quarantine',function(){
	header("Content-Type: text/plain");
	$info = $GLOBALS['api']->select_quarantine();
        foreach ($info as $i){
        	echo $i."\r\n";
        }
	unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /api/quarantine/%/ (GET)
//-----------------------------------------------------------------------------
Route::add('/api/quarantine/([a-zA-Z0-9]+)',function($type){
	header("Content-Type: text/plain");
	if($type=="ipv4" && isset($_REQUEST['optimized'])){

		if(isset($_REQUEST['q'])){

			switch($_REQUEST['q']){
				case "edl":
					$filepath = $GLOBALS['edl_output_file'];
					break;
				case "dag":
					$filepath = $GLOBALS['dag_output_file'];
					break;
				 
				case "overflow":
					$filepath = $GLOBALS['overflow_output_file'];
					break;
				 
				case "stats": 
					$filepath = $GLOBALS['stats_output_file'];
					break;
				
				default:
					$filepath = $GLOBALS['edl_output_file'];
					break;
			}
		} else{
			$filepath = $GLOBALS['edl_output_file'];
		}
		
		$f = fopen($filepath, 'r');
		
		if($f){
			$contents = fread($f, filesize($filepath));
			fclose($f);
			echo ($contents);
		}
	} else{
		$info = $GLOBALS['api']->select_quarantine_type($type);
		foreach ($info as $i){
			echo $i."\r\n";
		}
	}
	unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);



},'get');

//-----------------------------------------------------------------------------
//ROUTE: /api/quarantine/%/% (GET)
//-----------------------------------------------------------------------------
Route::add('/api/quarantine/([a-zA-Z0-9]+)/([0-9]+)',function($type, $jol){
	header("Content-Type: text/plain");
	$info = $GLOBALS['api']->select_quarantine_type_jol($type, $jol);
        foreach ($info as $i){
        	echo $i."\r\n";
        }
	unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /api/monitoring
//-----------------------------------------------------------------------------
Route::add('/api/monitoring',function(){
	header("Content-Type: text/plain");
	$info = $GLOBALS['api']->select_monitoring();
        foreach ($info as $i){
        	echo $i."\r\n";
        }
	unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /api/monitoring/%/ (GET)
//-----------------------------------------------------------------------------
Route::add('/api/monitoring/([a-zA-Z0-9]+)',function($type){
	header("Content-Type: text/plain");
	$info = $GLOBALS['api']->select_monitoring_type($type);
        foreach ($info as $i){
        	echo $i."\r\n";
        }
        unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /api/monitoring/%/% (GET)
//-----------------------------------------------------------------------------
Route::add('/api/monitoring/([a-zA-Z0-9]+)/([0-9]+)',function($type, $jol){
	header("Content-Type: text/plain");
	$info = $GLOBALS['api']->select_monitoring_type_jol($type, $jol);
        foreach ($info as $i){
        	echo $i."\r\n";
        }
        unset($_SESSION['_gestioioc']);
	unset($_SESSION['_gestioioc_userlang']);
},'get');

?> 

