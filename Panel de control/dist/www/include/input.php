<?php

include ("config.php");

class input
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
	// LIST_INPUT
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//					order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of input devices.
	//
	//--------------------------------------------------------------------------
	function list_input($order_by, $order)
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
		$sql_script = "SELECT id_input, name,description,ip FROM input ORDER BY $order_by $order";
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
	// CREATE_INPUT
	//--------------------------------------------------------------------------
	//	INPUT:	 	
	//		   input_name		
	//		   description		[Optional]
	//		   ip
	//				   			   
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it's created succesfully and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function create_input($input)	
	{
		if (!isset($input['input_name']) || !isset($input['input_ip']) || $input['input_name']=="" || $input['input_ip']=="" || $input['id_method']=="-1")
		{
			return FALSE;
		}
				
		// Check if Input device doesn't already exist
		$ip = preg_replace("[^A-Za-z0-9. _]", "", $input['input_ip']);
		$id_method = filter_var ($input['id_method'], FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT id_input FROM input WHERE ip='".$ip."' AND id_method=$id_method";
		$result=$this->bd->query($sql_script);		
		if(mysqli_num_rows($result)!=0){
			return FALSE;
		}
		// Sanitize input
		$input_name = preg_replace("[^A-Za-z0-9. _]", "", $input['input_name']);
		$description = preg_replace("[^A-Za-z0-9' _]", "", $input['input_description']);
		
		$format = json_decode($this->get_method_parameters($id_method), true);
		$parameters = '{';
		foreach($format as $values){
			$id = 'input_'.$values['name'];
			$id = str_replace(' ', '_', $id);
			$parameters.='"'.$values['name'].'": "'.$input[$id].'",';
		}
		$parameters = substr_replace($parameters,'}',-1);
		
		$sql_script = "INSERT INTO input (name, description, ip, id_method, parameters) VALUES ('".$input_name."','".$description."','".$ip."', ".$id_method.", '".$parameters."');";
		if($this->bd->query($sql_script)){
			if($id_method==$GLOBALS['methods']['SYSLOG']){
		      		$this->update_config();
		   	}
		   	
		   	$sql_script = "SELECT id_input FROM input WHERE name='".$input_name."' AND id_method='$id_method' AND ip='".$ip."'";
			$data = $this->bd->query($sql_script);
			if ($data->num_rows > 0) {
				$info = $data->fetch_assoc();
				$id_input = $info['id_input'];
				return $id_input;
			} else{
				return false;
			}

		} else {
		   return FALSE;
		}
	}
	

	//--------------------------------------------------------------------------
	// READ_INPUT
	//--------------------------------------------------------------------------
	//	INPUT:	id_input
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Get all the values of an Input Device.
	//
	//--------------------------------------------------------------------------
	function read_input($id_input)
	{
		$id_input = preg_replace("[^A-Za-z0-9. _]", "", $id_input);		
		$sql_script = "SELECT * FROM input WHERE id_input='$id_input'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info;
	}

	//--------------------------------------------------------------------------
	// LIST_INPUT_METHOD
	//--------------------------------------------------------------------------
	//	INPUT:	id_input
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the input methods.
	//
	//--------------------------------------------------------------------------
	function get_input_method($id_input)
  	{
  	
		$id_input = preg_replace("[^A-Za-z0-9. _]", "", $id_input);
	
		$sql_script = "SELECT IM.id_method, IM.method_name FROM input_method IM INNER JOIN input I ON IM.id_method=I.id_method WHERE I.id_input='$id_input'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		} 
		return $info;

	}
	
	function get_all_input_methods(){
		$sql_script = "SELECT id_method, method_name FROM input_method";
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
	//	INPUT:	 		   id_input,
	//				   input_name
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
	function update_input($id_input, $input)
   	{  	
		// Check if Input exists
		$id_input = preg_replace("[^A-Za-z0-9. _]", "", $id_input);
		$sql_script = "SELECT id_input,ip FROM input WHERE id_input='$id_input'";
		$data = $this->bd->query($sql_script);
		if ($data->num_rows > 0) 
		{
			$myrow = $data->fetch_row();
			$last_ip = $myrow[1];		
		} else{
			return FALSE;
		}

		// Sanitize inputs
		$description = preg_replace("[^A-Za-z0-9.' _]","",$input['edit_description']);
		$ip = preg_replace("[^A-Za-z0-9. _]", "", $input['edit_ip']);
		$id_method = filter_var ($input['input_method'], FILTER_SANITIZE_NUMBER_INT);
		$format = json_decode($this->get_method_parameters($id_method), true);

		$parameters = '{';
		foreach($format as $values){
			$id = 'input_'.$values['name'];
			$id = str_replace(' ', '_', $id);
			$parameters.='"'.$values['name'].'": "'.$input[$id].'",';
		}
		$parameters = substr_replace($parameters,'}',-1);
		
		$update_conf = $last_ip != $ip ? TRUE: FALSE;
		$sql_script = "UPDATE input SET description='".$description."',ip='".$ip."',id_method=".$id_method.", parameters='".$parameters."' WHERE id_input='".$id_input."'";

		if($this->bd->query($sql_script)){
			if($update_conf && $id_method==$GLOBALS['methods']['SYSLOG']){
				$this->update_config();
			}
			return TRUE;
		} else{
			return FALSE;
		}
	}
	
	function get_method_parameters($id_method){
		$sql_script = "SELECT parameters FROM input_method WHERE id_method=$id_method";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info['parameters'];
	}
	
	//--------------------------------------------------------------------------
	// DELETE_INPUT
	//--------------------------------------------------------------------------
	//	INPUT:	id_input
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been deleted succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function delete_input($id_input)
	{
		$id_input = preg_replace("[^A-Za-z0-9. _]", "", $id_input);
		// Get the ID of the Input  
		$sql_script = "SELECT id_method FROM input WHERE id_input='$id_input'";
		$data = $this->bd->query($sql_script);
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
			$id_method = $info['id_method'];
		} else{
			return FALSE;
		}
		
		$sql_script = "DELETE FROM input WHERE id_input='$id_input'";
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
	
		$allowedSenders = "";
		$inputs = $this->list_input(1, 'ASC');
		
		foreach($inputs as $input){
			$allowedSenders .= ", ".$input['ip'];
		}
		
		echo shell_exec('sudo sed -i "/^\$AllowedSender/c\$AllowedSender UDP'.$allowedSenders.'" /etc/rsyslog.conf');
		echo shell_exec("sudo systemctl restart rsyslog");
		return 0;	
	}
}
?>
