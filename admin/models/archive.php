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

class altauserpointsModelArchive extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _archive()
	{
		$app = JFactory::getApplication();
		
		$db = JFactory::getDBO();
		
		$testDate = 1;
		
		$dateCombine = JFactory::getApplication()->input->get('datestart', '', 'cmd');
		$count = '0';
		
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$dateCombine))
		{
			$testDate = 1;
		}else{
			$testDate = 0;
		}
		
		if ( $dateCombine!='' && $testDate == 1 ) 
		{
			$dateCombine = $dateCombine . ' 00:00:00';
			
			// create a combined activity
			$query ="SELECT SUM(points) AS sumAllPoints, referreid FROM #__alpha_userpoints_details WHERE insert_date < '".$dateCombine."' AND `enabled`='1' GROUP BY referreid";
			$db->setQuery($query );
			$resultCombined = $db->loadObjectList();			
			// archive
			$query ="INSERT INTO #__alpha_userpoints_details_archive SELECT * FROM #__alpha_userpoints_details AS a WHERE a.insert_date < '".$dateCombine."'";
			$db->setQuery($query );
			$db->query();
			// delete			
			$query ="DELETE FROM #__alpha_userpoints_details WHERE insert_date < '".$dateCombine."'";
			$db->setQuery($query );
			$db->query();
			// optimize
			$query ="OPTIMIZE TABLE #__alpha_userpoints_details";
			$db->setQuery($query );
			$db->query();			
			
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
			
			// retreive ID for rule Archive
			$id_archive_rule = getIdPluginFunction( 'sysplgaup_archive' );
			
			foreach ( $resultCombined as $combined ) 
			{
				if ( $combined->referreid!='' ) 
				{
					// save new points into alpha_userpointsdetails table
					$row = JTable::getInstance('userspointsdetails');
					$row->id				= NULL;
					$row->referreid			= $combined->referreid;
					$row->points			= $combined->sumAllPoints;
					$row->insert_date		= $dateCombine;
					$row->expire_date 		= '';		
					$row->rule				= intval($id_archive_rule);
					$row->approved			= 1;
					$row->status			= 1;
					$row->keyreference		= '';
					$row->datareference		= sprintf ( JText::_('AUP_COMBINED_ACTIVITIES_BEFORE_DATE'), JHTML::_('date', $dateCombine, JText::_('DATE_FORMAT_LC2')) );
					$row->enabled			= 1;
					if ( !$row->store() )
					{
						$app->enqueueMessage(  $row->getError(),'error');
						return;
					}
				}
			}			
			$app->enqueueMessage( JText::_('AUP_ACTIVITIES_COMBINED_SUCCESSFULLY') );
		} else {			
			$app->enqueueMessage( JText::_('AUP_MISSING_DATE') );
		}
		
		return;

	}
	
}
?>