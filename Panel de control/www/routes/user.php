<?php

  // Llistar tots els usuaris, juntament amb els moduls assignats i un boto de visualitzar, editar i eliminar.
  function draw_users_list()
  {
    $users_list = $GLOBALS['user']->list_user(1, 'ASC');

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Users").' ('.count($users_list).')</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped tablelist">
                  <thead>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("E-mail").'</th>
                    <th>'._("Roles").'</th>
                    <th>'._("Options").'</th>
                  </tr>
                  </thead>
                  <tbody>';

      foreach ($users_list as $username => $user_info)
      {
        $html_roles = '<ul>';
        foreach ($user_info['roles'] as $role_name) {
          $html_roles .= '<li>'.$role_name.'</li>';
        }
	$html_roles .= '</ul>';
        echo '<tr>
                <td>'.$username.'</td>
                <td>'.$user_info['email'].'</td>
                <td>'.$html_roles.'</td>
                <td class="text-right">
                  ';
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/users/delete/')){
           echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/users/delete/'.$username.'" class="btn btn-block btn-danger btn-sm"><i class="fas fa-trash"></i> '._("Delete").'</a>
                  </div>';
        }else{
           echo '  <div class="btn-group">
                    <a href="" class="btn btn-block btn-danger disabled btn-sm"><i class="fas fa-trash"></i> '._("Delete").'</a>
                  </div>';
        }
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/users/edit/')){
           echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/users/edit/'.$username.'" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
                  </div>';
        }else{
           echo '  <div class="btn-group">
                    <a href="" class="btn btn-block btn-primary disabled btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
                  </div>';
        }
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/users/view/')){
           echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/users/view/'.$username.'" class="btn btn-block btn-info btn-sm"><i class="fas fa-eye"></i> '._("View").'</a>
                  </div>';
        }else{
           echo '  <div class="btn-group">
                    <a href="" class="btn btn-block btn-info disabled btn-sm"><i class="fas fa-eye-slash"></i> '._("View").'</a>
                  </div>';
        }
        echo '  </td>
              </tr>';
      }

      echo '      </tbody>
                  <tfoot>
                    <tr>
                      <th>'._("Name").'</th>
                      <th>'._("E-mail").'</th>
                      <th>'._("Roles").'</th>
                      <th>'._("Options").'</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->';
  }

  // Panell de creacio d'usuaris
  function draw_user_create_form($response=NULL)
  {
	if(count($GLOBALS['available_languages']) > 1)
	{
		$options = '';
		foreach($GLOBALS['available_languages'] as $lang_code)
		{
			if(!empty($response['user_language']))
			{
			  $options .= '<option value="'.$lang_code.'" '.($response['user_language']==$lang_code ? 'selected="selected"' : '').'>'.$lang_code.'</option>';
			}
			else
			{
			  $options .= '<option value="'.$lang_code.'">'.$lang_code.'</option>';
			}
		}
		$select_language = '<label for="user_language">'._("Language").'</label>
				   <select id="user_language" name="user_language" size="1" class="custom-select">'.$options.'</select>';
	}
	else{
		$select_language = '<input type="hidden" name="user_language" value="'.$GLOBALS['available_languages'][0].'" />';
	}

    if(!empty($response['user_auth']))
    {
      $auth_options = '<option value="local" '.($response['user_auth']=='local' ? 'selected="selected"' : '').'>Local</option>
                        <option value="ldap" '.($response['user_auth']=='ldap' ? 'selected="selected"' : '').'>LDAP</option>';
    }
    else
    {
      $auth_options = '<option value="local">Local</option>
                        <option value="ldap">LDAP</option>';
    }


    $select_auth = '<label for="user_auth">'._("Authentication type").'</label>
                       <select id="user_auth" name="user_auth" size="1" class="custom-select" onchange="disable_password()">'.$auth_options.'</select>';

    if(!empty($response['user_username']))
      $response['user_username'] = preg_replace("[^A-Za-z0-9_]", "", $response['user_username']);
    if(!empty($response['user_email']))
      $response['user_email'] = filter_var ($response['user_email'], FILTER_SANITIZE_EMAIL);

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("User").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/users/new" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="user_username">'._("Name").'</label>
                    <input type="text" class="form-control" id="user_username" name="user_username" '.(!empty($response['user_username']) ? 'value="'.$response['user_username'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="user_email">'._("E-mail").'</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" '.(!empty($response['user_email']) ? 'value="'.$response['user_email'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_auth.'</div>
                  <div class="form-group">'.$select_language.'</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary">'._("Create user").'</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->';
  }

  // Pintar el panell de visualitzacio d'un usuari
  function draw_user_view($username)
  {
    $user_info = $GLOBALS['user']->read_user($username);

    $response = Array(
                  'user_username' => $user_info['username'],
                  'user_email' => $user_info['email'],
                  'user_auth'=> ($user_info['password'] == '-' ? 'LDAP' : 'Local'),
                  'user_language' => $user_info['language'],
                  'user_create_time' => $user_info['create_time'],
                  'user_api_key' => preg_replace(sprintf('/%s/', $GLOBALS['salt_api']), '', base64_decode($user_info['api_key']))
    );

   if(count($GLOBALS['available_languages']) > 1){
	$options = '';
	foreach($GLOBALS['available_languages'] as $lang_code){
		if(!empty($response['user_language']))
		{
		  $options .= '<option value="'.$lang_code.'" '.($response['user_language']==$lang_code ? 'selected="selected"' : '').'>'.$lang_code.'</option>';
		}
		else
		{
		  $options .= '<option value="'.$lang_code.'">'.$lang_code.'</option>';
		}
	}
	$select_language = '<label for="user_language">'._("Language").'</label>
		  	     <select id="user_language" name="user_language" size="1" class="custom-select">'.$options.'</select>';
   } else{
	$select_language = '<input type="hidden" name="user_language" value="'.$lang_code[0].'" />';
   }

    if(!empty($response['user_username']))
      $response['user_username'] = preg_replace("[^A-Za-z0-9_]", "", $response['user_username']);
    if(!empty($response['user_email']))
      $response['user_email'] = filter_var ($response['user_email'], FILTER_SANITIZE_EMAIL);

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("User").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="user_username">'._("Name").'</label>
                    <input type="text" class="form-control" id="user_username" name="user_username" '.(!empty($response['user_username']) ? 'value="'.$response['user_username'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="user_email">'._("E-mail").'</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" '.(!empty($response['user_email']) ? 'value="'.$response['user_email'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="user_language">'._("Language").'</label>
                    <input type="text" class="form-control" id="user_language" name="user_language" '.(!empty($response['user_language']) ? 'value="'.$response['user_language'].'"' : '').' disabled="">
                  </div>
	          <div class="form-group">
                    <label for="user_language">'._("Creation Date").'</label>
                    <input type="text" class="form-control" id="user_create_time" name="user_create_time" '.(!empty($response['user_create_time']) ? 'value="'.$response['user_create_time'].'"' : '').' disabled="">
                  </div>
          	   <div class="form-group">
                    <label for="user_authentication">'._("Authentication").'</label>
                    <input type="text" class="form-control" id="user_authentication" name="user_authentication" '.(!empty($response['user_auth']) ? 'value="'.$response['user_auth'].'"' : '').' disabled="">
                  </div>
            	   <div class="form-group">
                    <label for="user_api_key">'._("API Key").'</label>
                    <input type="text" class="form-control" id="user_api_key" name="user_api_key" '.(!empty($response['user_api_key']) ? 'value="'.$response['user_api_key'].'"' : '').' disabled="">
                  </div>
                </div>
                <!-- /.card-body -->

              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
	  <div class="col-12">
	    <div class="card">
	      <div class="card-header">
		<h3 class="card-title">'._("Roles").'</h3>
	      </div>
	      <!-- /.card-header -->
	      <div class="card-body">
		<table class="table table-bordered table-striped">
		  <thead>
		    <tr>
		      <th>'._("Name").'</th>
		      <th>'._("Description").'</th>
		    </tr>
		  </thead>
		  <tbody>';

        
        $info = $GLOBALS['user']->list_user_roles($response['user_username']);
	foreach ($info as $role_info)
	    {
	      echo '        <tr>
		              <td>'.$role_info['name'].'</td>
		              <td>'.$role_info['description'].'</td>
		            </tr>';
	    }
	    
  echo '      </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>                
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->';
  }

  // Pintar el formulari d'edicio d'usuaris
  function draw_user_edit_form($username)
  {
    $user_info = $GLOBALS['user']->read_user($username);

    $response = Array(
                  'user_username' => $user_info['username'],
                  'user_email' => $user_info['email'],
                  'user_auth'=> ($user_info['password'] == '-' ? 'ldap' : 'local'),
                  'user_language' => $user_info['language'],
                  'api_key' => preg_replace(sprintf('/%s/', $GLOBALS['salt_api']), '', base64_decode($user_info['api_key']))
    );

   if(count($GLOBALS['available_languages']) > 1){
	$options = '';
	foreach($GLOBALS['available_languages'] as $lang_code){
		if(!empty($response['user_language']))
		{
		  $options .= '<option value="'.$lang_code.'" '.($response['user_language']==$lang_code ? 'selected="selected"' : '').'>'.$lang_code.'</option>';
		}
		else
		{
		  $options .= '<option value="'.$lang_code.'">'.$lang_code.'</option>';
		}
	}
	$select_language = '<label for="user_language">'._("Language").'</label>
		  	     <select id="user_language" name="user_language" size="1" class="custom-select">'.$options.'</select>';
   } else{
	$select_language = '<input type="hidden" name="user_language" value="'.$lang_code[0].'" />';
   }

    if(!empty($response['user_username']))
      $response['user_username'] = preg_replace("[^A-Za-z0-9_]", "", $response['user_username']);
    if(!empty($response['user_email']))
      $response['user_email'] = filter_var ($response['user_email'], FILTER_SANITIZE_EMAIL);

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("User").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="user_username">'._("Name").'</label>
                    <input type="text" class="form-control" id="user_username" name="user_username" '.(!empty($response['user_username']) ? 'value="'.$response['user_username'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="user_email">'._("E-mail").'</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" '.(!empty($response['user_email']) ? 'value="'.$response['user_email'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_language.'</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="usumod" value="yes">'._("Modify user").'</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->';

    $options_auth = '<option value="local" '.($response['user_auth']=='local' ? 'selected="selected"' : '').'>Local</option>
                        <option value="ldap" '.($response['user_auth']=='ldap' ? 'selected="selected"' : '').'>LDAP</option>';

    $select_auth = '<label for="user_auth">'._("Authentication type").'</label>
                         <select id="user_auth" name="user_auth" size="1" class="custom-select" disabled="">'.$options_auth.'</select>';

    if ($response['user_auth'] == 'local')
    {
      $html_buttons_auth = '<a class="btn btn-warning" href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?authswitch=ldap">'._("Change authentication to LDAP").'</a>
                            <a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?resetpwd=yes">'._("Reset password").'</a>';
    }
    else {
      $html_buttons_auth = '<a class="btn btn-warning" href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?authswitch=local">'._("Change authentication to LOCAL").'</a>';
    }

    echo '<div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Authentication").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'" method="GET">
                <div class="card-body">
                  <div class="form-group">'.$select_auth.'</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  '.$html_buttons_auth.'
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
	<div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("API Key").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'" method="GET">
                <div class="card-body">
                  <div class="form-group">
                      <input type="text" class="form-control" id="user_api_key" name="api_key" '.(!empty($response['api_key']) ? 'value="'.$response['api_key'].'"' : '').' disabled="">
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
			<a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?delete_api_key">'._("Delete API KEY").'</a>
			<a class="btn btn-primary" href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?reset_api_key">'._("Reset API KEY").'</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Roles").'</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>'._("Name").'</th>
                      <th>'._("Options").'</th>
                    </tr>
                  </thead>
                  <tbody>';
    $info = $GLOBALS['user']->list_user_roles($response['user_username']);
    foreach ($info as $role_info)
    {
      echo '        <tr>
                      <td>'.$role_info['name'].'</td>
                      <td class="text-right">
                        <div class="btn-group">
                          <a href="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'?perdel='.$role_info['role_id'].'" class="btn btn-block btn-danger btn-sm"><i class="fas fa-trash"></i> '._("Delete").'</a>
                        </div>
                      </td>
                    </tr>';
    }

    $roles = $GLOBALS['user']->list_user_roles_not_assigned($response['user_username']);
    $options = '';
    $es_root_admin = $GLOBALS['user']->user_has_role($_SESSION['_gestioioc'], 1);	//ROOT-ADMIN
    foreach($roles as $role_info)
    {
      if (($es_root_admin && $role_info['role_id']==1) || $role_info['role_id']!=1) //If it's not ROOT and the user that is viewing this screen has the ROOT role, then do not show it.
        $options .= '<option value="'.$role_info['role_id'].'">'.$role_info['name'].'</option>';
    }

    $html_roles_footer = '     <form action="'.$GLOBALS['url_base'].'/users/edit/'.$response['user_username'].'" method="GET">
                                    <div class="form-group">
                                      <label>'._("Add role").'</label>
                                      <select class="custom-select" name=peradd>
                                        <option value=""> - '._("Choose a role").' - </option>
                                        '.$options.'
                                      </select>
                                    </div>
                                    <div class="text-right">
                                      <button type="submit" class="btn btn-primary">'._("Add role").'</button>
                                    </div>
                                  </form>';


      echo '      </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                '.$html_roles_footer.'
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->';
  }

//-----------------------------------------------------------------------------
//ROUTE: /users
//-----------------------------------------------------------------------------
Route::add('/users',function(){
   
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">

      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /users/list
//-----------------------------------------------------------------------------
Route::add('/users/list',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content"> 
        <?php draw_users_list(); ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /users/view/
//-----------------------------------------------------------------------------
Route::add('/users/view/([a-zA-Z0-9]+)',function($username){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php draw_user_view($username);?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');




//-----------------------------------------------------------------------------
//ROUTE: /users/new (GET)
//-----------------------------------------------------------------------------
Route::add('/users/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php draw_user_create_form(); ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /users/new (POST)
//-----------------------------------------------------------------------------
Route::add('/users/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
          if(isset($_POST['user_auth']))
          {
            if($_POST['user_auth']=='local')
            {
              $letters = substr(str_shuffle(str_repeat('abcdefghijkmnpqrstuvwxyz',4)),0,6);
              $numbers = substr(str_shuffle(str_repeat('23456789',4)),0,6);
              $password = str_shuffle($letters.$numbers);
            }
            else
            {
              $password = '-';
            }
          }
	  $actionok = $GLOBALS['user']->create_user($_POST['user_username'], $_POST['user_email'], $password, $_POST['user_language']);
          if (!$actionok)
          {
            echo '<div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                    '._("User could not be created.").'
                  </div>';
            draw_user_create_form($_POST);
          }
          else
          {
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                    '._("User has been created successfully.").'
                  </div>';
            draw_user_edit_form($_POST['user_username']);
          }
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');

//-----------------------------------------------------------------------------
//ROUTE: /users/edit/ (GET)
//-----------------------------------------------------------------------------
Route::add('/users/edit/([a-zA-Z0-9]+)',function($username){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
          if(isset($_GET['resetpwd']))
          {
            $user_info = $GLOBALS['user']->read_user($username);
            //Checks if user LOCAL so it can reset the password.
            if ($user_info['password'] == '-')
            {
              $actionok = FALSE;
            }
            else {
              $actionok = $GLOBALS['user']->reset_user_password($username);
            }
            if (!$actionok)
            {
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("User's password could not be reset.").'
                    </div>';
            }
            else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("User's password has been reset successfully. The new password is:").$actionok.'
                    </div>';

            }
          }
          elseif(isset($_GET['authswitch']))
          {
            $user_info = $GLOBALS['user']->read_user($username);
            if($_GET['authswitch']=='ldap' && $user_info['password'] != '-')
            {
              $actionok = $GLOBALS['user']->change_user_auth_to_ldap($username);
              if (!$actionok)
              {
                echo '<div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                        '._("Unable to change authentication type to LDAP.").'
                      </div>';
              }
              else
              {
                echo '<div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                        '._("Authentication type has been successfully changed to LDAP.").'
                      </div>';

              }
            }
            elseif($_GET['authswitch']=='local' && $user_info['password'] == '-')
            {
              $actionok = $GLOBALS['user']->reset_user_password($username);
              if (!$actionok)
              {
                echo '<div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                        '._("Unable to change authentication type to LOCAL.").'
                      </div>';
              }
              else
              {
                echo '<div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                        '._("Authentication type has been successfully changed to LOCAL.").'
                      </div>';

              }
            }
          }
          elseif (isset($_GET['peradd']))
          {
            $actionok = $GLOBALS['user']->add_user_role($username, $_GET['peradd']);
            if (!$actionok)
            {
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Unable to add the role.").'
                    </div>';
            }
            else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("Role has been successfully added.").'
                    </div>';

            }
          }
          elseif (isset($_GET['perdel']))
          {
            $actionok = $GLOBALS['user']->delete_user_role($username, $_GET['perdel']);
            if (!$actionok)
            {
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Unable to remove the role.").'
                    </div>';
            }
            else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("Role has been successfully removed.").'
                    </div>';

            }
          } elseif (isset($_GET['reset_api_key'])){
          	$actionok = $GLOBALS['user']->reset_api_key($username);
          	
            	if (!$actionok)
            	{
              		echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Unable to reset the API Key.").'
                    </div>';
            	}
            	else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("User's API Key has been reseted successfully. The new API Key is:").$actionok.'
                    </div>';

            }
          } elseif (isset($_GET['delete_api_key'])){
            	$actionok = $GLOBALS['user']->delete_api_key($username);
          	
            	if (!$actionok)
            	{
              		echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Unable to delete the API Key.").'
                    </div>';
            	}
            	else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("User's API Key has been deleted successfully.").'
                    </div>';

            }
    }
          draw_user_edit_form($username);
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /users/edit/ (POST)
//-----------------------------------------------------------------------------
Route::add('/users/edit/([a-zA-Z0-9]+)',function($username){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php

        $actionok = $GLOBALS['user']->update_user($username,$_POST['user_email'], $_POST['user_language']);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to modify the user.").'
                </div>';
        }
        else
        {
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("User has been successfully modified.").'
                </div>';
        }

        draw_user_edit_form($username);
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');

//-----------------------------------------------------------------------------
//ROUTE: /users/delete/ (GET)
//-----------------------------------------------------------------------------
Route::add('/users/delete/([a-zA-Z0-9]+)',function($username){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        if($username == $_SESSION['_gestioioc'])
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("You can't delete yourself.").'
                </div>';
        }
        else
        {
          $user_info = $GLOBALS['user']->read_user($username);
          echo '<div class="card card-warning card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-trash"></i>
                      '._("Delete user").'
                    </h3>
                  </div>
                  <div class="card-body">
                    <div class="text-muted mt-3">
                      '.sprintf(_("Are you sure you want to remove user %s from the application?"),$username).'
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-right">
                    <form action="'.$GLOBALS['url_base'].'/users/delete/'.$username.'" method="POST">
                      <button type="submit" class="btn btn-success">'._("Delete").'</button>
                      <a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/users/list">'._("Cancel").'</a>
                    </form>
                  </div>
                  <!-- /.card-footer -->
                </div>
                <!-- /.card -->';
        }
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /users/delete/ (POST)
//-----------------------------------------------------------------------------
Route::add('/users/delete/([a-zA-Z0-9]+)',function($username){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        $actionok = $GLOBALS['user']->delete_user($username);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to remove the user.").'
                </div>';
        }
        else
        {
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("User has been successfully removed.").'
                </div>';
        }

        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');


?> 
