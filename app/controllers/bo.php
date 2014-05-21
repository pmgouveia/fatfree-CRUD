<?php

namespace App\Controllers;

class Bo extends Controller{
	
	// var $acl = array('guest' => 'login');
	// var $allowed = array('backoffice');
	
	function __construct()
	{
		parent::__construct();
		$this->layout = 'backoffice';
		
	}
	function index()
	{
		$this->f3->set('title','');
		
		print_r($this->f3->get('SESSION'));
		if(!$this->f3->exists('SESSION.bo')){
			echo "DOEST EXIST";
			$this->reroute('bo','login');
		}
	}
	
	function login()
	{
		
		$this->f3->clear('SESSION.bo');
		
		if($this->f3->exists('POST.login_bo')){
			$login = $this->f3->get('POST.login_bo');
			$passwd = $this->f3->get('POST.password');
			
			if($login == 'admin' && $passwd == $this->f3->get('BO_PASS'))
			{
				$this->f3->set('SESSION.msg','Login success');
				$this->f3->set('SESSION.bo',array('login' => $login,'lastseen' => time(),'ip' => $_SERVER['REMOTE_ADDR']));
				$this->reroute('bo');
			}else
			{
				$this->f3->set('SESSION.error_msg','Erro de login');
				$this->reroute('bo');
			}
		}
	}
	function logout()
	{
		$this->f3->clear('SESSION');
		
		$this->f3->set('SESSION.msg','Logout success');
		$this->reroute('bo');
	}
}
