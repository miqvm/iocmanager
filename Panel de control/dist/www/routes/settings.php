<?php

  function draw_settings()
  {
    $user_info = $GLOBALS['user']->read_user($_SESSION['_gestioioc']);
    echo '<div class="container-fluid">';
    // MY-SETTINGS: Language
    if(count($GLOBALS['available_languages']) > 1)
		{
			$options = '';
			foreach($GLOBALS['available_languages'] as $lang_code)
			{
        if(!empty($user_info['language']))
        {
          $options .= '<option value="'.$lang_code.'" '.($user_info['language']==$lang_code ? 'selected="selected"' : '').'>'.$lang_code.'</option>';
        }
        else
        {
          $options .= '<option value="'.$lang_code.'">'.$lang_code.'</option>';
        }
			}
      echo '
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">'._('Language').'</h3>
                </div>
                <!-- /.card-header -->
                <form role="form" action="'.$GLOBALS['url_base'].'/my-settings" method="POST">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="user_language">'._('Language').'</label>
                      <select id="user_language" name="user_language" size="1" class="custom-select">'.$options.'</select>
                    </div>
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary" name="user_mod" value="yes">'._('Modify').'</button>
                  </div>
                </form>
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->';
		}

    // MY-SETTINGS: Password
    if ($user_info['password'] == '-')
    {
      $html_auth = '<div class="card-body">
                      <div class="text-muted">'._("Your user is authenticated using the institutional authentication server. To change credentials, use the usual mechanism.").'</div>
                    </div>
                    <!-- /.card-body -->';
    }
    else
    {
      $html_auth = '<form role="form" action="'.$GLOBALS['url_base'].'/my-settings" method="POST">
                      <div class="card-body">
                        <div class="form-group">
                          <label for="pwdold">'._("Actual password").'</label>
                          <input type="password" class="form-control" id="pwdold" name="pwdold">
                        </div>
                        <div class="form-group">
                          <label for="pwdnew">'._("New password").'</label>
                          <input type="password" class="form-control" id="pwdnew" name="pwdnew">
                        </div>
                        <div class="form-group">
                          <label for="pwdrpt">'._("Confirm new password").'</label>
                          <input type="password" class="form-control" id="pwdrpt" name="pwdrpt">
                        </div>
                      </div>
                      <!-- /.card-body -->

                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary" name="pwdchg" value="yes">'._("Change password").'</button>
                      </div>
                    </form>';
    }

    echo '<div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Authentication").'</h3>
              </div>
              <!-- /.card-header -->
              '.$html_auth.'
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
//  END OF FUNCTIONS
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// ROUTES
//-----------------------------------------------------------------------------

//-----------------------------------------------------------------------------
//ROUTE: /my-settings (GET)
//-----------------------------------------------------------------------------
Route::add('/my-settings',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
          draw_settings();
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /my-settings (POST)
//-----------------------------------------------------------------------------
Route::add('/my-settings',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
          if(isset($_POST['user_language']))
          {
            $actionok = $GLOBALS['user']->change_user_language($_SESSION['_gestioioc'], $_POST['user_language']);
            if (!$actionok)
            {
              $_SESSION['_gestioioc_userlang'] = $_POST['user_language'];
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Could not change language.").'
                    </div>';
            }
            else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("The language has been changed successfully.").'
                    </div>';

            }
          }
          elseif(isset($_POST['pwdchg']))
          {
            $actionok = $GLOBALS['user']->change_user_password($_SESSION['_gestioioc'], $_POST['pwdold'], $_POST['pwdnew'], $_POST['pwdrpt']);
            if (!$actionok)
            {
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                      '._("Could not change password.").'
                    </div>';
            }
            else
            {
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                      '._("The password has been changed successfully.").'
                    </div>';

            }
          }


          draw_settings();
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?>
