<?php
/**
 * Part of the DbAcl package for FuelPHP.
 *
 * @package    DbAcl
 * @author     OscaTutenchamon
 * @license    MIT License
 * @copyright  2012 PyOt Mangament System
 * @link       http://pyot-ms.github.com
 */


\Package::loaded('auth') or \Package::load('auth');
\Config::load('dbacl', true);

Autoloader::add_core_namespace('DbAcl');

Autoloader::add_classes(array(
	'DbAcl\\DbAcl' => __DIR__.'/classes/dbacl.php',
));


/* End of file bootstrap.php */