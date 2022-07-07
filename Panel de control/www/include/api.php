<?php

include ("config.php");

class api
{
	var $bd;

	function __construct($bbdd)
	{
		$this->bd = $bbdd->bbdd;
		if ($this->bd->connect_errno)
		{
			printf(_("ERROR: The connection could not be established (CODE: 1)"));
    		exit();
		}
	}

	function __destruct()
	{
	}
	
	//--------------------------------------------------------------------------
	// SELECT_QUARANTINE
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on quarantine.
	//
	//--------------------------------------------------------------------------
	function select_quarantine(){
		$id = $GLOBALS['ioc_types'][$type];
		$sql_script = "SELECT name_ioc FROM ioc WHERE quarantine_end>NOW()";

		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		if(!$data || $rows==0){
			return ["No results available"];
		}
		
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}
	
	//--------------------------------------------------------------------------
	// SELECT_QUARANTINE_TYPE
	//--------------------------------------------------------------------------
	//	INPUT:	
	//		Type as string
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on quarantine which type is input parameter.
	//
	//--------------------------------------------------------------------------
	function select_quarantine_type($type){

		$id = $GLOBALS['ioc_types'][$type];
		$sql_script = "SELECT name_ioc FROM ioc WHERE type_id=$id AND quarantine_end>NOW()";

		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		if(!$data || $rows==0){
			return ["No results available"];
		}
		
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}

	//--------------------------------------------------------------------------
	// SELECT_QUARANTINE_TYPE_JOL
	//--------------------------------------------------------------------------
	//	INPUT:	
	//		Type: as string
	//		JOL (JSON Offence Level): as integer
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on quarantine which type and JSON Offence Level is input parameter.
	//
	//--------------------------------------------------------------------------
	function select_quarantine_type_jol($type, $jol){
		$id = $GLOBALS['ioc_types'][$type];
		$sql_script = "SELECT name_ioc FROM ioc WHERE type_id=$id AND json_offence_level=$jol AND quarantine_end>NOW()";

		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		if(!$data || $rows==0){
			return ["No results available"];
		}
		
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}

	//--------------------------------------------------------------------------
	// SELECT_MONITORING
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on monitoring.
	//
	//--------------------------------------------------------------------------
	function select_monitoring(){
		$id = $GLOBALS['ioc_types'][$type];
		$sql_script = "SELECT name_ioc FROM ioc WHERE monitoring_end>NOW()";

		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		if(!$data || $rows==0){
			return ["No results available"];
		}
		
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}
	
	//--------------------------------------------------------------------------
	// SELECT_MONITORING_TYPE
	//--------------------------------------------------------------------------
	//	INPUT:	
	//		Type: as string
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on monitoring which type is input parameter.
	//
	//--------------------------------------------------------------------------
	function select_monitoring_type($type){

		$id = $GLOBALS['ioc_types'][$type];

		$sql_script = "SELECT name_ioc FROM ioc WHERE type_id=$id AND monitoring_end>NOW()";

		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		if(!$data || $rows==0){
			return ["No results available"];
		}
		
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}

	//--------------------------------------------------------------------------
	// SELECT_MONITORING_TYPE_JOL
	//--------------------------------------------------------------------------
	//	INPUT:	
	//		Type: as string
	//		JOL (JSON Offence Level): as integer
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the indicators on monitoring which type and JSON Offence Level is input parameter.
	//
	//--------------------------------------------------------------------------
	function select_monitoring_type_jol($type, $jol){
		$id = $GLOBALS['ioc_types'][$type];
		$sql_script = "SELECT name_ioc FROM ioc WHERE type_id=$id AND json_offence_level=$jol AND monitoring_end>NOW()";

		$data = $this->bd->query($sql_script);
		
		$rows = $data->num_rows;
		
		if(!$data || $rows==0){
			return ["No results available"];
		}
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$i] = $myrow[0];
		}
		

		return $result;
	}

	
}
?>
