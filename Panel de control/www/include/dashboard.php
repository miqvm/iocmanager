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

	// NO ESTA EN US A L'APLICACIO
	//--------------------------------------------------------------------------
	// countReasonsByYearMonth
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Retorna un array del total de Reasons per mes.
	//
	//--------------------------------------------------------------------------
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
	
	//--------------------------------------------------------------------------
	// countReasonsLastMonth
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Retorna un array del total de Reasons per dia durant el darrers 30 dies.
	//
	//--------------------------------------------------------------------------
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
	
	//--------------------------------------------------------------------------
	// countIoC
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Integer
	//
	//		Retorna el nombre total d'IOC de l'aplicacio.
	//
	//--------------------------------------------------------------------------
	function countIoC(){
		$sql_script = "select count(id_ioc) from ioc;";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}

	//--------------------------------------------------------------------------
	// countQuarantinedIoC
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Integer
	//
	//		Retorna el nombre total d'IOC en quarentena.
	//
	//--------------------------------------------------------------------------
	function countQuarantinedIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE quarantine_end>NOW();";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	//--------------------------------------------------------------------------
	// countMonitoringIoC
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Integer
	//
	//		Retorna el nombre total d'IOC en monitoritzacio
	//
	//--------------------------------------------------------------------------
	function countMonitoringIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE monitoring_end>NOW();";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}
	
	//--------------------------------------------------------------------------
	// countLast24hIoC
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Integer
	//
	//		Retorna el nombre total d'IOC detectats el darrer dia.
	//
	//--------------------------------------------------------------------------
	function countLast24hIoC(){
		$sql_script = "select count(id_ioc) from ioc WHERE last_seen >= NOW()-INTERVAL 1 DAY;";
		$data = $this->bd->query($sql_script);	
		$total = $data->fetch_row();
		return $total[0];
	}

	//--------------------------------------------------------------------------
	// countIoCTypes
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Retorna un array dels IOC totals de l'aplicacio per tipus. 
	//
	//--------------------------------------------------------------------------
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