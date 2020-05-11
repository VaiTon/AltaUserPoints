<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2015-2016. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAltaUserPointsMostReferralUsersHelper {

	public static function getList($params) {

		$db			      = JFactory::getDBO();
	
		$count		      = intval($params->get('count', 5));
		$usrname		  = trim($params->get('usrname', 'name'));

		//referrees
		$query = "SELECT aup.referreid, aup.referrees, u.".$usrname." AS usrname, aup.userid"
				." FROM #__alpha_userpoints AS aup, #__users AS u"
				." WHERE aup.userid=u.id AND aup.published='1'"
				." ORDER BY aup.referrees DESC"
				." LIMIT $count"
				;
		$db->setQuery($query);
		$rows = $db->loadObjectList();		
	
		return $rows;	
	}
}
?>