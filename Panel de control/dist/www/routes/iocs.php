<?php
function draw_iocs_list($iocs_list)
  {
  
    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("IoC").' ('.count($iocs_list).')</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-striped tablelist">
                  <thead>
                  <tr>
                    <th>'._("Name").'</th>
                    <th>'._("Type").'</th>
                    <th>'._("JSON Offence Level").'</th>
                    <th>'._("Quarantine End").'</th>
                    <th>'._("Monitoring End").'</th>
                    <th>'._("Options").'</th>
                  </tr>
                  </thead>
                  <tbody>';

      foreach ($iocs_list as $id_ioc => $ioc_info)
      {
	echo '<tr>
                <td>'.($ioc_info['type_id']==3 || $ioc_info['type_id']==5 ? $ioc_info['url'] : $ioc_info['ioc_name']).'</td>
		<td>'.($GLOBALS['ioc']->get_type_name($ioc_info['type_id'])).'</td>
                <td>'.$ioc_info['json_offence_level'].'</td>
                <td>'.$ioc_info['quarantine_end'].'</td>
                <td>'.$ioc_info['monitoring_end'].'</td>
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
                    <th>'._("JSON Offence Level").'</th>
                    <th>'._("Quarantine End").'</th>
                    <th>'._("Monitoring End").'</th>
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
  
  function draw_ioc_view($id_ioc)
  {
    $ioc_info = $GLOBALS['ioc']->read_ioc($id_ioc);

    $response = Array(
    	'name_ioc' => $ioc_info['name_ioc'],
	'first_seen' => $ioc_info['first_seen'],
	'last_seen'=> $ioc_info['last_seen'],
	'json_offence_level' => $ioc_info['json_offence_level'],
	'type_name' => ($GLOBALS['ioc']->get_type_name($ioc_info['type_id'])),
	'quarantine_end'=> $ioc_info['quarantine_end'],
	'monitoring_end'=> $ioc_info['monitoring_end']
    );

    if ($ioc_info['type_id']== 3 || $ioc_info['type_id']==5){
	$response['name_ioc'] = $ioc_info['url'];
    }
    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Compromise").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/view" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="ioc_name">'._("IoC Name").'</label>
                    <input type="text" class="form-control" id="ioc_name" name="ioc_name" '.(isset($response['name_ioc']) ? 'value="'.$response['name_ioc'].'"' : '').' disabled="">
                  </div>
            	   <div class="form-group">
                    <label for="ioc_type">'._("IoC Type").'</label>
                    <input type="text" class="form-control" id="ioc_type" name="ioc_type" '.(isset($response['type_name']) ? 'value="'.$response['type_name'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="ioc_first_seen">'._("First seen").'</label>
                    <input type="text" class="form-control" id="ioc_first_seen" name="ioc_first_seen" '.(isset($response['first_seen']) ? 'value="'.$response['first_seen'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="ioc_last_seen">'._("Last seen").'</label>
                    <input type="text" class="form-control" id="ioc_last_seen" name="ioc_last_seen" '.(isset($response['last_seen']) ? 'value="'.$response['last_seen'].'"' : '').' disabled="">
                  </div>
	          <div class="form-group">
                    <label for="ioc_jol">'._("JSON Offence Level").'</label>
                    <input type="text" class="form-control" id="ioc_jol" name="ioc_jol" '.(isset($response['json_offence_level']) ? 'value="'.$response['json_offence_level'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="ioc_quarantine_end">'._("Quarantine End").'</label>
                    <input type="text" class="form-control" id="ioc_quarantine_end" name="ioc_quarantine_end" '.(isset($response['quarantine_end']) ? 'value="'.$response['quarantine_end'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="ioc_monitoring_end">'._("Monitoring End").'</label>
                    <input type="text" class="form-control" id="ioc_monitoring_end" name="ioc_monitoring_end" '.(isset($response['monitoring_end']) ? 'value="'.$response['monitoring_end'].'"' : '').' disabled="">
                  </div>
                </div>
                <!-- /.card-body -->

              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>';
        
	if($response['type_name']=="ipv4" || $response['type_name']=="ipv6"){
		echo '
	      <div class="row">
		  <div class="col-12">
		    <div class="card">
		      <div class="card-header">
			<h3 class="card-title">'._("OSINT").'</h3>
		      </div>
		      <!-- /.card-header -->
		      <div class="card-body">
		        <table>
			  <thead>
			  	<ul>
				<li><a href="'.$GLOBALS['url_base'].'/reyes?ip='.$response['name_ioc'].'" class="load-reyes">REYES</a></li>
				<li><a href="https://www.ipinfo.io/'.$response['name_ioc'].'" target="_blank"> IPInfo  <i class="fas fa-link"></i></a></li>
				<li><a href="https://whois.domaintools.com/'.$response['name_ioc'].'" target="_blank"> Whois Lookup <i class="fas fa-link"></i></a></li>
				<li><a href="https://www.dshield.org/ipinfo.html?ip='.$response['name_ioc'].'" target="_blank"> DShield  <i class="fas fa-link"></i></a></li>
				<li><a href="https://www.ipvoid.com/scan/'.$response['name_ioc'].'" target="_blank"> IPVOID <i class="fas fa-link"></i></a></li>
				</ul>
			  </thead>
			</table>
		      </div>
		      <!-- /.card-body -->
		    </div>                
		    <!-- /.card -->
		  </div>
		  <!-- /.col -->
		</div>
		<!-- /.row -->  
	<div class="modal fade" id="modal-overlay">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">REYES</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="card-body">
                <div class="overlay">
                  <i class="fas fa-2x fa-sync fa-spin"></i>
	        </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

';
        }
        
        
echo '
        <div class="row">
	  <div class="col-12">
	    <div class="card">
	      <div class="card-header">
		<h3 class="card-title">'._("Reasons").'</h3>
	      </div>
	      <!-- /.card-header -->
	      <div class="card-body">
		<table class="table table-bordered table-striped tablelist">
		  <thead>
		    <tr>
		      <th>'._("Reason").'</th>
		      <th>'._("Source").'</th>
		      <th>'._("Direction").'</th>
		      <th>'._("Confidence").'</th>
		      <th>'._("Sin-Malos Source").'</th>
		      <th>'._("Reason Date").'</th>
                      <th>'._("Share Level").'</th>
      		      <th></th>
		    </tr>
		  </thead>
		  <tbody>';

          $info = $GLOBALS['ioc']->list_ioc_reason_short($id_ioc);

	foreach ($info as $reason_info)
	{
	      $sl_name = "";
	      foreach($GLOBALS['share_level'] as $share_name => $share_level){
	      	   if($share_level['index'] == $reason_info['share_level']){
			$sl_name = $share_name;
			break;
	      	   }
	      }
	      
	      $icon = "<i class='fas fa-circle' style='color:".$GLOBALS['share_level'][$sl_name]['rgb-code']."'></i>";
	
	      echo '        <tr '.($reason_info['disable'] == 1 ? 'class="disabled-reason"' : '').'>
		              <td>'.($reason_info['disable'] == 1 ? '<i class="fas fa-bell-slash btn btn-info" data-toggle="modal" data-target="#modal-'.$reason_info['id_reason'].'"></i>' : '').' ' .$reason_info['reason'].'</td>
			      <td>'.$reason_info['source'].'</td>
                             <td>'.$reason_info['direction'].'</td>
			      <td>'.$reason_info['confidence'].'</td>
			      <td>'.$reason_info['sin_malos_source'].'</td>
			      <td>'.$reason_info['date'].'</td>
			      <td>'.$icon.'</td>
      		              <td class="text-right">';
	      if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/edit/reason/%')){
	      	 echo '  
	      		<div class="btn-group">
		        	<a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit/reason/'.$reason_info['id_reason'].'" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
		        </div>';
	      }elseif($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/view/reason/%')){
	      	 echo '
	      	 	<div class="btn-group">
		        	<a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/view/reason/'.$reason_info['id_reason'].'" class="btn btn-block btn-info btn-sm"><i class="fas fa-eye"></i> '._("View").'</a>
		        </div>';
	      } else{
	      	 echo '
	      	 	<div class="btn-group">
		        	<a href="" class="btn btn-block disabled btn-primary btn-sm"><i class="fas fa-eye-slash"></i> '._("Edit").'</a>
		        </div>';
	      }
              echo '</td>
	            </tr>
		      <div class="modal fade" id="modal-'.$reason_info['id_reason'].'">
			<div class="modal-dialog">
			  <div class="modal-content">
			    <div class="modal-header">
			      <h4 class="modal-title">'.$reason_info['reason'].'</h4>
			      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			      </button>
			    </div>
			    <div class="modal-body">
			      <p>'.$reason_info['disable_reason'].'</p>
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
	    
	$sh_options = '<option value="-1">[Choose a Share Level]</option>';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($_POST['share_level']==$sh_id){
		  $sh_options .= '<option value="'.$sh_selector["index"].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector["index"].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="reason_sh">'._("Share Level").'</label>
			<select id="ioc_reason_sl" name="reason_sh" size="1" class="custom-select">'.$sh_options.'</select>';
		  
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
  
  function draw_ioc_edit($id_ioc)
  {
  
	$ioc_info = $GLOBALS['ioc']->read_ioc($id_ioc);
	$response = Array(
		'name_ioc' => $ioc_info['name_ioc'],
		'first_seen' => $ioc_info['first_seen'],
		'last_seen'=> $ioc_info['last_seen'],
		'json_offence_level' => $ioc_info['json_offence_level'],
		'type_name' => ($GLOBALS['ioc']->get_type_name($ioc_info['type_id'])),
		'quarantine_end' => $ioc_info['quarantine_end'],
		'monitoring_end' => $ioc_info['monitoring_end'],
	);
   	if ($ioc_info['type_id']== 3 || $ioc_info['type_id']==5){
		$response['name_ioc'] = $ioc_info['url'];
	}
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
		$select_type = '<label for="ioc_type">'._("IoC Type").'</label>
				<select id="ioc_type" name="ioc_type" size="1" class="custom-select">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="ioc_type" value="unknown" />';
	}

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Compromise").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit" method="POST" onsubmit="return(validate_ioc());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="ioc_name">'._("IoC Name").'</label>
                    <input type="text" class="form-control" id="ioc_name" name="ioc_name" '.(isset($response['name_ioc']) ? 'value="'.$response['name_ioc'].'"' : '').' disabled="">
                  </div>
    	  	   <div class="form-group">'.$select_type.'</div> 
                  
                  <div class="form-group">
                    <label for="edit_first_seen">'._("First Seen").'</label>
                    <div class="input-group date" id="edit_first_seen" data-target-input="nearest">
                      <input type="text" name="ioc_first_seen" class="form-control datepicker-input" data-target="#edit_first_seen" '.(isset($response['first_seen']) ? 'value="'.$response['first_seen'].'"' : '').'/>
                      <div class="input-group-append" data-target="#edit_first_seen" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="edit_last_seen">'._("Last Seen").'</label>
                    <div class="input-group date" id="edit_last_seen" data-target-input="nearest">
                      <input type="text" name="ioc_last_seen" class="form-control datepicker-input" data-target="#edit_last_seen" '.(isset($response['last_seen']) ? 'value="'.$response['last_seen'].'"' : '').'/>
                      <div class="input-group-append" data-target="#edit_last_seen" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div> 
	          <div class="form-group">
                    <label for="ioc_jol">'._("JSON Offence Level").'</label>
                    <input type="text" class="form-control" id="ioc_jol" name="ioc_jol" '.(isset($response['json_offence_level']) ? 'value="'.$response['json_offence_level'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="edit_quarantine_end">'._("Quarantine End").'</label>
                    <div class="input-group date" id="edit_quarantine_end" data-target-input="nearest">
                      <input type="text" name="quarantine_end" class="form-control datepicker-input" data-target="#edit_quarantine_end" '.(isset($response['quarantine_end']) ? 'value="'.$response['quarantine_end'].'"' : '').'/>
                      <div class="input-group-append" data-target="#edit_quarantine_end" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div> 
                  <div class="form-group">
                    <label for="edit_monitoring_end">'._("Monitoring End").'</label>
                    <div class="input-group date" id="edit_monitoring_end" data-target-input="nearest">
                      <input type="text" name="monitoring_end" class="form-control datepicker-input" data-target="#edit_monitoring_end" '.(isset($response['monitoring_end']) ? 'value="'.$response['monitoring_end'].'"' : '').'/>
                      <div class="input-group-append" data-target="#edit_monitoring_end" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div> 
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="iocmod" value="yes">'._("Modify IoC").'</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row --> ';
  
	if($response['type_name']=="ipv4" || $response['type_name']=="ipv6"){
		echo '
	      <div class="row">
		  <div class="col-12">
		    <div class="card">
		      <div class="card-header">
			<h3 class="card-title">'._("OSINT").'</h3>
		      </div>
		      <!-- /.card-header -->
		      <div class="card-body">
		        <table>
			  <thead>
			  	<ul>
				<li><a href="'.$GLOBALS['url_base'].'/reyes?ip='.$response['name_ioc'].'" class="load-reyes">REYES</a></li>
				<li><a href="https://www.ipinfo.io/'.$response['name_ioc'].'" target="_blank"> IPInfo  <i class="fas fa-link"></i></a></li>
				<li><a href="https://whois.domaintools.com/'.$response['name_ioc'].'" target="_blank"> Whois Lookup <i class="fas fa-link"></i></a></li>
				<li><a href="https://www.dshield.org/ipinfo.html?ip='.$response['name_ioc'].'" target="_blank"> DShield  <i class="fas fa-link"></i></a></li>
				<li><a href="https://www.ipvoid.com/scan/'.$response['name_ioc'].'" target="_blank"> IPVOID <i class="fas fa-link"></i></a></li>
				</ul>
			  </thead>
			</table>
		      </div>
		      <!-- /.card-body -->
		    </div>                
		    <!-- /.card -->
		  </div>
		  <!-- /.col -->
		</div>
		<!-- /.row -->  

	<div class="modal fade" id="modal-overlay">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">REYES</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="card-body">
                <div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
              </div>
              </div>

              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


';
        }
        
        
      echo '
        <div class="row">
	  <div class="col-12">
	    <div class="card">
	      <div class="card-header">
		<h3 class="card-title">'._("Reasons").'</h3>
	      </div>
	      <!-- /.card-header -->
	      <div class="card-body">
		<table class="table table-bordered table-striped tablelist">
		  <thead>
		    <tr>
		      <th>'._("Reason").'</th>
		      <th>'._("Source").'</th>
		      <th>'._("Direction").'</th>
		      <th>'._("Confidence").'</th>
		      <th>'._("Sin-Malos Source").'</th>
		      <th>'._("Reason Date").'</th>
                      <th>'._("Share Level").'</th>
      		      <th></th>
		    </tr>
		  </thead>
		  <tbody>';

          $info = $GLOBALS['ioc']->list_ioc_reason_short($id_ioc);

	foreach ($info as $reason_info)
	{
	      $sl_name = "";
	      foreach($GLOBALS['share_level'] as $share_name => $share_level){
	      	   if($share_level['index'] == $reason_info['share_level']){
			$sl_name = $share_name;
			break;
	      	   }
	      }
	      
	      $icon = "<i class='fas fa-circle' style='color:".$GLOBALS['share_level'][$sl_name]['rgb-code']."'></i>";
	
	      echo '        <tr '.($reason_info['disable'] == 1 ? 'class="disabled-reason"' : '').'>
		              <td>'.($reason_info['disable'] == 1 ? '<i class="fas fa-bell-slash btn btn-info" data-toggle="modal" data-target="#modal-'.$reason_info['id_reason'].'"></i>' : '').' ' .$reason_info['reason'].'</td>
			      <td>'.$reason_info['source'].'</td>
                             <td>'.$reason_info['direction'].'</td>
			      <td>'.$reason_info['confidence'].'</td>
			      <td>'.$reason_info['sin_malos_source'].'</td>
			      <td>'.$reason_info['date'].'</td>
			      <td>'.$icon.'</td>
      		              <td class="text-right">';
	      if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/edit/reason/%')){
	      	 echo '  
	      		<div class="btn-group">
		        	<a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit/reason/'.$reason_info['id_reason'].'" class="btn btn-block btn-primary btn-sm"><i class="fas fa-edit"></i> '._("Edit").'</a>
		        </div>';
	      }elseif($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/view/reason/%')){
	      	 echo '
	      	 	<div class="btn-group">
		        	<a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/view/reason/'.$reason_info['id_reason'].'" class="btn btn-block btn-info btn-sm"><i class="fas fa-eye"></i> '._("View").'</a>
		        </div>';
	      } else{
	      	 echo '
	      	 	<div class="btn-group">
		        	<a href="" class="btn btn-block disabled btn-primary btn-sm"><i class="fas fa-eye-slash"></i> '._("Edit").'</a>
		        </div>';
	      }
              echo '</td>
	            </tr>
		      <div class="modal fade" id="modal-'.$reason_info['id_reason'].'">
			<div class="modal-dialog">
			  <div class="modal-content">
			    <div class="modal-header">
			      <h4 class="modal-title">'.$reason_info['reason'].'</h4>
			      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			      </button>
			    </div>
			    <div class="modal-body">
			      <p>'.$reason_info['disable_reason'].'</p>
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
	    
	$sh_options = '<option value="-1">[Choose a Share Level]</option>';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($_POST['share_level']==$sh_id){
		  $sh_options .= '<option value="'.$sh_selector["index"].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector["index"].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="reason_sh">'._("Share Level").'</label>
			<select id="ioc_reason_sl" name="reason_sh" size="1" class="custom-select">'.$sh_options.'</select>';
			
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
        <div class="row">
	  <div class="col-12">
	    <div class="card">
	      <div class="card-header">
		<h3 class="card-title">'._("New Reason").'</h3>
	      </div>
	      <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit" method="POST" onsubmit="return(validate_reason());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="n_reason_reason">'._("Reason").'</label>
                    <input type="text" class="form-control" id="ioc_reason_reason" name="n_reason_reason" '.(isset($_POST['n_reason_reason']) ? 'value="'.$_POST['n_reason_reason'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="n_reason_source">'._("Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_source" name="n_reason_source" '.(isset($_POST['n_reason_source']) ? 'value="'.$_POST['n_reason_source'].'"' : '').'>
                  </div>
		  <div class="form-group">
		    <label for="n_reason_direction">Direction</label>
		    <select id="ioc_reason_direction" name="n_reason_direction" size="1" class="custom-select">
		      <option value="-1">[Choose a direction]</option>
		      <option value="inbound" '.(isset($_POST['n_reason_direction']) && $_POST['n_reason_direction']=="inbound" ? 'selected="selected"' : '').'>Inbound</option>
		      <option value="outbound" '.(isset($_POST['n_reason_direction']) && $_POST['n_reason_direction']=="outbound" ? 'selected="selected"' : '').'>Outbound</option>
		      <option value="both" '.(isset($_POST['n_reason_direction']) && $_POST['n_reason_direction']=="both" ? 'selected="selected"' : '').'>Both</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="n_reason_confidence">'._("Confidence").'</label>
                    <input type="text" class="form-control" id="ioc_reason_confidence" name="n_reason_confidence" '.(isset($_POST['n_reason_confidence']) ? 'value="'.$_POST['n_reason_confidence'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="n_reason_sms">'._("Sin-Malos Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_sms" name="n_reason_sms" '.(isset($_POST['n_reason_sms']) ? 'value="'.$_POST['n_reason_sms'].'"' : '').'>
                  </div>              
                  
                  <div class="form-group">
                    <label for="n_reason_date">'._("Reason Date").'</label>
                    <div class="input-group date" id="n_reason_date" data-target-input="nearest">
                      <input type="text" name="n_reason_date" class="form-control datepicker-input" data-target="#n_reason_date" '.(isset($_POST['n_reason_date']) ? 'value="'.$_POST['n_reason_date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#n_reason_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
    	  	   <div class="form-group">'.$select_sh.'</div> 
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary" name="newres" value="yes"><i class="fas fa-plus-circle"></i> '._("New Reason").'</button>
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

  function draw_ioc_edit_reason($id_ioc,$id_reason)
  {
	$reason_info = $GLOBALS['ioc']->get_ioc_reason($id_reason);
	
	$sh_options = '';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($reason_info['share_level']==$sh_selector['index']){
		  $sh_options .= '<option value="'.$sh_selector['index'].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector['index'].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="reason_sh">'._("Share Level").'</label>
			<select id="ioc_reason_sl" name="reason_sh" size="1" class="custom-select">'.$sh_options.'</select>';

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Compromise").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit/reason/'.$id_reason.'" method="POST" onsubmit="return(validate_reason());">
                <div class="card-body">
                  <div class="form-group">
                    <label for="ioc_name">'._("IoC Name").'</label>
                    <input type="text" class="form-control" id="ioc_name" name="ioc_name" '.(isset($reason_info['name_ioc']) ? 'value="'.$reason_info['name_ioc'].'"' : '').' disabled="">
                  </div>

                  <div class="form-group">
                    <label for="reason_reason">'._("Reason").'</label>
                    <input type="text" class="form-control" id="ioc_reason_reason" name="reason_reason" '.(isset($reason_info['reason']) ? 'value="'.$reason_info['reason'].'"' : '').'>
                  </div>

		  <div class="form-group">
                    <label for="reason_source">'._("Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_source" name="reason_source" '.(isset($reason_info['source']) ? 'value="'.$reason_info['source'].'"' : '').'>
                  </div>
                  <div class="form-group">
		    <label for="reason_direction">Direction</label>
		    <select id="ioc_reason_direction" name="reason_direction" size="1" class="custom-select">
		      <option value="inbound" '.($reason_info['direction']=="inbound" ? 'selected="selected"' : '').'>Inbound</option>
		      <option value="outbound" '.($reason_info['direction']=="outbound" ? 'selected="selected"' : '').'>Outbound</option>
		      <option value="both" '.($reason_info['direction']=="both" ? 'selected="selected"' : '').'>Both</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="reason_confidence">'._("Confidence").'</label>
                    <input type="text" class="form-control" id="ioc_reason_confidence" name="reason_confidence" '.(isset($reason_info['confidence']) ? 'value="'.$reason_info['confidence'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="reason_sms">'._("Sin-Malos Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_sms" name="reason_sms" '.(isset($reason_info['sin_malos_source']) ? 'value="'.$reason_info['sin_malos_source'].'"' : '').'>
                  </div>

                  <div class="form-group">
                    <label for="reason_date">'._("Reason Date").'</label>
                    <div class="input-group date" id="reason_date" data-target-input="nearest">
                      <input type="text" name="reason_date" class="form-control datepicker-input" data-target="#reason_date" '.(isset($reason_info['date']) ? 'value="'.$reason_info['date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#reason_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
      	  	   <div class="form-group">'.$select_sh.'</div> 
    	          <div class="form-group">
                    <label for="reason_dis">'._("Disable").'</label>
                    <input type="checkbox" id="reason_dis" onclick=disable_reason() name="reason_dis" '.($reason_info['disable']=="1" ? 'checked="checked"' : '').'>
                  </div>                    
                  <div class="form-group">
                    <label for="reason_dis_r">'._("Disable Reason").'</label>		      
                    <input type="text" class="form-control" id="reason_dis_r" name="reason_dis_r" '.($reason_info['disable']=="1" ? 'value="'.$reason_info['disable_reason'].'"' : 'disabled').'>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit" class="btn btn-danger">'._("Back to IoC").'</a>
                  <button type="submit" class="btn btn-primary" name="resmod" value="yes">'._("Modify Reason").'</button>
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
  
  
   function draw_ioc_view_reason($id_ioc,$id_reason)
  {
	$reason_info = $GLOBALS['ioc']->get_ioc_reason($id_reason);

        $sl_name = "";
	      foreach($GLOBALS['share_level'] as $share_name => $share_level){
	      	   if($share_level['index'] == $reason_info['share_level']){
			$sl_name = $share_name;
			break;
	      	   }
	      }
	
	$sh_options .= '<option>'.$sl_name.'</option>';

	$select_sh = '<label for="reason_sh">'._("IoC Type").'</label>
			<select id="reason_sh" name="reason_sh" size="1" class="custom-select" disabled="">'.$sh_options.'</select>';
	

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Compromise").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="" method="">
                <div class="card-body">
                  <div class="form-group">
                    <label for="ioc_name">'._("IoC Name").'</label>
                    <input type="text" class="form-control" id="ioc_name" name="ioc_name" '.(isset($reason_info['name_ioc']) ? 'value="'.$reason_info['name_ioc'].'"' : '').' disabled="">
                  </div>

                  <div class="form-group">
                    <label for="reason_reason">'._("Reason").'</label>
                    <input type="text" class="form-control" id="reason_reason" name="reason_reason" '.(isset($reason_info['reason']) ? 'value="'.$reason_info['reason'].'"' : '').' disabled="">
                  </div>

		  <div class="form-group">
                    <label for="reason_source">'._("Source").'</label>
                    <input type="text" class="form-control" id="reason_source" name="reason_source" '.(isset($reason_info['source']) ? 'value="'.$reason_info['source'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
		    <label for="reason_direction">Direction</label>
		    <select id="reason_direction" name="reason_direction" size="1" class="custom-select" disabled="">
		      <option value="inbound" '.($reason_info['direction']=="inbound" ? 'selected="selected"' : '').'>Inbound</option>
		      <option value="outbound" '.($reason_info['direction']=="outbound" ? 'selected="selected"' : '').'>Outbound</option>
		      <option value="both" '.($reason_info['direction']=="both" ? 'selected="selected"' : '').'>Both</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="reason_confidence">'._("Confidence").'</label>
                    <input type="text" class="form-control" id="reason_confidence" name="reason_confidence" '.(isset($reason_info['confidence']) ? 'value="'.$reason_info['confidence'].'"' : '').' disabled="">
                  </div>
                  <div class="form-group">
                    <label for="reason_sms">'._("Sin-Malos Source").'</label>
                    <input type="text" class="form-control" id="reason_sms" name="reason_sms" '.(isset($reason_info['sin_malos_source']) ? 'value="'.$reason_info['sin_malos_source'].'"' : '').' disabled="">
                  </div>
                                   
                  <div class="form-group">
                    <label for="view_reason_date">'._("Reason Date").'</label>
                    <div class="input-group date" id="view_reason_date" data-target-input="nearest">
                      <input type="text" name="view_reason_date" disabled="" class="form-control datepicker-input" data-target="#view_reason_date" '.(isset($reason_info['date']) ? 'value="'.$reason_info['date'].'"' : '').' disabled=""/>
                      <div class="input-group-append" data-target="#view_reason_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>

  	          <div class="form-group">
                    '.$select_sh.'
                  </div>     
    	          <div class="form-group">
                    <label for="reason_dis">'._("Disable").'</label>
                    <input type="checkbox" id="reason_dis" name="reason_dis" '.($reason_info['disable']=="1" ? 'checked="checked"' : '').' disabled="">
                  </div>                    
                  <div class="form-group">
                    <label for="reason_dis_r">'._("Disable Reason").'</label>		      
                    <input type="text" class="form-control" id="reason_dis_r" name="reason_dis_r" '.($reason_info['disable']=="1" ? 'value="'.$reason_info['disable_reason'].'"' : 'disabled').' disabled="">
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <a href="'.$GLOBALS['url_base'].'/ioc/'.$id_ioc.'/edit" class="btn btn-danger">'._("Back to IoC").'</a>
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
  

  function draw_ioc_create_form()
  {
 
	$type_names = $GLOBALS['ioc']->get_all_type_name();
	if(count($type_names) > 1)
	{
		$options = '<option value="-1">'._("[Select the IoC Type]").'</option>';
		foreach($type_names as $type_id => $type_name)
		{
			if(isset($_POST['ioc_type'])){
			  $options .= '<option value="'.$type_id.'" '.($_POST['ioc_type']==$type_id ? 'selected="selected"' : '').'>'.$type_name.'</option>';
			}else{
			  $options .= '<option value="'.$type_id.'">'.$type_name.'</option>';
			}
		}
		
		$select_type = '<label for="ioc_type">'._("IoC Type").'</label>
				<select id="ioc_type" name="ioc_type" size="1" class="custom-select">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="ioc_type" value="unknown" />';
	}
	
	$sh_options = '<option value="-1">'._("[Choose the Share Level]").'</option>';
	foreach($GLOBALS['share_level'] as $share_level_name => $sh_selector)
	{
		if($_POST['reason_sh']==$sh_id){
		  $sh_options .= '<option value="'.$sh_selector['index'].'" selected="selected">'.$share_level_name.'</option>';
		}else{
		  $sh_options .= '<option value="'.$sh_selector['index'].'">'.$share_level_name.'</option>';
		}
	}
	$select_sh = '<label for="reason_sh">'._("Share Level").'</label>
			<select id="ioc_reason_sl" name="reason_sh" size="1" class="custom-select">'.$sh_options.'</select>';
			


    echo '
      <div class="container-fluid">
       <form role="form" action="'.$GLOBALS['url_base'].'/ioc/new" method="POST" onsubmit="return(validate_new_ioc());">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Indicator of Compromise").'</h3>
              </div>
              <!-- /.card-header -->
                <div class="card-body">
                
                  <div class="form-group">
                    <label for="ioc_name">'._("Indicator of compromise").'</label>
                    <input type="text" class="form-control" id="ioc_name" name="ioc_name" '.(isset($_POST['ioc_name']) ? 'value="'.$_POST['ioc_name'].'"' : '').'>
                  </div>   
            	   <div class="form-group">'.$select_type.'</div>                   
                   <div class="form-group">
                    <label for="ioc_first_seen">'._("First Seen").'</label>
                    <div class="input-group date" id="ioc_first_seen" data-target-input="nearest">
                      <input type="text" name="ioc_first_seen" class="form-control datepicker-input" data-target="#ioc_first_seen" '.(isset($_POST['ioc_first_seen']) ? 'value="'.$_POST['ioc_first_seen'].'"' : '').'/>
                      <div class="input-group-append" data-target="#ioc_first_seen" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="ioc_last_seen">'._("Last Seen").'</label>
                    <div class="input-group date" id="ioc_last_seen" data-target-input="nearest">
                      <input type="text" name="ioc_last_seen" class="form-control datepicker-input" data-target="#ioc_last_seen" '.(isset($_POST['ioc_last_seen']) ? 'value="'.$_POST['ioc_last_seen'].'"' : '').'/>
                      <div class="input-group-append" data-target="#ioc_last_seen" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
               
                  <div class="form-group">
                    <label for="ioc_jol">'._("JSON Offence Level").'</label>
                    <input type="number" class="form-control" id="ioc_jol" name="ioc_jol" '.(isset($_POST['ioc_jol']) ? 'value="'.$_POST['ioc_jol'].'"' : '').'>
                  </div>   
                  
                  <div class="form-group">
                    <label for="ioc_quarantine_end">'._("Quarantine End").'</label>
                    <div class="input-group date" id="ioc_quarantine_end" data-target-input="nearest">
                      <input type="text" name="ioc_quarantine_end" class="form-control datepicker-input" data-target="#ioc_quarantine_end" '.(isset($_POST['ioc_quarantine_end']) ? 'value="'.$_POST['ioc_quarantine_end'].'"' : '').'/>
                      <div class="input-group-append" data-target="#ioc_quarantine_end" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="ioc_monitoring_end">'._("Monitoring End").'</label>
                    <div class="input-group date" id="ioc_monitoring_end" data-target-input="nearest">
                      <input type="text" name="ioc_monitoring_end" class="form-control datepicker-input" data-target="#ioc_monitoring_end" '.(isset($_POST['ioc_monitoring_end']) ? 'value="'.$_POST['ioc_monitoring_end'].'"' : '').'/>
                      <div class="input-group-append" data-target="#ioc_monitoring_end" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  
                </div>
                <!-- /.card-body -->
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
                <h3 class="card-title">'._("Reason").'</h3>
              </div>
              <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    <label for="ioc_reason_reason">'._("Reason").'</label>
                    <input type="text" class="form-control" id="ioc_reason_reason" name="ioc_reason_reason" '.(isset($_POST['ioc_reason_reason']) ? 'value="'.$_POST['ioc_reason_reason'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="ioc_reason_source">'._("Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_source" name="ioc_reason_source" '.(isset($_POST['ioc_reason_source']) ? 'value="'.$_POST['ioc_reason_source'].'"' : '').'>
                  </div>
		  <div class="form-group">
		    <label for="ioc_reason_direction">Direction</label>
		    <select id="ioc_reason_direction" name="ioc_reason_direction" size="1" class="custom-select">
    		      <option value="-1">'._("[Choose the Direction]").'</option>
		      <option value="inbound" '.($_POST['ioc_reason_direction']=="inbound" ? 'selected="selected"' : '').'>Inbound</option>
		      <option value="outbound" '.($_POST['ioc_reason_direction']=="outbound" ? 'selected="selected"' : '').'>Outbound</option>
		      <option value="both" '.($_POST['ioc_reason_direction']=="both" ? 'selected="selected"' : '').'>Both</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="ioc_reason_confidence">'._("Confidence").'</label>
                    <input type="number" class="form-control" id="ioc_reason_confidence" name="ioc_reason_confidence" '.(isset($_POST['ioc_reason_confidence']) ? 'value="'.$_POST['ioc_reason_confidence'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="ioc_reason_sms">'._("Sin-Malos Source").'</label>
                    <input type="text" class="form-control" id="ioc_reason_sms" name="ioc_reason_sms" '.(isset($_POST['ioc_reason_sms']) ? 'value="'.$_POST['ioc_reason_sms'].'"' : '').'>
                  </div>
                  <div class="form-group">
                    <label for="ioc_reason_date">'._("Reason Date").'</label>
                    <div class="input-group date" id="ioc_reason_date" data-target-input="nearest">
                      <input type="text" name="ioc_reason_date" class="form-control datepicker-input" data-target="#ioc_reason_date" '.(isset($_POST['ioc_reason_date']) ? 'value="'.$_POST['ioc_reason_date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#ioc_reason_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>  
		  <div class="form-group">'.$select_sh.'</div> 
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary">'._("Create IoC").'</button>
                </div>

            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
                      </form>        
      </div>
      <!-- /.container-fluid -->';
  }
  
  
  function draw_search_results($input)
  { 	
  	$result = $GLOBALS['ioc']->search_ioc($input);
  	draw_iocs_list($result);
  }
  
  
  
//-----------------------------------------------------------------------------
//ROUTE: /ioc
//-----------------------------------------------------------------------------
Route::add('/ioc',function(){
   
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
//ROUTE: /ioc/list
//-----------------------------------------------------------------------------
Route::add('/ioc/list',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content"> 
        <?php 
		$iocs_list = $GLOBALS['ioc']->list_iocs(1, 'ASC');
	        draw_iocs_list($iocs_list); 
        ?>
      </section>
    </div>
	<script src="<?php echo $GLOBALS['url_base']; ?>/docs/assets/js/test.js"></script>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /ioc/view/
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/view',function($id_ioc){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php draw_ioc_view($id_ioc);?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');




//-----------------------------------------------------------------------------
//ROUTE: /ioc/new (GET)
//-----------------------------------------------------------------------------
Route::add('/ioc/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php  draw_ioc_create_form(); ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /ioc/new (POST)
//-----------------------------------------------------------------------------
Route::add('/ioc/new',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php
	$id_ioc = $GLOBALS['ioc']->create_ioc($_POST);

        if (!$id_ioc){
            echo '<div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                    '._("IoC could not be created.").'
                  </div>';
            
            draw_ioc_create_form($_POST);
        }else{
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                    '._("IoC has been created successfully.").'
		  </div>';
            if($GLOBALS['user']->user_has_access_module($_SESSION['_gestioioc'], '/ioc/%/edit')){
		draw_ioc_edit($id_ioc);
	    }else {
                draw_ioc_view($id_ioc);
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
//ROUTE: /ioc/edit/ (GET)
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/edit',function($id_ioc){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php	draw_ioc_edit($id_ioc); ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');
},'get');

//-----------------------------------------------------------------------------
//ROUTE: /ioc/edit/ (POST)
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/edit',function($id_ioc){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
	if(isset($_POST['iocmod'])){
		$actionok = $GLOBALS['ioc']->update_ioc($id_ioc, $_POST);
		if (!$actionok)
		{
		  echo '<div class="alert alert-warning alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
		          '._("Unable to modify the IoC.").'
		        </div>';
		}
		else
		{
		  echo '<div class="alert alert-success alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
		          '._("IoC has been successfully modified.").'
		        </div>';
		}		
	} else{
		$actionok = $GLOBALS['ioc']->add_ioc_reason($id_ioc, $_POST);
		
		if (!$actionok)
		{
		  echo '<div class="alert alert-warning alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
		          '._("Unable to add a reason to the IoC.").'
		        </div>';
		}
		else
		{
		  echo '<div class="alert alert-success alert-dismissible">
		          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		          <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
		          '._("New reason has been successfully added.").'
		        </div>';
		}
        }
        draw_ioc_edit($id_ioc);

        ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');
},'post');

//-----------------------------------------------------------------------------
//ROUTE: /ioc/view reason/ (GET)
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/view/reason/([0-9]+)',function($id_ioc, $id_reason){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php');?>
      
      <section class="content">
	<?php	draw_ioc_view_reason($id_ioc, $id_reason); ?>
      </section>
    </div>
    <?php
    	include('www/controllers/footer.php');

},'get');



//-----------------------------------------------------------------------------
//ROUTE: /ioc/edit reason/ (GET)
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/edit/reason/([0-9]+)',function($id_ioc, $id_reason){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php');?>
      
      <section class="content">
	<?php	draw_ioc_edit_reason($id_ioc, $id_reason); ?>
      </section>
    </div>
    <?php
    	include('www/controllers/validator.php');
    	include('www/controllers/footer.php');

},'get');

//-----------------------------------------------------------------------------
//ROUTE: /ioc/edit reason/ (POST)
//-----------------------------------------------------------------------------
Route::add('/ioc/([a-zA-Z0-9. ]+)/edit/reason/([0-9]+)',function($id_ioc, $id_reason){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
        <?php
        $actionok = $GLOBALS['ioc']->update_ioc_reason($id_reason, $_POST);
        if (!$actionok)
        {
          echo '<div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> '._("ERROR").'</h5>
                  '._("Unable to modify the reason.").'
                </div>';
        }
        else
        {
          echo '<div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> '._("CORRECT").'</h5>
                  '._("Reason has been successfully modified.").'
                </div>';
        }

        draw_ioc_edit_reason($id_ioc, $id_reason);
        ?>
      </section>
    </div>
    <?php
	include('www/controllers/validator.php');
	include('www/controllers/footer.php');

},'post');


//-----------------------------------------------------------------------------
//ROUTE: /ioc/search (GET)
//-----------------------------------------------------------------------------
Route::add('/ioc/search',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php');?>
      <section class="content">
<?php     include('www/controllers/search.php'); ?>
      </section>
    </div>
    <?php
    	include('www/controllers/footer.php');

},'get');

//-----------------------------------------------------------------------------
//ROUTE: /ioc/search (POST)
//-----------------------------------------------------------------------------
Route::add('/ioc/search',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); ?>
      <section class="content">
	<?php include('www/controllers/search.php');
	  draw_search_results($_POST);
	?>
      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?> 
