<?php

include ("config.php");

class role
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
	// LIST_ROLE_ACTIONS
	//--------------------------------------------------------------------------
	//	INPUT: role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the list of allowed actions by a certain role.
	//
	//--------------------------------------------------------------------------
	function list_role_actions($role_id)
	{
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT action_id FROM role_has_action WHERE role_id=$role_id";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$info = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_assoc();
			$info[$i] = $myrow['action_id'];
		}
		return $info;
	}

	//--------------------------------------------------------------------------
	// UPDATE_ROLE_ACTIONS
	//--------------------------------------------------------------------------
	//	INPUT: 	role_id,
	//					actions	(array indexed by action_id)
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been updated succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function update_role_actions($role_id, $actions)
	{
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "DELETE FROM role_has_action WHERE role_id=$role_id";
		if($this->bd->query($sql_script))
		{
			$sql_values = '';
			foreach ($actions as $key => $value)
			{
        $action_id = filter_var ($key, FILTER_SANITIZE_NUMBER_INT);
				$sql_values .= ", ($role_id, $action_id)";
			}
			$sql_values = substr($sql_values,1);
			$sql_script = "INSERT INTO role_has_action (role_id, action_id) VALUES $sql_values";
			return $this->bd->query($sql_script);
		}
		else
		{
			return FALSE;
		}
	}

    //--------------------------------------------------------------------------
	// CREATE_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	name,
	//			    description,
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Integer
	//
	//		Returns the new role ID. If an error occurs it will return FALSE.
	//
	//--------------------------------------------------------------------------
	function create_role($name, $description)
  {
		if (strlen($name) == 0 || strlen($description) == 0)
		{
			return FALSE;
		}
		else
		{
			$this->bd->query("LOCK TABLES role WRITE");
			$sql_script = "INSERT INTO role (role_id, name, description) VALUES (0,'".htmlspecialchars($name, ENT_QUOTES)."','".htmlspecialchars($description, ENT_QUOTES)."')";
			if($this->bd->query($sql_script))
			{
				$role_id = $this->bd->insert_id;
				$this->bd->query("UNLOCK TABLES");
				return $role_id;
			}
			else
			{
				$this->bd->query("UNLOCK TABLES");
				return FALSE;
			}
		}
	}


	//--------------------------------------------------------------------------
	// UPDATE_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	role_id,
	//					name,
	//					description
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been updated succesfully and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function update_role($role_id, $name, $description)
  {
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		if (strlen($name) == 0 || strlen($description) == 0)
		{
			return FALSE;
		}
		$sql_script = "UPDATE role SET name='".htmlspecialchars($name, ENT_QUOTES)."', description='".htmlspecialchars($description, ENT_QUOTES)."'";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// DELETE_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been removed succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function delete_role($role_id)
  {
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "DELETE FROM role WHERE role_id=$role_id";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// LIST_ROLES
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//					order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an arrya with the list of roles. Every entry will have the
	//		following format:
	//
	//				['role_id']	  =>	role ID
	//				['name']	=>	role's name
	//				['description']	=>	role's description
	//
	//--------------------------------------------------------------------------
	function list_roles($order_by, $order)
  {
    $order_by = filter_var ($order_by, FILTER_SANITIZE_NUMBER_INT);
    if($order_by == 1 || $order_by == 2){
      //It's ok, no need to sanitize.
    }
    else {
      $order_by = 1;
    }
    if ($order === 'ASC' || $order === 'DESC'){
      //It's ok, no need to sanitize.
    }
    else {
      $order = 'ASC';
    }
		$sql_script = "SELECT role_id, name, description FROM role ORDER BY $order_by $order";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$info = array();
		for ($i=0; $i<$rows; $i++)
		{
			$info[$i] = $data->fetch_assoc();
		}
		return $info;
	}


	//--------------------------------------------------------------------------
	// READ_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the role's information. The array will have the
	//		following format:
	//
	//				['name']	=>	role's name
	//				['description']	=>	role's description
	//
	//--------------------------------------------------------------------------
	function read_role($role_id)
  	{
    		$role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT * FROM role WHERE role_id=$role_id";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0)
			$info = $data->fetch_assoc();

		return $info;
	}


	//--------------------------------------------------------------------------
	// LIST_ROLE_MODULES
	//--------------------------------------------------------------------------
	//	INPUT:	role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the list of modules that a certain role has
	//		rights for. Each entry will have the following format:
	//
	//				['module_id']	       =>	module ID
	//				['name']	          =>	module's name
	//				['description']			=>	modules's description
	//
	//--------------------------------------------------------------------------
	function list_role_modules($role_id)
  {
		$role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
    $sql_script = "SELECT M.module_id, M.name, M.description FROM role_has_module RM, module M WHERE RM.module_id=M.module_id AND RM.role_id=$role_id ORDER BY 2 ASC";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$info = array();
		for($i=0; $i<$rows; $i++)
		{
			$info[$i] = $data->fetch_assoc();
		}
		return $info;
	}


	//--------------------------------------------------------------------------
	// ADD_MODULE_TO_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	role_id,
	//					module_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been added succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function add_module_to_role($role_id, $module_id)
  {
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
    $module_id = filter_var ($module_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "INSERT INTO role_has_module (role_id, module_id) VALUES ($role_id, $module_id)";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// DELETE_MODULE_FROM_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	role_id,
	//					module_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE it it has been removed succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function delete_module_from_role($role_id, $module_id)
  {
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
    $module_id = filter_var ($module_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "DELETE FROM role_has_module WHERE role_id=$role_id AND module_id=$module_id";
		return $this->bd->query($sql_script);
	}
}
