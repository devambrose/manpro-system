<?php
class helper{

public function mainPage($activeTab=0){

$tbs=new tabs_layout;

$tbs->setActiveTab($activeTab);

$tbs->setWidth("895px");

$tbs->addTab("Install");

$tbs->addTabContent($this->install_page());

$tbs->addTab("Modules");

//System::createInstallationFile(ROOT."extensions/macros/macro_userhome","macro_userhome");
 
$tbs->addTabContent($this->module_list());

$tbs->addTab("Macros");

$tbs->addTabContent($this->macro_list());

$tbs->addTab("Plugins");

$tbs->addTabContent($this->plugin_list());

$tbs->showTabs();


}
private function install_page(){

$install_content=new objectString();

$install_content->generalTags(System::backButton("?"));

$install_content->generalTags(System::contentTitle("Install extension"));

$install_content->generalTags(System::categoryTitle("From .inst file","margin-bottom:5px;"));

$form=new form_control;

$form->enableUpload();

$install_content->generalTags($form->formHead());

$input=new input;

$install_content->generalTags($this->installExtensions());

$input->setClass("form_input");

$input->input("File","install_upload_file","?");

$install_content->generalTags("<div id=\"form_row\"><div id=\"label\" style=\"margin-top:5px;\"><strong>Installation File</strong></div>{$input->toString()}</div>");

$install_content->generalTags("<div id=\"form_row\"><input type=\"submit\" name=\"install_update\" value=\"Install\" class=\"form_button\" /></div>");

$install_content->generalTags("</form>");

$install_content->generalTags(System::categoryTitle("From market"));

$install_content->generalTags("<iframe src=\"http://localhost/\"></iframe>");
//$install_content->generalTags(System::info("Get Applications"));

$install_content->generalTags("<div id=\"form_row\" style=\"border-bottom:1px solid #444;\">".System::info("Get applications from the market")."</div>");

return $install_content->toString();  

}
private function installExtensions(){

if(isset($_POST['install_update'])){
 
 if($_FILES['install_upload_file']['name']!=""){
 
  
  return System::installExtension($_FILES['install_upload_file']['tmp_name']);
 
 }
 
}
//System::createInstallationFile(ROOT."plugins/pageaccess","pageaccessed");

}
private function macro_list(){

GLOBAL $db;

$macro_content=new objectString();
$list=new list_control;

$list->setHeaderFontBold();


$list->setColumnNames(array("No","","Macro Name","Date installed"));

$list->setColumnSizes(array("30px","30px","350px","250px"));

$list->setListId("mcr");

$list->setAlternateColor("#cbe7f8");

$macro_content->generalTags(System::backButton("?"));

$macro_content->generalTags(System::contentTitle("Installed Macros"));

$list->setSize("860px","390px");

//$list->setTitle("Installed Macros");

$macro_content->generalTags($this->uninstallMacro());

$macros=$db->getMacros(" where for_super=0");

for($i=0;$i<count($macros);$i++){
 
 $input=new input;
 
 $input->setTagOptions("onclick=\"isMacroSelected()\"");

 $input->input("radio","macro_name",$macros[$i]->macro_name);

 $list->addItem(array($i+1,$input->toString(),$macros[$i]->macro_name,$macros[$i]->macro_description));

}

$list->showList(false);

$func="
<script>
var macro=false;

function formConfirmMacro(){
if(macro){
return confirm('Are you sure you want to uninstall this macro?');
}else{ alert('Please select macro'); return false;}
}
function isMacroSelected(){
 macro=true;
}
</script>
";

$macro_content->generalTags($func);

$form=new form_control("return formConfirmMacro()");

$macro_content->generalTags($form->formHead());

$unistall_btn=new input;

$unistall_btn->setClass("form_button_delete");

$unistall_btn->setTagOptions("style=\"float:right;margin-right:5px;\"");

$unistall_btn->input("submit","uninstall_macro","Uninstall X");

$macro_content->generalTags(System::categoryTitle($unistall_btn->toString(),"margin-bottom:5px;"));

$macro_content->generalTags($list->toString());

$macro_content->generalTags("<div id=\"form_row\" style=\"border-bottom:1px solid #444;\"></div>");

$macro_content->generalTags("</form>");

return $macro_content->toString();
}

private function uninstallMacro(){

GLOBAL $db;

if((isset($_POST['uninstall_macro']))&&(isset($_POST['macro_name']))){

if(System::deleteFolder(ROOT."/extensions/macros/{$_POST['macro_name']}")){

$db->deleteQuery("macros","where macroname='{$_POST['macro_name']}'");

return System::successText("Macro '{$_POST['macro_name']}' uninstalled successfully");

}else{

return System::getWarningText("Error: Failed to uninstall '{$_POST['macro_name']}' ");


}

}

}

private function module_list(){

GLOBAL $db;

$module_content=new objectString();

$list=new list_control;

$list->setHeaderFontBold(true);

$list->setSize("860px","390px");

$list->setListId("mdl");

$list->setAlternateColor("#cbe7f8");

$list->setColumnNames(array("No","","Module Name","Module Type"));

$list->setColumnSizes(array("30px","30px","350px","100px"));

//$list->setTitle("Installed Modules");

$module_content->generalTags(System::backButton("?"));

$module_content->generalTags(System::contentTitle("Installed Modules"));

$module_content->generalTags($this->uninstallModule());

$modules=$db->getModules("where for_super=0 group by modulename");

for($i=0;$i<count($modules);$i++){
 
 $input=new input;

 $input->setTagOptions("onclick=\"isSelected()\"");

 $input->input("radio","module_name",$modules[$i]->module_name);

 $list->addItem(array($i+1,$input->toString(),$modules[$i]->module_name,System::moduleType($modules[$i]->module_is_menu)));

}

$list->showList(false);

$func="
<script>
var module=false;

function formConfirmModule(){
if(module){
return confirm('Are you sure you want to uninstall this module?');
}else{ alert('Please select module'); return false;}
}
function isSelected(){
 module=true;
}
</script>
";

$module_content->generalTags($func);

$d=new form_control("return formConfirmModule()");

$module_content->generalTags($d->formHead());

$uninstall_btn=new input;

$uninstall_btn->setClass("form_button_delete");

$uninstall_btn->setTagOptions("style=\"float:right;margin-right:5px;\"");

$uninstall_btn->input("submit","uninstall_module","Uninstall X");

$module_content->generalTags(System::categoryTitle($uninstall_btn->toString(),"margin-bottom:5px;"));

$module_content->generalTags($list->toString());

$module_content->generalTags("<div id=\"form_row\" style=\"border-bottom:1px solid #444;\"></div>");

$module_content->generalTags("</form>");

return $module_content->toString();

}

private function uninstallModule(){

GLOBAL $db;

if(isset($_POST['uninstall_module'])&&(isset($_POST['module_name']))){

if(System::deleteFolder(ROOT."/extensions/modules/{$_POST['module_name']}")){

$db->deleteQuery("modules","where modulename='{$_POST['module_name']}'");

return System::successText("Module uninstalled successfully");

}else{

return System::getWarningText("Error:Could not uninstall module");

}

}

}

private function plugin_list(){

GLOBAL $db;

$plugin_content=new objectString();

$list=new list_control;

$list->setHeaderFontBold();

$list->setListId("plg");

$list->setAlternateColor("#cbe7f8");

$list->setColumnNames(array("No","","Plugin Name","Plugin Type","Status"));

$list->setColumnSizes(array("30px","30px","350px","100px","100px"));

$plugin_content->generalTags(System::backButton("?"));

$plugin_content->generalTags(System::contentTitle("Installed Plugins"));

$plugin_content->generalTags($this->uninstallPlugin());

$plugins=$db->getPlugins("");

for($i=0;$i<count($plugins);$i++){
 
 $input=new input;

 $input->setTagOptions("onclick=\"isPluginSelected()\"");

 $input->input("radio","plugin_name",$plugins[$i]->plugin_name);

 $list->addItem(array($i+1,$input->toString(),$plugins[$i]->plugin_name,System::pluginType($plugins[$i]->plugin_type),System::statusIcon($plugins[$i]->plugin_status)));

}

$list->setSize("860px","390px");

//$list->setTitle("Installed Plugins");

$list->showList(false);

$func="<script>
var plugin=false;
var msgtype=\"\";
function formConfirmPlugin(){
if(plugin){
return confirm('Are you sure you want to '+msgtype+' this plugin?');
}else{ alert('Please select plugin'); return false;}
}
function isPluginSelected(){
 plugin=true;
}
function setAlertMess(al){
 msgtype=al;
}
</script>";

$plugin_content->generalTags($func);

$form=new form_control("return formConfirmPlugin()");

$plugin_content->generalTags($form->formHead());

$uninstall_btn=new input;

$uninstall_btn->setClass("form_button_delete");

$uninstall_btn->setTagOptions("style=\"float:right;margin-right:5px;\" onclick=\"setAlertMess('uninstall')\"");

$uninstall_btn->input("submit","uninstall_plugin","Uninstall X");


$enable_btn=new input;

$enable_btn->setClass("form_button");

$enable_btn->setTagOptions("style=\"margin-left:5px;\" onclick=\"setAlertMess('enable')\"");

$enable_btn->input("Submit","Enable_btn","Enable");

$disable_btn=new input;

$disable_btn->setTagOptions("onclick=\"setAlertMess('disable')\"");

$disable_btn->setClass("form_button_disable");

$disable_btn->input("Submit","disable_btn","Disable");

$plugin_content->generalTags(System::categoryTitle($enable_btn->toString().$disable_btn->toString().$uninstall_btn->toString(),"margin-bottom:5px;"));

$plugin_content->generalTags($list->toString());


$plugin_content->generalTags("<div id=\"form_row\" style=\"border-bottom:1px solid #444;\"></div>");

$plugin_content->generalTags("</form>");

return $plugin_content->toString();

}

private function uninstallPlugin(){
GLOBAL $db;

if((isset($_POST['plugin_name']))&&(isset($_POST['uninstall_plugin']))){

 if(System::deleteFolder(ROOT."/plugins/{$_POST['plugin_name']}")){
 
  $db->deleteQuery("plugins"," where plugin_name='{$_POST['plugin_name']}' ");
  
  unset($_SESSION['plugins']);
 
  return System::successText("Plugin '{$_POST['plugin_name']}' uninstalled successfully");

 }else{
 
  return System::getWarningText("Error:Could not uninstall plugin '{$_POST['plugin_name']}' ");
 
 }

}

if((isset($_POST['Enable_btn']))&(isset($_POST['plugin_name']))){

 $db->updateQuery(array("status=1"),"plugins","where plugin_name='{$_POST['plugin_name']}'");
 
 return System::successText("Plugin '{$_POST['plugin_name']}' enabled");

}

if((isset($_POST['disable_btn']))&(isset($_POST['plugin_name']))){

 $db->updateQuery(array("status=0"),"plugins","where plugin_name='{$_POST['plugin_name']}'");
 
 return System::successText("Plugin '{$_POST['plugin_name']}' disabled");

}

}
public function setActiveTab(){
$tab=0;

if(isset($_POST['uninstall_module'])){
$tab=1;
}

if(isset($_POST['uninstall_macro'])){
$tab=2;
}

if(isset($_POST['uninstall_plugin'])|isset($_POST['Enable_btn'])|isset($_POST['disable_btn'])){
$tab=3;
}

return $tab;
}

}
?>