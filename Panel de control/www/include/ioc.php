<?php

include ("config.php");

class ioc
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
	// CREATE_IOC
	//--------------------------------------------------------------------------
	//	INPUT:	 	
	//			For IoC:	   
	//				   name,
	//				   first_seen			[Optional]
	//				   last_seen			[Optional]
	//				   json_offence_level	[Optional]
	//				   url					[Optional]
	//				   type name
	//				   
	//			For it's reason:
	//				   reason
	//				   source,
	//				   direction			[Optional]
	//				   conficence			[Optional]
	//				   sin_malos_source		[Optional]
	//				   date					[Optional]
	//				   			   
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it's created succesfully and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function create_ioc($input)	
	{	

		if (!isset($input['ioc_name']) || !isset($input['ioc_type']) || !isset($input['ioc_reason_reason']) || !isset($input['ioc_reason_source']) ||  ($input['ioc_name']=="" || $input['ioc_reason_reason']=="" || $input['ioc_reason_source']==""))
		{

			return FALSE;
		}

		$ioc_name = preg_replace("[^A-Za-z0-9. _]", "", $input['ioc_name']);
		$sql_script = "SELECT id_whitelist FROM whitelist WHERE name='$ioc_name'";

		$result=$this->bd->query($sql_script);		
		if(mysqli_num_rows($result)!=0){
		
			return FALSE;
		}

		// Check if IoC doesn't already exist
			//If it's Type: URL OR Domain: name_ioc has sha1 result of the url
		if($input['ioc_type']==3 || $input['ioc_type']==5){
			$url= filter_var($input['ioc_name'], FILTER_SANITIZE_URL);
			$ioc_name=sha1($input['ioc_name']);
		} else {
			$ioc_name = preg_replace("[^A-Za-z0-9. _]", "", $input['ioc_name']);
		}

		$sql_script = "SELECT id_ioc FROM ioc WHERE name_ioc='$ioc_name'";
		$result=$this->bd->query($sql_script);		
		if(mysqli_num_rows($result)!=0){
			return FALSE;
		}

		// Sanitize input, on uninserted values set NULL or default value
		$first_seen = !strlen($input['ioc_first_seen']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['ioc_first_seen'])  ."'" : "NOW()";
		$last_seen = !strlen($input['ioc_last_seen']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['ioc_last_seen']) ."'" : "NOW()";
		$type_id = filter_var($input['ioc_type'], FILTER_SANITIZE_NUMBER_INT);
		$jol = !strlen($input['ioc_jol']) == 0 ? filter_var($input['ioc_jol'], FILTER_SANITIZE_NUMBER_INT) : 0;
		$reason = preg_replace("[^A-Za-z0-9_]", "", $input['ioc_reason_reason']);
		$source = preg_replace("[^A-Za-z0-9_]", "", $input['ioc_reason_source']);
		$direction = preg_replace("[^A-Za-z0-9_]", "", $input['ioc_reason_direction']);
		$conficence = isset($input['ioc_reason_confidence']) ? (int)$input['ioc_reason_confidence'] : 0;
		$sin_malos_source = preg_replace("[^A-Za-z0-9_]", "", $input['ioc_reason_sms']);
		$sharelevel = filter_var ($input['reason_sh'], FILTER_SANITIZE_NUMBER_INT);
		$reason_date = !strlen($input['ioc_reason_date']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['ioc_reason_date']) ."'" : "NOW()";
		// Insert new IoC
		
		if($jol != 0 && strlen($_POST['ioc_quarantine_end']) == 0){		// JOL is set & QE is not set
			$now = new DateTime();   
			$quarantine_hours = $GLOBALS['json_offence_level'][$jol]['quarantine_time'];
		   	$quarantine_end = (clone $now)->add(new DateInterval("PT{$quarantine_hours}H"))->format('Y-m-d H:i:s');
		   	
		} elseif($jol != 0){							// JOL is set & QE is set
			$quarantine_end = preg_replace('/[^0-9: \-]/','',$input['ioc_quarantine_end']);
			
		} elseif(strlen($_POST['ioc_quarantine_end']) != 0){			// JOL is not set & QE is set
			$quarantine_end = preg_replace('/[^0-9: \-]/','',$input['ioc_quarantine_end']);
			
		} else{								// JOL is not set & QE is not set
			$now = new DateTime();   
		   	$quarantine_end = $now->format('Y-m-d H:i:s');
		}
			
		if($jol != 0 && strlen($_POST['ioc_monitoring_end']) == 0){		// JOL is set & ME is not set
			$now = new DateTime();   
			$monitoring_hours = $GLOBALS['json_offence_level'][$jol]['monitoring_time'];
		   	$monitoring_end = (clone $now)->add(new DateInterval("PT{$monitoring_hours}H"))->format('Y-m-d H:i:s');
		   	
		} elseif($jol != 0){							// JOL is set & ME is set
			$monitoring_end = preg_replace('/[^0-9: \-]/','',$input['ioc_monitoring_end']);
			
		} elseif(strlen($_POST['ioc_monitoring_end']) != 0){			// JOL is not set & ME is set
			$monitoring_end = preg_replace('/[^0-9: \-]/','',$input['ioc_monitoring_end']);
			
		} else{								// JOL is not set & ME is not set
			$now = new DateTime();   
		   	$monitoring_end = $now->format('Y-m-d H:i:s');
		}
		
		if(strtotime($quarantine_end) > strtotime($monitoring_end)){
			$monitoring_end = $quarantine_end;
		}
		
		$sql_script = "INSERT INTO ioc (name_ioc, first_seen, last_seen, json_offence_level, url, type_id, quarantine_end, monitoring_end) VALUES ('$ioc_name',$first_seen,$last_seen,'$jol','$url','$type_id', '$quarantine_end', '$monitoring_end')";
		if(!$this->bd->query($sql_script)){
			return FALSE;
		}

		// Get ID of the inserted IoC for reason's foreing key "ioc_id"
		$sql_script = "SELECT id_ioc FROM ioc WHERE name_ioc='$ioc_name'";
		$data = $this->bd->query($sql_script);
		$id_ioc = array();
		if ($data->num_rows > 0) {
			$id_ioc = $data->fetch_assoc();
			$id_ioc = $id_ioc['id_ioc'];
		} else{
			return FALSE;
		}

		
		$sql_script = "INSERT INTO reason (reason,source,direction,confidence,sin_malos_source,date,share_level,ioc_id) VALUES ('$reason','$source','$direction',$conficence,'$sin_malos_source',$reason_date,$sharelevel,$id_ioc)";
		if( $this->bd->query($sql_script)){
			return $id_ioc;
		} else {
			return false;
		}
	}


	//--------------------------------------------------------------------------
	// UPDATE_IoC
	//--------------------------------------------------------------------------
	//	INPUT:	 	   name,
	//				   first_seen			[Optional]
	//				   last_seen			[Optional]
	//				   json_offence_level	[Optional]
	//				   url					[Optional]
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Updates the attributes for a given IoC. It will return
	//		TRUE if the action is taken correctly and FALSE if it doesn't.
	//
	//--------------------------------------------------------------------------
	function update_ioc($id_ioc, $input)
   	{
		// Check if IoC exists
		$id_ioc = preg_replace("[^A-Za-z0-9. _]", "", $id_ioc);
		$sql_script = "SELECT id_ioc,json_offence_level FROM ioc WHERE id_ioc='$id_ioc'";
		$data = $this->bd->query($sql_script);
		$response = array();
		if ($data->num_rows > 0) {
			$response = $data->fetch_assoc();
			$last_jol = $response['json_offence_level'];
		} else{
			return FALSE;
		}

		// Sanitize inputs
		$first_seen = preg_replace('/[^0-9: \-]/','',$input['ioc_first_seen']);
		$last_seen = preg_replace('/[^0-9: \-]/','',$input['ioc_last_seen']);
		$type_id = filter_var($input['ioc_type'], FILTER_SANITIZE_NUMBER_INT);
		$jol = isset($input['ioc_jol']) ? (int)$input['ioc_jol'] : 0;



		if($last_jol != $jol){

		   $now = new DateTime();

		   $quarantine_hours = $GLOBALS['json_offence_level'][$jol]['quarantine_time'];
		   $monitoring_hours = $GLOBALS['json_offence_level'][$jol]['monitoring_time'];
		   
		   $quarantine_end = (clone $now)->add(new DateInterval("PT{$quarantine_hours}H"))->format('Y-m-d H:i:s');
		   $monitoring_end = (clone $now)->add(new DateInterval("PT{$monitoring_hours}H"))->format('Y-m-d H:i:s');
		   
		}else{
		   $quarantine_end = preg_replace('/[^0-9: \-]/','',$input['quarantine_end']);
		   $monitoring_end = preg_replace('/[^0-9: \-]/','',$input['monitoring_end']);
		}
		
		if(strtotime($quarantine_end) > strtotime($monitoring_end)){
			$monitoring_end = $quarantine_end;
		}
		
		$sql_script = "UPDATE ioc SET first_seen='$first_seen', last_seen='$last_seen', json_offence_level=$jol, type_id='$type_id', quarantine_end='$quarantine_end', monitoring_end='$monitoring_end' WHERE id_ioc='$id_ioc'";

		return $this->bd->query($sql_script);
	}
	
	
	//--------------------------------------------------------------------------
	// UPDATE_IoC_REASON
	//--------------------------------------------------------------------------
	//	INPUT:	 		   reason,
	//				   source,
	//				   last_seen,		[Optional]
	//				   json_offence_level  [Optional]
	//				   reason_date		[Optional]
	//				   share_level		[Optional]
	//				   disable		[Optional]
	//				   disable_reason	[Optional]
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Updates the attributes for a given Reason. It will return
	//		TRUE if the action is taken correctly and FALSE if it doesn't.
	//
	//--------------------------------------------------------------------------
	function update_ioc_reason($id_reason, $input)
   	{

   		// Sanitize inputs
   		$id_reason = (int)$id_reason;
		$reason = preg_replace("[^A-Za-z0-9_]", "", $input['reason_reason']);
		$source = preg_replace("[^A-Za-z0-9_]", "", $input['reason_source']);
		$direction = preg_replace("[^A-Za-z0-9_]", "", $input['reason_direction']);
		$conficence = isset($input['reason_confidence']) ? (int)$input['reason_confidence'] : NULL;
		$sin_malos_source = preg_replace("[^A-Za-z0-9_]", "", $input['reason_sms']);
		$reason_date = preg_replace('/[^0-9: \-]/','',$input['reason_date']);
		$sharelevel = filter_var ($input['reason_sh'], FILTER_SANITIZE_NUMBER_INT);
		$disable = isset($input['reason_dis']) ? 1 : 0;
		$disable_reason = isset($input['reason_dis_r']) ? preg_replace("[^A-Za-z0-9_]", "", $input['reason_dis_r']) : "";
		
		$sql_script = "UPDATE reason SET reason='$reason', source='$source', direction='$direction', confidence='$conficence', sin_malos_source='$sin_malos_source', date='$reason_date', share_level='$sharelevel', disable='$disable', disable_reason='$disable_reason' WHERE id_reason='$id_reason'";
		return $this->bd->query($sql_script);
		
	}

	//--------------------------------------------------------------------------
	// LIST_IoCs
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//			order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of Indicators of Compromise.
	//		The array will be indexed by name_ioc. Each entry will have an array
	//		with the following format:
	//
	//--------------------------------------------------------------------------
	function list_iocs($order_by, $order)
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
		$sql_script = "SELECT id_ioc, name_ioc,first_seen,json_offence_level,type_id,url, quarantine_end, monitoring_end FROM ioc ORDER BY $order_by $order";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$myrow[0]]['ioc_name'] = $myrow[1];
			$result[$myrow[0]]['first_seen'] = $myrow[2];
			$result[$myrow[0]]['json_offence_level'] = $myrow[3];
			$result[$myrow[0]]['type_id'] = $myrow[4];
			$result[$myrow[0]]['url'] = $myrow[5];
			$result[$myrow[0]]['quarantine_end'] = $myrow[6];
			$result[$myrow[0]]['monitoring_end'] = $myrow[7];
		}
		return $result;
	}

	//--------------------------------------------------------------------------
	// READ_USER
	//--------------------------------------------------------------------------
	//	INPUT:	IoC ID
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Get all the values of an IoC.
	//
	//--------------------------------------------------------------------------
	function read_ioc($id_ioc)
	{

		$id_ioc = preg_replace("[^A-Za-z0-9. _]", "", $id_ioc);		
		$sql_script = "SELECT * FROM ioc WHERE id_ioc='$id_ioc'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info;
	}
	
	
	//--------------------------------------------------------------------------
	// GET_TYPE_NAME
	//--------------------------------------------------------------------------
	//	INPUT:	$type_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	String
	//
	//		Returns the name of the type indexed by the id passed as parameter
	//
	//--------------------------------------------------------------------------
	function get_type_name($type_id){

		$type_id = filter_var ($type_id, FILTER_SANITIZE_NUMBER_INT);
		
		$sql_script = "SELECT type_name FROM type WHERE id_type='$type_id'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
			return $info['type_name'];
		} else{
			return "";
		}
	}
	
	
	//--------------------------------------------------------------------------
	// GET_ALL_TYPE_NAME
	//--------------------------------------------------------------------------
	//	INPUT:	
	//
	//--------------------------------------------------------------------------
	//	OUTPUT: Array
	//
	//		Returns all types names
	//
	//--------------------------------------------------------------------------
	function get_all_type_name(){

		
		$sql_script = "SELECT id_type, type_name FROM type";
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
	// LIST_IOC_REASON_SHORT
	//--------------------------------------------------------------------------
	//	INPUT:	$id_ioc
	//
	//--------------------------------------------------------------------------
	//	OUTPUT: Array
	//
	//		Returns all reasons from 'id_ioc' on a short format ['id_reason','reason','source']
	//		Useful for listing all reasons of an IoC on ioc/edit or ioc/view
	//--------------------------------------------------------------------------
	function list_ioc_reason_short($id_ioc)
	{
		$id_ioc = preg_replace("[^A-Za-z0-9. _]", "", $id_ioc);

		$sql_script = "SELECT * FROM reason WHERE ioc_id='$id_ioc'";
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
	// GET_IOC_REASON
	//--------------------------------------------------------------------------
	//	INPUT:	$id_reason
	//
	//--------------------------------------------------------------------------
	//	OUTPUT: Array
	//
	//		Return all info from a reason
	//--------------------------------------------------------------------------
	function get_ioc_reason($id_reason)
	{
		$id_reason = (int)$id_reason;
		
		$sql_script = "SELECT I.name_ioc, R.* FROM reason R INNER JOIN ioc I ON R.ioc_id=I.id_ioc WHERE id_reason=$id_reason";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}

		return $info;
	}


	//--------------------------------------------------------------------------
	// ADD_IOC_REASON
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//					role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it adds the role to the user and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function add_ioc_reason($id_ioc, $input)
	{

		if (!isset($id_ioc) || !isset($input['n_reason_reason']) || !isset($input['n_reason_source']) || $input['n_reason_reason']=="" || $input['n_reason_source']=="")
		{
			return FALSE;
		} 

		//TODO CHECK IF INPUTS ARE CORRECT
		
		$reason = preg_replace("[^A-Za-z0-9_]", "", $input['n_reason_reason']);

		$source = preg_replace("[^A-Za-z0-9_]", "", $input['n_reason_source']);
		$direction = preg_replace("[^A-Za-z0-9_]", "", $input['n_reason_direction']);
		$conficence = isset($input['n_reason_confidence']) ? (int)$input['n_reason_confidence'] : NULL;
		$sin_malos_source = preg_replace("[^A-Za-z0-9_]", "", $input['n_reason_sms']);
		$sharelevel = filter_var ($input['reason_sh'], FILTER_SANITIZE_NUMBER_INT);
		$reason_date = !strlen($input['n_reason_date']) == 0 ? "'". preg_replace('/[^0-9: \-]/','',$input['n_reason_date'])  ."'" : "NOW()";
		
		$sql_script = "INSERT INTO reason (reason,source,direction,confidence,sin_malos_source,date,share_level,ioc_id) VALUES ('$reason','$source','$direction',$conficence,'$sin_malos_source',$reason_date,$sharelevel,$id_ioc);";
		return $this->bd->query($sql_script);
	}
	
	
	//--------------------------------------------------------------------------
	// SEARCH_IOC
	//--------------------------------------------------------------------------
	//	INPUT:	 		   ioc_name 		[Optional]
	//				   start_date		[Optional]
	//				   end_date		[Optional]
	//				   json_offence_level	[Optional]
	//				   url			[Optional]
	//				   type_id		[Optional]
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Select from search
	//
	//
	//--------------------------------------------------------------------------
	function search_ioc($input)
	{
		$sql_script="SELECT id_ioc, name_ioc, type_id, json_offence_level, quarantine_end, monitoring_end,url FROM ioc";
		$where = array();
		if(!strlen($input['search_ioc_name']) == 0){
			if($input['search_ioc_type']=="3" || $input['search_ioc_type']=="5"){
				$temp=$input['search_ioc_name'];
				array_push($where, "url LIKE '%$temp%'");
			} else {
				$temp=$input['search_ioc_name'];
				array_push($where, "name_ioc LIKE '%$temp%'");
			}
		}
		if(!strlen($input['search_ioc_jof']) == 0){
			$temp=$input['search_ioc_jof'];
			array_push($where, "json_offence_level=$temp");
		}
		if(!strlen($input['search_ioc_start_date']) == 0){
			$temp = preg_replace('/[^0-9: \-]/','',$input['search_ioc_start_date']);
			array_push($where, "first_seen>='$temp'");
		}
		if(!strlen($input['search_ioc_end_date']) == 0){
			$temp = preg_replace('/[^0-9: \-]/','',$input['search_ioc_end_date']);
			array_push($where, "last_seen<='$temp'");
		}
		if(!strlen($input['search_ioc_type']) == 0 && $input['search_ioc_type']!="-1"){
			$temp=$input['search_ioc_type'];
			array_push($where, "type_id=$temp");
		}

		if(!count($where) == 0){

			$temp = implode(" AND ", $where);
			$sql_script.= ' WHERE '.$temp;
		}
		
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$result = array();

		for ($i=0; $i<$rows; $i++)
		{		

			$myrow = $data->fetch_row();
			$result[$myrow[0]]['ioc_name'] = $myrow[1];
			$result[$myrow[0]]['type_id'] = $myrow[2];
			$result[$myrow[0]]['json_offence_level'] = $myrow[3];
			$result[$myrow[0]]['quarantine_end'] = $myrow[4];
			$result[$myrow[0]]['monitoring_end'] = $myrow[5];
			$result[$myrow[0]]['url'] = $myrow[6];
		}
		
		return $result;
	}
}?>
