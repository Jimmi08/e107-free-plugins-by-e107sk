<?php

/**
 * @file
 * Contains callback functions for global tokens.
 */

/**
 * Returns with Site Name.
 */
function metatag_global_token_site_name()
{
	return defset('SITENAME', '');
}

/**
 * Returns with Site Tagline.
 */
function metatag_global_token_site_tagline()
{
	return defset('SITETAG', '');
}


/**
 * Returns with Site Description.
 */
function metatag_global_token_site_description()
{
	$tp = e107::getParser();       
	$tag = e107::getPref('meta_description');
	//$desc = defset('SITEDESCRIPTION', '');   
	$desc = varset($tag[e_LANGUAGE], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$meta  = trim(preg_replace('/\s+/', ' ', $desc));      
	return $meta ;
}

/**
 * Returns with Site Keywords.
 */
function metatag_global_token_site_keywords()
{
  $tag = e107::getPref('meta_keywords');
  $meta = varset($tag[e_LANGUAGE], '');
	// Removes tags.
	return $meta ;
}

/**
 * Returns with Site Buttons.
 */
function metatag_global_token_site_image()
{
  $meta = (strpos(e107::getPref('sitebutton'),'{e_MEDIA') !== false) ? e107::getParser()->thumbUrl($pref['sitebutton'],'w=0&h=0',false, true) : e107::getParser()->replaceConstants($pref['sitebutton'],'full');
 
	// Removes tags.
	return $meta ;
}

 
/**
 * Returns with Site Email.
 */
function metatag_global_token_site_email()
{
	return defset('SITEEMAIL', '');
}

/**
 * Returns with Site URL.
 */
function metatag_global_token_site_url()
{
	return defset('SITEURL', '');
}

/**
 * Returns with login URL.
 */
function metatag_global_token_site_login_url()
{
	return defset('e_LOGIN', '');
}

/**
 * Returns with signup URL.
 */
function metatag_global_token_site_signup_url()
{
	return defset('e_SIGNUP', '');
}

/**
 * Returns with e_LAN.
 */
function metatag_global_token_site_lan()
{
	return defset('e_LAN', '');
}

/**
 * Returns with e_LANCODE.
 */
function metatag_global_token_site_lancode()
{
	return defset('e_LANCODE', '');
}

/**
 * Returns with e_LANGUAGE.
 */
function metatag_global_token_site_language()
{
	return defset('e_LANGUAGE', '');
}

/**
 * Returns with the facebook App ID.
 */
function metatag_global_token_site_fb_app_id()
{
	if(e107::isInstalled('social'))
	{
		$social = e107::pref('core', 'social_login');

		if(!empty($social) && is_array($social))
		{
			return varset($social['Facebook']['keys']['id'], '');
		}
	}

	return '';
}

/**
 * Returns with Site Admin Name.
 */
function metatag_global_token_site_admin_name()
{
	return defset('SITEADMIN', '');
}

/**
 * Returns with Site Admin Email.
 */
function metatag_global_token_site_admin_email()
{
	return defset('SITEADMINEMAIL', '');
}

/**
 * Returns with the current page title.
 */
function metatag_global_token_site_current_page_title()
{
	$page_name = defset('PAGE_NAME', '');
	return defset('e_PAGETITLE', $page_name);
}

/**
 * Returns with the current URL.
 */
function metatag_global_token_site_current_page_url()
{
	return defset('e_SELF', '');
}