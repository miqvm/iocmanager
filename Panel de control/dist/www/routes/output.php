<?php

function draw_output_list($output_list)
  {
   
    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Output Devices").' ('.count($output_list).')</h3>
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

      foreach ($output_list as $id_output => $output_info)
      {

	$method = $GLOBALS['output']->get_output_method($id_output);

	echo '<tr>
                <td>'.$output_info['name'].'</td>
                <td>'.$output_info['description'].'</td>
                <td>'.$output_info['ip'].'</td>
                <td>'.$method['method_name'].'</td>
                <td class="text-right">
                  ';


    
        if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/output/%/edit')){
          echo '  <div class="btn-group">
                    <a href="'.$GLOBALS['url_base'].'/output/'.$id_output.'/edit" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
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
  
  function draw_output_create_form($response=NULL)
  {		
  	
    	$methods = $GLOBALS['output']->get_all_output_methods();
    	
	if(count($methods) >= 1)
	{
		$options = '<option value="-1">-</option>';
		
		$div_global = '<div id="output_global">';
		foreach($methods as $method_id => $method_name)
		{
		    	$parameters = json_decode($GLOBALS['output']->get_method_parameters($method_id), true);

			$div_metodes .= '<div id="output_'.$method_id.'" '.($method_id == $_POST['id_method'] ? 'style="display:block"' : 'style="display:none"').'>'; 
			$div_parametres = '';
			foreach($parameters as $i=>$config)
			{
				$id = 'output_'.$config["name"];
				$div_parametres.='<div id="" class="form-group">
						<label for="output_'.($config["name"]).'">'._($config["name"]).'</label>
						<input type="'.$config["type"].'" class="form-control" id="output_'.($method_id).'_'.($config["name"]).'" name="output_'.($config["name"]).'" '.(isset($response[$id]) ? 'value="'.$response[$id].'"' : '').'>
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
	
		$select_method = '<label for="id_method">'._("Output Method").'</label>
				<select id="id_method" onchange="parameters_form(\'output\')" name="id_method" size="1" class="custom-select">'.$options.'</select>';
				
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
                <h3 class="card-title">'._("New Output Device").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/output/new" method="POST" onsubmit="return(validate_output());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="output_name">'._("Output Device Name").'</label>
                    <input type="text" class="form-control" id="output_name" name="output_name" '.(isset($response['output_name']) ? 'value="'.$response['output_name'].'"' : '').'>
                  </div>                     
                  <div class="form-group">
                    <label for="output_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="output_description" name="output_description" '.(isset($response['output_description']) ? 'value="'.$response['output_description'].'"' : '').'>
                  </div>
                  
                  <div class="form-group">
                    <label for="output_ip">'._("IP").'</label>
                    <input type="text" class="form-control" id="output_ip" name="output_ip" '.(isset($response['output_ip']) ? 'value="'.$response['output_ip'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_method.'</div>                   
                  '.$div_global.'
                  <div class="form-group">
                    <label for="output_message">'._("Message").'</label>
                    <i class="fas fa-info-circle" data-toggle="modal" data-target="#modal-info"></i>
                    <input type="text" class="form-control" id="output_message" name="output_message" '.(isset($response['output_message']) ? 'value="'.$response['output_message'].'"' : '').'>
                  </div>

                  

                </div>

                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="newres" value="yes"><i class="fas fa-plus-circle"></i> '._("New Output Device").'</button>
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

          echo '		      
     <div class="modal fade" id="modal-info">
	<div class="modal-dialog modal-xl">
	  <div class="modal-content">
	    <div class="modal-header">
	      <h4 class="modal-title">'._("Message Format Guide").'</h4>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	      </button>
	    </div>
	    <div class="modal-body">
	      <p>'._("Introduce the desired fields to be sended").'</p>
	      <p>'._("<em>Example: SIEMSYSLOG LOGIN SIEMSrcIP {ioc} SIEMSrcType {type_name} SIEMUser sinmalos-uib-add-siem-syslog END</em>").'</p>
	      <ul>
	        <li><b>{ioc}:</b> '._("Indicator of Contet").'</li>
	        <li><b>{first_seen}:</b> '._("First Seen date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{last_seen}:</b> '._("Last Seen date (yyyy-mm-dd HH:mm:ss)").'</b></li>
	        <li><b>{json_offence_level}:</b> '._("JSON Offence Level as integer").'</li>
	        <li><b>{type_name}:</b> '._("Which type the IoC is").'</li>
	        <li><b>{quarantine_end}:</b> '._("Quarantine end date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{monitoring_end}:</b> '._("Monitoring end date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{reason}:</b> '._("Reason cause for being detected").'</li>
	        <li><b>{source}:</b> '._("Input device who detected the IoC").'</li>
	        <li><b>{direction}:</b> '._("Direction from the received IoC (inbound/outbound/both)").'</li>
	        <li><b>{confidence}:</b> '._("Confidence level").'</li>
	        <li><b>{sin_malos_source}:</b> '._("Sin Malos source ").'</li>
	        <li><b>{reason_date}:</b> '._("Date of detection (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{share_level}:</b> '._("Based on TLP (Trafic Light Protocol)").'</li>
	        <li><b>{disable}:</b> '._("Disabled (Boolean value)").'</li>
	        <li><b>{disable_reason}:</b> '._("Disable reason").'</li>	        	        	        	        
	      </ul>
	    </div>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    </div>
	  </div>
	  <!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->';
        

  }
  
    function draw_output_edit($id_output)
    {
	$output_info = $GLOBALS['output']->read_output($id_output);
	
	$response = Array(
		'output_name' => $output_info['name'],
		'description' => $output_info['description'],
		'ip' => $output_info['ip'],
		'id_method' => $output_info['id_method'],
		'parameters' => $output_info['parameters'],
		'message' => $output_info['message'],
	);
   	
    	
    	$methods = $GLOBALS['output']->get_all_output_methods();
	if(count($methods) >= 1)
	{
		$options = '';
		$options .= '<option value="-1">-</option>';
		$div_global = '<div id="output_global">';

		$old_param = json_decode($response["parameters"], true);
		foreach($methods as $id_method => $method_name)
		{
		    	$parameters = json_decode($GLOBALS['output']->get_method_parameters($id_method), true);
		    	$div_metodes .= '<div id="output_'.$id_method.'" '.($id_method == $response['id_method'] ? 'style="display:block"' : 'style="display:none"').' >'; 
			$div_parametres = '';

			foreach($parameters as $i=>$config)
			{
				$div_parametres.='<div id="" class="form-group">
						<label for="output_'.($config["name"]).'">'._($config["name"]).'</label>
						<input type="'.$config["type"].'" class="form-control" id="output_'.($id_method).'_'.($config["name"]).'" name="output_'.($config["name"]).'" '.($id_method == $response["id_method"] ? 'value="'.$old_param[$config["name"]].'"' : '').'>					
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
		$select_method = '<label for="id_method">'._("Output Method").'</label>
				<select id="id_method" onchange="parameters_form(\'output\')" name="id_method" size="1" class="custom-select">'.$options.'</select>';

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
                <h3 class="card-title">'._("Output Device").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/output/'.$id_output.'/edit" method="POST" onsubmit="return(validate_output());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="edit_output_name">'._("Output Device Name").'</label>
                    <input type="text" class="form-control" id="output_name" name="edit_output_name" '.(isset($response['output_name']) ? 'value="'.$response['output_name'].'"' : '').' disabled="">
                  </div>
                  
	          <div class="form-group">
                    <label for="edit_description">'._("Description").'</label>
                    <input type="text" class="form-control" id="output_description" name="edit_description" '.(isset($response['description']) ? 'value="'.$response['description'].'"' : '').'>
                  </div>
  	          <div class="form-group">
                    <label for="edit_ip">'._("IP").'</label>
                    <input type="text" class="form-control" id="output_ip" name="edit_ip" '.(isset($response['ip']) ? 'value="'.$response['ip'].'"' : '').'>
                  </div>
                  <div class="form-group">'.$select_method.'</div>   
                  '.$div_global.'
                  <div class="form-group">
                    <label for="edit_message">'._("Message").'</label>
                    <i class="fas fa-info-circle" data-toggle="modal" data-target="#modal-info"></i>
                    <input type="text" class="form-control" id="output_message" name="edit_message" '.(isset($response['message']) ? 'value="'.$response['message'].'"' : '').'>
                  </div>         
                  </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="outputmod" value="yes">'._("Modify Output Device").'</button>
                  <button type="submit" class="btn btn-danger" name="outputdel" value="yes">'._("Delete Output Device").'</button>
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
      
        echo '		      
     <div class="modal fade" id="modal-info">
	<div class="modal-dialog modal-xl">
	  <div class="modal-content">
	    <div class="modal-header">
	      <h4 class="modal-title">'._("Message Format Guide").'</h4>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	      </button>
	    </div>
	    <div class="modal-body">
	      <p>'._("Introduce the desired fields to be sended").'</p>
	      <p>'._("<em>Example: SIEMSYSLOG LOGIN SIEMSrcIP {ioc} SIEMSrcType {type_name} SIEMUser sinmalos-uib-add-siem-syslog END</em>").'</p>
	      <ul>
	        <li><b>{ioc}:</b> '._("Indicator of Contet").'</li>
	        <li><b>{first_seen}:</b> '._("First Seen date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{last_seen}:</b> '._("Last Seen date (yyyy-mm-dd HH:mm:ss)").'</b></li>
	        <li><b>{json_offence_level}:</b> '._("JSON Offence Level as integer").'</li>
	        <li><b>{type_name}:</b> '._("Which type the IoC is").'</li>
	        <li><b>{quarantine_end}:</b> '._("Quarantine end date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{monitoring_end}:</b> '._("Monitoring end date (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{reason}:</b> '._("Reason cause for being detected").'</li>
	        <li><b>{source}:</b> '._("Input device who detected the IoC").'</li>
	        <li><b>{direction}:</b> '._("Direction from the received IoC (inbound/outbound/both)").'</li>
	        <li><b>{confidence}:</b> '._("Confidence level").'</li>
	        <li><b>{sin_malos_source}:</b> '._("Sin Malos source ").'</li>
	        <li><b>{reason_date}:</b> '._("Date of detection (yyyy-mm-dd HH:mm:ss)").'</li>
	        <li><b>{share_level}:</b> '._("Based on TLP (Trafic Light Protocol)").'</li>
	        <li><b>{disable}:</b> '._("Disabled (Boolean value)").'</li>
	        <li><b>{disable_reason}:</b> '._("Disable reason").'</li>	        	        	        	        
	      </ul>
	    </div>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    </div>
	  </div>
	  <!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->';
  }
//-----------------------------------------------------------------------------
//ROUTE: /output
//-----------------------------------------------------------------------------
Route::add('/output',function(){
   
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
//ROUTE: /output/list
//-----------------------------------------------------------------------------
Route::add('/output/list',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content"> 
        <?php
		$output_list = $GLOBALS['output']->list_output(1, 'ASC');
	        draw_output_list($output_list); 
  	?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /output/new (GET)
//-----------------------------------------------------------------------------
Route::add('/output/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
 	<?php draw_output_create_form(); ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /output/new (POST)
//-----------------------------------------------------------------------------
Route::add('/output/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	$id_output = $GLOBALS['output']->create_output($_POST);
        if (!$id_output){
            echo '<div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                    '._("Output Device could not be created.").'
                  </div>';
            
            draw_output_create_form($_POST);
        }else{
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                    '._("Output Device has been created successfully.").'
		  </div>';
            if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/output/%/edit')){
		draw_output_edit($id_output);
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
//ROUTE: /output/edit/ (GET)
//-----------------------------------------------------------------------------
Route::add('/output/([a-zA-Z0-9. ]+)/edit',function($id_output){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
      	<?php draw_output_edit($id_output); ?>
      </section>
    </div>
    <?php
    include('www/controllers/validator.php');
    include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /output/edit/ (POST)
//-----------------------------------------------------------------------------
Route::add('/output/([a-zA-Z0-9. ]+)/edit',function($id_output){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	if(isset($_POST['outputmod'])){
		$actionok = $GLOBALS['output']->update_output($id_output, $_POST);
		if (!$actionok)
		{
		  echo '<div class="alert alert-warning alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
		          '._("Unable to modify the Output Device.").'
		        </div>';
		}
		else
		{
		  echo '<div class="alert alert-success alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
		          '._("Output Device has been successfully modified.").'
		        </div>';
		}

	        draw_output_edit($id_output);
	} elseif(isset($_POST['outputdel']))
        {
          echo '<div class="card card-warning card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-trash"></i>
                      '._("Delete Output Device").'
                    </h3>
                  </div>
                  <div class="card-body">
                    <div class="text-muted mt-3">
                      '.sprintf(_("Are you sure you want to remove this Output Device  with ID=%s from the application?"),$id_output).'
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-right">
                    <form action="'.$GLOBALS['url_base'].'/output/'.$id_output.'/delete" method="POST">
                      <button type="submit" class="btn btn-success">'._("Delete").'</button>
                      <a class="btn btn-danger" href="'.$GLOBALS['url_base'].'/output/list">'._("Cancel").'</a>
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
//ROUTE: /output/delete/ (POST)
//-----------------------------------------------------------------------------
Route::add('/output/([a-zA-Z0-9. ]+)/delete',function($id_output){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        $actionok = $GLOBALS['output']->delete_output($id_output);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to remove the Output Device.").'
                </div>';
        }
        else
        {
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("Output Device has been successfully removed.").'
                </div>';
        }
        ?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?>
