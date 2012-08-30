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

namespace DbAcl;


/**
 * DbAcl
 *
 * @package     DbAcl
 */
class DbAcl extends \Auth
{

	/**
	 * @var  int  holds id of currently logged in user
	 */
	protected static $_user_id = array();

	/**
	 * @var  Array  holds cached user's roles
	 */
	protected static $_roles_cache = array();

	public static function _init()
	{
		//init Auth class if it wasn't called yet
		if (is_null(static::$_instance))
		{
			parent::_init();
		}

		static::$_user_id = static::instance()->get_user_id();
		if (is_array(static::$_user_id))
		{
			static::$_user_id = static::$_user_id[1];
		}
	}

	/**
	 * Chceck if currently logged in user has access to given resource
	 * with given role.
	 *
	 * @param   String  name of class with namespace if possible
	 * @param   String  name of method within class
	 * @param   String  name of role to check
	 * @throws  InvalidArgumentException  when non-valid string param suplied
	 * @return  bool
	 */
	public static function has_access($class, $method, $role_name)
	{
		if ( ! static::validate($class, \Config::get('dbacl.regex.class'))
				or ! static::validate($method, \Config::get('dbacl.regex.method'))
				or ! static::validate($role_name, \Config::get('dbacl.regex.role')))
		{
			throw new \InvalidArgumentException('has_access method only accepts valid string params');
		}
		if ( ! static::check())
		{
			return false;
		}
		if (in_array(static::$_user_id, \Config::get('dbacl.superusers', array()), true))
		{
			return true;
		}

		$namespace = \Inflector::get_namespace($class);
		$class     = \Inflector::denamespace($class);
		empty($namespace) and $namespace = 'Main\\';

		if (isset(static::$_roles_cache[$namespace][$class][$method][$role_name]))
		{
			return static::$_roles_cache[$namespace][$class][$method][$role_name];
		}

		//Does the $role exists in given namespace?
		$role = \DB::select()
					->from(\Config::get('dbacl.table.roles', 'dbacl_role'))
					->where('name', $role_name)
					->and_where('namespace', $namespace)->execute(\Config::get('dbacl.connection', null))->current();
		if (empty($role))
		{
			return false;
		}

		//Find permission to resource with $role within all groups that user belongs to
		$groups_permissions = \DB::query('SELECT COUNT(*) AS count FROM `'.\Config::get('dbacl.table.users_groups', 'dbacl_user_group').'`, `'.\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission').'`, `'.\Config::get('dbacl.table.resources', 'dbacl_resource').'`
							WHERE '.\Config::get('dbacl.table.users_groups', 'dbacl_user_group').'.user_id = '.static::$_user_id.'
							AND '.\Config::get('dbacl.table.users_groups', 'dbacl_user_group').'.group_id = '.\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission').'.group_id
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.namespace = '.\DB::escape($namespace).'
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.class = '.\DB::escape($class).'
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.method = '.\DB::escape($method).'
							AND '.\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission').'.resource_id = '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.id
							AND '.\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission').'.role_id = '.$role['id'].'
							')->execute(\Config::get('dbacl.connection', null))->current();
		if ((int)$groups_permissions['count'] > 0)
		{
			static::$_roles_cache[$namespace][$class][$method][$role_name] = true;

			return true;
		}

		//Get direct user's permissions for resource with $role
		$user_permissions = \DB::query('SELECT COUNT(*) AS count FROM `'.\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission').'`, `'.\Config::get('dbacl.table.resources', 'dbacl_resource').'`
							WHERE '.\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission').'.user_id = '.static::$_user_id.'
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.namespace = '.\DB::escape($namespace).'
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.class = '.\DB::escape($class).'
							AND '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.method = '.\DB::escape($method).'
							AND '.\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission').'.resource_id = '.\Config::get('dbacl.table.resources', 'dbacl_resource').'.id
							AND '.\Config::get('dbacl.table.users_permissions', 'dbacl_guser_permission').'.role_id = '.$role['id'].'
							')->execute(\Config::get('dbacl.connection', null))->current();
		if ((int)$user_permissions['count'] > 0)
		{
			static::$_roles_cache[$namespace][$class][$method][$role_name] = true;

			return true;
		}
		static::$_roles_cache[$namespace][$class][$method][$role_name] = false;

		return false;
	}

	/**
	 * Runs filter_var with given REGEX at given string.
	 *
	 * @param   String  text that should be validated
	 * @param   String  regex expressions to apply
	 * @return  bool
	 */
	public static function validate($param, $regex)
	{
		$v = filter_var($param, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regex)));

		return $v === false ? false : true;
	}
}

/* end of file dbacl.php */
