<?php
$_ENV['config'] = require(dirname(__FILE__) . '/config.php');
require(dirname(__FILE__) . '/globals.php');
require(bp() . '/vendors/kint/Kint.class.php');

error_reporting(E_ALL);
date_default_timezone_set('Australia/Adelaide');

function __autoload($class_name)
{
    include bp() . '/classes/' . $class_name . '.php';
}