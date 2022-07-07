<?php

include ("config.php");

class user
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
	// CREATE_USER
	//--------------------------------------------------------------------------
	//	INPUT:	 username,
	//				   email,
	//				   password,
	//				   language
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it's created succesfully and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function create_user($username, $email, $password, $language)
	{

		if (strlen($username) == 0 || strlen($email) == 0 || strlen($password) == 0)
		{
			return FALSE;
		}
		else
		{
		      	if($password==='-'){
				$user_password ='-';
		      	}
			else{
				$user_password=sha1($password);
		      	}

			$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
			$user_email = filter_var ($email, FILTER_SANITIZE_EMAIL);

			if(in_array($language, $GLOBALS['available_languages'])){
				$user_language = $language;
			}
			else{
				$user_language = $GLOBALS['default_language'];
			}

			$sql_script = "INSERT INTO user (username, email, password, password_recovery_code, create_time, language) VALUES ('$user_username', '$user_email', '$user_password', NULL, NOW(), '$user_language')";

			return $this->bd->query($sql_script);
		}
	}


	//--------------------------------------------------------------------------
	// UPDATE_USER
	//--------------------------------------------------------------------------
	//	INPUT:	 username,
	//				   email,
	//				   language
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Updates the email and language for a given username. It will return
	//		TRUE if the action is taken correctly and FALSE if it doesn't.
	//
	//--------------------------------------------------------------------------
	function update_user($username, $email, $language)
   {
		if (strlen($username) == 0 || strlen($email) == 0 || strlen($language) == 0)
		{
			return FALSE;
		}

		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$user_email = filter_var ($email, FILTER_SANITIZE_EMAIL);

		if(in_array($language, $GLOBALS['available_languages'])){
			$user_language = $language;
		}
		else{
			$user_language = $GLOBALS['default_language'];
		}

		$sql_script = "UPDATE user SET email='$user_email', language='$user_language' WHERE username='$user_username'";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// CHANGE_USER_PASSWORD
	//--------------------------------------------------------------------------
	//	INPUT:	 username,
	//				   old_password,
	//				   new_password,
	//				   confirm_password
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if the password has been changed succesfully and FALSE
	//		if an error occurs. As a restriction, password must have at least
	//		8 characters..
	//
	//--------------------------------------------------------------------------
	function change_user_password($username, $old_password, $new_password, $confirm_password)
	{
		if (strlen($new_password) >= 8 && $new_password==$confirm_password)
		{
			$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
			$sql_script = "SELECT password FROM user WHERE username='$user_username'";
			$data = $this->bd->query($sql_script);
			if ($data->num_rows > 0)
			{
				$myrow = $data->fetch_row();
				if(strcmp($myrow[0],sha1($old_password)) == 0)
				{
					$sql_script = "UPDATE user SET password='".sha1($new_password)."' WHERE username='$user_username'";
					return $this->bd->query($sql_script);
				}
			}
		}
		return FALSE;
	}


	//--------------------------------------------------------------------------
	// CHANGE_USER_AUTH_TO_LDAP
	//--------------------------------------------------------------------------
	//	INPUT:	 username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it changes succesfully and FALSE if an error occurs.
	//
	//--------------------------------------------------------------------------
	function change_user_auth_to_ldap($username)
	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "UPDATE user SET password='-' WHERE username='$user_username'";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// RESET_USER_PASSWORD
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	String
	//
	//		Returns the new password if it works succesfully and FALSE if an
	//		error occurs.
	//
	//--------------------------------------------------------------------------
	function reset_user_password($username)
	{
    		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$letters = substr(str_shuffle(str_repeat('abcdefghijkmnpqrstuvwxyz',4)),0,6);
		$numbers = substr(str_shuffle(str_repeat('23456789',4)),0,6);
		$new_password = str_shuffle($letters.$numbers);
		$sql_script = "UPDATE user SET password='".sha1($new_password)."' WHERE username='$user_username'";
		if($this->bd->query($sql_script))
		{
			return $new_password;
		}
		else
		{
			return false;
		}
	}


	//--------------------------------------------------------------------------
	// CHANGE_USER_LANGUAGE
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//					language
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it changes succesfully and FALSE if an error occurs.
	//
	//--------------------------------------------------------------------------
	function change_user_language($username, $language)
	{
		if (strlen($username) == 0 || strlen($language) == 0) {
			return FALSE;
		}

 		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);

 		if(in_array($language, $GLOBALS['available_languages'])){
 			$user_language = $language;
 		}
 		else{
 			$user_language = $GLOBALS['default_language'];
 		}

 		$sql_script = "UPDATE user SET language='$user_language' WHERE username='$user_username'";
 		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// DELETE_USER
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if it has been deleted succesfully and FALSE elsewhere.
	//
	//--------------------------------------------------------------------------
	function delete_user($username)
	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "DELETE FROM user_has_role WHERE username='$user_username'";
     if(!$this->bd->query($sql_script))
     {
       return false;
     }
     $sql_script = "DELETE FROM user WHERE username='$user_username'";
     return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// LIST_USER
	//--------------------------------------------------------------------------
	//	INPUT:	order_by,
	//					order
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of users. It can be ordered by 1 (email),
	//		2(username) in order ASC or DESC.
	//		The array will be indexed by username. Each entry will have an array
	//		with the following format:
	//
	//				['email']	=>	email
	//				['roles']	=>	Array with the roles name.
	//
	//--------------------------------------------------------------------------
	function list_user($order_by, $order)
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
		$sql_script = "SELECT U.email, U.username, R.name FROM user U LEFT JOIN (user_has_role UR, role R) ON U.username=UR.username AND UR.role_id=R.role_id ORDER BY $order_by $order";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		$result = array();
		for ($i=0; $i<$rows; $i++)
		{
			$myrow = $data->fetch_row();
			$result[$myrow[1]]['email'] = $myrow[0];
			if (!isset($result[$myrow[1]]['roles']))
				$result[$myrow[1]]['roles'] = array();

			array_push($result[$myrow[1]]['roles'],$myrow[2]);
		}
		return $result;
	}

	//--------------------------------------------------------------------------
	// READ_USER
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the users' information.
	//
	//--------------------------------------------------------------------------
	function read_user($username)
	{
		
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT * FROM user WHERE username='$user_username'";
		$data = $this->bd->query($sql_script);
		$info = array();
		if ($data->num_rows > 0) {
			$info = $data->fetch_assoc();
		}
		return $info;
	}


	//--------------------------------------------------------------------------
	// LIST_USER_ROLES
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the users' roles. Every entry will have the
	//		following format:
	//
	//				[role_id]	=>	role id
	//				[name]	=>	role name
	//				[description] => role description
	//
	//--------------------------------------------------------------------------
	function list_user_roles($username)
  	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT R.role_id, R.name, R.description FROM user U, user_has_role UR, role R WHERE U.username=UR.username AND UR.role_id=R.role_id AND U.username='$user_username'";
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
	// ADD_USER_ROLE
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
	function add_user_role($username, $role_id)
	{
		$role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "INSERT INTO user_has_role (username, role_id) VALUES ('$user_username', $role_id)";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// DELETE_USER_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//					role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if the role has been removed from the user and FALSE if
	//		an error has occurred.
	//
	//--------------------------------------------------------------------------
	function delete_user_role($username, $role_id)
	{
		$role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "DELETE FROM user_has_role WHERE role_id=$role_id AND username='$user_username'";
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// VALIDTE_USER_CREDENTIALS
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//			    password
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if credentials are valid and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function validate_user_credentials($username, $password)
	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT password FROM user WHERE username='$user_username'";
		$data = $this->bd->query($sql_script);
		if ($data->num_rows == 0){
			return FALSE;
    		}
    		else {
			$myrow = $data->fetch_row();
			if ($myrow[0] == "-"){
				//Check credentials over LDAP.
				$permit_access = FALSE;
				@set_time_limit(0);
				$ds = ldap_connect($GLOBALS['ldap_server']) or die(_("ERROR: Could not connect to the LDAP server."));
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $GLOBALS['ldap_protocol_version']);
				if (@ldap_bind ($ds, $GLOBALS['ldap_filter'].'='.$user_username.','.$GLOBALS['ldap_dn'], $password)) {
					ldap_unbind($ds);
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				return (strcmp($myrow[0],sha1($password)) == 0);
			}
		}
	}
				
	function validate_api_key($key)
	{
		$user_key = preg_replace("[^A-Za-z0-9_]", "", $key);
		$encrypted_key = base64_encode($user_key . $GLOBALS['salt_api']);
		$sql_script = "SELECT username FROM user WHERE api_key='".$encrypted_key."'";

		$data = $this->bd->query($sql_script);
		if ($data->num_rows == 0){
			return FALSE;
    		}
      		$myrow = $data->fetch_row();
		
      		return $myrow[0];
  	}

	
	function reset_api_key($username)
	{

    		$username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$letters = substr(str_shuffle(str_repeat('abcdefghijkmnpqrstuvwxyz',16)),0,16);
		$numbers = substr(str_shuffle(str_repeat('23456789',16)),0,16);
		$new_key = str_shuffle($letters.$numbers);
		$encrypted_key = base64_encode($new_key . $GLOBALS['salt_api']);
		$sql_script = "UPDATE user SET api_key='".$encrypted_key."' WHERE username='$username'";
		if($this->bd->query($sql_script))
		{
			return $new_key;
		}
		else
		{
			return false;
		}
	}
	
	function delete_api_key($username)
	{

    		$username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "UPDATE user SET api_key=NULL WHERE username='$username'";
		
		return $this->bd->query($sql_script);
	}


	//--------------------------------------------------------------------------
	// USER_HAS_ACCESS_MODULE
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//					module_url
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if the user has the rights to access the module and FALSE
	//		in other case.
	//
	//--------------------------------------------------------------------------
	function user_has_access_module($username, $module_url)
  {
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
    		$sql_script = "SELECT U.username FROM user U, user_has_role UR, role R, role_has_module RM, module M WHERE U.username = UR.username AND UR.role_id = R.role_id AND R.role_id = RM.role_id AND '$module_url' LIKE M.url AND RM.module_id = M.module_id AND U.username='$user_username'";
		$data = $this->bd->query($sql_script);
		$rows = $data->num_rows;
		return ($rows != 0);
  }


	//--------------------------------------------------------------------------
	// GET_USER_LANGUAGE
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	String
	//
	//		Returns the users' default language.
	//
	//--------------------------------------------------------------------------
	function get_user_language($username)
  	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT language FROM user WHERE username='$user_username'";
		$data = $this->bd->query($sql_script);
		$myrow = $data->fetch_row();
		return $myrow[0];
	}

  //--------------------------------------------------------------------------
	// GET_USER_EMAIL
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	String
	//
	//		Returns users' email.
	//
	//--------------------------------------------------------------------------
	function get_user_email($username)
  	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT email FROM user WHERE username='$user_username'";
		$data = $this->bd->query($sql_script);
		$myrow = $data->fetch_row();
		return $myrow[0];
	}


	//--------------------------------------------------------------------------
	// USER_HAS_ROLE
	//--------------------------------------------------------------------------
	//	INPUT:	username,
	//					role_id
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Boolean
	//
	//		Returns TRUE if user has the role and FALSE in other case.
	//
	//--------------------------------------------------------------------------
	function user_has_role($username, $role_id)
	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
    $role_id = filter_var ($role_id, FILTER_SANITIZE_NUMBER_INT);
		$sql_script = "SELECT * FROM user_has_role WHERE role_id=$role_id AND username='$user_username'";
		$data = $this->bd->query($sql_script);
		return ($data->num_rows > 0);
	}


	//--------------------------------------------------------------------------
	// LIST_USER_ROLES_NOT_ASSIGNED
	//--------------------------------------------------------------------------
	//	INPUT:	username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of roles that the user hasn't got assigned.
	//		Each entry will have the following format:
	//
	//				['role_id']	=>	role_id
	//				['name']	=>	name
	//
	//--------------------------------------------------------------------------
	function list_user_roles_not_assigned($username)
  	{
		$user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "SELECT R.role_id, R.name FROM role R WHERE R.role_id NOT IN( SELECT role_id FROM user_has_role WHERE username='$user_username') ORDER BY 2 ASC";
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
	// LIST_ACTIONS
	//--------------------------------------------------------------------------
	//	INPUT:
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with a list of the actions.
	//
	//--------------------------------------------------------------------------
	function list_actions()
	{
		$sql_script = "SELECT * FROM action ORDER BY module_id ASC, action_id ASC";
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
	// LIST_USER_ACTIONS
	//--------------------------------------------------------------------------
	//	INPUT: username
	//
	//--------------------------------------------------------------------------
	//	OUTPUT:	Array
	//
	//		Returns an array with the list of the allowed actions for a certain user.
	//
	//--------------------------------------------------------------------------
	function list_user_actions($username)
	{
    $user_username = preg_replace("[^A-Za-z0-9_]", "", $username);
		$sql_script = "	SELECT
											RA.action_id
										FROM
											user_has_role UR,
											role_has_action RA
										WHERE
											RU.role_id=RA.role_id AND
											RU.username='$user_username'";

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


}
