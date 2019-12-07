<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * BBCode template for calendar menu (pretend we're custom page)
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/calendar_menu/e_bb.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */

/**
 *	e107 Event calendar plugin
 *
 * BBCode template
 *
 *	@package	e107_plugins
 *	@subpackage	event_calendar
 *	@version 	$Id$;
 */

$temp['event'] = "
	{BB_HELP=ec_event}<br />
	{BB=link}{BB=b}{BB=i}{BB=u}{BB=img}{BB=center}{BB=left}{BB=right}
	{BB=bq}{BB=list}{BB=fontcol}{BB=fontsize}{BB=emotes}
	{BB_PREIMAGEDIR=".e_IMAGE."newspost_images/}
	{BB=preimage}{BB=prefile}
";



?>