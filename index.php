<?php


$fatfree = 'app/fatfree/lib/base.php';
if(!is_file($fatfree))
{
	echo 'Missing fatfree directory. Please copy the fatfree library to the app folder. ( $ cd app && git clone https://github.com/bcosca/fatfree.git )';
	die(1);
}

$f3=require($fatfree);

$f3->acl = array(
		"guest" => array("page","bo" => array("login","index","logout")), //GUEST USER
		"auth" => array("page"), //AUTH USER
		"auth_bo" => array("bo")  //AUTH ADMIN
	);



if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

if(!is_file('app/config.ini'))
{
	trigger_error('Missing app/config.ini file. Please copy and edit the file app/config.ini.dist to app/config.ini');
}
$f3->config('app/config.ini');



$f3->set('autoload','app/controllers/|app/models/|app/plugins/');
$f3->set('UI','app/views/');
$f3->set('TEMP','app/tmp/');
$f3->set('encoding','ISO-8859-15');

$f3->route('GET /','App\Controllers\Page->index');

$lang_str = '';

if($f3->exists('LANG') || $f3->exists('LANGS')){
	$lang_str = '@lang/';
}

$f3->route("GET /$lang_str@controller/@action","App\Controllers\@controller->@action");
$f3->route("GET /$lang_str@controller/@action/@p1","App\Controllers\@controller->@action");
$f3->route("GET /$lang_str@controller/@action/@p1/@p2","App\Controllers\@controller->@action");
$f3->route("GET /$lang_str@controller/@action/@p1/@p2/@p3","App\Controllers\@controller->@action");
$f3->route("GET /$lang_str@controller/@action/@p1/@p2/@p3/@p4","App\Controllers\@controller->@action");

$f3->route("POST /$lang_str@controller/@action","App\Controllers\@controller->@action");
$f3->route("POST /$lang_str@controller/@action/@p1","App\Controllers\@controller->@action");
$f3->route("POST /$lang_str@controller/@action/@p1/@p2","App\Controllers\@controller->@action");
$f3->route("POST /$lang_str@controller/@action/@p1/@p2/@p3","App\Controllers\@controller->@action");
$f3->route("POST /$lang_str@controller/@action/@p1/@p2/@p3/@p4","App\Controllers\@controller->@action");

$f3->route("GET /$lang_str@controller","App\Controllers\@controller->index");
$f3->route("GET /$lang_str","App\Controllers\Page->index");

// SQL
if($f3->exists('db_dns')){
	$f3->set('db',new \DB\SQL($f3->get('db_dns') . $f3->get('db_name'),$f3->get('db_user'),$f3->get('db_pass')));
}


if( !$f3->exists('DEBUG') ||  $f3->get('DEBUG') < 3 ){
	$f3->set('ONERROR',function($f3){
			$f3->set('CRASH','Page not found!');
			$f3->reroute('/');
	});
}
if(!is_writable('app/tmp/')){
	trigger_error('Please create the folder app/tmp and give writtable permissions!');
}
$f3->run();
