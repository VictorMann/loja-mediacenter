<?php
require 'environment.php';

global $config;
global $db;

$config = array();

if (ENVIRONMENT == 'development')
{
	$config['dbname'] = DBNAME;
	$config['host']   = HOST;
	$config['dbuser'] = DBUSER;
	$config['dbpass'] = DBPASS;
} 
else 
{
	$config['dbname'] = DBNAME;
	$config['host']   = HOST;
	$config['dbuser'] = DBUSER;
	$config['dbpass'] = DBPASS;
}

$config['default_lang'] = 'pt-br';
$config['cep_origin'] = '07145000';

$db = new PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'], $config['dbuser'], $config['dbpass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>