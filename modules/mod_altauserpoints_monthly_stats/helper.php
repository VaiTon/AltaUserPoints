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

class modAltaUserPointsMonthlyStatsHelper {

	public static function getList($searchdate, $params) {

		$db			      = JFactory::getDBO();
			
		$count		      = intval($params->get('count', 5));
		$showheader		  = intval($params->get('showheader', 1));
		$showleaderboard  = intval($params->get('showdate', 1));
		$usrname		  = trim($params->get('usrname', 'name'));
	
		$query = "SELECT a.referreid, SUM(a.points) AS sumpoints, u.".$usrname." AS usrname, u.id AS userid"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.status='1' AND a.enabled='1'"
			   . " AND a.insert_date LIKE '".$searchdate."%'"
			   . " GROUP BY a.referreid "
			   . " ORDER BY sumpoints DESC"
			   ;
			   

		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
	
		return $rows;
	
	}
	
	public static function getCurrentMonthList($selectmonth, $params) {
	
		$curr_month   	= date("m");
		$current_year 	= date("Y");
		
		$month 			= array (1=>JText::_('JANUARY'), JText::_('FEBRUARY'), JText::_('MARCH'), JText::_('APRIL'), JText::_('MAY'), JText::_('JUNE'), JText::_('JULY'), JText::_('AUGUST'), JText::_('SEPTEMBER'), JText::_('OCTOBER'), JText::_('NOVEMBER'), JText::_('DECEMBER'));
		$select 		= "<select name=\"mod_aup_ms_month\" id=\"mod_aup_ms_month\" class=\"input-small".$params->get( 'pageclass_sfx' )."\" size=\"1\" onchange=\"document.forms['frmModAUPMS'].submit();\">\n";
		
		if ( $curr_month==1 ) {		
			$previousmonth = 12;
			$previousyear = $current_year - 1;		
		} else {		
			$previousmonth = $curr_month - 1;
			$previousyear = $current_year;
		}
		
		$select .= "\t<option value=\"".$curr_month."\"";
		if ($curr_month == $selectmonth) {
			$select .= " selected>".JText::_('MODAUP_MS_CURRENT_MONTH');
		} else {
			$select .= ">".JText::_('MODAUP_MS_CURRENT_MONTH');
		}
		$select .= "</option>\n";
		$select .= "\t<option value=\"".$previousmonth."\"";
		if ($previousmonth == $selectmonth) {
			$select .= " selected>".$month[$previousmonth];
		} else {
			$select .= ">".$month[$previousmonth];
		}
		$select .= "</option>\n";
		$select .= "</select>";
		$select .= "<input type=\"hidden\" name=\"mod_aup_ms_current_year\" value=\"".$current_year."\"/>";
		$select .= "<input type=\"hidden\" name=\"mod_aup_ms_previousyear\" value=\"".$previousyear."\"/>";
		
		return $select;
	}
}
?>