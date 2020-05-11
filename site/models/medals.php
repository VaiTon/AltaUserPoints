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

class AltauserpointsModelmedals extends JmodelLegacy {

	function __construct(){
		parent::__construct();
		
	}
	
	
	function _getMedalsList()
	{
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
				
		$total 			= 0;
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);	
		
		$q = "SELECT *, '' AS nummedals FROM #__alpha_userpoints_levelrank "
				. "\nWHERE typerank='1' "
				. "\nORDER BY typerank ASC, ordering ASC, levelpoints DESC";
		$total = @$this->_getListCount($q);
		$results = $this->_getList($q, $limitstart, $limit);
		
		for ($i=0, $n=count( $results ); $i < $n; $i++)			
		{
			$row   = $results[$i];
			
			$q = "SELECT COUNT(*) FROM #__alpha_userpoints_medals "
					. "\nWHERE medal=".$db->quote($row->id)." ";
			$db->setQuery($q);
			$row->nummedals = $db->loadResult();
			
		}		
		
		return array($results, $total, $limit, $limitstart);

	}
	
	function _getDetailsMedalsListUsers()
	{
		$app = JFactory::getApplication();
		$db			    = JFactory::getDBO();				
		$total 			= 0;	
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		$cid  		= JFactory::getApplication()->input->get('cid', 0, 'int');
	
		$q = "SELECT aup.referreid, m.medaldate AS dateawarded, m.reason , u.name, u.username, lv.id AS cid, lv.typerank, lv.icon, lv.rank FROM #__alpha_userpoints_medals AS m"
				. "\n LEFT JOIN #__alpha_userpoints_levelrank AS lv ON m.medal=lv.id"
				. "\n LEFT JOIN #__alpha_userpoints AS aup ON m.rid=aup.id"
				. "\n LEFT JOIN #__users AS u ON aup.userid=u.id"
				. "\n WHERE m.medal=".$db->quote($cid)." "
				. "\n ORDER BY m.medaldate DESC";
		$total = @$this->_getListCount($q);
		$results = $this->_getList($q, $limitstart, $limit);
		
		return array($results, $total, $limit, $limitstart);
	}
}
?>