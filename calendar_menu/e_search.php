<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *	Search shim for event calendar
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/calendar_menu/e_search.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */
 
/**
 *	e107 Event calendar plugin
 *
 *	Search shim for event calendar
 *
 *	@package	e107_plugins
 *	@subpackage	event_calendar
 *	@version 	$Id$;
 */

if (!defined('e107_INIT')) { exit(); }

include_lan(e_PLUGIN.'calendar_menu/languages/'.e_LANGUAGE.'_search.php');

$search_info[] = array('sfile' => e_PLUGIN.'calendar_menu/search/search_parser.php', 'qtype' => CM_SCH_LAN_1, 'refpage' => 'calendar.php');

