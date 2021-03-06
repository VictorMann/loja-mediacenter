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
$config['cep_origin'] = CEP_ORIGIN;

$db = new PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'], $config['dbuser'], $config['dbpass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

\PagSeguro\Library::initialize();
\PagSeguro\Library::cmsVersion()->setName('NovaLoja')->setRelease('1.0.0');
\PagSeguro\Library::moduleVersion()->setName('NovaLoja')->setRelease('1.0.0');

\PagSeguro\Configuration\Configure::setEnvironment('sandbox');
\PagSeguro\Configuration\Configure::setAccountCredentials(PAGSEGURO_USER, PAGSEGURO_TOKEN);
\PagSeguro\Configuration\Configure::setCharset('UTF-8');
\PagSeguro\Configuration\Configure::setLog(true, 'logs/pagseguro.log');

