<?php
if (!defined('e107_INIT')) { exit; }

$prefix = '' ;
// example e107_unnuke_links_links  $prefix = unnuke_,  + # = e107 table 

define("UN_TABLENAME_LINKS_CATEGORIES", $prefix."links_categories");
define("UN_TABLENAME_LINKS_CATEGORIES", $prefix."links_categories");
define("UN_TABLENAME_LINKS_EDITORIALS", $prefix."links_editorials");
define("UN_TABLENAME_LINKS_LINKS", 		$prefix."links_links");
define("UN_TABLENAME_LINKS_MODREQUEST", $prefix."links_modrequest");
define("UN_TABLENAME_LINKS_NEWLINK", 	$prefix."links_newlink");
define("UN_TABLENAME_LINKS_VOTEDATA", 	$prefix."links_votedata");


define("WEB_LINKS_APP", 		e_PLUGIN.'web_links/');
define("WEB_LINKS_APP_ABS", 	e_PLUGIN_ABS.'web_links/');
define("WEB_LINKS_FRONTFILE",   'web_links.php' ); 
define("WEB_LINKS_FOLDER",  	'web_links');
define("WEB_LINKS_INDEX",  	WEB_LINKS_APP.WEB_LINKS_FRONTFILE);


 
 