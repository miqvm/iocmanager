<script>
     function validate_whitelist(){

	if(document.getElementById("whitelist_name").value == ""){
     		alert("Introduce a name");
     		document.getElementById("whitelist_name").focus();
     		return false;
     	}
	if(document.getElementById("whitelist_description").value == ""){
     		alert("You should introduce a description");
     		document.getElementById("whitelist_description").focus();
     		return false;
     	}
	if(document.getElementById("whitelist_type").value == "-1"){
     		alert("Introduce a valid type");
     		document.getElementById("whitelist_type").focus();
     		return false;
     	}
     
	var regexp = /^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])\s(0[0-2]|\d{2})\:(\d{2}))$/;
	if(!document.getElementById("whitelist_date").value == "" && !regexp.test(document.getElementById("whitelist_date").value)){
     		alert("Introduce a valid Date");
     		document.getElementById("whitelist_date").focus();
     		return false;
     	}
     	
     	return true;
     }
     
     
    function validate_input(){

	if(document.getElementById("input_name").value == ""){
     		alert("Introduce a valid name");
     		document.getElementById("input_name").focus();
     		return false;
     	}
     	
     	var regexp_ip = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/;
	if(document.getElementById("input_ip").value == "" || !regexp_ip.test(document.getElementById("input_ip").value)){
     		alert("Introduce a valid IP");
     		document.getElementById("input_ip").focus();
     		return false;
     	}
	if(document.getElementById("input_method").value == "-1"){
     		alert("Introduce an Input Method");
     		document.getElementById("input_method").focus();
     		return false;
     	}
     	
     	return true;
     }
     
    function validate_output(){

	if(document.getElementById("output_name").value == ""){
     		alert("Introduce a valid name");
     		document.getElementById("output_name").focus();
     		return false;
     	}
     	
     	var regexp_ip = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/;
	if(document.getElementById("output_ip").value == "" || !regexp_ip.test(document.getElementById("output_ip").value)){
     		alert("Introduce a valid IP");
     		document.getElementById("output_ip").focus();
     		return false;
     	}
	if(document.getElementById("id_method").value == "-1"){
     		alert("Introduce an Output Method");
     		document.getElementById("id_method").focus();
     		return false;
     	}
	
     	switch(document.getElementById("id_method").value){

     	<?php 
	     	$methods = $GLOBALS['output']->get_all_output_methods();
		foreach($methods as $id_method => $method_name)
		{
			echo 'case "'.$id_method.'":';
		    	$parameters = json_decode($GLOBALS['output']->get_method_parameters($id_method), true);
			foreach($parameters as $i=>$config){
				$id='output_'.($id_method).'_'.($config["name"]).'';
				
				echo "
				if(document.getElementById(\"".$id."\").value == \"\"){
				     	alert(\"Introduce a valid ".$method_name." ".$config['name']."\");
					document.getElementById(\"".$id."\").focus();
					return false;
				}     			
	     			";
			}
			echo "break;";
		}
		
    	?>     	
     	}
     	return true;
    }
     
     function validate_new_ioc(){
     
     	if(validate_ioc()) {
     		return validate_reason();
     	} else {
     		return false;     
     	}
     }
     
     function validate_ioc(){

	if(document.getElementById("ioc_name").value == ""){
     		alert("Introduce a valid name");
     		document.getElementById("ioc_name").focus();
     		return false;
     	}
     
	if(document.getElementById("ioc_type").value == "-1"){
     		alert("Introduce a valid type");
     		document.getElementById("ioc_type").focus();
     		return false;
     	}
     	
	if(isNaN(document.getElementById("ioc_jol").value) || document.getElementById("ioc_jol").value<0){
     		alert("Introduce a valid JSON Offence Level");
     		document.getElementById("ioc_jol").focus();
     		return false;
     	}
     
     	return true;
     }
     
  function validate_reason(){
	if(document.getElementById("ioc_reason_reason").value == ""){
     		alert("Introduce a valid reason");
     		document.getElementById("ioc_reason_reason").focus();
     		return false;
     	}
     
	if(document.getElementById("ioc_reason_source").value == ""){
     		alert("Introduce a valid source");
     		document.getElementById("ioc_reason_source").focus();
     		return false;
     	}

	if(document.getElementById("ioc_reason_direction").value == "-1"){
     		alert("Introduce a valid direction");
     		document.getElementById("ioc_reason_direction").focus();
     		return false;
     	}
     	
	if(isNaN(document.getElementById("ioc_reason_confidence").value) || document.getElementById("ioc_reason_confidence").value<0){
     		alert("Introduce a valid confidence level");
     		document.getElementById("ioc_reason_confidence").focus();
     		return false;
     	}
	if(document.getElementById("ioc_reason_sl").value == "-1"){
     		alert("Introduce a valid Shared Level");
     		document.getElementById("ioc_reason_sl").focus();
     		return false;
     	}
     	return true;
}
</script>
     }

