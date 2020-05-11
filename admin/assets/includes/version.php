<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// num version
if(!defined("_ALTAUSERPOINTS_NUM_VERSION")) {
   DEFINE( "_ALTAUSERPOINTS_NUM_VERSION", "1.1" );
}

function aup_update_db_version () {
	
	$db	= JFactory::getDBO(); 
	// update table version
	$q = "UPDATE #__alpha_userpoints_version SET `version`='"._ALTAUSERPOINTS_NUM_VERSION."' WHERE 1";
	$db->setQuery( $q );
	$db->execute();

}
?>