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

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(

	/*
	 * Connection to use
	 * Defined in database configs
	 */
	'connection' => null,

	/*
	 * Table Names
	 */
	'table' => array(
		'roles'           => 'dbacl_role',
		'groups'          => 'dbacl_group',
		'users_groups'    => 'dbacl_user_group',
		'users_permissions'  => 'dbacl_user_permission',
		'groups_permissions' => 'dbacl_group_permission',
		'resources' => 'dbacl_resource',
	),

	/*
	 * Contains IDs of users that have access to everything everytime.
	 * If guest login is enabled in Auth package adding 0
	 * here will make every guest a superuser!
	 */
	'superusers' => array(),

	/*
	 * Regex expressions to validate class / method / role name.
	 */
	'regex' => array(
		'class'  => '/[_\\\]+[a-zA-Z0-9]{1,}[a-zA-Z0-9]{1,}\z/',
		'method' => '/[a-zA-Z0-9_]+\z/',
		'role'   => '/[a-zA-Z0-9_]+\z/',
	),
);
