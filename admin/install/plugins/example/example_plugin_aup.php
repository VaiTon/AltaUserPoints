<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * AltaUserPoints Plugin
 *
 * @package		Joomla
 * @subpackage	AltaUserPoints
 * @since 		1.6
 */
class plgAltauserpointsExample_plugin_aup extends JPlugin
{
	
	public function onUpdateAltaUserPoints( &$result, $rule_name, $assignpoints, $referrerid, $userID )
	{	
		// on insert activity and save points
			
		return true;
			
	}
	
	public function onAfterUpdateAltaUserPoints( &$result, $rule_name, $assignpoints, $referrerid, $userID )
	{	
		// after inserted activity and saved points and send notification
			
		return true;
			
	}	
	
	public function onSendNotificationAltaUserPoints( &$result, $rule_name, $assignpoints, $newtotal, $referrerid, $userID )
	{
	
		// on send email notification points
			
		return true;	
	
	}
	
	public function onUnlockMedalAltaUserPoints( &$userinfo, &$medal )
	{	
		
		return true;				
	
	}
	
	
	public function onGetNewRankAltaUserPoints( &$userinfo, &$rankdetail )
	{	
	
		return true;
			
	}
	
	
	public function onBeforeInsertUserActivityAltaUserPoints( &$result, $points, $referrerid, $keyreference, $datareference )
	{
	
		return true;
		
	}
	
	public function onResetGeneralPointsAltaUserPoints( $date )
	{
	
		return true;
		
	}
	
	
	public function onBeforeDeleteUserActivityAltaUserPoints ( $cid, $referrerid )
	{	
		// $cid is an array : each id in cid is an activity of the user with this $referrerid
		
		return true;
	
	}
	
	
	public function onAfterDeleteUserActivityAltaUserPoints ( $referrerid )
	{	
				
		return true;
	
	}
	
	
	public function onBeforeDeleteAllUserActivitiesAltaUserPoints ( $referrerid )
	{	
				
		return true;
	
	}
	
	
	public function onAfterDeleteAllUserActivitiesAltaUserPoints ( $referrerid )
	{	
				
		return true;
	
	}
	

	public function onBeforeMakeRaffleAltaUserPoints ( &$rowRaffle, $now )
	{	
				
		return true;
	
	}
	

	public function onAfterMakeRaffleAltaUserPoints ( &$rowRaffle, $now )
	{	
				
		return true;
	
	}

}
?>