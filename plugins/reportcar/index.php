<?php

define("ROOT",dirname(__FILE__)."/../../");

define("FACE","front");

define("IS_AJAX",1);

session_start();

include(ROOT."app_configs.php");

$config=new config;

include(ROOT."library/objects/toStringConverter.php");

include_once(ROOT."library/globals/common_controls.php");

include_once(ROOT."library/globals/core_database_interface.php");

$db=new database;

function showForm(){
 
 include(dirname(__FILE__)."/temps/uploadtemp.php");
 
}
echo showForm();
?>