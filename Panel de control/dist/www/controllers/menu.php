<?php

  function draw_menu($menu, $url) {
    $html = '';
    foreach ($menu as $entry) {

      $str_class= '';
      

      if(count($entry['submodules']) > 0){
	$str_class.= ' has-treeview';
	if(strpos($url,$entry['url']) === 0){
	  $str_class.= ' menu-open';
	}
      }

	if($entry['menu']==='1'){
	      $html .= '<li class="nav-item'.$str_class.'">
		      <a href="'.$GLOBALS['url_base'].$entry['url'].'" class="nav-link'.($entry['url']==$url ? ' active':'').'">
			<i class="nav-icon '.$entry['icon'].'"></i>
			<p>'.$entry['name'].(count($entry['submodules']) > 0 ? '<i class="fas fa-angle-left right"></i>':'').'</p>
		      </a>
		      '.(count($entry['submodules']) > 0 ? '<ul class="nav nav-treeview">'.draw_menu($entry['submodules'], $url).'</ul>':'').'
		    </li>';
	}
		    
    }
    
    return $html;
  }

  include_once('www/include/module.php');

  $module = new module($GLOBALS['bbdd']);

  $menu = $module->list_user_modules($_SESSION['_gestioioc']);

  $path = str_replace($GLOBALS['url_base_path'], '', $_SERVER['REQUEST_URI']);
  echo draw_menu($menu,$path);


?>
