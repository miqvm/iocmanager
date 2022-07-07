<!DOCTYPE html>
<html lang="<?php echo $_SESSION['_gestioioc_userlang']; ?>">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Gestio IoC</title>
  <style>
     tr.disabled-reason td {
  	text-decoration: line-through;
     }
  </style>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->

  <!-- daterange picker -->

  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['url_base']; ?>/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="">
    
  <!-- jQuery -->
  <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery/jquery.min.js"></script>
  <!-- LeaFlet -->
  <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
  
  </head>
  <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Notifications Dropdown Menu -->

          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-user"></i> <?php echo $_SESSION['_gestioioc']; ?> <i class="fas fa-angle-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <div class="dropdown-divider"></div>
              <a href="<?php echo $GLOBALS['url_base']; ?>/my-settings" class="dropdown-item"><i class="fas fa-cog"></i> <?php echo _('Settings'); ?></a>
                            <div class="dropdown-divider"></div>
              <a href="<?php echo $GLOBALS['url_base']; ?>/help" class="dropdown-item"><i class="fas fa-question"></i> <?php echo _('Help'); ?></a>
              <div class="dropdown-divider"></div>
              <a href="<?php echo $GLOBALS['url_base']; ?>/?logout=yes" class="dropdown-item"><i class="fas fa-power-off"></i> <?php echo _('Logout'); ?></a>
            </div>
          </li>
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
	<a href="<?php echo $GLOBALS['url_base']; ?>" class="brand-link">
          <img src="<?php echo $GLOBALS['url_base']; ?>/dist/img/AdminLTELogo.png" alt="Gestio IoC Logo" class="brand-image img-circle elevation-3"
               style="opacity: .8">
          <span class="brand-text font-weight-light">Gestio IoC</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column zz" data-widget="treeview" role="menu" data-accordion="false">     
               	
              <?php include('www/controllers/menu.php'); ?>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>
