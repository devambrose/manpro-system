<?php
include(ROOT."library/objects/toStringConverter.php");


include_once("common_controls.php");


include_once(ROOT."app_configs.php");


GLOBAL $config;

session_start();

$config=new config;


include(ROOT."library/objects/database_objects.php");


include_once(ROOT."library/globals/extension_manager.php");


include_once("core_database_interface.php");


GLOBAL $db;

$db=new database;

if(defined("IS_AJAX")){
	
 define("PARENT",0);

 $user=$db->getUserSession();
 
 
 //define("PARENT",$user->parent_id);

}

include_once(ROOT."library/user_manager/user_manager.php");


define("USER_MANAGER",serialize(new Manage_User));



include_once(ROOT."library/layout_manager/interface_manager.php");


interfaceLoader::loadTemplate(FACE);

?>