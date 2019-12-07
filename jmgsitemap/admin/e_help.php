<?php

$content = '';
$links = array();
$links['supportforum']    =  "https://www.e107sk.com/forum/plugins-in-active-mode/";
$links['documentation']   =  "https://www.e107sk.com/documentation/jm-google-sitemap/?cat.257";	
$links['demo']            =  "";	
$links['github']          =  "https://github.com/Jimmi08/jmgsitemap-e107-plugin";	    
$links['download']        =  "https://www.e107sk.com/download/58/jm-sitemap-plugin/";    

$content .= '<h2 class="text-center">' . LAN_JM_ADMIN_HELP_01 . '</h2>';
if($links['supportforum'])  {    
	$content .= '<p class="text-center">' . LAN_JM_ADMIN_HELP_02 . '</p>';
	$content .= '<p class="text-center">';
	$content .= '<a href="'.$links['supportforum'].'" target="_blank">' . LAN_JM_ADMIN_HELP_03. '</a>';
	$content .= '</p>';
}
if($links['documentation'])  {
	$content .= '<p class="text-center">' . LAN_JM_ADMIN_HELP_04 . '</p>';
	$content .= '<p class="text-center">';
	$content .= '<a href="'.$links['documentation'].'" target="_blank">' . LAN_JM_ADMIN_HELP_05. '</a>';
	$content .= '</p>';
} 
if($links['demo'])  {
	$content .= '<p class="text-center">' . LAN_JM_ADMIN_HELP_06 . '</p>';
	$content .= '<p class="text-center">';
	$content .= '<a href="'.$links['demo'].'" target="_blank">' . LAN_JM_ADMIN_HELP_07. '</a>';
	$content .= '</p>';
}
if($links['github'])  {
	$content .= '<p class="text-center">' . LAN_JM_ADMIN_HELP_08 . '</p>';
	$content .= '<p class="text-center">';
	$content .= '<a href="'.$links['github'].'" target="_blank">' . LAN_JM_ADMIN_HELP_09. '</a>';
	$content .= '</p>';
}
if($links['download'])  {    
	$content .= '<p class="text-center">' . LAN_JM_ADMIN_HELP_10 . '</p>';
	$content .= '<p class="text-center">';
	$content .= '<a href="'.$links['download'].'" target="_blank">' . LAN_JM_ADMIN_HELP_11. '</a>';
	$content .= '</p>';
}    

$helplink_text = array(
	'title' => LAN_JM_ADMIN_HELP_01,
	'body'  => $content,
);

$helptext = $content;
//$helptext = e107::getRender()->tablerender($helplink_text['title'], $helplink_text['body'], 'hduhelp');

?>
