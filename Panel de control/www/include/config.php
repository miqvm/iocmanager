<?php
  $ini_file_url = '/opt/config.ini';
  $ini_config = parse_ini_file($ini_file_url,true);
  
  //--------------------------------------------------------------------------
  // PARÀMETRES D'ACCÉS A LA DASHBOARD
  //--------------------------------------------------------------------------
  $url_base = $ini_config['dashboard']['url_base'];
  $url_base_path = $ini_config['dashboard']['url_base_path'];

  //--------------------------------------------------------------------------
  // PARÀMETRES DE CONFIGURACIÓ D'IDIOMES
  //--------------------------------------------------------------------------
  $available_languages = json_decode($ini_config['languages']['available_languages'],true);
  $default_language = $ini_config['languages']['default_language'];
  
  //--------------------------------------------------------------------------
  // PARÀMETRES DE VARIABLES GLOBALS DE LA DASHBOARD
  //--------------------------------------------------------------------------
  $share_level = json_decode($ini_config['share_level']['share_level'],true);
  $json_offence_level = json_decode($ini_config['json_offence_level']['json_offence_level'],true);
  $methods = $ini_config['method'];
  $ioc_types = json_decode($ini_config['ioc_type']['ioc_type'],true);
  
  //--------------------------------------------------------------------------
  // FITXERS DE ESCRIPTURA DE L'API OPTIMITZADA
  //--------------------------------------------------------------------------
  $edl_output_file = $ini_config['ipv4_optimizer']['edl_output_file'];
  $dag_output_file = $ini_config['ipv4_optimizer']['dag_output_file'];
  $overflow_output_file = $ini_config['ipv4_optimizer']['overflow_output_file'];
  $stats_output_file = $ini_config['ipv4_optimizer']['stats_output_file'];

  //--------------------------------------------------------------------------
  // PARÀMETRES D'ACCÉS A LA BBDD MYSQL
  //--------------------------------------------------------------------------
  $db_server = $ini_config['database']['db_server'];
  $db_user = $ini_config['database']['db_user'];
  $db_pass = $ini_config['database']['db_pass'];
  $db_database = $ini_config['database']['db_database'];
  $db_socket = $ini_config['database']['db_socket'];
  $db_port = $ini_config['database']['db_port'];

  //--------------------------------------------------------------------------
  // PARÀMETRES D'ACCÉS A LDAP
  //-------------------------------------------------------------------------- 
  $ldap_server = $ini_config['ldap']['ldap_server'];
  $ldap_protocol_version = $ini_config['ldap']['ldap_protocol_version'];
  $ldap_dn = $ini_config['ldap']['ldap_dn'];
  $ldap_filter = $ini_config['ldap']['ldap_filter'];
  $ldap_bind = $ini_config['ldap']['ldap_bind'];
  
  $salt_api = $ini_config['api']['salt'];
  
  //--------------------------------------------------------------------------
  // PARÀMETRES D'ACCÉS A REYES
  //--------------------------------------------------------------------------
  $reyes_url = $ini_config['reyes']['reyes_url'];
  $reyes_cert_path = $ini_config['reyes']['reyes_cert_path'];
  $reyes_url_pass = $ini_config['reyes']['reyes_pass'];
  $reyes_bearer = $ini_config['reyes']['reyes_bearer'];
  
?>
