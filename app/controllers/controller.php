<?php
namespace App\Controllers;

class Controller{
	
	protected $f3;
	
	protected $db;
	
	public $layout = "default";
	public $title = "";
	
	public $controller = "page";
	public $action = "index";
	
	
	
	function __construct() {
        	
        	$this->f3 = \Base::instance();
		$this->db = new \App\Plugins\db();
		
		if(!empty($this->models)){
			foreach($this->models as $model){
				$name = $model;
				$namespace = "\\App\\Models\\$name";
				$this->$model = new $namespace;
				
			}
		}
		$this->layout = $this->f3->get('LAYOUT');
        }
        
	function index()
	{
		
	}
	
	function beforeroute() {
		$this->f3->clear('SESSION.validate');
		if($this->f3->exists('POST.id'))
		{
			$this->f3->set('POST.modified',date("Y-m-d H:i:s"));
		}
		if($this->f3->exists('SESSION.login'))
		{
			$this->f3->set('POST.login',$this->f3->get('SESSION.login'));
		}
		
		
		
        	$action = $this->f3->get('PARAMS.action');
        	$controller = $this->f3->get('PARAMS.controller');
        	
        	if(empty($action)) $action = "index";
        	if(empty($controller)) $controller = "page";
        	
        	
        	if(isset($this->translate) && $this->translate){
        		$lang = $this->f3->get('LANG');
        		if(!empty($lang) && $controller != 'bo'){
        			
        			if( $this->f3->exists('PARAMS.lang')){
        				$lang = $this->f3->get('PARAMS.lang');
        			}
        			$this->f3->set('lang_set',$lang);
        			
        		}
        	}
        	
        	$hasPermissions = false;
        	
        	if($this->f3->exists('SESSION.login'))
        	{
        		
        		foreach($this->f3->acl["auth"] as $acl_key => $acl_vals)
        		{
        			if(!is_array($acl_vals) && $controller == $acl_vals)
        			{
        				$hasPermissions = true;
        				break;
        			}
        			if(is_array($acl_vals) && $controller ==  $acl_key )
        			{
        				if(in_array($action,$acl_vals))
        				{
        					$hasPermissions = true;
        					break;
        				}
        			}
        		}
        		
        	}
        	if($this->f3->exists('SESSION.bo.login'))
        	{
        		
        		$hasPermissions = true;
        		
        	}
        	if(!$hasPermissions){
        		
        		if(empty($controller))
        		{
        			$hasPermissions = true;
        			break;
        		}
        		
        		foreach($this->f3->acl["guest"] as $acl_key => $acl_vals)
        		{
        			
        			if(!is_array($acl_vals) && $controller == $acl_vals)
        			{
        				$hasPermissions = true;
        				break;
        			}
        			if(is_array($acl_vals) && $controller ==  $acl_key )
        			{
        				if(in_array($action,$acl_vals))
        				{
        					$hasPermissions = true;
        					break;
        				}
        			}
        		}
        		
        		/**
        		if(array_search($controller,$this->f3->acl["guest"]) !== false){
        			if(is_array($this->f3->acl["guest"][array_search($controller,$this->f3->acl["guest"])] ))
        			{
        				//check actions
        			}else{
        				$hasPermissions = true;
        			}
        		}**/
        	}
        	
        	if(!$hasPermissions)
        	{
        		$this->f3->set('SESSION.error_msg',"Reserved Zone!");
        		$this->f3->reroute("/");
        	}
        	if(strlen($this->title) < 1)
        	{
        		$this->title =  $this->f3->get('PARAMS.controller').' '. $this->f3->get('PARAMS.action');
        	}
        	$this->f3->set('title',$this->title);
        	
        	$this->f3->set('controller',$controller);
        	$this->f3->set('action',$action);
        	$this->controller = $controller;
        	$this->action = $action;
        	if(!empty($this->viewsPrefix)){
        		$this->f3->set('content',$this->viewsPrefix.'/'.$controller.'/'.$action.'.htm');
        	}else{
        		$this->f3->set('content',$controller.'/'.$action.'.htm');
        	}
        	
        }

        function afterroute() {
        	
        	if($this->f3->exists('SESSION.msg'))
        	{
        		$this->f3->set('msg',$this->f3->get('SESSION.msg'));
        		$this->f3->clear('SESSION.msg');
        	}
        	
        	if($this->f3->exists('SESSION.error_msg'))
        	{
        		$this->f3->set('error_msg',$this->f3->get('SESSION.error_msg'));
        		$this->f3->clear('SESSION.error_msg');
        	}
        	if(!empty($this->menu_right))
        	{
        		$this->f3->set('menu_right',$this->menu_right);
        	}
        	
                echo \Template::instance()->render('layouts/'.$this->layout.".htm");
                
        	$this->f3->set('SESSION.lastroute',str_replace($this->f3->get('BASE'),"",$this->f3->get('URI')));
                
        }
        
        function error_msg($msg)
        {
        	$this->f3->set('SESSION.error_msg',$msg);
        }
        
        function msg($msg)
        {
        	$this->f3->set('SESSION.msg',$msg);
        }
        
        function goback()
        {
        	$referer = $this->f3->get('SERVER.HTTP_REFERER');
        	if(!empty($referer)){
        		$this->f3->reroute($referer);
        	}else{
        		$this->f3->reroute($this->f3->get('SESSION.lastroute'));
        	}
        }
        
	function copyToPost($array){
		foreach($array as $key => $val){
			$this->f3->set("POST.$key",$val);
		}
	}
}
