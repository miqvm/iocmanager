<?php

// Mostra un llistat dels IOC que tenen mes de 2 Reasons les darreres 24 hores junt al nombre de Reasons totals
function NotBlockedLast24(){
	$list_notBlockedIoC = $GLOBALS['reports']->notBlockedIoC();
	echo '          
	<div class="row">
           <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("IOCs not blocked").' ('.count($list_notBlockedIoC).')</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped tablelist">
                  <thead>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("Type").'</th>
                    <th>'._("Total Reasons").'</th>
                    <th>'._("Options").'</th>
                  </tr>
                  </thead>
                  <tbody>';

      foreach ($list_notBlockedIoC as $id_ioc => $ioc_info)
      {

        echo '<tr>
                <td>'.$ioc_info['name'].'</td>
                <td>'.($GLOBALS['ioc']->get_type_name($ioc_info['type_id'])).'</td>
                <td>'.$ioc_info['total'].'</td>
                <td class="text-right">
                  ';
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/edit')){
           echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
                  </div>';
        } else if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/view')){
           echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/view" class="btn btn-block btn-info btn-sm"><i class="fas fa-eye"></i> '._("View").'</a>
                  </div>';
        } else{
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
                    <th>'._("Type").'</th>
                    <th>'._("Total Reasons").'</th>
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
        <!-- /.row -->';
}


//-----------------------------------------------------------------------------
//ROUTE: /reports/notblocked24h/ (POST)
//-----------------------------------------------------------------------------
Route::add('/reports/notblocked24h',function(){
  include('www/controllers/head.php');
  ?>
  <div class="content-wrapper">
    <?php include('www/controllers/content-header.php'); ?>
    <section class="content">
      <?php NotBlockedLast24(); ?>
    </section>
  </div>
  <?php
  include('www/controllers/footer.php');
},'get');
