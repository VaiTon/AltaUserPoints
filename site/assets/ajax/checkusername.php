<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
define("_JEXEC", 1);
defined('_JEXEC') or die('Restricted access');

if (stristr( $_SERVER['SERVER_SOFTWARE'], 'win32' )) {
	define( 'JPATH_BASE', realpath(dirname(__FILE__).'\..\..\..\..' ));
} else define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../..' ));


require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );
$app = JFactory::getApplication('site');
$app->initialise();

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.environment.uri' );

$lang = JFactory::getLanguage();
$lang->load( 'com_altauserpoints', JPATH_SITE);	

$username = JFactory::getApplication()->input->get( 'n', '', 'username' );
$username = str_replace("'", "", $username);

$baseimg = str_replace('ajax', 'images', JURI::base());

$url_tick_img = $baseimg . 'tick.png';
$url_load_img = $baseimg . 'loader.gif';

if ( strlen($username)>3  ) 
	{
	$db	   = JFactory::getDBO();	
	$q = "SELECT id FROM #__users WHERE `username`=".$db->quote(trim($username))." LIMIT 1";
	$db->setQuery( $q );
	$userexist = $db->loadResult();
	
	if( $userexist )
		{
		echo '<img src="'.$url_tick_img.'" alt="" align="absmiddle" />';
		}
		else
		{
		echo '<font color="red">'.JText::_( 'AUP_THIS_USERNAME_NOT_EXIST' ).'</font>';
		}
	}
	else 
	{	
	echo '<img src="'.$url_load_img.'" alt="" align="absmiddle" /> ';
	}
?>
