<?php

namespace App\Controllers;

class Page extends Controller{
	
	// public $translate = true;
	// protected $models = array("Pages","Areas","Noticias","Definicoes","Traducoes");
	// $layout
	function __construct()
	{
		parent::__construct();
		
		$this->f3->set('SESSION.lastseen',time());
		$this->f3->set('SESSION.ip',$_SERVER['REMOTE_ADDR']);
		
	}
	
	function index()
	{
		if(!$this->f3->exists('PARAMS.lang')){
			$this->f3->reroute('/'.$this->f3->get('LANG').'/');
		}
		
		
	}
	
	function beforeroute(){
		parent::beforeroute();
	}
	
}
