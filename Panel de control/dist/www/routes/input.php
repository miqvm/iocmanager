<?php

function draw_input_list($input_list)
  {
   
    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Input Devices").' ('.count($input_list).')</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped tablelist">
                  <thead>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("Description").'</th>
                    <th>'._("IP").'</th>
                    <th>'._("Method").'</th>
                    <th>'._("Options").'</th>
                  </tr>
                  </thead>
                  <tbody>';

      foreach ($input_list as $id_input => $input_info)
      {

	$method = $GLOBALS['input']->get_input_method($id_input);

	echo '<tr>
                <td>'.$input_info['name'].'</td>
                <td>'.$input_info['description'].'</td>
                <td>'.$input_info['ip'].'</td>
                <td>'.$method['method_name'].'</td>
                <td class="text-right">
                  ';


    
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/input/%/edit')){
          echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/input/'.$id_input.'/edit" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
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
                    <th>'._("IP").'</th>
                    <th>'._("Method").'</th>
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
  
  function draw_input_create_form($response=NULL)
  {		
  	
    	$methods = $GLOBALS['input']->get_all_input_methods();
    	
	if(count($methods) >= 1)
	{
		$options = '<option value="-1">'._("[Choose an Input Method]").'</option>';
		$div_global = '<div id="input_global">';
		foreach($methods as $method_id => $method_name)
		{
		    	$parameters = json_decode($GLOBALS['input']->get_method_parameters($method_id), true);
		    	
			$div_metodes .= '<div id="input_'.$method_id.'" '.($method_id == $_POST['id_method'] ? 'style="display:block"' : 'style="display:none"').'>'; 
			$div_parametres = '';
			foreach($parameters as $i=>$config)
			{
				$id = 'intput_'.$config["name"];
				$div_parametres.='<div id="" class="form-group">
						<label for="input_'.($config["name"]).'">'._($config["name"]).'</label>
						<input type="'.$config["type"].'" class="form-control" id="input_'.($method_id).'_'.($config["name"]).'" name="input_'.($config["name"]).'" '.(isset($response[$id]) ? 'value="'.$response[$id].'"' : '').'>
					</div>
				';
			}
			$div_metodes .= $div_parametres;
			$div_metodes .= '</div>';
		    	
			if(isset($_POST['id_method'])){
			  $options .= '<option value="'.$method_id.'" '.($_POST['id_method']==$method_id ? 'selected="selected"' : '').'>'.$method_name.'</option>';
			}else{
			  $options .= '<option value="'.$method_id.'">'.$method_name.'</option>';
			}
		}
		
		$select_method = '<label for="ioc_type">'._("Input Method").'</label>
				<select id="id_method" onchange="parameters_form(\'input\')" name="id_method" size="1" class="custom-select">'.$options.'</select>';
			
		$div_global .= $div_metodes;
		$div_global.='</div>';
	}else{
		$select_method = '<input type="hidden" name="id_method" value="unknown" />';
	}

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("New Input Device").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/input/new" method="POST" onsubmit="return(validate_input());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="input_name">'._("Input Device Name").'</label>
                    <input type="text" class="form-control" id="input_name" name="input_name" '.(isset($response['input_name']) ? 'value="'.$response['input_name'].'"' : '').'>
                  </div>                     
                  <div class="form-group">
                    <label for="input_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="input_description" name="input_description" '.(isset($response['input_description']) ? 'value="'.$response['input_description'].'"' : '').'>
                  </div>
                  
                  <div class="form-group">
                    <label for="input_ip">'._("IP").'</label>
                    <input type="text" class="form-control" id="input_ip" name="input_ip" '.(isset($response['input_ip']) ? 'value="'.$response['input_ip'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_method.'</div>   
                  '.$div_global.'                
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="newres" value="yes"><i class="fas fa-plus-circle"></i> '._("New Input Device").'</button>
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
  
    function draw_input_edit($id_input)
    {
	$input_info = $GLOBALS['input']->read_input($id_input);
	
	$response = Array(
		'input_name' => $input_info['name'],
		'description' => $input_info['description'],
		'ip' => $input_info['ip'],
		'id_method' => $input_info['id_method'],
		'parameters' => $input_info['parameters'],
	);

    	$methods = $GLOBALS['input']->get_all_input_methods();
	if(count($methods) >= 1)
	{
		$options = '';
		$options .= '<option value="-1">-</option>';
		$div_global = '<div id="input_global">';
		
		$old_param = json_decode($response["parameters"], true);
		foreach($methods as $id_method => $method_name)
		{
		    	$parameters = json_decode($GLOBALS['input']->get_method_parameters($id_method), true);
		    	$div_metodes .= '<div id="input_'.$id_method.'" '.($id_method == $response['id_method'] ? 'style="display:block"' : 'style="display:none"').' >'; 
			$div_parametres = '';
			
			foreach($parameters as $i=>$config)
			{
				$div_parametres.='<div id="" class="form-group">
						<label for="input_'.($config["name"]).'">'._($config["name"]).'</label>
						<input type="'.$config["type"].'" class="form-control" id="input_'.($id_method).'_'.($config["name"]).'" name="input_'.($config["name"]).'" '.($id_method == $response["id_method"] ? 'value="'.$old_param[$config["name"]].'"' : '').'>					
					</div>';			    	
			}
			$div_metodes .= $div_parametres;
			$div_metodes .= '</div>';
			
			
			if(isset($response['id_method'])){
			  $options .= '<option value="'.$id_method.'" '.($response['id_method']==$id_method ? 'selected="selected"' : '').'>'.$method_name.'</option>';
			}else{
			  $options .= '<option value="'.$id_method.'">'.$method_name.'</option>';
			}
		}
		$select_method = '<label for="input_method">'._("Input Method").'</label>
				<select id="id_method" onchange="parameters_form(\'input\')" name="input_method" size="1" class="custom-select">'.$options.'</select>';
				
		$div_global .= $div_metodes;
		$div_global.='</div>';
	}else{
		$select_method = '<input type="hidden" name="input_method" value="unknown" />';
	}

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Input Device").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/input/'.$id_input.'/edit" method="POST" onsubmit="return(validate_input());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="edit_input_name">'._("Input Device Name").'</label>
                    <input type="text" class="form-control" id="input_name" name="edit_input_name" '.(isset($response['input_name']) ? 'value="'.$response['input_name'].'"' : '').' disabled="">
                  </div>
                  
	          <div class="form-group">
                    <label for="edit_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="input_description" name="edit_description" '.(isset($response['description']) ? 'value="'.$response['description'].'"' : '').'>
                  </div>
  	          <div class="form-group">
                    <label for="edit_ip">'._("IP").'</label>
                    <input type="text" class="form-control" id="input_ip" name="edit_ip" '.(isset($response['ip']) ? 'value="'.$response['ip'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_method.'</div>                   
            	   '.$div_global.'                
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="inputmod" value="yes">'._("Modify Input Device").'</button>
                  <button type="submit" class="btn btn-danger" name="inputdel" value="yes">'._("Delete Input Device").'</button>
                </div>
              </form>
            </div>                
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
	</div>
      </div>
      <!-- /.container-fluid -->';
  }
//-----------------------------------------------------------------------------
//ROUTE: /input
//-----------------------------------------------------------------------------
Route::add('/input',function(){
   
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
//ROUTE: /input/list
//-----------------------------------------------------------------------------
Route::add('/input/list',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content"> 
        <?php
		$input_list = $GLOBALS['input']->list_input(1, 'ASC');
	        draw_input_list($input_list); 
  	?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /input/new (GET)
//-----------------------------------------------------------------------------
Route::add('/input/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
 	<?php draw_input_create_form(); ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /input/new (POST)
//-----------------------------------------------------------------------------
Route::add('/input/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	$id_input = $GLOBALS['input']->create_input($_POST);

        if (!$id_input){
            echo '<div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                    '._("Input Device could not be created.").'
                  </div>';
            
            draw_input_create_form($_POST);
        }else{
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                    '._("Input Device has been created successfully.").'
		  </div>';
            if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/input/%/edit')){
		draw_input_edit($id_input);
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
//ROUTE: /input/edit/ (GET)
//-----------------------------------------------------------------------------
Route::add('/input/([a-zA-Z0-9. ]+)/edit',function($id_input){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
      	<?php draw_input_edit($id_input); ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /input/edit/ (POST)
//-----------------------------------------------------------------------------
Route::add('/input/([a-zA-Z0-9. ]+)/edit',function($id_input){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	if(isset($_POST['inputmod'])){
		$actionok = $GLOBALS['input']->update_input($id_input, $_POST);
		if (!$actionok)
		{
		  echo '<div class="alert alert-warning alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
		          '._("Unable to modify the Input Device.").'
		        </div>';
		}
		else
		{
		  echo '<div class="alert alert-success alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
		          '._("Input Device has been successfully modified.").'
		        </div>';
		}		
	        draw_input_edit($id_input);
	} elseif(isset($_POST['inputdel']))
        {
          echo '<div class="card card-warning card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-trash"></i>
                      '._("Delete Input Device").'
                    </h3>
                  </div>
                  <div class="card-body">
                    <div class="text-muted mt-3">
                      '.sprintf(_("Are you sure you want to remove this Input Device with ID=%s from the application?"),$id_input).'
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-right">
                    <form action="'.$GLOBALS['url_base'].'/input/'.$id_input.'/delete" method="POST">
                      <button type="submit" class="btn btn-success">'._("Delete").'</button>
                      <a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/input/list">'._("Cancel").'</a>
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
//ROUTE: /input/delete/ (POST)
//-----------------------------------------------------------------------------
Route::add('/input/([a-zA-Z0-9. ]+)/delete',function($id_input){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        $actionok = $GLOBALS['input']->delete_input($id_input);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to remove the Input Device.").'
                </div>';
        }
        else
        {
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("Input Device has been successfully removed.").'
                </div>';
        }
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?>
