<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class AltauserpointsModelLatestactivity extends JmodelLegacy {

	function __construct(){
		parent::__construct();
		
	}

	function getLatestActivity()//$params
	{
		$app = JFactory::getApplication();
		$db			      = JFactory::getDBO();
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSql();
		
		$format = $app->input->get('format');
		
		
		$menus 	= $app->getMenu();
		$menu   = $menus->getActive();
		$menuid = $menu->id;
		$params = $menus->getParams($menuid);
		$rsscount = $params->get('count' ); // rss feed limt

		if($format=='feed' && (!$menuid || !$rsscount) )
		{ 
			$msg = 'Missing or incorect Itemid for Latest Activity Feed ! Aborting for security reason.';
			$app->redirect('index.php', $msg , 'warning');
		}
		
		$activity   = $params->get('activity', 0);
		
		$typeActivity = "";
		
		if ( $activity == 1 )			
			$typeActivity = " AND a.points >= 1";
		elseif ( $activity == 2 ) 
			$typeActivity = " AND a.points <= 0";		

		if($format=='feed')
			$limit = $rsscount;
		else
			$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		
		$limitstart = $app->input->get('limitstart', 0, 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);		
		$q = "SELECT a.insert_date, a.referreid, a.points AS last_points, a.datareference, 
		u.".$params->get('usrname', 'username')." AS usrname, 
		r.rule_name, r.plugin_function, r.category"
		. " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
		. " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND r.displayactivity='1' AND aup.published='1' 
		AND a.approved='1' AND a.enabled='1' 
		AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule"
			   . $typeActivity
			   . " ORDER BY a.insert_date DESC";
		$total = @$this->_getListCount($q);
		$result = $this->_getList($q, $limitstart, $limit);
		return array($result, $total, $limit, $limitstart);
	
	}	

}
?>