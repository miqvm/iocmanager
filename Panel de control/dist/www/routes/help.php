<?php

function draw_help(){
echo '
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">'._("Index").'</h3>
              </div>
              <div class="card-body">
                <ul>
			<li><a href="#new_method">Create new Input/Output Method</a></li>
			<li><a href="#api">API</a></li>
                  </ul>             
              </div>
              
              <div class="card-header">
              </div>
              <div class="card-body">
                <h2 id="new_method">'._("Create new Input/Output Method").'</h2>
                  <p>'._("To create new methods must be done from the database. There are two fields: name and parameters, both in VARCHAR format. The parameters must be written in JSON format in order to be able to create the dynamic form and to be able to call it by the output script.").'</p>
                  
                  <p>'._("To do this, the JSON will be an array of objects with the key-value pairs \"name\" and \"type\". The first one indicates what it refers to, while the second indicats which type will be the input in the HTML form. The following types are valid:").'</p> 
                  <ul>
			<li>color</li>
			<li>date</li>
			<li>datetime-local</li>
			<li>email</li>
			<li>month</li>
			<li>number</li>
			<li>password</li>
			<li>tel</li>
			<li>text</li>
			<li>time</li>
			<li>url</li>
			<li>week</li>
                  </ul>

		<p><b>'._("Example: </b><em>Method name: 'Syslog' // Parameters: '[{\"name\":\"Port\",\"type\":\"number\"},{\"name\":\"Facility\",\"type\":\"text\"}]'").'</em></p>
     
              <div class="card-header">
              </div>
		<h2 id="api">'._("API").'</h2>                  
                  <p>'._("").'</p> 
              </div>
            </div>
          </div>
        </div>
      </div>';
}

//-----------------------------------------------------------------------------
//ROUTE: /help (GET)
//-----------------------------------------------------------------------------
Route::add('/help',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); 
      	draw_help();
      ?>

      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'get');


//-----------------------------------------------------------------------------
//ROUTE: /help (POST)
//-----------------------------------------------------------------------------
Route::add('/help',function(){
    include('www/controllers/head.php');
    ?>
    <div class="content-wrapper">
      <?php include('www/controllers/content-header.php'); 
      	draw_help();
      ?>

      </section>
    </div>
    <?php
    include('www/controllers/footer.php');
},'post');
?>
