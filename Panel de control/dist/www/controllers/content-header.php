<?php

  include_once('www/include/module.php');

  $module = new module($GLOBALS['bbdd']);

  $module_id = $module->get_module_by_url(str_replace($GLOBALS['url_base_path'], '', $_SERVER['REQUEST_URI']));
  $module_info = $module->read_module($module_id);

  $module_name = $module_info['name'];
  $breadcrumb = '<li class="breadcrumb-item active">'.$module_name.'</li>';
  while(!empty($module_info['parent_module_id']))
  {
    $module_info = $module->read_module($module_info['parent_module_id']);
    $breadcrumb = '<li class="breadcrumb-item"><a href="'.$GLOBALS['url_base'].$module_info['url'].'">'.$module_info['name'].'</a></li>'.$breadcrumb ;
  }

  if($module_info['url'] != '/')
  {
    $module_id = $module->get_module_by_url('/');
    $module_info = $module->read_module($module_id);
    $breadcrumb = '<li class="breadcrumb-item"><a href="'.$GLOBALS['url_base'].'/">'.$module_info['name'].'</a></li>'.$breadcrumb ;
  }

?>


<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><?php echo $module_name; ?></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <?php echo $breadcrumb; ?>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
