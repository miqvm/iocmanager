<?php 
	$type_names = $GLOBALS['ioc']->get_all_type_name();
	if(count($type_names) > 1)
	{
		$options = '';
		$options .= '<option value="-1">All</option>'; 
		foreach($type_names as $type_id => $type_name)
		{
			if(!empty($_POST['search_ioc_type'])){
				  $options .= '<option value="'.$type_id.'" '.($_POST['search_ioc_type']==$type_id ? 'selected="selected"' : '').'>'.$type_name.'</option>';
			}else{

				  $options .= '<option value="'.$type_id.'">'.$type_name.'</option>';
			}	  
		}
		$select_type = '<label for="search_ioc_type">'._("IoC Type").'</label>
				<select id="search_ioc_type" name="search_ioc_type" size="1" class="custom-select">'.$options.'</select>';
		
	}else{
		$select_type = '<input type="hidden" name="search_ioc_type" value="-1" />';
	}

    if(isset($_POST['ioc_name'])){
      $_POST['ioc_name'] = preg_replace("[^A-Za-z0-9. _]", "", $_POST['ioc_name']);
    }
    
    

    echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Search an IoC").'</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="'.$GLOBALS['url_base'].'/ioc/search" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="search_ioc_name">'._("Indicator of context").'</label>
                    <input type="text" class="form-control" id="search_ioc_name" name="search_ioc_name" '.(isset($_POST['search_ioc_name']) ? 'value="'.$_POST['search_ioc_name'].'"' : '').'>
                  </div>   
                  
            	   <div class="form-group">'.$select_type.'</div> 
            	   
                  
                   <div class="form-group">
                    <label for="search_ioc_start_date">'._("Start Date").'</label>
                    <div class="input-group date" id="search_ioc_start_date" data-target-input="nearest">
                      <input type="text" name="search_ioc_start_date" class="form-control datepicker-input" data-target="#search_ioc_start_date" '.(isset($_POST['search_ioc_start_date']) ? 'value="'.$_POST['search_ioc_start_date'].'"' : '').'/>    
                      <div class="input-group-append" data-target="#search_ioc_start_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="search_ioc_end_date">'._("End Date").'</label>
                    <div class="input-group date" id="search_ioc_end_date" data-target-input="nearest">
                      <input type="text" name="search_ioc_end_date" class="form-control datepicker-input" data-target="#search_ioc_end_date" '.(isset($_POST['search_ioc_end_date']) ? 'value="'.$_POST['search_ioc_end_date'].'"' : '').'/>
                      <div class="input-group-append" data-target="#search_ioc_end_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="search_ioc_jol">'._("JSON Offence Level").'</label>
                    <input type="text" class="form-control" id="search_ioc_jof" name="search_ioc_jof" '.(isset($_POST['search_ioc_jof']) ? 'value="'.$_POST['search_ioc_jof'].'"' : '').'>
                  </div>   
                  
                </div>
                <!-- /.card-body -->
                        <div class="card-footer text-right">
          <button type="submit" class="btn btn-primary">'._("Search IoC").'</button>
        </div> 
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->';

?>
