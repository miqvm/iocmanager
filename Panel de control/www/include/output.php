<?php

include ("config.php");

class output
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
	// LIST_OUTPUT
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//			order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of output devices.
	//
	//--------------------------------------------------------------------------
	function list_output($order_by, $order)
	{
		$order_by = filter_var ($order_by, FILTER_SANITIZE_NUMBER_INT);
		if($order_by == 1 || $order_by == 2){
			//It's correct and there is no need to sanitize.
		}
		else {
			$order_by = 1;
		}
		if ($order === 'ASC' || $order === 'DESC'){
			//It's correct and there is no need to sanitize.
		}
		else {
			$order = 'ASC';
		}
		$sql_script = "SELECT id_output, name,description,ip FROM output ORDER BY $order_by $order";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$myrow[0]]['name'] = $myrow[1];
			$result[$myrow[0]]['description'] = $myrow[2];
			$result[$myrow[0]]['ip'] = $myrow[3];
		}
		return $result;
	}


	//--------------------------------------------------------------------------
	// CREATE_OUTPUT
	//--------------------------------------------------------------------------
	//	INPUT:	 	
	//		   output_name		
	//		   description		[Optional]
	//		   ip
	//				   			   
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it's created succesfully and FALSE in other case.
	//		Also update config
	//--------------------------------------------------------------------------
	function create_output($input)	
	{

		if (!isset($input['output_name']) || !isset($input['output_ip']) || $input['output_name']=="" || $input['output_ip']=="" || $input['id_method']=="-1")
		{
			return FALSE;
		}

		// Check if output device doesn't already exist

		$ip = preg_replace("[^A-Za-z0-9. _]", "", $input['output_ip']);
		$id_method = filter_var ($input['id_method'], FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT id_output FROM output WHERE ip='".$ip."' AND id_method=$id_method";
		
		$result=$this->bd->query($sql_script);		
		if(mysqli_num_rows($result)!=0){
			return FALSE;
		}
		// Sanitize input
		$output_name = preg_replace("[^A-Za-z0-9. _]", "", $input['output_name']);
		$description = preg_replace("[^A-Za-z0-9' _]", "", $input['output_description']);
		$message = preg_replace("[^A-Za-z0-9'\{\} _]", "", $input['output_message']);
		
		$format = json_decode($this->get_method_parameters($id_method), true);
		$parameters = '{';
		foreach($format as $values){
			$id = 'output_'.$values['name'];
			$id = str_replace(' ', '_', $id);
			$parameters.='"'.$values['name'].'": "'.$input[$id].'",';
		}
		$parameters = substr_replace($parameters,'}',-1);

		$sql_script = "INSERT INTO output (name, description, ip, id_method, parameters, message) VALUES ('".$output_name."','".$description."','".$ip."', ".$id_method.", '".$parameters."', '".$message."');";
		if($this->bd->query($sql_script)){
			if($id_method==$GLOBALS['methods']['SYSLOG']){
					$this->update_config();
			}
			$sql_script = "SELECT id_output FROM output WHERE name='".$output_name."' AND id_method='$id_method' AND ip='".$ip."'";
			$data = $this->bd->query($sql_script);
			if ($data->num_rows > 0) {
				$info = $data->fetch_assoc();
				$id_output = $info['id_output'];
				return $id_output;
			} else{
				return false;
			}
		} else {
			return false;
		}
	}
	
	//--------------------------------------------------------------------------
	// READ_OUTPUT
	//--------------------------------------------------------------------------
	//	INPUT:	output name
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Get all the values of an output Device.
	//
	//--------------------------------------------------------------------------
	function read_output($id_output)
	{
		// TODO sanitize id
		//$output_name = preg_replace("[^A-Za-z0-9. _]", "", $output_name);		
		$sql_script = "SELECT * FROM output WHERE id_output='$id_output'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info;
	}

	//--------------------------------------------------------------------------
	// LIST_OUTPUT_METHOD
	//--------------------------------------------------------------------------
	//	INPUT:	output_name
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the output methods.
	//
	//--------------------------------------------------------------------------
	function get_output_method($id_output)
  	{
  		//TODO
		$id_output = preg_replace("[^A-Za-z0-9. _]", "", $id_output);
		$sql_script = "SELECT OM.id_method, OM.method_name FROM output_method OM INNER JOIN output O ON OM.id_method=O.id_method WHERE O.id_output='$id_output'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		} 
		return $info;

	}
	
	//--------------------------------------------------------------------------
	// GET_METHOD_PARAMETERS
	//--------------------------------------------------------------------------
	//	INPUT:	id_method
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns parameters of the method passed as parameter.
	//
	//--------------------------------------------------------------------------
	function get_method_parameters($id_method){
		$sql_script = "SELECT parameters FROM output_method WHERE id_method=$id_method";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info['parameters'];
	}
	
	//--------------------------------------------------------------------------
	// GET_ALL_OUTPUT_METHODS
	//--------------------------------------------------------------------------
	//	INPUT:
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns all output methods available.
	//
	//--------------------------------------------------------------------------
	function get_all_output_methods(){
		$sql_script = "SELECT id_method, method_name FROM output_method";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$info = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$info[$myrow[0]] = $myrow[1];
		}

		return $info;
	}

	//--------------------------------------------------------------------------
	// UPDATE_INPUT
	//--------------------------------------------------------------------------
	//	INPUT:	 		   output_name,
	//				   description		[Optional]
	//				   ip			[Optional]
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Updates the attributes for a given Input device. It will return
	//		TRUE if the action is taken correctly and FALSE if it doesn't.
	//
	//--------------------------------------------------------------------------
	function update_output($id_output, $input)
   	{  	

		// Check if Input exists
		$id_output = preg_replace("[^A-Za-z0-9. _]", "", $id_output);
		$sql_script = "SELECT id_output,ip, message FROM output WHERE id_output='$id_output'";
		$data = $this->bd->query($sql_script);
		if ($data->num_rows > 0) 
		{
			$myrow = $data->fetch_row();
			$id_output = $myrow[0];
			$last_ip = $myrow[1];	
			$last_msg = $myrow[2];	
		} else{
			return FALSE;
		}

		// Sanitize inputs
		$description = preg_replace("[^A-Za-z0-9.' _]","",$input['edit_description']);
		$ip = preg_replace("[^A-Za-z0-9. _]", "", $input['edit_ip']);
		$id_method = filter_var ($input['id_method'], FILTER_SANITIZE_NUMBER_INT);
		$format = json_decode($this->get_method_parameters($id_method), true);
		$message = preg_replace("[^A-Za-z0-9'\{\} _]", "", $input['edit_message']);

		// Create JSON of parameters
		$parameters = '{';
		foreach($format as $values){
			$id = 'output_'.$values['name'];
			$id = str_replace(' ', '_', $id);
			$parameters.='"'.$values['name'].'": "'.$input[$id].'",';
		}
		$parameters = substr_replace($parameters,'}',-1);
		
		// Update config if the IP or the message has been modified
		$update_conf = $last_ip != $ip || $last_msg != $message ? TRUE: FALSE;
		$sql_script = "UPDATE output SET description='".$description."',ip='".$ip."',id_method=".$id_method.", parameters='".$parameters."', message='".$message."' WHERE id_output='".$id_output."'";

		if($this->bd->query($sql_script)){
			if($update_conf && $id_method==$GLOBALS['methods']['SYSLOG']){
				$this->update_config();
			}
			return TRUE;
		} else{
			return FALSE;
		}
	}
	
	//--------------------------------------------------------------------------
	// DELETE_OUTPUT
	//--------------------------------------------------------------------------
	//	INPUT:	output_name
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been deleted succesfully and FALSE elsewhere.
	//		Also update config
	//--------------------------------------------------------------------------
	function delete_output($id_output)
	{
		$id_output = preg_replace("[^A-Za-z0-9. _]", "", $id_output);
		// Get the ID of the Input  
		$sql_script = "SELECT id_method FROM output WHERE id_output='$id_output'";
		$data = $this->bd->query($sql_script);
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
			$id_method = $info['id_method'];
		} else{
			return FALSE;
		}
		
		$sql_script = "DELETE FROM output WHERE id_output='$id_output'";
		if($this->bd->query($sql_script)){
		   if($id_method==$GLOBALS['methods']['SYSLOG']){
		      $this->update_config();
		   }
		   return TRUE;
		} else{
		   return FALSE;
		}
	}
	
	function update_config(){
		echo shell_exec("sudo systemctl restart rsyslog");
		return 0;	
	}
}
?>
