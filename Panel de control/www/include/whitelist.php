<?php

include ("config.php");

class whitelist
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
	// LIST_WHITELIST
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//					order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of whitelisted indicators.
	//
	//--------------------------------------------------------------------------
	function list_whitelist($order_by, $order)
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
		$sql_script = "SELECT id_whitelist,name,description,share_level FROM whitelist ORDER BY $order_by $order";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$myrow[0]]['name'] = $myrow[1];
			$result[$myrow[0]]['description'] = $myrow[2];
			$result[$myrow[0]]['share_level'] = $myrow[3];
		}
		return $result;
	}

	


	//--------------------------------------------------------------------------
	// CREATE_WHITELIST
	//--------------------------------------------------------------------------
	//	INPUT:	 	
	//		   whitelist_name
	//		   ioc_type	
	//		   description		[Optional]		
	//		   date 		[Optional]
	//		   sharelevel		[Optional]
	//				   			   
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it's created succesfully and FALSE in other case.
	//
	//--------------------------------------------------------------------------

	function create_whitelist($input)	
	{		
		if (!isset($input['whitelist_name']) || !isset($input['ioc_type']) || ($input['whitelist_name']=="" || $input['ioc_type']==""))
		{
			return FALSE;
		}
		
		// Check if Whitelist indicator doesn't already exist

		$whitelist_name = preg_replace("[^A-Za-z0-9. _]", "", $input['whitelist_name']);
		
		$sql_script = "SELECT id_whitelist FROM whitelist WHERE name='$whitelist_name'";
		$result=$this->bd->query($sql_script);		
		if(mysqli_num_rows($result)!=0){
			return FALSE;
		}

		// Sanitize input, on uninserted values set NULL or default value
		$date = !strlen($input['whitelist_date']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['whitelist_date']) ."'" : "NOW()";
		$type_id = filter_var($input['ioc_type'], FILTER_SANITIZE_NUMBER_INT);
		$description = preg_replace("[^A-Za-z0-9' _]", "", $input['whitelist_description']);
		$sharelevel = filter_var ($input['whitelist_sl'], FILTER_SANITIZE_NUMBER_INT);
		
		// Insert new Whitelist
		$sql_script = 'INSERT INTO whitelist (name, description, date, share_level,type_id) VALUES ("'.$whitelist_name.'","'.$description.'",'.$date.','.$sharelevel.',"'.$type_id.'");';

		if($this->bd->query($sql_script)){
			$sql_script = "SELECT id_whitelist FROM whitelist WHERE name='".$whitelist_name."';";
			$data = $this->bd->query($sql_script);
			if ($data->num_rows > 0) {
				$info = $data->fetch_assoc();
				$id_whitelist = $info['id_whitelist'];
				return $id_whitelist;
			} else{
				return false;
			}
		} else {
			return false;
		}
	}
	
	//--------------------------------------------------------------------------
	// READ_WHITELIST
	//--------------------------------------------------------------------------
	//	INPUT:	Whitelist name
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Get all the values of an IoC.
	//
	//--------------------------------------------------------------------------
	function read_whitelist($id_whitelist)
	{
		$id_whitelist = preg_replace("[^A-Za-z0-9. _]", "", $id_whitelist);		
		$sql_script = "SELECT * FROM whitelist WHERE id_whitelist='$id_whitelist'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info;
	}


	//--------------------------------------------------------------------------
	// UPDATE_IoC
	//--------------------------------------------------------------------------
	//	INPUT:	 		   name,
	//				   first_seen		[Optional]
	//				   last_seen		[Optional]
	//				   json_offence_level	[Optional]
	//				   url			[Optional]
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Updates the attributes for a given IoC. It will return
	//		TRUE if the action is taken correctly and FALSE if it doesn't.
	//
	//--------------------------------------------------------------------------
	function update_whitelist($id_whitelist, $input)
   	{  		
		// Check if Whitelist exists
		$id_whitelist = preg_replace("[^A-Za-z0-9. _]", "", $id_whitelist);
		$sql_script = "SELECT id_whitelist FROM whitelist WHERE name='$whitelist_name'";
		if(!$this->bd->query($sql_script)){
			return false;
		}

		// Sanitize inputs
		$description = preg_replace("[^A-Za-z0-9.' _]","",$input['edit_description']);
		$date = !strlen($input['edit_whitelist_date']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['edit_whitelist_date']) ."'" : "NOW()";
		$type_id = filter_var($input['whitelist_type'], FILTER_SANITIZE_NUMBER_INT);
		$share_level = filter_var($input['whitelist_sl'], FILTER_SANITIZE_NUMBER_INT);
	
		
		$sql_script = 'UPDATE whitelist SET description="'.$description.'",date='.$date.', share_level='.$share_level.',type_id="'.$type_id.'" WHERE id_whitelist="'.$id_whitelist.'"';
		return $this->bd->query($sql_script);
	}
	
	//--------------------------------------------------------------------------
	// DELETE_WHITELIST
	//--------------------------------------------------------------------------
	//	INPUT:	whitelist_ip
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been deleted succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function delete_whitelist($id_whitelist)
	{
		$id_whitelist = preg_replace("[^A-Za-z0-9. _]", "", $id_whitelist);
		// Get the ID of the Whitelist 
		
		$sql_script = "DELETE FROM whitelist WHERE id_whitelist='$id_whitelist'";
		return $this->bd->query($sql_script);
	}
	
	function update_config(){
		echo shell_exec("sudo systemctl restart rsyslog");
		return 0;	
	}
}
?>
