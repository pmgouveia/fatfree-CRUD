<?php

$f3=require('app/lib/base.php');
$f3->acl = array(
		"guest" => array("page","newsletter","bo" => array("login","index")), //GUEST USER
		"auth" => array("page"), //AUTH USER
		"auth_bo" => array("bo")  //AUTH ADMIN
	);

$f3->config('app/config.ini');
if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$f3->set('autoload','app/controllers/|app/models/|app/plugins/');
$f3->set('UI','app/views/');
$f3->set('TEMP','app/tmp/');
$f3->set('encoding','ISO-8859-15');

$f3->route('GET /','App\Controllers\Page->index');
$f3->route('GET /@lang','App\Controllers\Page->index');

// $f3->route('GET /@lang/@action','App\Controllers\Page->@action');
$f3->route('POST /@lang/@action/@p1','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4/@p5','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4/@p5/@p6','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4/@p5/@p6/@p7','App\Controllers\Page->@action');

$f3->route('GET /@lang/@action/@p1/@p2//@p4','App\Controllers\Page->@action');

$f3->route('GET /@lang/@action/@p1/@p2/@p3///@p6','App\Controllers\Page->@action');
$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4//@p6','App\Controllers\Page->@action');

$f3->route('GET /@lang/@action/@p1/@p2/@p3/@p4/@p5/@p6/@p7','App\Controllers\Page->@action');

$f3->route('GET /bo/@controller/@action','App\Controllers\@controller->@action');
$f3->route('GET /bo/@controller/@action/@p1','App\Controllers\@controller->@action');
$f3->route('GET /bo/@controller/@action/@p1/@p2','App\Controllers\@controller->@action');
$f3->route('GET /bo/@controller/@action/@p1/@p2/@p3','App\Controllers\@controller->@action');
$f3->route('GET /bo/@controller/@action/@p1/@p2/@p3/@p4','App\Controllers\@controller->@action');

$f3->route('POST /bo/@controller/@action','App\Controllers\@controller->@action');
$f3->route('POST /bo/@controller/@action/@p1','App\Controllers\@controller->@action');
$f3->route('POST /bo/@controller/@action/@p1/@p2','App\Controllers\@controller->@action');
$f3->route('POST /bo/@controller/@action/@p1/@p2/@p3','App\Controllers\@controller->@action');
$f3->route('POST /bo/@controller/@action/@p1/@p2/@p3/@p4','App\Controllers\@controller->@action');

$f3->route('GET /bo/@controller','App\Controllers\@controller->index');
$f3->route('GET /bo','App\Controllers\Bo->index');

// SQL
$f3->set('db',new \DB\SQL($f3->get('db_dns') . $f3->get('db_name'),$f3->get('db_user'),$f3->get('db_pass')));

if( !$f3->exists('DEBUG') ||  $f3->get('DEBUG') < 3 ){
	$f3->set('ONERROR',function($f3){
			$f3->set('CRASH','Page not found!');
			$f3->reroute('/');
	});
}
$f3->run();
