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

namespace Fuel\Migrations;

class Init
{

    function up()
    {
		\Config::load('dbacl', true);
        \DBUtil::create_table(\Config::get('dbacl.table.groups', 'dbacl_group'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'name' => array('type' => 'varchar', 'constraint' => 128),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.groups', 'dbacl_group'), 'name');

        \DBUtil::create_table(\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'group_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            'resource_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            'role_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission'), 'group_id');
		\DBUtil::create_index(\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission'), 'resource_id');
		\DBUtil::create_index(\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission'), 'role_id');

		\DBUtil::create_table(\Config::get('dbacl.table.resources', 'dbacl_resource'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'namespace' => array('type' => 'varchar', 'constraint' => 128),
            'class' => array('type' => 'varchar', 'constraint' => 128),
            'method' => array('type' => 'varchar', 'constraint' => 128),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.resources', 'dbacl_resource'), 'namespace');
		\DBUtil::create_index(\Config::get('dbacl.table.resources', 'dbacl_resource'), 'class');
		\DBUtil::create_index(\Config::get('dbacl.table.resources', 'dbacl_resource'), 'method');

		\DBUtil::create_table(\Config::get('dbacl.table.roles', 'dbacl_role'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'namespace' => array('type' => 'varchar', 'constraint' => 128),
            'name' => array('type' => 'varchar', 'constraint' => 128),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.roles', 'dbacl_role'), 'namespace');
		\DBUtil::create_index(\Config::get('dbacl.table.roles', 'dbacl_role'), 'name');

        \DBUtil::create_table(\Config::get('dbacl.table.users_groups', 'dbacl_user_group'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'user_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            'group_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.users_groups', 'dbacl_user_group'), 'user_id');
		\DBUtil::create_index(\Config::get('dbacl.table.users_groups', 'dbacl_user_group'), 'group_id');

        \DBUtil::create_table(\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission'), array(
            'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'user_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            'resource_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
            'role_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
		),
        array('id'), false, 'InnoDB', 'utf8_unicode_ci');
		\DBUtil::create_index(\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission'), 'user_id');
		\DBUtil::create_index(\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission'), 'resource_id');
		\DBUtil::create_index(\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission'), 'role_id');
    }

    function down()
    {
       \DBUtil::drop_table(\Config::get('dbacl.table.groups', 'dbacl_group'));
       \DBUtil::drop_table(\Config::get('dbacl.table.groups_permissions', 'dbacl_group_permission'));
       \DBUtil::drop_table(\Config::get('dbacl.table.resources', 'dbacl_resource'));
       \DBUtil::drop_table(\Config::get('dbacl.table.roles', 'dbacl_role'));
       \DBUtil::drop_table(\Config::get('dbacl.table.users_groups', 'dbacl_user_group'));
       \DBUtil::drop_table(\Config::get('dbacl.table.users_permissions', 'dbacl_user_permission'));
    }
}

/* End of file 001_init.php */