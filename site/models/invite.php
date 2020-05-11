<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modelitem');
class AltauserpointsModelInvite extends JModelForm
{
	
	
	function _getParamsAUP()
	{
		// Get the parameters of the active menu item
		$app 		= JFactory::getApplication();
		$menus 		= $app->getMenu();		
		$menu       = $menus->getActive();
		$menuid     = @$menu->id;
		$params     = $menus->getParams($menuid);
		return $params;
	
	}
	function _getReferreid() 
	{
		// check referre ID
		$referrerid = @$_SESSION['referrerid'];		
		return $referrerid;
	}
	function _getRuleID ( $plugin_function )
	{	
		$db	= JFactory::getDBO(); 
		$q = "SELECT id FROM #__alpha_userpoints_rules 
		WHERE plugin_function=".$db->quote($plugin_function)." ";
		$db->setQuery( $q );
		$result = $db->loadResult();
		return $result;	
	}
	function _checkCurrentMaxPerDay( $ruleid, $userid, $referrerid, $ip )
	{	
		$db	= JFactory::getDBO();
		$curdate = date( "Y-m-d" );
		if ( $userid )
		{			
			// count invite sent this day
			$q = "SELECT count(*) FROM #__alpha_userpoints_details WHERE rule='$ruleid' 
			AND referreid=".$db->quote($referrerid)." AND `insert_date` LIKE '$curdate%' AND enabled='1'";
		}
		else
		{
			// count guest invite sent this day
			$q = "SELECT count(*) FROM #__alpha_userpoints_details WHERE rule='$ruleid' 
			AND referreid='GUEST' AND `insert_date` LIKE '$curdate%' AND keyreference=".$db->quote($ip)." AND enabled='1'";
		}	
		$db->setQuery( $q );
		$result = $db->loadResult();
		return $result;
	}
	
	function _checkLastInviteForDelay( $ruleid, $userid=0, $referrerid, $ip, $delay )
	{	
		$db			= JFactory::getDBO();
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$ts 		= strtotime( $now );		
		$checkdelay = 1;
		if ( $userid )
		{			
			$q = "SELECT `insert_date` FROM #__alpha_userpoints_details WHERE rule='$ruleid' 
			AND referreid=".$db->quote($referrerid)." AND enabled='1' ORDER BY `insert_date` DESC LIMIT 1";
		}
		else
		{
			$q = "SELECT `insert_date` FROM #__alpha_userpoints_details WHERE rule='$ruleid' 
			AND referreid='GUEST' AND keyreference=".$db->quote($ip)." AND enabled='1' ORDER BY `insert_date` DESC LIMIT 1";
		}	
		$db->setQuery( $q );
		$result = $db->loadResult();
		// if exist -> compare
		if( $result )
		{				
			$lasttime = strtotime($result) + $delay;						
			if ( $lasttime > $ts )
				$checkdelay = 0;
		}
		return $checkdelay;
	}
	
	function _extractEmailsFromString($sChaine) {	 
		if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', $sChaine, $aEmails)) {
			if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
				return array_unique($aEmails[0]);
			}
		}		 
		return null;
	}
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_altauserpoints.invite', 'invite', array('control' => 'jform', 'load_data' => true));
		if (empty($form))
			return false;
			
		return $form;
	}
}