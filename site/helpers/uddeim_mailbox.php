<?php
// ********************************************************************************************
// Title          Module to show mailbox status in udde Instant Messages (uddeIM)
// Description    Instant Messages System for Mambo 4.5 / Joomla 1.0 / Joomla 1.5
// Author         © 2007-208 Stephan Slabihoud
// License        This is free software and you may redistribute it under the GPL.
//                uddeIM comes with absolutely no warranty.
//                Use at your own risk. For details, see the license at
//                http://www.gnu.org/licenses/gpl.txt
//                Other licenses can be found in LICENSES folder.
//                Redistributing this file is only allowed when keeping the header unchanged.
// ********************************************************************************************

if (!(defined('_JEXEC') || defined('_VALID_MOS'))) { die( 'Direct Access to this location is not allowed.' ); }

$uddeim_isadmin = 0;
/*
if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	require_once(JPATH_SITE.'/components/com_uddeim/uddeimlib15.php');
} else {	
	$app = JFactory::getApplication();
	require_once($app->getCfg('absolute_path').'/components/com_uddeim/uddeimlib10.php');
}
*/
$uddpathtoadmin = uddeIMgetPath('admin');
$uddpathtouser  = uddeIMgetPath('user');
$uddpathtosite  = uddeIMgetPath('live_site');
$udddatabase 	= uddeIMgetDatabase();
$uddmosConfig_lang = uddeIMgetLang();

require_once($uddpathtouser.'/crypt.class.php');
require_once($uddpathtoadmin.'/config.class.php');
$uddconfig = new uddeimconfigclass();

if(!defined('_UDDEIM_INBOX')) {
	$uddpostfix = "";
	if ($uddconfig->languagecharset)
		$uddpostfix = ".utf8";
	if (file_exists($uddpathtoadmin.'/language'.$uddpostfix.'/'.$uddmosConfig_lang.'.php')) {
		include_once($uddpathtoadmin.'/language'.$uddpostfix.'/'.$uddmosConfig_lang.'.php');
	} elseif (file_exists($uddpathtoadmin.'/language'.$uddpostfix.'/english.php')) {
		include_once($uddpathtoadmin.'/language'.$uddpostfix.'/english.php');
	} elseif (file_exists($uddpathtoadmin.'/language/english.php')) {
		include_once($uddpathtoadmin.'/language/english.php');
	}
	$GLOBALS['udde_smon'] = $udde_smon;
	$GLOBALS['udde_lmon'] = $udde_lmon;
	$GLOBALS['udde_sweekday'] = $udde_sweekday;
	$GLOBALS['udde_lweekday'] = $udde_lweekday;
}

$uddshownew		= 1;
$uddshowinbox	= 1;
$uddshowoutbox	= 1;
$uddshowtrashcan= 1;
$uddshowarchive	= 1;
$uddshowcontacts= 1;
$uddshowsettings= 0;
$uddshowcompose	= 1;
$uddshowicons	= 1;

if (file_exists($uddpathtouser.'/templates/'.$uddconfig->templatedir.'/css/uddemodule.css')) {
	echo '<link rel="stylesheet" href="'.$uddpathtosite.'/components/com_uddeim/templates/'.$uddconfig->templatedir.'/css/uddemodule.css" type="text/css" />';
} elseif(file_exists($uddpathtouser.'/templates/default/css/uddemodule.css')) {
	echo '<link rel="stylesheet" href="'.$uddpathtosite.'/components/com_uddeim/templates/default/css/uddemodule.css" type="text/css" />';
}

$udduserid    = uddeIMgetUserID();
$uddmygroupid = uddeIMgetGroupID();

if (!$udduserid) {
	echo "<div id='uddeim-module'>";
	echo "<p class='uddeim-module-head'>"._UDDEIM_NOTLOGGEDIN."</p>";
	echo "</div>";
	return;
}

//$uddsql = "SELECT gid FROM #__users WHERE id=".(int)$udduserid;
$uddsql = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=".(int)$udduserid;
$udddatabase->setQuery($uddsql);
$uddmy_gid=(int)$udddatabase->loadResult();

// first try to find a published link
$uddsql = "SELECT id FROM #__menu WHERE link LIKE '%com_uddeim%' AND published=1 AND access".
		($uddmygroupid==0 ? "=" : "<=").$uddmygroupid." LIMIT 1";
$udddatabase->setQuery($uddsql);
$udditem_id = (int)$udddatabase->loadResult();
if (!$udditem_id) {
	// when no published link has been found, try to find an unpublished one
	$uddsql="SELECT id FROM #__menu WHERE link LIKE '%com_uddeim%' AND published=0 AND access".
			($uddmygroupid==0 ? "=" : "<=").$uddmygroupid." LIMIT 1";
	$udddatabase->setQuery($uddsql);
	$udditem_id = (int)$udddatabase->loadResult();
}
if ($uddconfig->overwriteitemid)
	$udditem_id = $uddconfig->useitemid;

$uddout = "<div id='uddeim-module'>";

if ( $uddshownew ) {
	$uddsql="SELECT count(a.id) FROM #__uddeim AS a WHERE a.totrash=0 AND a.toread=0 AND a.toid=".(int)$udduserid;
//	$uddsql="SELECT count(a.id) FROM #__uddeim AS a LEFT JOIN #__users AS b ON a.fromid=b.id WHERE a.totrash=0 AND a.toread=0 AND a.toid=".(int)$udduserid;
	$udddatabase->setQuery($uddsql);
	$uddresult=(int)$udddatabase->loadResult();
	if ($uddresult>0) {
		$uddout .= "<span class='uddeim-module-head'>";
		$uddout .= _UDDEMODULE_NEWMESSAGES." ".$uddresult;
		$uddout .= "</span>&nbsp;&nbsp;";
	}
}

if ( $uddshowinbox ) {
	$uddsql="SELECT count(a.id) FROM #__uddeim AS a WHERE a.totrash=0 AND archived=0 AND a.toid=".(int)$udduserid;
//	$uddsql="SELECT count(a.id) FROM #__uddeim AS a LEFT JOIN #__users AS b ON a.fromid=b.id WHERE a.totrash=0 AND archived=0 AND a.toid=".(int)$udduserid;
	$udddatabase->setQuery($uddsql);
	$uddresult=(int)$udddatabase->loadResult();

	$uddout .= "<span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_inbox.gif' alt='"._UDDEIM_INBOX."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=inbox".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_INBOX.'">';
	$uddout .= _UDDEIM_INBOX.": ".$uddresult;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

if ( $uddshowoutbox ) {
	$uddsql="SELECT count(a.id) FROM #__uddeim AS a WHERE a.totrashoutbox=0 AND ((a.systemmessage IS NULL) OR (a.systemmessage='')) AND a.fromid=".(int)$udduserid;
//	$uddsql="SELECT count(a.id) FROM #__uddeim AS a LEFT JOIN #__users AS b ON a.toid=b.id WHERE a.totrashoutbox=0 AND ((a.systemmessage IS NULL) OR (a.systemmessage='')) AND a.fromid=".(int)$udduserid;
	$udddatabase->setQuery($uddsql);
	$uddresult=(int)$udddatabase->loadResult();

	$uddout .= "  <span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_outbox.gif' alt='"._UDDEIM_OUTBOX."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=outbox".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_OUTBOX.'">';
	$uddout .= _UDDEIM_OUTBOX.": ".$uddresult;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

if ( $uddshowtrashcan ) {
	$uddrightnow=aup_moduddemailboxtime((int)$uddconfig->timezone);
	$uddoffset=((float)$uddconfig->TrashLifespan) * 86400;
	$uddtimeframe=$uddrightnow-$uddoffset;

	$uddsql="SELECT count(id) FROM #__uddeim WHERE (totrashdate>=".$uddtimeframe." AND toid=".(int)$udduserid." AND totrash=1) OR (totrashdateoutbox>=".$uddtimeframe." AND fromid=".(int)$udduserid." AND totrashoutbox=1 AND toid<>".(int)$udduserid." AND ((systemmessage IS NULL) OR (systemmessage='')))";
//	$uddsql="SELECT count(id) FROM #__uddeim WHERE (totrashdate>=".$uddtimeframe." AND toid=".(int)$udduserid." AND totrash=1) OR (totrashdateoutbox>=".$uddtimeframe." AND fromid=".(int)$udduserid." AND totrashoutbox=1 AND toid<>fromid AND ((systemmessage IS NULL) OR (systemmessage='')))";
	$udddatabase->setQuery($uddsql);
	$uddresult=(int)$udddatabase->loadResult();

	$uddout .= "  <span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_trashcan.gif' alt='"._UDDEIM_TRASHCAN."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=trashcan".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_TRASHCAN.'">';
	$uddout .= _UDDEIM_TRASHCAN.": ".$uddresult;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

if ( $uddshowarchive && $uddconfig->allowarchive) {
	$uddsql="SELECT count(a.id) FROM #__uddeim AS a WHERE a.totrash=0 AND archived=1 AND a.toid=".(int)$udduserid;
//	$uddsql="SELECT count(a.id) FROM #__uddeim AS a LEFT JOIN #__users AS b ON a.fromid=b.id WHERE a.totrash=0 AND archived=1 AND a.toid=".(int)$udduserid;
	$udddatabase->setQuery($uddsql);
	$uddresult=(int)$udddatabase->loadResult();

	$uddout .= "  <span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_archive.gif' alt='"._UDDEIM_ARCHIVE."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=archive".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_ARCHIVE.'">';
	$uddout .= _UDDEIM_ARCHIVE.": ".$uddresult;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

if( ($uddconfig->enablelists==1) ||
    ($uddconfig->enablelists==2 && in_array($uddmy_gid,array(19,20,21,23,24,25))) || 
    ($uddconfig->enablelists==3 && in_array($uddmy_gid,array(24,25))) ) {
	// ok contact lists are enabled
	if ( $uddshowcontacts ) {
		$uddout .= "  <span class='uddeim-module-body'>";
		if($uddshowicons)
			$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_book.gif' alt='"._UDDEIM_LISTS."' style='vertical-align:text-bottom;' /> ";
		$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=showlists".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_LISTS.'">';
		$uddout .= _UDDEIM_LISTS;
		$uddout .= '</a>';
		$uddout .= "</span>&nbsp;";
	}
}

if ( $uddshowsettings ) {
	$uddout .= "  <span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_settings.gif' alt='"._UDDEIM_SETTINGS."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=settings".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_SETTINGS.'">';
	$uddout .= _UDDEIM_SETTINGS;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

if ( $uddshowcompose ) {
	$uddout .= "  <span class='uddeim-module-body'>";
	if($uddshowicons)
		$uddout .= "<img src='".$uddpathtosite."/components/com_uddeim/templates/".$uddconfig->templatedir."/images/menu_new.gif' alt='"._UDDEIM_COMPOSE."' style='vertical-align:text-bottom;' /> ";
	$uddout .= '<a href="'.uddeIMsefRelToAbs( "index.php?option=com_uddeim&task=new".($udditem_id ? "&Itemid=".$udditem_id : "") ).'" title="'._UDDEIM_COMPOSE.'">';
	$uddout .= _UDDEIM_COMPOSE;
	$uddout .= '</a>';
	$uddout .= "</span>&nbsp;";
}

$uddout .= "</div>";

echo $uddout;

function aup_moduddemailboxtime($uddtimezone = 0) {
	$uddmosConfig_offset = uddeIMgetOffset();
	$uddrightnow=time()+(($uddmosConfig_offset+$uddtimezone)*3600);
	return $uddrightnow;
}
?>