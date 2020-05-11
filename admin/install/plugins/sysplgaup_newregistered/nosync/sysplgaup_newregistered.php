<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgUserSysplgaup_newregistered extends JPlugin
{

	public function onUserAfterSave($user, $isnew, $succes, $msg) {
		
		if ( $isnew ) {

			$app = JFactory::getApplication();
		
      		$lang = JFactory::getLanguage();
      		$lang->load( 'com_altauserpoints', JPATH_SITE);
			
			$jnow		= JFactory::getDate();		
			$now		= $jnow->toSql();
		
			require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';
			
			// get params definitions
			$params = JComponentHelper::getParams( 'com_altauserpoints' );		
			
			$prefixSelfRegister = $params->get('prefix_selfregister');
			$prefixReferralRegister = $params->get('prefix_referralregister');
		
			$referrerid = trim(@$_SESSION['referrerid']);
			unset($_SESSION['referrerid']);
		
			$db	   = JFactory::getDBO();
			$query = "SELECT * FROM #__alpha_userpoints_rules 
			WHERE `plugin_function`='sysplgaup_newregistered' AND `published`='1'";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			
			$prefixNewReferreid = ( $referrerid!='' ) ? strtoupper($prefixReferralRegister) : strtoupper($prefixSelfRegister); 
	
			// if rule enabled
			if ( $result ) {			
				
				if( $params->get('referralIDtype')=='r' ) {
					$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));	
				} 
				elseif( $params->get('referralIDtype')=='u' )
				{					
					$newreferreid = $prefixNewReferreid . strtoupper($user['username']);
					$newreferreid = str_replace( ' ', '-', $newreferreid );				
					$newreferreid = str_replace( ',', '-', $newreferreid );
					$newreferreid = str_replace( "'", "-", $newreferreid );
				}
				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
				
				$row = JTable::getInstance('userspoints');
				// insert this new user into altauserpoints table
			    $row->id			= NULL;
				$row->userid		= $user['id'];
			    $row->referreid		= $newreferreid;
			    $row->points		= $result[0]->points;
			    $row->max_points	= 0;
				$row->last_update	= $now;
			    $row->referraluser	= $referrerid;
				$row->published		= 1;
				$row->shareinfos	= 1;
				
				/*if (isset($user['profile']) && (count($user['profile'])))				
				{
					foreach ($user['profile'] as $k => $v)
					{
						$value = str_replace('"', '', $v);
						$row = $this->checkProfileField($k, $row, $value);
					}						
				}*/
				
				if (!$row->store()) {
					JFactory::getApplication()->enqueueMessage( $row->getError(),'error');
				}
				
				// save new points into altauserpoints table details
				$row2 = JTable::getInstance('userspointsdetails');
			    $row2->id				= NULL;
			    $row2->referreid		= $newreferreid;
			    $row2->points			= $result[0]->points;
				$row2->insert_date		= $now;
			    $row2->expire_date 		= $result[0]->rule_expire;
			    $row2->status			= $result[0]->autoapproved;
				$row2->rule				= $result[0]->id;
			    $row2->approved			= $result[0]->autoapproved;
				$row2->datareference	= JText::_( 'AUP_WELCOME' );
				$row2->enabled			= 1;
			  			
				if (!$row2->store()) {
					$app->enqueueMessage(  $row2->getError(),'error');
				}
				
				// frontend message
				if ( $result[0]->displaymsg ) 
				{
					$msg = str_replace('{username}', $user['username'], $result[0]->msg);
					$msg = str_replace('{name}', $user['name'], $result[0]->msg);
					$msg = str_replace('{email}', $user['email'], $result[0]->msg);
					if ( $msg!='' )
					{								
						$app->enqueueMessage( str_replace ( '{points}', AltaUserPointsHelper::getFPoints($result[0]->points), JText::_( $msg ) ));
					} else {
						$app->enqueueMessage( sprintf ( JText::_('AUP_CONGRATULATIONS_PLURAL'), $result[0]->points ));
					}			
				}
				
				// send notification		
				if ( $result[0]->notification ) AltaUserPointsHelper::sendnotification ( $newreferreid, $result[0]->points, $result[0]->points, $result[0], 1 );
				
				if ( $referrerid ) {
					$data = htmlspecialchars( $user['name'], ENT_QUOTES, 'UTF-8') . " (" . $user['username'] . ") ";
					$data = sprintf ( JText::_('AUP_X_HASJOINEDTHEWEBSITE'), $data );
					$this->sysplgaup_invitewithsuccess( $referrerid, $data );
				}
				
				return true;
				
			} else return false;						
		}
	}
	
	public function onUserAfterDelete($user, $succes, $msg) {

		$db	   = JFactory::getDBO();
		
		$query = "SELECT `id`, `referreid`, `referraluser` FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$result = $db->loadObject();
		$referreid = $result->referreid;
		$referraluser = $result->referraluser;

		$query = "DELETE FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_details WHERE `referreid`='".$referreid."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_details_archive WHERE `referreid`='".$referreid."'";
		$db->setQuery( $query );
		$db->query();		
	
		$query = "DELETE FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_medals WHERE `rid`='".$result->id."'";
		$db->setQuery( $query );
		$db->query();
		
		// if the user has been a referral user
		$query = "UPDATE #__alpha_userpoints SET referraluser='' WHERE referraluser='".$referreid."'";
		$db->setQuery($query);
		$db->query();
		
		// recount referrees for the referral user
		$query = "SELECT referrees FROM #__alpha_userpoints WHERE referreid='".$referraluser."'";
		$db->setQuery($query);
		$numreferrees = $db->loadResult();
		if ( $numreferrees > 0 )
		{
			$query = "UPDATE #__alpha_userpoints SET referrees=referrees-1 WHERE referreid='".$referraluser."'";
			$db->setQuery($query);
			$db->query();
		}
		
	}
	
	public function sysplgaup_invitewithsuccess( $referrerid, $data ) {

		$ip = $_SERVER["REMOTE_ADDR"];
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';
		$keyreference = AltaUserPointsHelper::buildKeyreference( 'sysplgaup_invitewithsuccess', $ip );
		AltaUserPointsHelper::userpoints( 'sysplgaup_invitewithsuccess', $referrerid, 0, $keyreference, $data );

	}
	
	public function onUserLogin($user, $options = array())
	{
		$app = JFactory::getApplication();
		$db	   = JFactory::getDBO();
		$cparams = JComponentHelper::getParams( 'com_altauserpoints' );	
		
		jimport('joomla.user.helper');
		
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
		}
		
		$q = "SELECT * FROM #__alpha_userpoints WHERE userid='".$id."'";
		$db->setQuery($q);
		$checkuser = $db->loadObject();
		if ( !$checkuser )
				{								
					//$app->enqueueMessage( $cparams->get('referralIDtype') );
					
					if ( $cparams->get('referralIDtype')=='r' )
					{
						$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));
					}
					elseif ( $cparams->get('referralIDtype')=='u' )
					{
						$newreferreid = $prefixNewReferreid . strtoupper($user['username']);
						$newreferreid = str_replace( ' ', '-', $newreferreid );				
						$newreferreid = str_replace( ',', '-', $newreferreid );
						$newreferreid = str_replace( "'", "-", $newreferreid );	 
					}
								
					JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
								
					$row = JTable::getInstance('userspoints');
								
					// insert this new user into altauserpoints table
					$row->id			= NULL;
					$row->userid		= $id;
					$row->referreid		= $newreferreid;
					//$row->points		= $ruleNewUser[0]->points;
					$row->max_points	= 0;
					$row->last_update	= $now;
					$row->referraluser	= '';						
					
					if (!$row->store()) 
					{
						$error = $row->getError();
						$app->enqueueMessage( $error, 'error' );
					}
					//else
						//$app->enqueueMessage( "AUP user added");
				}
				elseif(!$checkuser->referreid)
				{
					if ( $cparams->get('referralIDtype')=='r' )
					{
						$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));
					}
					elseif ( $cparams->get('referralIDtype')=='u' )
					{
						$newreferreid = $prefixNewReferreid . strtoupper($user['username']);
						$newreferreid = str_replace( ' ', '-', $newreferreid );				
						$newreferreid = str_replace( ',', '-', $newreferreid );
						$newreferreid = str_replace( "'", "-", $newreferreid );	 
					}
					
					$q = "UPDATE #__alpha_userpoints 
					SET referreid='".$newreferreid."' WHERE userid='".$id."'	";
					$db->setQuery( $q );
					$db->execute();
					//$app->enqueueMessage( "Referreid added to exisiting AUP user");
		}
		
		
		if ($instance->get('block') == 0) {
			require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
			// start the user session for AltaUserpoints
			AltaUserPointsHelper::getReferreid( intval($instance->get('id')) );
						
			if( $app->isSite() ){
			
				// load language component
        		$lang = JFactory::getLanguage();
        		$lang->load( 'com_altauserpoints', JPATH_SITE);
				
				// check raffle subscription to showing a reminder message
					
				// check first if rule for raffle is enabled
				$result = AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_raffle', 1 );
				if ( $result ) {				
					$resultCurrentRaffle = $this->checkIfCurrentRaffleSubscription(intval($instance->get('id')));
					if ($resultCurrentRaffle=='stillRegistered') {
						$messageAvailable = JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE');
						if ( $messageAvailable!='' ) {
							$messageRaffle = sprintf ( JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE'), $user['username'] );
							$app->enqueueMessage( $messageRaffle );
						}		
					}				
				}
			}
			
			//return true;
		}		
		
	}
	
	public function onUserLogout($user, $options = array()) {
		//Make sure we're a valid user first
		if($user['id'] == 0) return true;

		unset($_SESSION['referrerid']);
		return true;
	}
		
	private function checkIfCurrentRaffleSubscription($userid) {
	
		$db	   = JFactory::getDBO();
		
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();		
		
		$query = "SELECT id FROM #__alpha_userpoints_raffle WHERE published='1' AND inscription='1' 
		AND raffledate>'$now' AND raffledate!='0000-00-00 00:00:00' AND winner1='' 
		AND winner2='' AND winner3='' LIMIT 1";
		$db->setQuery( $query );
		$nextraffle = $db->loadResult();
		
		if ( $nextraffle ) {
			// check if already subscription
			$query = "SELECT COUNT(*) FROM #__alpha_userpoints_raffle_inscriptions 
			WHERE userid='$userid' AND raffleid='$nextraffle'";
			$db->setQuery( $query );
			$alreadySubscription = $db->loadResult();
			if ( $alreadySubscription ) {
				return 'alreadyRegistered';
			} return 'stillRegistered';
			
		} else return 'noRaffleAvailable';
	}	
}
?>