#DbAcl package for FuelPHP

## Description

DbAcl extends standard Fuel's Auth package providing more complex ACL stored entirely in database.

## Features
* Separate roles sets for every namespace
* Multiple permissions per user
* Multiple groups per user
* Multiple permissions per group

## Installation
* Make sure Auth package is configured and working
* Clone / download DbAcl into ```PKGPATH/dbacl/```
* Copy config file ```PKGPATH/dbacl/config/dbacl.php``` into ```APPPATH/config/``` directory and edit it as you wish
* Load DbAcl
  * by adding DbAcl to ```always_load``` array inside your application ```config.php```
  <pre>
  'always\_load'  => array(
            'packages'  => array(
                'auth',
                'dbacl',
  ),
  </pre>
  * by replacing auth package in ```always_load``` array (DbAcl will load it automatically)
  <pre>
'always\_load'  => array(
            'packages'  => array(
                'dbacl',
),
  </pre>
 * or using Package class
  <pre>
\Package::load('dbacl');
</pre>
* Create required database tables
  * using migrations with oil
  <pre>
php oil refine migrate:up --packages=dbacl
</pre>
  * or manually importing shema.sql

## Configuration
Apart of obvious settings you can decide which users are being treated as _superusers_ with access to anything without even checking if resource / role exists.
In this example, users with ID 1 and 2 have access to anything with no further checks:
<pre>
'superusers' => array(1, 2),
</pre>
Never insert 0 here if you have _guest\_login_ enabled (ID of guest = 0).

## Usage
For now DbAcl has only one static method which check access of currently logged in user: <pre>bool DbAcl::has\_access(string $class\_name, string $method\_name, string $role\_name)</pre>
<table>
  <tr>
    <th>Parameters</th><td>Param</td><td>Description</td>
  </tr>
  <tr>
    <td></td><td>$class_name</td><td>name of class with its namespace (useful with HMVC modules), when no namespace provided Main\ will be useed</td>
  </tr>
  <tr>
    <td></td><td>$method_name</td><td>name of method that we chack access to</td>
  </tr>
  <tr>
    <td></td><td>$role_name</td><td>name of role that we require, role have to be defined inside namespace</td>
  </tr>
</table>
<pre>
if (DbAcl::has_access('Module\\Controller_Settings', 'save', 'test1'))
{
	//user has access to Module\Controller_Settings::save() with role "test1"
}
else
{
	//sorry
}
</pre>

## Definitions

### Role
Every role is assigned to namespace and can be used only under this specific namespace. Roles with the same name but under different namespaces are **NOT**  equals.
<pre>
//role "test1" is defined and user has access
var_dump(DbAcl::has_access('Module\\Settings', 'save', 'test1'));
//role "test1" is not defined under this namespace
var_dump(DbAcl::has_access('Main\\Example', 'index', 'test1'));
</pre>
<pre>
bool(true)
bool(false)
</pre>

### Resource
Resource is one _method_ inside one _class_ under one _namespace_.

### Permission
Permission stores information about

* User / group it belongs to
* Resource that access is being granted to
* Role on which access it allowed

Every information is stored as ID number of row inside individual table and needs to point to existing row or ```has_access``` method will return _false_.

## How to...
### ...create new role?
Every namespace need to define its own set of rules. One of first things done by ```has_access``` method is check if given role exists under given namespace.
Creating new role is just simple insert:
<pre>
list($insert_id, $rows_affected) = \DB::insert('dbacl_role')
		->columns(array('namespace', 'name'))
		->values(array('Main\\', 'make_magic'))
		->execute();
</pre>
### ...create new group?
Similar to creating new role this is one insert too:
<pre>
list($insert_id, $rows_affected) = \DB::insert('dbacl_group')
		->columns(array('name'))
		->values(array('VIPs'))
		->execute();
</pre>
### ...create new resource?
Another simple insert:
<pre>
list($insert_id, $rows_affected) = \DB::insert('dbacl_resource')
		->columns(array('namespace', 'class', 'method'))
		->values(array('Main\\', 'Controller_Admin', 'dashboard'))
		->execute();
</pre>
### ...add new access permission to group?
First we need to get required IDs from database:

* ID of group that we want to add permission to
* ID of resource which we give access to
* ID of role

Then just insert them in into ```group_premission``` table
<pre>
list($insert_id, $rows_affected) = \DB::insert('dbacl_group_permission')
		->columns(array('group_id', 'resource_id', 'role_id'))
		->values(array($group_id, $resource_id, $role_id))
		->execute();
</pre>
### ...add new access permission directly to user?
Likewise adding permission to a group we need to make database insert, only difference is _user\_id_ instead of _group\_id_ and of course different table is being used
<pre>
list($insert_id, $rows_affected) = \DB::insert('dbacl_user_permission')
		->columns(array('user_id', 'resource_id', 'role_id'))
		->values(array($user_id, $resource_id, $role_id))
		->execute();
</pre>
