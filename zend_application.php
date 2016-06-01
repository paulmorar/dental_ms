<?php

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
defined('APPLICATION_PUBLIC_PATH') || define('APPLICATION_PUBLIC_PATH', realpath(dirname(__FILE__)));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));


defined('THEME_PATH') || define('THEME_PATH', APPLICATION_PUBLIC_PATH.'/theme/');

set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PUBLIC_PATH . '/library'),
	realpath(APPLICATION_PUBLIC_PATH . '/library/App/lib'),
	get_include_path(),
)));



require_once 'Zend/Application.php';
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();