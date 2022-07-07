<?php

include ("config.php");

class module
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
	
	function read_module($module_id)
	{
		$module_id = filter_var ($module_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT * FROM module WHERE module_id=$module_id";
		$data = $this->bd->query($sql_script);
		$info=array();
		if ($data->num_rows > 0)
			$info = $data->fetch_assoc();

		return $info;
	}
	
	
	function list_user_modules($username)
	{

		// fer filter varS
		$username = filter_var($username, FILTER_SANITIZE_STRING);	
		$sql_script = "SELECT m.* FROM user_has_role uhr INNER JOIN role r ON uhr.role_id = r.role_id INNER JOIN role_has_module rhm ON r.role_id = rhm.role_id JOIN module m ON rhm.module_id = m.module_id WHERE username='$username' AND m.parent_module_id IS NULL";

		$data = $this->bd->query($sql_script);

		$rows = $data->num_rows;
		$info = array();

		for ($i=0; $i<$rows; $i++)
		{
			$info[$i] = $data->fetch_assoc();
			
			$module_id=$info[$i]['module_id'];
			$submodules = $this->list_submodules($module_id);
			$info[$i]['submodules']= $submodules;

		}
		
		return $info;
		
	}
	
	
	function list_submodules($module_id)
	{

		$sql_script = "SELECT * FROM module WHERE parent_module_id=$module_id"; 
		$data = $this->bd->query($sql_script);
			$rows = $data->num_rows;
		$submodules = array();
		for ($i=0; $i<$rows; $i++)
		{
			$submodules[$i] = $data->fetch_assoc();
					
			$submodule_id=$submodules[$i]['module_id'];
			$ssmodules = $this->list_submodules($submodule_id);
			$submodules[$i]['submodules']= $ssmodules;

		}

		return $submodules;
	}
	
	
	function get_module_by_url($module_url)
	{
		$sql_script = "SELECT module_id FROM module WHERE '$module_url' LIKE url";
		$data = $this->bd->query($sql_script);
		$myrow = $data->fetch_row();
		return $myrow[0];
	}

}?>
