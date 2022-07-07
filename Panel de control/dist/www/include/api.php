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
