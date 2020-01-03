<?php
/*
* e107 website system
*
* Copyright (C) 2008-2015 e107 Inc (e107.org)
* Released under the terms and conditions of the
* GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
*
* Plugin Trackback
*
*
*/

if (!defined('e107_INIT'))
{
	exit;
}          

if (!e107::isInstalled('jm_canonical'))
{
	exit;
}

$pluginPrefs = e107::getPlugPref('jm_canonical');
 
$canonicalurl = '';

e107_require_once(e_PLUGIN . 'jm_canonical/canonical.class.php');

$canonicalPlugin = new Canonical;   
 
if (!$canonicalPlugin->getCanonicalActive() )
{
	return;
}
if (!e_REQUEST_URL)
{
	return;
}
              
if (!e_ADMIN_AREA)
{
 
	$canonicalPlugin->runFixConstants();
 
  $request_url = $tp->toDB(e_REQUEST_URL);
 
	// manual canonical urls MOVED TO METATAG PLUGIN
  /*	$result = $canonicalPlugin->getManualCanonicalUrl($request_url);
     
	if($result === true) { 
	  return;
	}
  */
	// return from short way, so gallery is managed by METATAG
	if(e_QUERY == '') {   
	   return;
	}
	
	
	// related canonical urls  for content plugin is done in content plugin itself
	if (e_PAGE == "news.php")
	{
			if($pluginPrefs['news_auto'] != "none") {
          //autogeneration
          $canonicalPlugin->getAutogeneratedCanonicalUrl($table, $pluginPrefs['news_auto'], $pluginPrefs['backslash']);
  			  return;
      }
      $supported = false;
      list($mode, $id, $page ) = explode(".", e_QUERY);
 
			if (!empty($mode)  && !empty($id))
			{
				if($mode == 'extend')  {
          $table = 'news'; 
          $supported = true;
          $page = 0;
          $p='';
          }
        elseif($mode == 'list')  {
          $table = 'news_category'; 
          $supported = true;
        } 
        else $supported = false; 
				 
				if($page > 1)  {
				    $p = '?page='.$page;
				}
        if($supported) {
          $result = $canonicalPlugin->getRelatedCanonicalUrl($table,$id,true, $p);
					if($result === true) {      
					  return;
					}
				}
			}
		}
		elseif (e_PAGE == "page.php")
		{
      if($pluginPrefs['page_auto'] != "none") {
          //autogeneration
          $canonicalPlugin->getAutogeneratedCanonicalUrl($table, $pluginPrefs['page_auto'], $pluginPrefs['backslash']);
  			  return;
      }
      
      if($_GET['bk']) {   // prepared for books
             
      }
      elseif($_GET['ch']) {  // prepared for chapters
      
      }
      elseif($_GET['id']) {
         $table = 'page';
         $id = $_GET['id'];
 
              
  			 $result = $canonicalPlugin->getRelatedCanonicalUrl('page',$id, true);
  		   if($result === true) {      
  					return;
  		 	}		    
      }
		}
    /**********************  FORUM PLUGIN **********************************************/
    /**********************  FORUM THREAD **********************************************/    
		elseif ((strpos(e_PAGE, 'forum_viewtopic.php') !== false))
		{
      if($pluginPrefs['forum_thread_auto'] != "none") {
          //autogeneration
          $canonicalPlugin->getAutogeneratedCanonicalUrl($table, $pluginPrefs['forum_thread_auto'], $pluginPrefs['backslash']);
  			  return;
      }			
      
      if($_GET['id']) {
        $id  =  $_GET['id'];
        $page  = $_GET['p'];

				if($page > 1)  {
				    $p = '&p='.$page;
				}
				else $p = '';
				
        $result = $canonicalPlugin->getRelatedCanonicalUrl('forum_thread',$id,true, $p);
				if($result === true) {      
					return;
				}
			}     
    }
		elseif (e_PAGE ==  'content.php') {
       if (strpos(e_QUERY, ".") !== false) {
       list($mode, $id) = explode(".", e_QUERY); 
			  if ($row = e107::getDb()->retrieve("canonical", "can_url, can_redirect", "can_table='pcontent' AND can_pid=" . $id))
				{
						$canonicalurl=$row['can_url'];
            if($row['can_redirect'])  {
              e107::redirect($canonicalurl);
              exit;
            }
            $type = "from pcontent related urls table";
						echo '<link rel="canonical" href="' . $canonicalurl . '" />';
						echo "\n";
				} 
        else { 
	   		   $canonicalurl = SITEURL.e_URL_LEGACY;
            $type= "Canonical URL from content plugin:  " . $canonicalurl . "<br />"; 
	   		 	 e107::link(array('rel'=>"canonical", "href" =>$canonicalurl, 'type'=> 'canonical2' ));
	        }

      }
    }    
    /**********************  DOWNLOAD PLUGIN **********************************************/
    /*********************** LIST OF DOWNLOADS = CATEGORY  ********************************/
    /*********************** DOWNLOAD VIEW ************************************************/
 
  	elseif ((strpos(e_PAGE, 'download.php') !== false) && e_QUERY != '') {
        
        if($pluginPrefs['download_auto'] != "none") {
          //autogeneration
          $canonicalPlugin->getAutogeneratedCanonicalUrl($table, $pluginPrefs['download_auto'], $pluginPrefs['backslash']);
  			  return;
        }		
        if($_GET['action'] == 'list') {
             $table = 'download_category';		
             $id  =  $_GET['id'];
             $p = intval($_GET['from'] / $_GET['view']) +  1;
        }
        elseif($_GET['action'] == 'view') {
             $table = 'download';		
             $id  =  $_GET['id'];
             $p = $_GET['from'];               
        }
 			 
        if (!empty($id) )
		  	{
          $result = $canonicalPlugin->getRelatedCanonicalUrl($table,$id, true, $p);
  				if($result === true) {      
  					return;
			 	}
			}         	
   	}    
    $canonicalPlugin->renderDebugInfo($canonicalurl,  $type );
	}
     



 