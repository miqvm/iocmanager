<?php

include ("config.php");

class dashboard
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
	
	function countReasonsByYearMonth(){
		$sql_script = "select year(date) as year, monthname(date) as Month, count(*) as Total from reason group by year, monthname(date), month(date) order by year, month(date);";

		$data = $this->bd->query($sql_script);	
		$rows = $data->num_rows;
		$result = array();

		for ($i=0; $i<$rows; $i++)
		{	
			$myrow = $data->fetch_row();
			$result[$i]["year"] = $myrow[0];
			$result[$i]["month"] =  $myrow[1];
			$result[$i]["total"] = $myrow[2];
			
		}
		
		return $result;
	}
	
	function countReasonsLastMonth(){
		$sql_script = "SELECT DATE(date) DateOnly, count(id_reason) from reason where date>=NOW()-INTERVAL 30 DAY group by DateOnly";

		$data = $this->bd->query($sql_script);	
		$rows = $data->num_rows;
		$result = array();

		for ($i=0; $i<$rows; $i++)
		{	
			$myrow = $data->fetch_row();
			$result[$myrow[0]] =  $myrow[1];
		}

		return $result;
	}
	
	
	
	function countIoC(){
		$sql_script = "select count(id_ioc) from ioc;";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	function countQuarantinedIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE quarantine_end>NOW();";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	function countMonitoringIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE monitoring_end>NOW();";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	function countLast24hIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE last_seen >= NOW()-INTERVAL 1 DAY;";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	function countIoCTypes(){
		$sql_script = "select type_id, count(*) from ioc group by type_id;";
		$data = $this->bd->query($sql_script);	
		$rows = $data->num_rows;
		$result = array();

		for ($i=0; $i<$rows; $i++)
		{	
			$myrow = $data->fetch_row();
			$result[$myrow[0]] = $myrow[1];			
		}
		
		return $result;
	}
	


	
}
?>
