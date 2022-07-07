<?php

include ("config.php");

class reports
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
	
	function notBlockedIoC(){

		$sql_script = "select I.id_ioc, I.name_ioc, I.type_id, R.total from ioc I inner join (select  ioc_id, count(id_reason) AS total from reason WHERE date >= NOW()-INTERVAL 1 DAY group by ioc_id) R ON I.id_ioc=R.ioc_id WHERE R.total>1";
		$data = $this->bd->query($sql_script);	
		$rows = $data->num_rows;
		$result = array();

		for ($i=0; $i<$rows; $i++)
		{	
			$myrow = $data->fetch_row();
			$result[$myrow[0]]['name'] = $myrow[1];
			$result[$myrow[0]]['type_id'] = $myrow[2];
			$result[$myrow[0]]['total'] = $myrow[3];
		}
		
		return $result;
	
	}
	

	
}
?>
