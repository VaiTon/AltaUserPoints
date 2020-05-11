<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2015-2020. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAltaUserPointsMostActiveUsersHelper {

	public static function getList($params)
	{
		$db			      = JFactory::getDBO();				
		$count		    	= intval($params->get('count', 5));
		$showavatar			= intval($params->get('showavatar', 1));
		$usrname			= trim($params->get('usrname', 'name'));
		$exclude_users		= $params->get('exclude_users',array() );
		$usergroup_filter	= $params->get('usergroup_filter');

		$nullDate		= $db->getNullDate();
		$date 			= JFactory::getDate();
		$now  			= $date->toSql();	
		
		$q = $db->getQuery(true);	
		$q->select('aup.points, u.'.$usrname.' AS usrname, aup.userid, aup.referreid');
		$q->from('#__alpha_userpoints AS aup');
		$q->join('','#__users AS u');
		if($usergroup_filter){
			$q->join('','#__user_usergroup_map AS um ON um.user_id=u.id');
			$q->where('um.group_id IN('.join(',',$usergroup_filter).')');
		}
		$q->where('aup.userid = u.id AND aup.published="1" AND u.block="0" ');
		
		if(count($exclude_users)){
			$q->where('u.id NOT IN('.join(',',$exclude_users).')');	
		}
		
		$q->order('aup.points DESC, u.'.$usrname.' ASC');
		$db->setQuery($q, 0, $count);
		$rows = $db->loadObjectList();
		return $rows;
	}
}
?>