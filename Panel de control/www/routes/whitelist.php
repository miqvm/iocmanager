<?php
function draw_whitelist_list($whitelist_list)
  {
  
    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Whitelist").' ('.count($whitelist_list).')</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped tablelist">
                  <thead>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("Description").'</th>
                    <th>'._("Share Level").'</th>
                    <th>'._("Options").'</th>
                  </tr>
                  </thead>
                  <tbody>';

      foreach ($whitelist_list as $id_whitelist => $whitelist_info)
      {
      	      $sl_name = "";
	      foreach($GLOBALS['share_level'] as $share_name => $share_level){
	      	   if($share_level['index'] == $whitelist_info['share_level']){
			$sl_name = $share_name;
			break;
	      	   }
	      }
      	$icon = "<i class='fas fa-circle' style='color:".$GLOBALS['share_level'][$sl_name]['rgb-code']."'></i>";
	echo '<tr>
                <td>'.$whitelist_info['name'].'</td>
                <td>'.$whitelist_info['description'].'</td>
                <td>'.$icon.'</td>
                <td class="text-right">
                  ';

        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/whitelist/%/edit')){
          echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/whitelist/'.$id_whitelist.'/edit" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
                  </div>';
        } else if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/whitelist/%/view')){
          echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/whitelist/'.$id_whitelist.'/view" class="btn btn-block btn-info btn-sm"><i class="fas fa-eye"></i> '._("View").'</a>
                  </div>';
        }
        
        echo '  </td>
              </tr>';
      }

      echo '      </tbody>
                  <tfoot>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("Description").'</th>
                    <th>'._("Share Level").'</th>
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
  
   function draw_whitelist_create_form($response=NULL)
  {
 	
	$type_names = $GLOBALS['ioc']->get_all_type_name();
	if(count($type_names) > 1)
	{
		$options = '<option value="-1">'._("[Choose Whitelist Type]").'</option>';
		foreach($type_names as $type_id => $type_name)
		{
			if(isset($response['ioc_type'])){
			  $options .= '<option value="'.$type_id.'" '.($response['ioc_type']==$type_id ? 'selected="selected"' : '').'>'.$type_name.'</option>';
			}else{
			  $options .= '<option value="'.$type_id.'">'.$type_name.'</option>';
			}
		}
		
		$select_type = '<label for="ioc_type">'._("Whitelist Type").'</label>
				<select id="whitelist_type" name="ioc_type" size="1" class="custom-select">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="ioc_type" value="unknown" />';
	}
	
	$sh_options = '';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($_POST['whitelist_sl']==$sh_id){
		  $sh_options .= '<option value="'.$sh_selector['index'].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector['index'].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="whitelist_sl">'._("Share Level").'</label>
			<select id="whitelist_sl" name="whitelist_sl" size="1" class="custom-select">'.$sh_options.'</select>';

    if(isset($response['whitelist_name'])){
      $response['whitelist_name'] = preg_replace("[^A-Za-z0-9. _]", "", $response['whitelist_name']);
    }

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Whitelist").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/whitelist/new" method="POST" onsubmit="return(validate_whitelist());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="whitelist_name">'._("Whitelist Name").'</label>
                    <input type="text" class="form-control" id="whitelist_name" name="whitelist_name" '.(isset($response['whitelist_name']) ? 'value="'.$response['whitelist_name'].'"' : '').'>
                  </div>   
            	   <div class="form-group">'.$select_type.'</div> 
                    <div class="form-group">
                    <label for="whitelist_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="whitelist_description" name="whitelist_description" '.(isset($response['whitelist_description']) ? 'value="'.$response['whitelist_description'].'"' : '').'>
                  </div>
                  
                   <div class="form-group">
                    <label for="whitelist_date">'._("Date").'</label>
                    <div class="input-group date" id="whitelist_date" data-target-input="nearest">
                      <input type="text" name="whitelist_date" class="form-control datepicker-input" data-target="#whitelist_date" '.(isset($response['whitelist_date']) ? 'value="'.$response['whitelist_date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#whitelist_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>               
   		<div class="form-group">'.$select_sh.'</div> 
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="newres" value="yes"><i class="fas fa-plus-circle"></i> '._("New Whitelist Indicator").'</button>
                </div>
              </form>
            </div>     
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        </div>
        <!-- /.row -->';
  }
  
  
  function draw_whitelist_edit($id_whitelist)
  {
	$whitelist_info = $GLOBALS['whitelist']->read_whitelist($id_whitelist);

	$response = Array(
		'whitelist_name' => $whitelist_info['name'],
		'description' => $whitelist_info['description'],
		'date'=> $whitelist_info['date'],
		'share_level' => $whitelist_info['share_level'],
		'type_name' => ($GLOBALS['ioc']->get_type_name($whitelist_info['type_id'])),
	);
	
	$type_names = $GLOBALS['ioc']->get_all_type_name();
	if(count($type_names) > 1)
	{
		$options = '';
		foreach($type_names as $type_id => $type_name)
		{
			if(isset($response['type_name'])){
			  $options .= '<option value="'.$type_id.'" '.($response['type_name']==$type_name ? 'selected="selected"' : '').'>'.$type_name.'</option>';
			}else{
			  $options .= '<option value="'.$type_id.'">'.$type_name.'</option>';
			}
		}
		$select_type = '<label for="whitelist_type">'._("Whitelist Type").'</label>
				<select id="whitelist_type" name="whitelist_type" size="1" class="custom-select">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="whitelist_type" value="unknown" />';
	}
	
	$sh_options = '';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($response['share_level']==$sh_selector['index']){
		  $sh_options .= '<option value="'.$sh_selector['index'].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector['index'].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="whitelist_sl">'._("Share Level").'</label>
			<select id="whitelist_sl" name="whitelist_sl" size="1" class="custom-select">'.$sh_options.'</select>';

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Context").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/whitelist/'.$id_whitelist.'/edit" method="POST" onsubmit="return(validate_whitelist());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="whitelist_name">'._("Whitelist Name").'</label>
                    <input type="text" class="form-control" id="whitelist_name" name="whitelist_name" '.(isset($response['whitelist_name']) ? 'value="'.$response['whitelist_name'].'"' : '').' disabled="">
                  </div>
                  
	          <div class="form-group">
                    <label for="edit_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="whitelist_description" name="edit_description" '.(isset($response['description']) ? 'value="'.$response['description'].'"' : '').'>
                  </div>         
    	  	   <div class="form-group">'.$select_type.'</div> 
                  <div class="form-group">
                    <label for="edit_whitelist_date">'._("Date").'</label>
                    <div class="input-group date" id="edit_whitelist_date" data-target-input="nearest">
                      <input type="text" name="edit_whitelist_date" class="form-control datepicker-input" data-target="#edit_whitelist_date" '.(isset($response['date']) ? 'value="'.$response['date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#edit_whitelist_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
		<div class="form-group">'.$select_sh.'</div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="whitelistmod" value="yes">'._("Modify Whitelist").'</button>
                  <button type="submit" class="btn btn-danger" name="whitelistdel" value="yes">'._("Delete Whitelist").'</button>
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
  
  function draw_whitelist_view($id_whitelist)
  {
	$whitelist_info = $GLOBALS['whitelist']->read_whitelist($id_whitelist);

	$response = Array(
		'whitelist_name' => $whitelist_info['name'],
		'description' => $whitelist_info['description'],
		'date'=> $whitelist_info['date'],
		'share_level' => $whitelist_info['share_level'],
		'type_name' => ($GLOBALS['ioc']->get_type_name($whitelist_info['type_id'])),
	);
	
	$type_names = $GLOBALS['ioc']->get_all_type_name();
	if(count($type_names) > 1)
	{
		$options = '';
		foreach($type_names as $type_id => $type_name)
		{
			if(isset($response['type_name'])){
			  $options .= '<option value="'.$type_id.'" '.($response['type_name']==$type_name ? 'selected="selected"' : '').'>'.$type_name.'</option>';
			}else{
			  $options .= '<option value="'.$type_id.'">'.$type_name.'</option>';
			}
		}
		$select_type = '<label for="whitelist_type">'._("Whitelist Type").'</label>
				<select id="whitelist_type" name="whitelist_type" size="1" class="custom-select" disabled="">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="whitelist_type" value="unknown" disabled=""/>';
	}
	
	$sh_options = '';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($response['share_level']==$sh_selector['index']){
		  $sh_options .= '<option value="'.$sh_selector['index'].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector['index'].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="whitelist_sl">'._("Share Level").'</label>
			<select id="whitelist_sl" name="whitelist_sl" size="1" class="custom-select" disabled="">'.$sh_options.'</select>';

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Context").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/whitelist/'.$id_whitelist.'/edit" method="POST" onsubmit="return(validate_whitelist());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="whitelist_name">'._("Whitelist Name").'</label>
                    <input type="text" class="form-control" id="whitelist_name" name="whitelist_name" '.(isset($response['whitelist_name']) ? 'value="'.$response['whitelist_name'].'"' : '').' disabled="">
                  </div>
                  
	          <div class="form-group">
                    <label for="edit_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="whitelist_description" name="edit_description" '.(isset($response['description']) ? 'value="'.$response['description'].'"' : '').' disabled="">
                  </div>         
    	  	   <div class="form-group">'.$select_type.'</div> 
                  <div class="form-group">
                    <label for="edit_whitelist_date">'._("Date").'</label>
                    <div class="input-group date" id="edit_whitelist_date" data-target-input="nearest">
                      <input type="text" name="edit_whitelist_date" class="form-control datepicker-input" data-target="#edit_whitelist_date" '.(isset($response['date']) ? 'value="'.$response['date'].'"' : '').'/ disabled="">
                      <div class="input-group-append" data-target="#edit_whitelist_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
		<div class="form-group">'.$select_sh.'</div>
                <!-- /.card-body -->
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
  
  
  

//-----------------------------------------------------------------------------
//ROUTE: /whitelist
//-----------------------------------------------------------------------------
Route::add('/whitelist',function(){
   
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
//ROUTE: /whitelist/list
//-----------------------------------------------------------------------------
Route::add('/whitelist/list',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content"> 
        <?php
		$whitelist_list = $GLOBALS['whitelist']->list_whitelist(1, 'ASC');
	        draw_whitelist_list($whitelist_list); 
  	?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /whitelist/new (GET)
//-----------------------------------------------------------------------------
Route::add('/whitelist/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
		<?php draw_whitelist_create_form(); ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /whitelist/new (POST)
//-----------------------------------------------------------------------------
Route::add('/whitelist/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	$id_whitelist = $GLOBALS['whitelist']->create_whitelist($_POST);

        if (!$id_whitelist){
            echo '<div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                    '._("Whitelist Indicator could not be created.").'
                  </div>';
            
            draw_whitelist_create_form($_POST);
        }else{
            $GLOBALS['whitelist']->update_config();
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                    '._("Whitelist Indicator has been created successfully.").'
		  </div>';
            if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/whitelist/%/edit')){
		draw_whitelist_edit($id_whitelist);
	    }else {
                draw_whitelist_view($id_whitelist);
            }
        } 
       ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'post');

//-----------------------------------------------------------------------------
//ROUTE: /whitelist/edit/ (GET)
//-----------------------------------------------------------------------------
Route::add('/whitelist/([a-zA-Z0-9. ]+)/edit',function($id_whitelist){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
      	<?php draw_whitelist_edit($id_whitelist); ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /whitelist/view/ (GET)
//-----------------------------------------------------------------------------
Route::add('/whitelist/([a-zA-Z0-9. ]+)/view',function($id_whitelist){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
      	<?php draw_whitelist_view($id_whitelist); ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /whitelist/edit/ (POST)
//-----------------------------------------------------------------------------
Route::add('/whitelist/([a-zA-Z0-9. ]+)/edit',function($id_whitelist){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	if(isset($_POST['whitelistmod'])){
		$actionok = $GLOBALS['whitelist']->update_whitelist($id_whitelist, $_POST);
		if (!$actionok)
		{
		  echo '<div class="alert alert-warning alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
		          '._("Unable to modify the Whitelisted Indicator.").'
		        </div>';
		}
		else
		{
	          $GLOBALS['whitelist']->update_config();
		  echo '<div class="alert alert-success alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
		          '._("Whitelist Indicator has been successfully modified.").'
		        </div>';
		}		
	        draw_whitelist_edit($id_whitelist);
	} elseif(isset($_POST['whitelistdel']))
        {
          echo '<div class="card card-warning card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-trash"></i>
                      '._("Delete user").'
                    </h3>
                  </div>
                  <div class="card-body">
                    <div class="text-muted mt-3">
                      '.sprintf(_("Are you sure you want to remove Whitelist with ID=%s from the application?"),$id_whitelist).'
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-right">
                    <form action="'.$GLOBALS['url_base'].'/whitelist/'.$id_whitelist.'/delete" method="POST">
                      <button type="submit" class="btn btn-success">'._("Delete").'</button>
                      <a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/whitelist/list">'._("Cancel").'</a>
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
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'post');

//-----------------------------------------------------------------------------
//ROUTE: /whitelist/delete/ (POST)
//-----------------------------------------------------------------------------
Route::add('/whitelist/([a-zA-Z0-9. ]+)/delete',function($id_whitelist){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        $actionok = $GLOBALS['whitelist']->delete_whitelist($id_whitelist);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to remove the Whitelisted Indicator.").'
                </div>';
        }
        else
        {
          $GLOBALS['whitelist']->update_config();
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("Whitelisted Indicator has been successfully removed.").'
                </div>';
        }
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?> 

