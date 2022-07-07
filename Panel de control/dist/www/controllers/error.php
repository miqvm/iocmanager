<!DOCTYPE html>
<html lang="<?php echo $_SESSION['_gestioioc_userlang']; ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gestio IoC</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/fontawesome-free/css/all.min.css">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/dist/css/adminlte.min.css">
  </head>
  <body class="login-page" style="min-height: 512.391px;">
    <section class="content">
      <div class="error-page">
        <h2 class="headline text-warning"> <?php echo $error_code; ?></h2>

        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> <?php echo $error_title; ?></h3>

          <p>
            <?php echo $error_msg; ?>
          </p>
          <a href="#" class="btn btn-primary btn-block" onclick="history.go(-1)"><?php echo _('Go back'); ?></a>
          <a href="<?php echo $GLOBALS['url_base']; ?>" class="btn btn-primary btn-block"><?php echo _('Home'); ?></a>
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
    </section>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/dist/js/adminlte.min.js"></script>

  </body>
</html>
