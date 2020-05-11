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
jimport('joomla.plugin.plugin');

class altauserpointsModelUsers extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _setmaxpoints() {
	
		$db			=  JFactory::getDBO();
		$maxpoints	= JFactory::getApplication()->input->get( 'setpointsperuser', 0, 'int' );

		$query = "UPDATE #__alpha_userpoints SET `max_points`='$maxpoints'";
		$db->setQuery($query);
		$db->query();
		
		return $maxpoints;
		
	}
	
	function _resetpoints() {
		
		$db	=  JFactory::getDBO();
				
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();
			
		// main query
		$query = "UPDATE #__alpha_userpoints SET `points`='0', `last_update`='$now'";
		$db->setQuery( $query );
		$db->query();
		
		// main query
		//$query = "DELETE FROM #__alpha_userpoints_details";
		$query = "UPDATE #__alpha_userpoints_details SET enabled='0'";
		$db->setQuery( $query );
		$db->query();
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		$results = $dispatcher->trigger( 'onResetGeneralPointsAltaUserPoints', array($now) );
		
		return true;
		
	}
	
	function _recalculate_points () {
		
		$this->_purge_expires ();
	
	}
	
	function _purge_expires () {
	
		$db	=  JFactory::getDBO();
		
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();		
	
		// delete old points after expire date before recount 
		//$query = "DELETE FROM #__alpha_userpoints_details WHERE `expire_date`!='0000-00-00 00:00:00' AND `expire_date`<='$now'";
		$query = "UPDATE #__alpha_userpoints_details SET enabled='0'"
		      . " WHERE `expire_date`!='0000-00-00 00:00:00' AND `expire_date`<='$now'";
		$db->setQuery( $query );
		$db->query();
		
		return true;
		
	}
	
	function _last_Activities() {

		$db			      = JFactory::getDBO();
		
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSQL();
		
		$count		      = 10;
			
		$query = "SELECT a.insert_date, a.referreid, a.points AS last_points, u.username AS usrname, u.name AS uname, aup.userid AS userID, r.rule_name"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.enabled='1'"
			   . " AND a.rule=r.id"
			   . " AND (a.expire_date>='$now' OR a.expire_date='0000-00-00 00:00:00')"
			   . " ORDER BY a.insert_date DESC"		
		 	   ;
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
	
		return $rows;
	
	}
	
	function _load_activities() {
		
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
		
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSql();
		
		$total 			= 0;
		
		$filter_state 		= $app->getUserStateFromRequest( 'com_altauserpoints.filter_state', 'filter_state', '', 'string' );
		$filter_order		= $app->getUserStateFromRequest( "com_altauserpoints.filter_order",		'filter_order',		'a.insert_date',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( "com_altauserpoints.filter_order_Dir",	'filter_order_Dir',	'desc',			'word' );
		$search				= $app->getUserStateFromRequest( "com_altauserpoints.search",			'search',			'',		  'string' );
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		
		// check filter order
		if ($filter_order!='a.insert_date' || $filter_order!='r.rule_name' || $filter_order!='u.name' || $filter_order!='u.username' || $filter_order!='a.points' || $filter_order!='a.approved' )
		{
			$filter_order = 'a.insert_date';		
		}
		
		if ( $filter_state!='' ) {
			$filter = " AND a.approved='".$filter_state."' ";						
		} else {
			$filter = " AND (a.approved='1' OR a.approved='0') ";		
		}
		
		$total 			= 0;
		
		$where = array();
		
		if ($search) {
			$where[] = 'LOWER(u.name) LIKE '. $db->quote( '%'.$db->escape( $search, true ).'%' );
			$where[] = 'LOWER(u.username) LIKE '. $db->quote( '%'.$db->escape( $search, true ).'%' );
			$where[] = 'LOWER(r.rule_name) LIKE '. $db->quote( '%'.$db->escape( $search, true ).'%' );
			$where[] = 'LOWER(a.insert_date) LIKE '. $db->quote( '%'.$db->escape( $search, true ).'%' );
		}
		$where 		= ( count( $where ) ? " AND (" . implode( ' OR ', $where ) .")" : "" );
		
		$orderby = " ORDER BY " . $filter_order . " " . $filter_order_Dir;
		
		$query = "SELECT a.id, a.insert_date, a.referreid, a.points AS last_points, a.approved, u.username AS usrname, u.name AS uname, aup.userid AS userID, r.rule_name"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1'"
			   . " AND a.rule=r.id"
			   . " AND (a.expire_date>='$now' OR a.expire_date='0000-00-00 00:00:00')"
			   . " AND a.enabled='1'"
			   . $filter . $where 
			   . $orderby
			   ;
				
		$total = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		$lists = array();		
		$options[] = JHTML::_('select.option', '', JText::_( 'AUP_ALL' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_APPROVED' ) );
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_UNAPPROVED' ) );
		$lists['filter_state'] = JHTML::_('select.genericlist', $options, 'filter_state', 'class="inputbox" size="1" onchange="document.adminForm.submit();"' ,'value', 'text', $filter_state );		

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search']    = $search;

		return array($result, $total, $limit, $limitstart, $lists);
	
	}

	
}
?>