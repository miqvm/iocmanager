<?php

include ("config.php");

class bbdd
{
	var $bbdd;

	function __construct()
	{
		$this->bbdd = new mysqli($GLOBALS['db_server'], $GLOBALS['db_user'], $GLOBALS['db_pass'],$GLOBALS['db_database'],$GLOBALS['db_port'],$GLOBALS['db_socket']);
		if ($this->bbdd->connect_errno)
		{
			printf("ERROR: No se ha podido establecer la conexión (CODIGO: 1)");
    			exit();
		}
		if (!$this->bbdd->set_charset("utf8"))
		{
			printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
			exit();
		}

	}

	function __destruct()
	{
		$this->bbdd->close();
	}

}
