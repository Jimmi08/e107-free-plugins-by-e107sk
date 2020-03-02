<?php

if (!defined('e107_INIT'))
{
	exit;
}


function OpenTable() {
 
    $text = '<table><tbody>';
    return $text;
}

function CloseTable() {
 
    $text = '</tbody></table>';
    return $text;
}

/*********************************************************/
/* Links Modified Web Links                              */
/*********************************************************/

function getparent($parentid,$title) {
	$sql = e107::getDb();
	$title = stripslashes(check_html($title, "nohtml"));
	$parentid = intval($parentid);
	$result = $sql->gen("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." WHERE cid='".$parentid."'");
	$row = $sql->fetch($result);
	$cid = $row['cid'];
	$ptitle = $row['title'];
	$pparentid = $row['parentid'];
 
	if ($ptitle != "") $title = $ptitle."/".$title;
		if ($pparentid != 0) {
			$title = getparent($pparentid,$title);
		}
	return $title;
}


// save time, move to class complicates things 

function LinksLinkCheck() {
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._VALIDATELINKS;
 	$sql = e107::getDb();
 
	$content .= OpenTable();
	$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
	$content .= CloseTable();
	$content .= "<br>";
    $content .=	OpenTable();
	$content .= "<div class='center'><font class=\"option\"><b>"._LINKVALIDATION."</b></font></div><br>"
	."<table width=\"100%\" align=\"center\"><tr><td colspan=\"2\" align=\"center\">"
	."<a href=\"".UN_FILENAME_ADMIN."?op=LinksValidate&amp;cid=0&amp;sid=0\">"._CHECKALLLINKS."</a><br><br></td></tr>"
	."<tr><td valign=\"top\"><div class='center'><b>"._CHECKCATEGORIES."</b><br>"._INCLUDESUBCATEGORIES."<br><br><font class=\"tiny\">";
	$result = $sql->retrieve("SELECT cid, title FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY title", true);
        foreach($result AS $row) {
			$cid = $row['cid'];
			$title = $row['title'];
			$transfertitle = str_replace (" ", "_", $title);
			$content .="<a href=\"".UN_FILENAME_ADMIN."?op=LinksValidate&amp;cid=".$cid."&amp;sid=0&amp;ttitle=".$transfertitle."\">".$title."</a><br>";
		}
 
	$content .= "</font></div></td></tr></table>";
	$content .= CloseTable();
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}


function LinksValidate() {
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._CLEANLINKSDB;
 	$sql = e107::getDb();   
    /* 
      $fl = e107::getFile();  $fp =  $fl->isValidURL($row['url']);
      - it doesn't return code to display reason and it uses get_headers without @
    */
	$content = OpenTable();
	$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
	$content .= CloseTable();
	$content .= "<br>";
	$content .= OpenTable();
	$cid = intval($cid);
	$sid = intval($sid);
	$ttitle = stripslashes(check_html($ttitle, "nohtml"));
	$transfertitle = str_replace ("_", "", $ttitle);
	/* Check ALL Links */
	$content .="<table width=\"100%\" border=\"0\">";
		if ($cid==0 && $sid==0) {
			$content .="<tr><td colspan=\"3\"><div class='center'><b>"._CHECKALLLINKS."</b><br>"._BEPATIENT."</div><br><br></td></tr>";
			$result = $sql->retrieve("SELECT lid, title, url FROM #".UN_TABLENAME_LINKS_LINKS." ORDER BY title", true);
		}
		/* Check Categories & Subcategories */
		if ($cid!=0 && $sid==0) {
			$content .="<tr><td colspan=\"3\"><div class='center'><b>"._VALIDATINGCAT.": ".$transfertitle."</b><br>"._BEPATIENT."</div><br><br></td></tr>";
			$result = $sql->retrieve("SELECT lid, title, url FROM #".UN_TABLENAME_LINKS_LINKS." WHERE cid='".$cid."' ORDER BY title", true);
		}
		/* Check Only Subcategory */
		if ($cid==0 && $sid!=0) {
			$content .="<tr><td colspan=\"3\"><div class='center'><b>"._VALIDATINGSUBCAT.": ".$transfertitle."</b><br>"._BEPATIENT."</div><br><br></td></tr>";
			$result = $sql->retrieve("SELECT lid, title, url FROM #".UN_TABLENAME_LINKS_LINKS." WHERE sid='".$sid."' ORDER BY title", true);
		}
	       $content .= "<tr><td bgcolor=\"$bgcolor2\" align=\"center\"><b>".LAN_STATUS."</b></td>
           <td bgcolor=\"".$bgcolor2."\" width=\"100%\"><b>"._LINKTITLE."</b></td><td bgcolor=\"".$bgcolor2."\" align=\"center\"><b>".LAN_OPTIONS."</b></td></tr>";
        foreach($result AS $row) {
			$lid = $row['lid'];
			$title = stripslashes($row['title']);
			$url = stripslashes($row['url']);
			$vurl = parse_url($row['url']);
	 
            $fp = getHttpResponseCode_using_getheaders($url, false);
             
            //convert code to message
            $code = getCode($fp);
 
            if ($fp == '200' ) { 
				$content .="<tr><td align=\"center\">&nbsp;&nbsp;"._OK."&nbsp;&nbsp;</td>"
				."<td>&nbsp;&nbsp;<a href=\"".$url."\" target=\"_blank\">".$title."</a>&nbsp;&nbsp;</td>"
				."<td align=\"center\"><font class=\"content\">&nbsp;&nbsp;"._NONE."&nbsp;&nbsp;</font>"
				."</td></tr>";
		    } 
            else{ 
				$content .="<tr><td align=\"center\"><b>&nbsp;&nbsp;"._FAILED."&nbsp;&nbsp;</b></td>"
				."<td>&nbsp;&nbsp;<a href=\"".$url."\" target=\"_blank\">".$title."</a>&nbsp;&nbsp;".$code." </td>"
				."<td align=\"center\"><font class=\"content\">&nbsp;&nbsp;[ <a href=\"".UN_FILENAME_ADMIN."?op=LinksModLink&amp;lid=".$lid."\">".LAN_EDIT."</a> | <a href=\"".UN_FILENAME_ADMIN."?op=LinksDelLink&amp;lid=".$lid."\">".LAN_DELETE."</a> ]&nbsp;&nbsp;</font>"
				."</td></tr>";
			}		
		}
 
	$content .= "</table>";
	$content .= CloseTable(); 
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}


/**
 * @param $url
 * @param array $options
 * @return string
 * @throws Exception
 */
function getCode($returnedStatusCode) {
 
    // list of HTTP status codes
    $httpStatusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error'
    );
    if (array_key_exists($returnedStatusCode, $httpStatusCodes)) {
        return "Code: {$returnedStatusCode} - Definition: {$httpStatusCodes[$returnedStatusCode]}";
    } else {
        return "Url does not exist";
    } 
    return   $httpStatusCodes[$returnedStatusCode];
 
}
 

    function getHttpResponseCode_using_getheaders($url, $followredirects = false){
        // returns string responsecode, or false if no responsecode found in headers (or url does not exist)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if(! $url || ! is_string($url)){
            return false;
        }
        $headers = @get_headers($url);
        if($headers && is_array($headers)){
 
            if($followredirects){
                // we want the the last errorcode, reverse array so we start at the end:
                $headers = array_reverse($headers);
            }
            foreach($headers as $hline){
                // search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
                // note that the exact syntax/version/output differs, so there is some string magic involved here
                if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
                    $code = $matches[1];
                    return $code;
                }
            }
            // no HTTP/xxx found in headers:
            return false;
        }
        // no headers :
        return false;
    }
    
function LinksCleanVotes() {
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._CLEANLINKSDB;
	$sql = e107::getDb();
	$result = $sql->retrieve("SELECT distinct ratinglid FROM #".UN_TABLENAME_LINKS_VOTEDATA, true); 
    foreach($result AS $row) {   
		$ratinglid = $row['ratinglid'];     
        $voteresult = $sql->retrieve("SELECT rating, ratinguser, ratingcomments FROM #".UN_TABLENAME_LINKS_VOTEDATA." WHERE ratinglid = '".$ratinglid."'", true);
		$totalvotesDB = count($voteresult);
		include ("../voteinclude.php");
		$sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET linkratingsummary='".$finalrating."', totalvotes='".$totalvotesDB."', totalcomments='".$truecomments."' WHERE lid = '".$ratinglid."'");
    }
   $content .= "</table>";
  
  
	$content .= OpenTable();
	$content .= "<br><div class='center'>"
	."<font class=\"option\">"
	._LINKVOTEDCLEANED."<br><br>"
	."[ <a href=\"".UN_FILENAME_ADMIN."?op=Links\">"._WEBLINKSADMIN."</a> ]<br><br>";
	$content .= CloseTable();
    e107::getRender()->tablerender($caption, $content, 'web_links_index');  
 
}

function LinksListBrokenLinks() {
    $sql = e107::getDb();
    $tp = e107::getParser();
    
    $anonymous        = $this->plugPrefs['xanonymous']; 
    
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._BROKENLINKS;
    $content .= OpenTable();
	$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
    $content .= CloseTable();
	$content .= "<br>";
    $content .= OpenTable();
	$result = $sql->retrieve("SELECT requestid, lid, modifysubmitter FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE brokenlink='1' ORDER BY requestid", true);
     
	$totalbrokenlinks = count($result);
	$content .= "<div class='center'><font class=\"option\"><b>"._USERREPBROKEN." (".$totalbrokenlinks.")</b></font></div><br><br><div class='center'>"
	._IGNOREINFO."<br>"
	._DELETEINFO."</div><br><br><br>"
	."<table align=\"center\" width=\"450\">";
		if ($totalbrokenlinks==0) {
			$content .= "<div class='center'><font class=\"option\">"._NOREPORTEDBROKEN."</font></div><br><br><br>";
		} else {
			$colorswitch = $bgcolor2;
			$content .= "<tr>"
			."<td><b>"._LINK."</b></td>"
			."<td><b>"._SUBMITTER."</b></td>"
			."<td><b>"._LINKOWNER."</b></td>"
			."<td><b>".LAN_EDIT."</b></td>"
			."<td><b>"._IGNORE."</b></td>"
			."<td><b>".LAN_DELETE."</b></td>"
			."</tr>";
 
                foreach($result AS $row) {      print_a($row2);
					$requestid = $row['requestid'];
					$lid = $row['lid'];
					$modifysubmitter = $row['modifysubmitter'];
					$row2 = $sql->retrieve("SELECT title, url, submitter FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'", true);
						if ($modifysubmitter != $anonymous) {
							$row3 = $sql->retrieve("SELECT ".UN_TABLENAME_USEREMAIL_ALIAS." FROM #".UN_TABLENAME_USERS." WHERE ".UN_TABLENAME_USERNAME."='".$modifysubmitter."'");
							$email = stripslashes($row3);
						}

					$title = stripslashes($row2[0]['title']);
					$url = $row2[0]['url'];
					//$url2 = urlencode($url);
					$owner = $row2[0]['submitter'];
					$row4 = $sql->retrieve("SELECT ".UN_TABLENAME_USEREMAIL_ALIAS." FROM #".UN_TABLENAME_USERS." WHERE ".UN_TABLENAME_USERNAME."='".$owner."'");
					$owneremail = stripslashes($row4);
					//$url = urlencode($url);
					$content .= "<tr>"
					."<td bgcolor=\"".$colorswitch."\"><a href=\"".$url."\" target='_blank' >".$title."</a>"
					."</td>";
						if ($email=='') {
							$content .= "<td bgcolor=\"".$colorswitch."\">".$modifysubmitter;
						} else {
							$content .= "<td bgcolor=\"".$colorswitch."\"><a href=\"mailto:".$email."\">".$modifysubmitter."</a>";
						}
					$content .= "</td>";
						if ($owneremail=='') {
							$content .= "<td bgcolor=\"".$colorswitch."\">".$owner;
						} else {
							$content .= "<td bgcolor=\"".$colorswitch."\"><a href=\"mailto:".$owneremail."\">".$owner."</a>";
						}
					$content .= "</td>"
					."<td bgcolor=\"".$colorswitch."\"><div class='center'><a href=\"".UN_FILENAME_ADMIN."?op=LinksEditBrokenLinks&amp;lid=".$lid."\">X</a></div>"
					."<td bgcolor=\"".$colorswitch."\"><div class='center'><a href=\"".UN_FILENAME_ADMIN."?op=LinksIgnoreBrokenLinks&amp;lid=".$lid."\">X</a></div>"
					."</td>"
					."<td bgcolor=\"".$colorswitch."\"><div class='center'><a href=\"".UN_FILENAME_ADMIN."?op=LinksDelBrokenLinks&amp;lid=".$lid."\">X</a></div>"
					."</td>"
					."</tr>";
						if ($colorswitch == $bgcolor2) {
							$colorswitch = $bgcolor1;
						} else {
							$colorswitch = $bgcolor2;
						}
				}
		}
 
	$content .= "</table>";
    $content .= CloseTable();
    
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

function LinksListModRequests() {
    $sql = e107::getDb();
    
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '. _USERMODREQUEST;
    
    $content .= OpenTable();
    $content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></div>";
    $content .= CloseTable();

    $content .= "<br>";
    $content .= OpenTable();
    $result = $sql->retrieve("SELECT requestid, lid, cid, sid, title, url, description, modifysubmitter FROM #".UN_TABLENAME_LINKS_MODREQUEST." 
    WHERE brokenlink='0' ORDER BY requestid", true);
     
    $totalmodrequests = count($result);
    $content .= "<div class='center'><font class=\"option\"><b>"._USERMODREQUEST." (".$totalmodrequests.")</b></div><br><br><br>";
    $content .= "<table width=\"95%\"><tr><td>";
      foreach($result AS $row) {
 
        $requestid = $row['requestid'];
        $lid = $row['lid'];
        $cid = $row['cid'];
        $sid = $row['sid'];
        $title = stripslashes($row['title']);
        $url = $row['url'];
        $url = urlencode($url);
        $description = stripslashes($row['description']);
      //	$xdescription = eregix_replace("<a href=\"http://", "<a href=\"index.php?url=http://", $description);
        $xdescription = preg_replace("#<a href=\"http://#i", "<a href=\"index.php?url=http://", $description);			
        $modifysubmitter = $row['modifysubmitter'];
        $rows2 = $sql->retrieve("SELECT cid, sid, title, url, description, submitter FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'", true );  
        $row2 = $rows2[0];           
 
        $origcid = $row2['cid'];
        $origsid = $row2['sid'];
        $origtitle = stripslashes($row2['title']);
        $origurl = $row2['url'];
        $origurl = urlencode($origurl);
        $origdescription = stripslashes($row2['description']);
    //			$xorigdescription = eregix_replace("<a href=\"http://", "<a href=\"index.php?url=http://", $xorigdescription);
        $xorigdescription = preg_replace("#<a href=\"http://#i", "<a href=\"index.php?url=http://", $xorigdescription);
        $owner = $row2['submitter'];

        $row3 = $sql->retrieve("SELECT title FROM #".UN_TABLENAME_LINKS_CATEGORIES." WHERE cid='".$cid."'" );        
        $row5 = $sql->retrieve("SELECT title FROM #".UN_TABLENAME_LINKS_CATEGORIES." WHERE cid='".$origcid."'");
        $row7 = $sql->retrieve("SELECT ".UN_TABLENAME_USEREMAIL_ALIAS." FROM #".UN_TABLENAME_USERS." WHERE ".UN_TABLENAME_USERNAME."='".$modifysubmitter."'");
        $row8 = $sql->retrieve("SELECT ".UN_TABLENAME_USEREMAIL_ALIAS." FROM #".UN_TABLENAME_USERS." WHERE ".UN_TABLENAME_USERNAME."='".$owner."'");
        $cidtitle = stripslashes($row3);
        $origcidtitle = stripslashes($row5);
        $modifysubmitteremail = $row7['user_email'];
        $owneremail = $row8['user_email'];
          if ($owner == "") {
            $owner = _OWNERISADMIN;
          }
          if ($origsidtitle == "") {
            $origsidtitle= "-----";
          }
          if ($sidtitle == "") {
            $sidtitle = "-----";
          }
        $content .=  
         "<table class='table adminlist text-left' >"
        ."<thead><tr>"
        ."<td colspan=2><b>"._ORIGINAL."</b></td></tr></thead>"
        ."<tbody>"
        ."<tr><td><br>".LAN_DESCRIPTION.":</td><td>".$origdescription."</td></tr>"
        ."<tr><td>".LAN_TITLE.":</td><td>".$origtitle."</td></tr>"
        ."<tr><td>".LAN_URL.":</td><td> <a href=\"index.php?url=".$origurl."\">".$origurl."</a></td></tr>"
        ."<tr><td>".LAN_CATEGORY.":</td><td> ".$origcidtitle."</td></tr>"
        ."<tr><td>"._SUBCATEGORY.":</td><td> ".$origsidtitle."</td></tr>"
        ."</table><br>";
        
        $content .=  
         "<table class='table adminlist text-left' >"
        ."<thead><tr>"
        ."<td colspan=2><b>"._PROPOSED."</b></td></tr></thead>"
        ."<tbody>"        
        ."<td><br>".LAN_DESCRIPTION.":</td><td>".$xdescription."</td>"
        ."</tr>"
        ."<tr><td>".LAN_TITLE.":</td><td> ".$title."</td></tr>"
        ."<tr><td>".LAN_URL.":</td><td> <a href=\"index.php?url=".$url."\">".$url."</a></td></tr>"
        ."<tr><td>".LAN_CATEGORY.":</td><td> ".$cidtitle."</td></tr>"
        ."<tr><td>"._SUBCATEGORY.":</td><td> ".$sidtitle."</td></tr>"
        ."</table>"
        
        ."<div class='buttons-bar form-inline'>"
        ."<table align=\"center\" width=\"450\">"
        ."<tr>";
          if ($modifysubmitteremail=="") {
            $content .= "<td align=\"left\">"._SUBMITTER.":  ".$modifysubmitter."</td>";
          } else {
            $content .= "<td align=\"left\">"._SUBMITTER.":  <a href=\"mailto:".$modifysubmitteremail."\">".$modifysubmitter."</a></td>";
          }
          if ($owneremail=="") {
            $content .= "<td align=\"center\">"._OWNER.":  ".$owner."</td>";
          } else {
            $content .= "<td align=\"center\">"._OWNER.": <a href=\"mailto:".$owneremail."\">$owner</a></td>";
          }
        $content .= "<td align=\"right\">( <a class='btn update btn-success' href=\"".UN_FILENAME_ADMIN."?op=LinksChangeModRequests&amp;requestid=".$requestid."\">"._ACCEPT."</a> /
         <a class='btn update btn-warning' href=\"".UN_FILENAME_ADMIN."?op=LinksChangeIgnoreRequests&amp;requestid=".$requestid."\">"._IGNORE."</a> )</td></tr></table></div>";
      }
     
    if ($totalmodrequests == 0) {
      $content .= "<div class='center'>"._NOMODREQUESTS."</div><br><br>";
    }
    $content .= "</td></tr></table>";
    $content .= CloseTable();
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

function LinksEditBrokenLinks($lid) {
    $sql = e107::getDb();
    
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._EZBROKENLINKS;
	$content = OpenTable();
	$content .="<div class='center'><font class=\"option\"><b>"._EZBROKENLINKS."</b></font></div><br><br>";
	$lid = intval($lid);
	$result = $sql->retrieve("SELECT requestid, lid, cid, title, url, description, modifysubmitter FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE brokenlink='1' AND lid='".$lid."'", true);
	$row = $result[0];
    print_a($row);
	$requestid = $row['requestid'];
	$lid = $row['lid'];
	$cid = $row['cid'];
	$title = stripslashes($row['title']);
	$url = $row['url'];
	$url2 = urlencode($url);
	$description = stripslashes($row['description']);
	$modifysubmitter = $row['modifysubmitter'];
	$result2 = $sql->retrieve("SELECT name, email, hits FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'", true);
 
	$row2 = $result2[0];
 
	$name = $row2['name'];
	$email = $row2['email'];
	$hits = $row2['hits'];
	$content .="<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
	."<b>"._LINKID.": ".$lid."</b><br><br>"
	._SUBMITTER.":  ".$modifysubmitter."<br>"
	._PAGETITLE.": <input type=\"text\" name=\"title\" value=\"".$title."\" size=\"50\" maxlength=\"100\"><br>"
	._PAGEURL.": <input type=\"text\" name=\"url\" value=\"".$url."\" size=\"50\" maxlength=\"100\">&nbsp;[ <a  href='.$url.' target=\"_blank\">"._VISIT."</a> ]<br>"
	.LAN_DESCRIPTION.": <br><textarea name=\"description\" id=\"weblinks_link_broken_edit\" cols=\"70\" rows=\"15\">".un_htmlentities($description, ENT_QUOTES)."</textarea><br>"
	.LAN_NAME.": <input type=\"text\" name=\"name\" size=\"20\" maxlength=\"100\" value=\"".$name."\">&nbsp;&nbsp;"
	.LAN_EMAIL.": <input type=\"text\" name=\"email\" size=\"20\" maxlength=\"100\" value=\"".$email."\"><br>";
	$content .="<input type=\"hidden\" name=\"lid\" value=\"".$lid."\">";
	$content .="<input type=\"hidden\" name=\"hits\" value=\"".$hits."\">";
	$content .=_CATEGORY.": <select class='form-control tbox' name=\"cat\">";
	$result = $sql->retrieve("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY title", true);
		foreach($result AS $row) {
			$cid2 = $row['cid'];
			$ctitle2 = $row['title'];
			$parentid2 = $row['parentid'];
			if ($cid2==$cid) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
				$content .="<option value=\"".$cid2."\" ".$sel.">".$ctitle2."</option>";
		}
 
	$content .="</select><input type=\"hidden\" name=\"op\" value=\"LinksModLinkS\">
    <input type=\"submit\" value=\""._MODIFY."\"> [ <a href=\"".UN_FILENAME_ADMIN."?op=LinksDelNew&amp;lid=".$lid."\">".LAN_DELETE."</a> ]</form>";
	$content .= CloseTable();
	$content .="<br>";
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

function LinksModLink($lid) {     
    $sql = e107::getDb();
 
	$anonymous        = $this->plugPrefs['xanonymous'];    
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._MODLINK;    
	$lid = intval($lid);
	$result = $sql->retrieve("SELECT cid, title, url, description, name, email, hits FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'", true);
	$content = OpenTable();
	$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
	$content .= CloseTable();
	$content .= "<br>";
	$content .= OpenTable();
	$content .= "<div class='center'><font class=\"option\"><b>"._MODLINK."</b></font></div><br><br>";
    foreach($result AS $row) {
		$cid = $row['cid'];
		$title = stripslashes($row['title']);
		$url = $row['url'];
		$description = stripslashes($row['description']);
		$name = $row['name'];
		$email = $row['email'];
		$hits = $row['hits'];
		$content .= "<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
		._LINKID.": <b>".$lid."</b><br>"
		._PAGETITLE.": <input type=\"text\" name=\"title\" value=\"".$title."\" size=\"50\" maxlength=\"100\"><br>"
		._PAGEURL.": <input type=\"text\" name=\"url\" value=\"".$url."\" size=\"50\" maxlength=\"100\">&nbsp;[ <a href=\"index.php?url=".$url."\">"._VISIT."</a> ]<br>"
		.LAN_DESCRIPTION.":<br><textarea name=\"description\" id=\"weblinks_link_edit\" cols=\"70\" rows=\"15\">".un_htmlentities($description, ENT_QUOTES)."</textarea><br>"
		.LAN_NAME.": <input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\" value=\"".$name."\"><br>"
		.LAN_EMAIL.": <input type=\"text\" name=\"email\" size=\"50\" maxlength=\"100\" value=\"".$email."\"><br>"
		._HITS.": <input type=\"text\" name=\"hits\" value=\"".$hits."\" size=\"12\" maxlength=\"11\"><br>";
		$content .= "<input type=\"hidden\" name=\"lid\" value=\"".$lid."\">"
		.LAN_CATEGORY.": <select class='form-control tbox' name=\"cat\">";
		$result2 = $sql->gen("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY title");
			while($row2 = $sql->fetch($result2)) {
				$cid2 = $row2['cid'];
				$ctitle2 = stripslashes($row2['title']);
				$parentid2 = $row2['parentid'];
					if ($cid2 == $cid) {
						$sel = "selected";
					} else {
						$sel = "";
					}
				if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
				$content .= "<option value=\"".$cid2."\" ".$sel.">".$ctitle2."</option>";
			}
 
		$content .= "</select>"
		."<input type=\"hidden\" name=\"op\" value=\"LinksModLinkS\">"
		."<input type=\"submit\" value=\""._MODIFY."\"> [ <a href=\"".UN_FILENAME_ADMIN."?op=LinksDelLink&amp;lid=".$lid."\">".LAN_DELETE."</a> ]</form><br>";
		$content .= CloseTable();   
		$content .= "<br>";    
		/* Modify or Add Editorial */
		$resulted2 = $sql->retrieve("SELECT adminid, editorialtimestamp, editorialtext, editorialtitle FROM #".UN_TABLENAME_LINKS_EDITORIALS." WHERE linkid='".$lid."'", true);
		$recordexist = count($resulted2);    
                
		$content .= OpenTable();  
		/* if returns 'bad query' status 0 (add editorial) */
			if ($recordexist == 0) {
				$content .= "<div class='center'><font class=\"option\"><b>"._ADDEDITORIAL."</b></font></div><br><br>"
				."<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
				."<input type=\"hidden\" name=\"linkid\" value=\"".$lid."\">"
				._EDITORIALTITLE.":<br><input type=\"text\" name=\"editorialtitle\" value=\"".$editorialtitle."\" size=\"50\" maxlength=\"100\"><br>"
				._EDITORIALTEXT.":<br><textarea name=\"editorialtext\" id=\"weblinks_editorial_new\" cols=\"70\" rows=\"15\">".un_htmlentities($editorialtext, ENT_QUOTES)."</textarea><br>"
				."</select><input type=\"hidden\" name=\"op\" value=\"LinksAddEditorial\"><input type=\"submit\" value=\"Add\">";
			} else {
				/* if returns 'cool' then status 1 (modify editorial) */
                foreach($resulted2 AS $row3) {
					$editorialtimestamp = $row3['editorialtimestamp'];
					$editorialtext = stripslashes($row3['editorialtext']);
					$editorialtitle = stripslashes($row3['editorialtitle']);
					//eregx ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $editorialtimestamp, $editorialtime);
					preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#i", $editorialtimestamp, $editorialtime);					
					$editorialtime = strftime("%F",mktime($editorialtime[4],$editorialtime[5],$editorialtime[6],$editorialtime[2],$editorialtime[3],$editorialtime[1]));
					$date_array = explode("-", $editorialtime); 
					$timestamp = mktime(0, 0, 0, $date_array['1'], $date_array['2'], $date_array['0']); 
					$formatted_date = date("F j, Y", $timestamp);         	
					$content .= "<div class='center'><font class=\"option\"><b>"._WLMODEDITORIAL."</b></font></div><br><br>"
					."<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
					._AUTHOR.": ".$adminid."<br>"
					._DATEWRITTEN.": ".$formatted_date."<br><br>"
					."<input type=\"hidden\" name=\"linkid\" value=\"".$lid."\">"
					._EDITORIALTITLE.":<br><input type=\"text\" name=\"editorialtitle\" value=\"".$editorialtitle."\" size=\"50\" maxlength=\"100\"><br>"
					._EDITORIALTEXT.":<br><textarea name=\"editorialtext\" cols=\"70\" id=\"weblinks_editorial_edit\" rows=\"15\">".un_htmlentities($editorialtext, ENT_QUOTES)."</textarea><br>"
					."</select><input type=\"hidden\" name=\"op\" value=\"LinksModEditorial\"><input type=\"submit\" value=\""._MODIFY."\"> [ <a href=\"".UN_FILENAME_ADMIN."?op=LinksDelEditorial&amp;linkid=".$lid."\">".LAN_DELETE."</a> ]";
				}
			}
 
		$content .= CloseTable();    
		$content .= "<br>";
		$content .= OpenTable();       
		/* Show Comments */
		$result4 = $sql->retrieve("SELECT ratingdbid, ratinguser, ratingcomments, ratingtimestamp FROM #".UN_TABLENAME_LINKS_VOTEDATA." 
        WHERE ratinglid = '".$lid."' AND ratingcomments <> '' ORDER BY ratingtimestamp DESC", true);
		$totalcomments = count($result4);
		$content .= "<table width=\"100%\">";    
		$content .= "<tr><td colspan=\"7\"><b>"._WEBLINKCOMMENTS." ("._WEBLINKCOMMENTSTOTAL." ".$totalcomments.")</b><br><br></td></tr>";    
		$content .= "<tr><td width=\"20\" colspan=\"1\"><b>"._WEBLINKCOMMENTSUSER."  </b></td><td colspan=\"5\"><b>"._WEBLINKCOMMENTSUSERCOM."  </b></td><td><b><div class='center'>"._WEBLINKCOMMENTSUSERDEL."</div></b></td></tr>";
		if ($totalcomments == 0) $content .= "<tr><td colspan=\"7\"><div class='center'><font color=\"#cccccc\">"._WEBLINKCOMMENTNOCOM."<br></font></div></td></tr>";
		$x=0;
		$colorswitch = "#dddddd";
            foreach($result4 AS $row4) {
				$ratingdbid = $row4['ratingdbid'];
				$ratinguser = $row4['ratinguser'];
				$ratingcomments = stripslashes($row4['ratingcomments']);
				$ratingtimestamp = $row4['ratingtimestamp'];
				//eregx ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $ratingtimestamp, $ratingtime);
				preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#i", $ratingtimestamp, $ratingtime);				
				$ratingtime = strftime("%F",mktime($ratingtime[4],$ratingtime[5],$ratingtime[6],$ratingtime[2],$ratingtime[3],$ratingtime[1]));
				$date_array = explode("-", $ratingtime); 
				$timestamp = mktime(0, 0, 0, $date_array['1'], $date_array['2'], $date_array['0']); 
				$formatted_date = date("F j, Y", $timestamp);
				$content .= "<tr><td bgcolor=\"".$colorswitch."\">".$ratinguser."</td><td colspan=\"5\" bgcolor=\"".$colorswitch."\">".$ratingcomments."</td><td bgcolor=\"".$colorswitch."\"><div class='center'><b><a href=\"".UN_FILENAME_ADMIN."?op=LinksDelComment&amp;lid=".$lid."&amp;rid=".$ratingdbid."\">X</a></b></div></td></tr>";                       
				$x++;
				if ($colorswitch=="#dddddd") { $colorswitch="#ffffff"; } else { $colorswitch="#dddddd"; }
			}
 
		// Show Registered Users Votes
		$result5 = $sql->retrieve("SELECT ratingdbid, ratinguser, rating, ratinghostname, ratingtimestamp FROM #".UN_TABLENAME_LINKS_VOTEDATA." 
        WHERE ratinglid = '".$lid."' AND ratinguser <> 'outside' AND ratinguser <> '".$anonymous."' ORDER BY ratingtimestamp DESC", true);
		$totalvotes = count($result5);
		$content .= "<tr><td colspan=\"7\"><br><br><b>"._WEBLINKREGUSERVOTES." ("._WEBLINKTOTALVOTES." ".$totalvotes.")</b><br><br></td></tr>";
		$content .= "<tr><td><b>"._WEBLINKCOMMENTSUSER."  </b></td><td><b>"._WEBLINKVOTESIPADDR."  </b></td><td><b>"._WEBLINKVOTERATING."  </b></td><td><b>"._WEBLINKVOTEAVGRATING."  </b></td><td><b>"._WEBLINKVOTETOTALRATING."  </b></td><td><b>"._WEBLINKVOTEDATE."  </b></td></font></b><td><b><div class='center'>"._WEBLINKCOMMENTSUSERDEL."</div></b></td></tr>";
		if ($totalvotes == 0) $content .= "<tr><td colspan=\"7\"><div class='center'><font color=\"#cccccc\">"._WEBLINKVOTEREGVOTES."<br></font></div></td></tr>";
		$x=0;
		$colorswitch="#dddddd";
			foreach($result5 AS $row5) {
				$ratingdbid = $row5['ratingdbid'];
				$ratinguser = $row5['ratinguser'];
				$rating = $row5['rating'];
				$ratinghostname = $row5['ratinghostname'];
				$ratingtimestamp = $row5['ratingtimestamp'];
				//eregx ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $ratingtimestamp, $ratingtime);
				preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#i", $ratingtimestamp, $ratingtime);				
				$ratingtime = strftime("%F",mktime($ratingtime[4],$ratingtime[5],$ratingtime[6],$ratingtime[2],$ratingtime[3],$ratingtime[1]));
				$date_array = explode("-", $ratingtime); 
				$timestamp = mktime(0, 0, 0, $date_array['1'], $date_array['2'], $date_array['0']); 
				$formatted_date = date("F j, Y", $timestamp); 
				//Individual user information
				$result6 = $sql->retrieve("SELECT rating FROM #".UN_TABLENAME_LINKS_VOTEDATA." WHERE ratinguser = '".$ratinguser."'", true);
				$usertotalcomments = count($result6);
				$useravgrating = 0;
                foreach($result6 AS $row6)    {  $useravgrating = $useravgrating + $rating2;     }
				$useravgrating = $useravgrating / $usertotalcomments;
				$useravgrating = number_format($useravgrating, 1);
				$content .= "<tr><td bgcolor=\"".$colorswitch."\">".$ratinguser."</td><td bgcolor=\"".$colorswitch."\">".$ratinghostname."</td><td bgcolor=\"".$colorswitch."\">".$rating."</td><td bgcolor=\"".$colorswitch."\">".$useravgrating."</td><td bgcolor=\"".$colorswitch."\">".$usertotalcomments."</td><td bgcolor=\"".$colorswitch."\">".$formatted_date."  </font></b></td><td bgcolor=\"".$colorswitch."\"><div class='center'><b><a href=\"".UN_FILENAME_ADMIN."?op=LinksDelVote&amp;lid=".$lid."&amp;rid=".$ratingdbid."\">X</a></b></div></td></tr><br>";
				$x++;
				if ($colorswitch=="#dddddd") { $colorswitch="#ffffff"; } else { $colorswitch="#dddddd"; }
			}
           
		// Show Unregistered Users Votes
		$result7 = $sql->retrieve("SELECT ratingdbid, rating, ratinghostname, ratingtimestamp FROM #".UN_TABLENAME_LINKS_VOTEDATA." 
        WHERE ratinglid = '".$lid."' AND ratinguser = '".$anonymous."' ORDER BY ratingtimestamp DESC", true);
		$totalvotes = count($result7);
		$content .= "<tr><td colspan=\"7\"><b><br><br>"._WEBLINKUNREGUSERVOTES." ("._WEBLINKTOTALVOTES." ".$totalvotes.")</b><br><br></td></tr>";
		$content .= "<tr><td colspan=\"2\"><b>"._WEBLINKVOTESIPADDR."  </b></td><td colspan=\"3\"><b>"._WEBLINKVOTERATING."  </b></td><td><b>"._WEBLINKVOTEDATE."  </b></font></td><td><b><div class='center'>"._WEBLINKCOMMENTSUSERDEL."</div></b></td></tr>";
		if ($totalvotes == 0) $content .= "<tr><td colspan=\"7\"><div class='center'><font color=\"#cccccc\">"._WEBLINKVOTEUNREGVOTES."<br></font></div></td></tr>";
		$x=0;
		$colorswitch="#dddddd";
            foreach($result7 AS $row7) { 
				$ratingdbid = $row7['ratingdbid'];
				$rating = $row7['rating'];
				$ratinghostname = $row7['ratinghostname'];
				$ratingtimestamp = $row7['ratingtimestamp'];
				//eregx ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $ratingtimestamp, $ratingtime);
				preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#i", $ratingtimestamp, $ratingtime);				
				$ratingtime = strftime("%F",mktime($ratingtime[4],$ratingtime[5],$ratingtime[6],$ratingtime[2],$ratingtime[3],$ratingtime[1]));
				$date_array = explode("-", $ratingtime); 
				$timestamp = mktime(0, 0, 0, $date_array['1'], $date_array['2'], $date_array['0']); 
				$formatted_date = date("F j, Y", $timestamp); 
				$content .= "<td colspan=\"2\" bgcolor=\"".$colorswitch."\">".$ratinghostname."</td><td colspan=\"3\" bgcolor=\"".$colorswitch."\">".$rating."</td><td bgcolor=\"".$colorswitch."\">".$formatted_date."  </font></b></td><td bgcolor=\"".$colorswitch."\"><div class='center'><b><a href=\"".UN_FILENAME_ADMIN."?op=LinksDelVote&amp;lid=".$lid."&amp;rid=".$ratingdbid."\">X</a></b></div></td></tr><br>";           
				$x++;
				if ($colorswitch=="#dddddd") { $colorswitch="#ffffff"; } else { $colorswitch="#dddddd"; }
			}
 
		// Show Outside Users Votes
		$result8 = $sql->retrieve("SELECT ratingdbid, rating, ratinghostname, ratingtimestamp FROM #".UN_TABLENAME_LINKS_VOTEDATA." 
        WHERE ratinglid = '".$lid."' AND ratinguser = 'outside' ORDER BY ratingtimestamp DESC", true );
		$totalvotes = count($result8);
		$content .= "<tr><td colspan=\"7\"><b><br><br>"._WEBLINKUNOUTUSERVOTES." ("._WEBLINKTOTALVOTES." ".$totalvotes.")</b><br><br></td></tr>";
		$content .= "<tr><td colspan=\"2\"><b>"._WEBLINKVOTESIPADDR."  </b></td><td colspan=\"3\"><b>"._WEBLINKVOTERATING."  </b></td><td><b>"._WEBLINKVOTEDATE."  </b></td></font></b><td><b><div class='center'>"._WEBLINKCOMMENTSUSERDEL."</div></b></td></tr>";
		if ($totalvotes == 0) $content .= "<tr><td colspan=\"7\"><div class='center'><font color=\"#cccccc\">"._WEBLINKVOTEOUTVOTES." ".$sitename."<br></font></div></td></tr>";
		$x=0;
		$colorswitch="#dddddd";
            foreach($result8 AS $row) { 
				$ratingdbid = $row8['ratingdbid'];
				$rating = $row8['rating'];
				$ratinghostname = $row8['ratinghostname'];
				$ratingtimestamp = $row8['ratingtimestamp'];
				//eregx ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $ratingtimestamp, $ratingtime);
				preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#i", $ratingtimestamp, $ratingtime);				
				$ratingtime = strftime("%F",mktime($ratingtime[4],$ratingtime[5],$ratingtime[6],$ratingtime[2],$ratingtime[3],$ratingtime[1]));
				$date_array = explode("-", $ratingtime); 
				$timestamp = mktime(0, 0, 0, $date_array['1'], $date_array['2'], $date_array['0']); 
				$formatted_date = date("F j, Y", $timestamp); 
				$content .= "<tr><td colspan=\"2\" bgcolor=\"".$colorswitch."\">".$ratinghostname."</td><td colspan=\"3\" bgcolor=\"".$colorswitch."\">".$rating."</td><td bgcolor=\"".$colorswitch."\">".$formatted_date."  </font></b></td><td bgcolor=\"".$colorswitch."\"><div class='center'><b><a href=\"".UN_FILENAME_ADMIN."?op=LinksDelVote&amp;lid=".$lid."&amp;rid=".$ratingdbid."\">X</a></b></div></td></tr><br>";           
				$x++;
				if ($colorswitch=="#dddddd") { $colorswitch="#ffffff"; } else { $colorswitch="#dddddd"; }
			}
        
		$content .= "<tr><td colspan=\"6\"><br></td></tr>";	    
		$content .= "</table>";
	}
 
	$content .= "</form>";
	$content .= CloseTable();
	$content .= "<br>";
      
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}


function LinksModLinkS($lid, $title, $url, $description, $name, $email, $hits, $cat) {      
	$sql = e107::getDb();
	$cat = explode("-", $cat);
		if ($cat[1] == "") {
			$cat[1] = 0;
		}
	$lid = intval($lid);
	$title = e107::getParser()->toDb($title);
	$url = e107::getParser()->toDb($url);
	$description = e107::getParser()->toDb($description);
	$name = e107::getParser()->toDb($name);
	$email = e107::getParser()->toDb($email);
	$hits = intval($hits);
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET cid='".$cat[0]."', sid='".$cat[1]."', title='".$title."', url='".$url."', description='".$description."', name='".$name."', email='".$email."', hits='".$hits."' WHERE lid='".$lid."'");
 
    $query = "SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."'";
	$result = $sql->gen($query);
	$row = $sql->fetch($result);
    $numrows = $result['numrows'];
	if ($numrows>0) {
		$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."'");
	}     
	Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
}

function LinksAddEditorial($linkid, $editorialtitle, $editorialtext) {
    $sql = e107::getDb();
    $aid = USERID;
	$linkid = intval($linkid);
	$editorialtitle = addslashes($editorialtitle);
	$editorialtext = e107::getParser()->toDb($editorialtext);
	$sql->gen("INSERT INTO #".UN_TABLENAME_LINKS_EDITORIALS." VALUES ('".$linkid."', '".$aid."', now(), '".$editorialtext."', '".$editorialtitle."')");
 
	$content .= OpenTable();
	$content .= "<div class='center'><br>"
	."<font class=\"option\">"
	._EDITORIALADDED."<br><br>"
	."[ <a href=\"".UN_FILENAME_ADMIN."?op=Links\">"._WEBLINKSADMIN."</a> ]<br><br>";
	$content .= $linkid."  ".$adminid.", ".$editorialtitle.", ".$editorialtext;
	$content .= CloseTable();
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

function LinksModEditorial($linkid, $editorialtitle, $editorialtext) {
	$sql = e107::getDb();
	$linkid = intval($linkid);
	$editorialtitle = addslashes($editorialtitle);
	$editorialtext = e107::getParser()->toDb($editorialtext);
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_EDITORIALS." SET editorialtext='".$editorialtext."', editorialtitle='".$editorialtitle."' WHERE linkid='".$linkid."'");
 
 
	$content .= OpenTable();
	$content .= "<br><div class='center'>"
	."<font class=\"option\">"
	._EDITORIALMODIFIED."<br><br>"
	."[ <a href=\"".UN_FILENAME_ADMIN."?op=Links\">"._WEBLINKSADMIN."</a> ]<br><br>";
	$content .= CloseTable();
    e107::getRender()->tablerender($caption, $content, 'web_links_index');    
}

function LinksDelEditorial($linkid) {
	$sql = e107::getDb();
	$linkid = intval($linkid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_EDITORIALS." WHERE linkid='".$linkid."'");
 
	$content .= OpenTable();
	$content .= "<br><div class='center'>"
	."<font class=\"option\">"
	._EDITORIALREMOVED."<br><br>"
	."[ <a href=\"".UN_FILENAME_ADMIN."?op=Links\">"._WEBLINKSADMIN."</a> ]<br><br>";
	$content .= CloseTable();
    
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

 
function LinksDelNew($lid) {
	$sql = e107::getDb();
	$lid = intval($lid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_NEWLINK." WHERE lid='".$lid."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
}

function LinksDelBrokenLinks($lid) {
	$sql = e107::getDb();
	$lid = intval($lid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."'");
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=LinksListBrokenLinks");
}


function LinksIgnoreBrokenLinks($lid) {
	$sql = e107::getDb();
	$lid = intval($lid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."' AND brokenlink='1'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=LinksListBrokenLinks");
}

function links() {
    $caption = _WEBLINKSADMIN. ' <span class="fa fa-angle-double-right e-breadcrumb"></span> '._WLINKS;
	$sql = e107::getDb();
 
	$content .= OpenTable();
 
    $content .= "<div class='center'><a href=\"modules.php?name=Web_Links\"><img src=\"".e_PLUGIN."/Web_Links/images/link-logo.gif\" border=\"0\" alt=\"\"></a><br><br>";
 
	$result = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_LINKS);
	$row = $sql->fetch($result);
 
	$numrows = $row['numrows'];
	$content .= "<font class=\"content\">"._THEREARE." <b>".$numrows."</b> "._LINKSINDB."</font></div>";
	$content .= CloseTable();
	$content .= "<br>";
	/* Temporarily 'homeless' links functions (to be revised in admin.php breakup) */
	$result2 = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE brokenlink='1'");
	$row2 = $sql->fetch($result2);
 
	$totalbrokenlinks = $row2['numrows'];
	$result3 = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE brokenlink='0'");
	$row3 = $sql->fetch($result3);
 
	$totalmodrequests = $row3['numrows'];
	$content .= OpenTable();
	$content .= "<div class='center'><font class=\"content\">[ <a href=\"".UN_FILENAME_ADMIN."?op=LinksCleanVotes\">"._CLEANLINKSDB."</a> | "
	."<a href=\"".UN_FILENAME_ADMIN."?op=LinksListBrokenLinks\">"._BROKENLINKSREP." (".$totalbrokenlinks.")</a> | "
	."<a href=\"".UN_FILENAME_ADMIN."?op=LinksListModRequests\">"._LINKMODREQUEST." (".$totalmodrequests.")</a> | "
	."<a href=\"".UN_FILENAME_ADMIN."?op=LinksLinkCheck\">"._VALIDATELINKS."</a> ]</font></div>";
	$content .= CloseTable();
	$content .= "<br>";
	/* List Links waiting for validation */
	$result4 = $sql->retrieve("SELECT lid, cid, sid, title, url, description, name, email, submitter FROM #".UN_TABLENAME_LINKS_NEWLINK." ORDER BY lid", true);
	$numrows = count($result4);
	if ($numrows>0) {
		$content .= OpenTable();
		$content .= "<div class='center'><font class=\"option\"><b>"._LINKSWAITINGVAL."</b></font></div><br><br>";        
		foreach($result4 AS $row4) {
				$lid = $row4['lid'];
				$cid = $row4['cid'];
				$sid = $row4['sid'];
				$title = stripslashes($row4['title']);
				$url = $row4['url'];
				$description = stripslashes($row4['description']);
				$name = $row4['name'];
				$email = $row4['email'];
				$submitter = $row4['submitter'];
				$url2 = urlencode($url);
					if ($submitter == "") {
						$submitter = _NONE;
					}
				$content .= "<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
				."<b>"._LINKID.": ".$lid."</b><br><br>"
				._SUBMITTER.":  ".$submitter."<br>"
				._PAGETITLE.": <input type=\"text\" name=\"title\" value=\"".$title."\" size=\"50\" maxlength=\"100\"><br>"
				._PAGEURL.": <input type=\"text\" name=\"url\" value=\"".$url."\" size=\"50\" maxlength=\"100\">&nbsp;[ <a href=\"index.php?url=".$url2."\" target=\"_blank\">"._VISIT."</a> ]<br>"
				.LAN_DESCRIPTION.": <br><textarea name=\"description\" id=\"weblinks_waiting\" cols=\"70\" rows=\"15\">".un_htmlentities($description, ENT_QUOTES)."</textarea><br>"
				.LAN_NAME.": <input type=\"text\" name=\"name\" size=\"20\" maxlength=\"100\" value=\"".$name."\">&nbsp;&nbsp;"
				.LAN_EMAIL.": <input type=\"text\" name=\"email\" size=\"20\" maxlength=\"100\" value=\"".$email."\"><br>";
				$content .= "<input type=\"hidden\" name=\"new\" value=\"1\">";
				$content .= "<input type=\"hidden\" name=\"lid\" value=\"".$lid."\">";
				$content .= "<input type=\"hidden\" name=\"submitter\" value=\"".$submitter."\">";
				$content .=  _CATEGORY.": <select class='form-control tbox' name=\"cat\">";
				$result5 = $sql->retrieve("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY title", true);
                    foreach($result5 AS $row5) {
						$cid2 = $row5['cid'];
						$ctitle2 = stripslashes($row5['title']);
						$parentid2 = $row5['parentid'];
							if ($cid2 == $cid) {
								$sel = "selected";
							} else {
								$sel = "";
							}
							if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
						$content .= "<option value=\"".$cid2."\" ".$sel.">".$ctitle2."</option>";
					}
 
					$content .= "<input type=\"hidden\" name=\"submitter\" value=\"".$submitter."\">";
					$content .= "</select><input type=\"hidden\" name=\"op\" value=\"LinksAddLink\"><input type=\"submit\" value=\""._ADD."\"> [ <a href=\"".UN_FILENAME_ADMIN."?op=LinksDelNew&amp;lid=".$lid."\">".LAN_DELETE."</a> ]</form><br><hr noshade><br>";
			}
 
	$content .= CloseTable();
	$content .= "<br>";
	}
    $url01 =  UN_FILENAME_ADMIN_FOLDER."admin_links_categories.php?mode=links_categories&action=create";
    $url02 =  UN_FILENAME_ADMIN_FOLDER."admin_links_links.php?mode=links_categories&action=create";
    $content .= '
                  
    <div class="row">                    
        <div class="row">                    
            <div class="col-md-3">                        
                <div class="box-placeholder">                            
                    <a href="'.$url01.'"  class="btn btn-primary btn-block">'._ADD_CATEGORY.'</a>                        
                </div>                    
            </div> 
            <div class="col-md-3">                        
                <div class="box-placeholder">                            
                    <a href="'.$url02.'"  class="btn btn-primary btn-block">'._ADDNEWLINK.'</a>                        
                </div>                    
            </div>                                      
        </div>                
    </div>            
 ';
 
$content .= CloseTable();
$content .= "<br>";
		

	// Modify Category
	$result10 = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_CATEGORIES);
	$row10 = $sql->fetch($result10);
 
	$numrows = $row10['numrows'];
		if ($numrows>0) {
			$content .= OpenTable();
			$content .= "<form method=\"post\" action=\"".UN_FILENAME_ADMIN."\">"
			."<font class=\"option\"><b>"._MODCATEGORY."</b></font><br><br>";
			$result11 = $sql->retrieve("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY title", true);
			$content .= _CATEGORY.": <select class='form-control tbox' name=\"cat\">";
                foreach($result11 AS $row11) {
					$cid2 = $row11['cid'];
					$ctitle2 = stripslashes($row11['title']);
					$parentid2 = $row11['parentid'];
						if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
					$content .= "<option value=\"".$cid2."\">".$ctitle2."</option>";
				}
 
			$content .= "</select>"
			."<input type=\"hidden\" name=\"op\" value=\"LinksModCat\">"
			."<input type=\"submit\" value=\""._MODIFY."\">"
			."</form>";
			$content .= CloseTable();
			$content .= "<br>";
		}
	// Modify Links
	$result12 = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_LINKS);
	$row12 = $sql->fetch($result12);
 
	$numrows = $row12['numrows'];
		if ($numrows>0) {
			$content .= OpenTable();
			$content .= "<form method=\"post\" action=\"".UN_FILENAME_ADMIN."\">"
			."<font class=\"option\"><b>"._MODLINK."</b></font><br><br>"
			._LINKID.": <input type=\"text\" name=\"lid\" size=\"12\" maxlength=\"11\">&nbsp;&nbsp;"
			."<input type=\"hidden\" name=\"op\" value=\"LinksModLink\">"
			."<input type=\"submit\" value=\""._MODIFY."\">"
			."</form>";
			$content .= CloseTable();
			$content .= "<br>";
		}
	// Transfer Categories
	$result13 = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_LINKS);
	$row13 = $sql->fetch($result13);
 
	$numrows = $row13['numrows'];
		if ($numrows>0) {
			$content .= OpenTable();
			$content .= "<form method=\"post\" action=\"".UN_FILENAME_ADMIN."\">"
			."<font class=\"option\"><b>"._EZTRANSFERLINKS."</b></font><br><br>"
			._CATEGORY.": "
			."<select class='form-control tbox' name=\"cidfrom\">";
			$result14 = $sql->retrieve("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY parentid, title", true);
            foreach($result14 AS $row14) {
 
					$cid2 = $row14['cid'];
					$ctitle2 = stripslashes($row14['title']);
					$parentid2 = $row14['parentid'];
					if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
					$content .= "<option value=\"".$cid2."\">".$ctitle2."</option>";
				}
 
			$content .= "</select><br>"
			._IN."&nbsp;"._CATEGORY.": ";
			$result15 = $sql->retrieve("SELECT cid, title, parentid FROM #".UN_TABLENAME_LINKS_CATEGORIES." ORDER BY parentid, title", true);
			$content .= "<select class='form-control tbox' name=\"cidto\">";
				foreach($result15 AS $row15) {
					$cid2 = intval($row15['cid']);
					$ctitle2 = stripslashes($row15['title']);
					$parentid2 = $row15['parentid'];
					if ($parentid2 != 0) $ctitle2 = getparent($parentid2,$ctitle2);
					$content .= "<option value=\"".$cid2."\">".$ctitle2."</option>";
				}
 
			$content .= "</select><br>"
			."<input type=\"hidden\" name=\"op\" value=\"LinksTransfer\">"
			."<input type=\"submit\" value=\""._EZTRANSFER."\"><br>"
			."</form>";
			$content .= CloseTable();
			$content .= "<br>";
		}
 
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
}

function LinksChangeIgnoreRequests($requestid) {
	$sql = e107::getDb();
	$requestid = intval($requestid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE requestid='".$requestid."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=LinksListModRequests");
}

function LinksChangeModRequests($requestid) {  
	$sql = e107::getDb();
    $tp  = e107::getParser();
	$requestid = intval($requestid);
 
	$result = $sql->retrieve("SELECT requestid, lid, cid, sid, title, url, description FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE requestid='".$requestid."'", true);  
 
    foreach($result as $row) {        
		$requestid = $row['requestid'];
		$lid = $row['lid'];
		$cid = $row['cid'];
		$sid = $row['sid'];
		$title = $tp->toDb($row['title']);
		$url = $row['url'];
		$description = stripslashes($row['description']);
      
        $update = array(
        'data' => 
             array(
             'cid' =>  $cid,
             'sid'=>   $sid,
             'title'=>   $title,
             'url'=>  $url,
             'description'=>   $description
             ),
        'WHERE' =>  "lid = ".$lid
        );  
        $sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET cid='".$cid."', sid='".$sid."', title='".$title."', url='".$url."', description='".$description."' WHERE lid = ".$lid."");
       // e107::getDb()->update( UN_TABLENAME_LINKS_LINKS, $update);
    
    }
 
 	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE requestid=".$requestid);
    Header("Location: ".UN_FILENAME_ADMIN."?op=LinksListModRequests");
}


function LinksDelLink($lid) {
	$sql = e107::getDb();
	$lid = intval($lid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_LINKS." WHERE lid='".$lid."'");
	$query = "SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."'";
	$result = $sql->gen($query);
	$row = $sql->fetch($result);
 
	$numrows = $row['numrows'];
		if ($numrows>0) {
			$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_MODREQUEST." WHERE lid='".$lid."'");
		}
	Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
}


function LinksTransfer($cidfrom,$cidto) {
	$sql = e107::getDb();
	$cidfrom = intval($cidfrom);
	$cidto = intval($cidto);
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET cid='".$cidto."' WHERE cid='".$cidfrom."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
}

function LinksDelComment($lid, $rid) {
	$sql = e107::getDb();
	$rid = intval($rid);
	$lid = intval($lid);
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_VOTEDATA." SET ratingcomments='' WHERE ratingdbid = '".$rid."'");
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET totalcomments = totalcomments-1 WHERE lid = '".$lid."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=LinksModLink&lid=".$lid);
}


function LinksDelVote($lid, $rid) {
	$sql = e107::getDb();
	$rid = intval($rid);
	$lid = intval($lid);
	$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_VOTEDATA." WHERE ratingdbid='".$rid."'");
    $voteresult = $sql->retrieve("SELECT rating, ratinguser, ratingcomments FROM #".UN_TABLENAME_LINKS_VOTEDATA." WHERE ratinglid = '".$lid."'", true);
	$totalvotesDB = count($voteresult);
	include ("../voteinclude.php");
	$sql->gen("UPDATE #".UN_TABLENAME_LINKS_LINKS." SET linkratingsummary='".$finalrating."', totalvotes='".$totalvotesDB."', totalcomments='".$truecomments."' WHERE lid = '".$lid."'");
	Header("Location: ".UN_FILENAME_ADMIN."?op=LinksModLink&lid=".$lid);
}


function LinksModCat($cat) {
	$sql = e107::getDb();
 
	$content .= OpenTable();
	$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
	$content .= CloseTable();
	$content .= "<br>";
	$content .= OpenTable();
	$content .= "<div class='center'><font class=\"option\"><b>"._MODCATEGORY."</b></font></div><br><br>";
	$cat = explode("-", $cat);
		if ($cat[1] == "") {
			$cat[1] = 0;
		}
	$result = $sql->gen("SELECT title, cdescription FROM #".UN_TABLENAME_LINKS_CATEGORIES." WHERE cid='".$cat[0]."'");
	$row = $sql->fetch($result);
 
	$title = stripslashes($row['title']);
	$cdescription = stripslashes($row['cdescription']);
	$content .= "<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
	.LAN_NAME.": <input type=\"text\" name=\"title\" value=\"".$title."\" size=\"51\" maxlength=\"50\"><br>"
	.LAN_DESCRIPTION.":<br><textarea name=\"cdescription\" id=\"weblinks_category_edit\" cols=\"70\" rows=\"15\">".un_htmlentities($cdescription, ENT_QUOTES)."</textarea><br>"
	."<input type=\"hidden\" name=\"sub\" value=\"0\">"
	."<input type=\"hidden\" name=\"cid\" value=\"".$cat[0]."\">"
	."<input type=\"hidden\" name=\"op\" value=\"LinksModCatS\">"
	."<table border=\"0\"><tr><td>"
	."<input type=\"submit\" value=\"".LAN_SAVE ."\"></form></td><td>"
	."<form action=\"".UN_FILENAME_ADMIN."\" method=\"post\">"
	."<input type=\"hidden\" name=\"sub\" value=\"0\">"
	."<input type=\"hidden\" name=\"cid\" value=\"".$cat[0]."\">"
	."<input type=\"hidden\" name=\"op\" value=\"LinksDelCat\">"
	."<input type=\"submit\" value=\"".LAN_DELETE."\"></form></td></tr></table>";
	$content .= CloseTable();
    
    e107::getRender()->tablerender($caption, $content, 'web_links_index');
 
}


function LinksModCatS($cid, $sid, $sub, $title, $cdescription) {
	$sql = e107::getDb();
	$cid = intval($cid);
		if ($sub==0) {
			$sql->gen("UPDATE #".UN_TABLENAME_LINKS_CATEGORIES." SET title='".addslashes($title)."', cdescription='".addslashes($cdescription)."' WHERE cid='".$cid."'");
		}
	Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
}


function LinksAddSubCat($cid, $title, $cdescription) {
	$sql = e107::getDb();
	$cid = intval($cid);
	$title = addslashes($title);
	$cdescription = addslashes($cdescription);
	$result = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_CATEGORIES." WHERE title='".$title."' AND cid='".$cid."'");
	$row = $sql->fetch($result);
 
	$numrows =$row['numrows'];
		if ($numrows>0) {
 
 
			$content .= OpenTable();
			$content .= "<br><div class='center'>";
			$content .= "<font class=\"option\">"
			."<b>"._ERRORTHESUBCATEGORY." ".$title." "._ALREADYEXIST."</b><br><br>"
			._GOBACK."<br><br>";
 
		} else {
			$sql->gen("INSERT INTO #".UN_TABLENAME_LINKS_CATEGORIES." VALUES (NULL, '".$title."', '".$cdescription."', '".$cid."')");
			Header("Location: ".UN_FILENAME_ADMIN."?op=Links");
		}
}

// REPLACE THIS TODO
function LinksAddLink($new, $lid, $title, $url, $cat, $description, $name, $email, $submitter) {
	global  $sitename, $nukeurl;
    
    $sql = e107::getDb();
        
	$result = $sql->gen("SELECT COUNT(*) AS numrows FROM #".UN_TABLENAME_LINKS_LINKS." WHERE url='".addslashes($url)."'");
	$row = $sql->fetch($result);
 
	$numrows = $row['numrows'];
	if ($numrows>0) {
 
		$content .= OpenTable();
		$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
		$content .= CloseTable();
		$content .= "<br>";
		$content .= OpenTable();
		$content .= "<br><div class='center'>"
		."<font class=\"option\">"
		."<b>"._ERRORURLEXISTWL."</b></font><br><br>"
		._GOBACK."<br><br>";
	   e107::getRender()->tablerender($caption, $content, 'web_links_index');
	 
	} else {
		/* Check if Title exist */
		if ($title=="") {
 
			$content .= OpenTable();
			$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
			$content .= CloseTable();
			$content .= "<br>";
			$content .= OpenTable();
			$content .= "<br><div class='center'>"
			."<font class=\"option\">"
			."<b>"._ERRORNOTITLEWL."</b></font><br><br>"
			._GOBACK."<br><br>";
			$content .= CloseTable();
		     e107::getRender()->tablerender($caption, $content, 'web_links_index');
		}
		/* Check if URL exist */
		if ($url=="") {
 
			$content .= OpenTable();
			$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
			$content .= CloseTable();
			$content .= "<br>";
			$content .= OpenTable();
			$content .= "<br><div class='center'>"
			."<font class=\"option\">"
			."<b>"._ERRORNOURLWL."</b></font><br><br>"
			._GOBACK."<br><br>";
			$content .= CloseTable();
			 e107::getRender()->tablerender($caption, $content, 'web_links_index');
		}
		// Check if Description exist
		if ($description=="") {
 
			$content .= OpenTable();
			$content .= "<div class='center'><font class=\"title\"><b>"._WEBLINKSADMIN."</b></font></div>";
			$content .= CloseTable();
			$content .= "<br>";
			$content .= OpenTable();
			$content .= "<br><div class='center'>"
			."<font class=\"option\">"
			."<b>"._ERRORNODESCRIPTIONWL."</b></font><br><br>"
			._GOBACK."<br><br>";
			$content .= CloseTable();
			 e107::getRender()->tablerender($caption, $content, 'web_links_index');
		}
	$cat = explode("-", $cat);
		if ($cat[1] == "") {
			$cat[1] = 0;
		}
	$title = e107::getParser()->toDB($title);
	$url = e107::getParser()->toDB($url);
	$description = e107::getParser()->toDB($description);
	$name = e107::getParser()->toDB($name);
	$email = e107::getParser()->toDB($email);
	$sql->gen("INSERT INTO #".UN_TABLENAME_LINKS_LINKS." VALUES (NULL, '".$cat[0]."', '".$cat[1]."', '".$title."', '".$url."', '".$description."', now(), '".$name."', '".$email."', '0', '".$submitter."', '0', '0', '0')");
 
	$content .= OpenTable();
	$content .= "<br><div class='center'>";
	$content .= "<font class=\"option\">";
	$content .= _NEWLINKADDED."</font><br><br>";
	$content .= "[ <a href=\"".UN_FILENAME_ADMIN."?op=Links\">"._WEBLINKSADMIN."</a> ]</div><br><br>";
	$content .= CloseTable();
	if ($new==1) {
		$sql->gen("DELETE FROM #".UN_TABLENAME_LINKS_NEWLINK." WHERE lid='".$lid."'");
		if ($email != "") {
			$subject = _YOURLINKAT." ".$sitename;
			$message = _HELLO." ".$name.":\n\n"._LINKAPPROVEDMSG."\n\n"._LINKTITLE.": ".$title."\n"._URL.": ".$url."\n".LAN_DESCRIPTION.": ".$description."\n\n\n"._YOUCANBROWSEUS." ".$nukeurl."/modules.php?name=Web_Links\n\n"._THANKS4YOURSUBMISSION."\n\n".$sitename." "._TEAM;
			$from = $sitename;
			//un_mail($email, $subject, $message, "From: ".$from."\n");
            //TODO NOTIFY
		}
	}
 
    }
}
 