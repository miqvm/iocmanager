<?php

	session_start();
	include_once('www/include/bbdd.php');
	$bbdd = new bbdd();
	include('www/include/user.php');
	$user = new user($bbdd);
	include('www/include/ioc.php');
	$ioc = new ioc($bbdd);
	include('www/include/module.php');
	$module = new module($bbdd);
	include('www/include/whitelist.php');
	$whitelist = new whitelist($bbdd);
	include('www/include/input.php');
	$input = new input($bbdd);
	include('www/include/output.php');
	$output = new output($bbdd);
	include('www/include/api.php');
	$api = new api($bbdd);
	include('www/include/dashboard.php');
	$dashboard = new dashboard($bbdd);
	include('www/include/reports.php');
	$reports = new reports($bbdd);
  /* --------------------------------------------------------------------------
   * Controla si el usuario esta logueado
   * ------------------------------------------------------------------------*/
	if(isset($_REQUEST['logout']))
	{
	  unset($_SESSION['_gestioioc']);
	  unset($_SESSION['_gestioioc_userlang']);
	}
	else
	{
	  if(isset($_POST['_gestioioc_username']))
	  {
		 if (!$user->validate_user_credentials($_POST['_gestioioc_username'],$_POST['_gestioioc_password']))
		 {
			$mensaje = 'Usuari o contrasenya incorrecta.';
		 }
		 else
		 {
			 $user_info = $user->read_user($_POST['_gestioioc_username']);
			 $_SESSION['_gestioioc'] = $user_info['username'];
			 $_SESSION['_gestioioc_userlang'] = $user_info['language'];
		 }
	  } elseif(isset($_REQUEST['key']) && strpos($_SERVER['REQUEST_URI'],  "api")){
	  	$username = $user->validate_api_key($_REQUEST['key']);
	  	$user_info = $user->read_user($username);
		$_SESSION['_gestioioc'] = $user_info['username'];
		$_SESSION['_gestioioc_userlang'] = $user_info['language'];
	  }
	}

  /* --------------------------------------------------------------------------
   * Controla el idioma
   * ------------------------------------------------------------------------*/
	function validate_language($lang){
		if(in_array($lang, $GLOBALS['available_languages'])){
			return $lang;
		}
		else{
			return $GLOBALS['default_language'];
		}
	}

	if(isset($_REQUEST['user_language'])) {
    		$_SESSION['_gestioioc_userlang'] = validate_language($_REQUEST['user_language']);
  	}
  	else {
    		if (!isset($_SESSION['_gestioioc_userlang'])) {
			$_SESSION['_gestioioc_userlang'] = $GLOBALS['default_language'];
		}
  	}
	$locale = $_SESSION['_gestioioc_userlang'].'.utf8';
	define('TEXTDOMAIN', 'gestio_ioc');
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain(TEXTDOMAIN, "./languages");
	textdomain(TEXTDOMAIN);


  /* --------------------------------------------------------------------------
   * Encamina hacia el controller adecuado
   * ------------------------------------------------------------------------*/
  if (!isset($_SESSION['_gestioioc']))
	{
    include('www/controllers/login.php');
	}
	else
	{
    // Include router class
    include('www/include/route.php');
    foreach (glob("www/routes/*.php") as $filename)
    {
        include $filename;
    }
    Route::methodNotAllowed(function(){
		$error_code = '405';
		$error_title = _("Oops! You should not see this screen.");
		$error_msg = _("We don't know why you came to this screen. Try going back or back to the home page.");
		include('www/controllers/error.php');
    });

    Route::pathNotFound(function(){
		$error_code = '404';
		$error_title = _("Oops! You should not see this screen.");
		$error_msg = _("We don't know why you came to this screen. Try going back or back to the home page.");
		include('www/controllers/error.php');
    });

    Route::moduleNotAllowed(function(){
		$error_code = '403';
		$error_title = _("Oops! You should not see this screen.");
		$error_msg = _("We don't know why you came to this screen. Try going back or back to the home page.");
		include('www/controllers/error.php');
    });

    Route::run('/');
	}
?>
