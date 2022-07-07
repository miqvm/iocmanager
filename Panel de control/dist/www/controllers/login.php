<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Gestio IoC</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
        <!-- TO DO -->
    <a href=""></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo _("Enter your username and password"); ?></p>
      
      <form action="<?php echo $GLOBALS['url_base']; ?>/" method="post">
        <div class="input-group mb-3"> 
             <input type="text" class="form-control" placeholder="<?php echo _("Username"); ?>" name="_gestioioc_username">        
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="<?php echo _("Password"); ?>" name="_gestioioc_password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col-8">

            </div>
            <!-- /.col -->
            <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block"><?php echo _("Login"); ?></button>
            </div>
            <!-- /.col -->
        </div>
      </form>
      <?php
            if(isset($mensaje)){
              echo '<br><div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-ban"></i> '._("Error").'</h5>
                      '.$mensaje.'
                    </div>';
            }
          ?>
    </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->
<!-- jQuery -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $GLOBALS['url_base']; ?>/dist/js/adminlte.min.js"></script>
</body>
</html>
