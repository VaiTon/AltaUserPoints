<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('_MINIMUM_LEVEL')) {
   define('_MINIMUM_LEVEL', 2);   
}


jimport('joomla.plugin.plugin');

class AltaUserPointsHelper 
{

	public static function newpoints ( $plugin_function='', $referrerid='', $keyreference='', $datareference='', $randompoints=0, $feedback=false, $force=0, $frontmessage='' ) 
	{		
		if ( $plugin_function!='' ) 
		{
			return AltaUserPointsHelper::userpoints ( $plugin_function, $referrerid, 0, $keyreference, $datareference, $randompoints, $feedback, $force, $frontmessage );
		} 
		else 
		{
			return;
		}	
	}	
	
	public static function userpoints ( $plugin_function , $referrerid='', $referraluserpoints=0, $keyreference='', $datareference='', $randompoints=0, $feedback=false, $force=0, $frontmessage='' ) 
	{	

		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		$allowNegativeAccount = $params->get( 'allowNegativeAccount', 0 );
		
		$checkReference = 0;

		if ( $plugin_function=='sysplgaup_recommend' && $referrerid!='' && $keyreference!='' && $datareference!='' )
		{
			$force = 1;		
		}		
		if ( !$referraluserpoints )
		{
			if ( !$referrerid )
			{
				$referrerid = @$_SESSION['referrerid'];
			}
		}
		if ( !$referrerid ) return;		
		if ( !AltaUserPointsHelper::checkExcludeUsers( $referrerid) ) return ;		
		$result = AltaUserPointsHelper::checkRuleEnabled( $plugin_function, $force, $referrerid );
		
		// check reference if not exist
		if ( $keyreference!='' && $result ) $checkReference = AltaUserPointsHelper::checkReference( $referrerid, $keyreference, $result[0]->id );		
		if ( $result && !$checkReference )
		{
			// force specific points -> overwriting rule points or if rule points is 0			
			// This points can be negative (example : single rule in backend rule manager for several products on frontend, each products with different prices...)
			if ( $randompoints ) $result[0]->points = $randompoints;
			
			// check if limit daily points
			if ( $result[0]->points > 0 && $referraluserpoints==0 ) 
			{
				$result[0]->points = AltaUserPointsHelper::checkMaxDailyPoints( $referrerid, $result[0]->points, $plugin_function );
			}
			
			if ( AltaUserPointsHelper::operationIsFeasible ( $referrerid, $result[0]->points ) || $allowNegativeAccount )
			{   
			
				// check account
				AltaUserPointsHelper::insertUserPoints( $referrerid, $result[0], $referraluserpoints, $keyreference, $datareference, $frontmessage );
				if ( $feedback==true ) return true;
			}
			else
			{
				// used for negative operation, 				
				$error = JText::_('AUP_YOUDONOTHAVEENOUGHPOINTSTOPERFORMTHISOPERATION' );
				JFactory::getApplication()->enqueueMessage( $error ,'notice');
				
				if ( $feedback==true )
				{
					return false;
				} 
				else
				{
					return;
				}
			}
		} else return true;
		
	}

	public static function insertUserPoints( $referrerid, $result, $referraluserpoints=0, $keyreference='', $datareference='', $frontmessage='' ) 
	{	
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();		
				
		$points = $result->points;
		$rule_expire = AltaUserPointsHelper::checkRuleExpireDate( $result->rule_expire, $result->type_expire_date );
		$rule_id = $result->id;
		$autoapproved = $result->autoapproved;
		$rule_plugin = $result->plugin_function;
		$rule_name = $result->rule_name;
		$noupdate  = 0;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		$insertAllActivities = $params->get( 'insertAllActivities', 0 );
		
		if ( $insertAllActivities )
		{	
			if ( !$referrerid ) return;
		}	
		else 
		{		
			if ( !$referrerid || $points==0 ) return;
		}			
		
		$points = ( $referraluserpoints>0 ) ? $referraluserpoints : $points ;	
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		$results = $dispatcher->trigger( 'onBeforeInsertUserActivityAltaUserPoints', array(&$result, $points, $referrerid, $keyreference, $datareference) );		
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
		// save new points into alpha_userpointsdetails table
		$row = JTable::getInstance('userspointsdetails');
		$row->id				= NULL;
		$row->referreid			= $referrerid;
		$row->points			= $points;
		$row->insert_date		= $now;
		$row->expire_date 		= $rule_expire;		
		$row->rule				= $rule_id;
		$row->approved			= $autoapproved;
		$row->status			= $autoapproved;
		$row->keyreference		= $keyreference;
		$row->datareference		= $datareference;
		$row->enabled			= 1;

		if ( !$row->store() )
		{
			JFactory::getApplication()->enqueueMessage(  $row->getError(),'error');
		}
		
		if ( $noupdate )
			return;		
		
		if ( $referrerid!='GUEST' || $referraluserpoints>0 )
		{
			if ( $rule_plugin=='' ) $rule_plugin = AltaUserPointsHelper::getPluginFunction( $rule_id ) ;
			if ( $rule_name==''   ) $rule_name   = AltaUserPointsHelper::getNameRule( $rule_plugin );
			AltaUserPointsHelper::updateUserPoints( $result, $referrerid, $points, $now, $referraluserpoints, $autoapproved, $rule_plugin, $rule_id, $rule_name, $datareference, $frontmessage );
		}
	}

	public static function updateUserPoints( $result, $referrerid, $assignpoints, $now, $referraluserpoints, $autoapproved, $rule_plugin='', $rule_id='', $rule_name='', $datareference='', $frontmessage='' ) 
	{
		$app = JFactory::getApplication();		
		
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		$user =  JFactory::getUser();	
		$username = ( $user->id ) ? $user->username : '' ;
				
		if ( !AltaUserPointsHelper::checkExcludeUsers( $referrerid) ) return ;		
		
		$displaymsg = $result->displaymsg;
		$msg = str_replace('{username}', $username, $result->msg);
		$method = $result->method;
		
		$db	   = JFactory::getDBO();	
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		
		$q = "SELECT id FROM #__alpha_userpoints WHERE `referreid`=".$db->quote($referrerid)." ";
		$db->setQuery( $q );
		$referrerUser = $db->loadResult();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');				
		$row = JTable::getInstance('userspoints');
			
		// update points into alpha_userpoints table
		$row->load( intval($referrerUser) );
		
		$referraluser = $row->referraluser;
		
		$newtotal = ( !$referraluserpoints ) ? ($row->points + $assignpoints) : $row->points + $referraluserpoints ;		
		$row->last_update = $now;	
		
		$checkWinner = 0;		
		
		if ( $row->max_points >=1 && ( $newtotal > $row->max_points ) )
		{
			// Max total was reached !
			//$newtotal = $row->max_points;

			if ( AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_winnernotification', 0, $referrerid ) )
			{
				// get email admins in rule
				$q = "SELECT `content_items` FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_winnernotification'";
				$db->setQuery( $q );
				$emailadmins = $db->loadResult();
				if ( $autoapproved || $referraluserpoints )
				{
					AltaUserPointsHelper::sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins );
					
					// Uddeim notification integration
					if ( $params->get( 'sendMsgUddeim', 0 ) )
					{
						AltaUserPointsHelper::sendUddeimWinnerNotification ( $referrerid, $assignpoints, $newtotal );
					}			
					
					$checkWinner = 1;
				}
			}
		}	
		
		if ( $autoapproved )  
		{
			if ( $rule_plugin=='sysplgaup_invitewithsuccess' )
			{
				$row->referrees = $row->referrees+1;		
			}
			
			$row->points = $newtotal;
			
			$db->updateObject( '#__alpha_userpoints', $row, 'id' );
		}
		
		if ( $displaymsg && !$referraluserpoints )			
		{
			$realcurrentreferrerid = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
			switch ( $rule_plugin )
			{
				case 'sysplgaup_bonuspoints':	
				case 'sysplgaup_recommend':	
				case 'sysplgaup_reader2author':
				case 'sysplgaup_buypointswithpaypal':
				case 'sysplgaup_invite':		
					// No need congratulation...								
					break;					
				case 'sysplgaup_invitewithsuccess':
					// number points in message = assign points to new user
					$numpoints = AltaUserPointsHelper::getPointsRule( 'sysplgaup_newregistered' );
					if ( $numpoints && $user->id ) 
					{
							if ( $msg!='' )
							{								
								$msg =  str_replace ( '{points}', AltaUserPointsHelper::getFPoints($numpoints), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AltaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$getFpoints =  AltaUserPointsHelper::getFPoints($numpoints);
								if($getFpoints>1)
									$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_PLURAL'), $getFpoints );
								else
									$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_SINGULAR'), $getFpoints );
								$app->enqueueMessage( $msg );
							} 
					}
					break;
				
				default:
					if ( ($referrerid == $realcurrentreferrerid) && $user->id )
					{
						if ( $assignpoints>0  ) 
						{		
							if ( $msg!='' )
							{
								$msg =  str_replace ( '{points}', AltaUserPointsHelper::getFPoints($assignpoints), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AltaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$getFpoints = AltaUserPointsHelper::getFPoints($assignpoints);
								if($getFpoints>1)
									$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_PLURAL'), $getFpoints );
								else
									$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_SINGULAR'), $getFpoints );
								$app->enqueueMessage( $msg );
								
							}
							
						} 
						elseif ( $assignpoints<0 )
						{
							if ( $msg!='' )
							{
								$msg =  str_replace ( '{points}', AltaUserPointsHelper::getFPoints(abs($assignpoints)), JText::_( $msg ) );
								$msg =  str_replace ( '{newtotal}', AltaUserPointsHelper::getFPoints($newtotal), $msg );
								$app->enqueueMessage( $msg );
							} else {
								$getFpoints = AltaUserPointsHelper::getFPoints(abs($assignpoints));
								if( $getFpoints>1)
									$msg = sprintf ( JText::_('AUP_X_POINTS_DEDUCTED_FROM_YOUR_ACCOUNT_PLURAL'), $getFpoints );
								else
									$msg = sprintf ( JText::_('AUP_X_POINTS_DEDUCTED_FROM_YOUR_ACCOUNT_SINGULAR'), $getFpoints );
								$app->enqueueMessage( $msg );
							}
						}
					}
			}
		}		
		
		if ( $rule_plugin=='sysplgaup_custom' && $datareference )
			$rule_name = JText::_( $datareference );
		
		// email notification
		if ( $result->notification && !$checkWinner ) 
		{
			$result->datareference = JText::_( $datareference );
			AltaUserPointsHelper::sendnotification ( $referrerid, $assignpoints, $newtotal, $result );
			// load external plugins
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('altauserpoints');
			$results = $dispatcher->trigger( 'onSendNotificationAltaUserPoints', array(&$result, $rule_name, $assignpoints, $newtotal, $referrerid, $user->id ) );
		}
		
		// Uddeim notification integration
		if ( $params->get( 'sendMsgUddeim', 0 ) && !$checkWinner )
		{
			AltaUserPointsHelper::sendUddeimNotification ( $referrerid, $assignpoints, $newtotal, $rule_name );
		}		
	
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		$results = $dispatcher->trigger( 'onUpdateAltaUserPoints', array(&$result, $rule_name, $assignpoints, $referrerid, $user->id ) );
				
		// checking rank and medals and update if necessary
		if ( $rule_id=='' ) $rule_id = AltaUserPointsHelper::getRuleID( $rule_plugin );
		AltaUserPointsHelper::checkRankMedal( $referrerid, $rule_id );
		
		// referral points rule
		if ( $referraluser!='' 
				&& $rule_plugin!='sysplgaup_buypointswithpaypal' // not assigned for this rule
					&& $rule_plugin!='sysplgaup_raffle' // not assigned for this rule
						&& $assignpoints>0 )
		{ // if not already assigned
			$q = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_referralpoints' 
			AND `published`='1' AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
			$db->setQuery( $q );
			$referralpoints = $db->loadObjectList();
			if ( $referralpoints )
			{
				$referraluserpoints = round(($assignpoints*$referralpoints[0]->points)/100, 2) ;
				if ( $referraluserpoints>0 ) AltaUserPointsHelper::userpoints( 'sysplgaup_referralpoints', $referraluser, $referraluserpoints );
			}
		}
		
		// check change user group rule 
		//if ( $rule_plugin!='sysplgaup_changelevel1' && $rule_plugin!='sysplgaup_changelevel2' && $rule_plugin!='sysplgaup_changelevel3' ) 
		//{
			AltaUserPointsHelper::checkChangeLevel( $referrerid, AltaUserPointsHelper::getCurrentTotalPoints ( $referrerid ) );
		//}
		
		if ( $frontmessage!='' )
			AltaUserPointsHelper::displayMessageSystem($frontmessage);
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		$results = $dispatcher->trigger( 'onAfterUpdateAltaUserPoints', array(&$result, $rule_name, $assignpoints, $referrerid, $user->id ) );
		
		// link up rule
		if ($result->linkup) 
		{
			$plugin_function_linkup = AltaUserPointsHelper::getPluginFunction( $result->linkup );
			AltaUserPointsHelper::newpoints ( $plugin_function_linkup, $referrerid );
		}
		
	}
	
	public static function checkRuleExpireDate( $rule_expire, $type_expire_date )	
	{	
		if ( $rule_expire!='0000-00-00 00:00:00' || $type_expire_date ) 
		{
			if ( $type_expire_date )
			{
				switch ( $type_expire_date )
				{
					case (($type_expire_date >1) && ($type_expire_date < 30)) :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+'.intval($type_expire_date).' days');
						break;
					case $type_expire_date==1 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 day');
						break;
					case $type_expire_date==30 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 month');
						break;
					case $type_expire_date==60 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+2 months');
						break;
					case $type_expire_date==90 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+3 months');
						break;
					case $type_expire_date==180 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+6 months');
						break;
					case $type_expire_date==360 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+1 year');						
						break;
					case $type_expire_date==720 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+2 years');						
						break;
					case $type_expire_date==1080 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+3 years');						
						break;
					case $type_expire_date==1440 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+4 years');						
						break;
					case $type_expire_date==1800 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+5 years');						
						break;
					case $type_expire_date==3600 :
						$date = new DateTime(date( "Y-m-d"));
						$date->modify('+10 years');						
						break;

				}
				$expire_date = $date->format("Y-m-d");
				$expire_date .= " 23:59:59";
			} else $expire_date = $rule_expire;
		
		 } else $expire_date = '0000-00-00 00:00:00';
	
		return $expire_date;
	}
	
	public static function checkParentLevel ( $userid )
	{	
		$app = JFactory::getApplication();
		$db	   = JFactory::getDBO();

		// actual level group 
		//$result = array_keys(JUserHelper::getUserGroups($userid));
		//$actualgroup = end($result);		
		$q = "SELECT `group_id` FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
		$db->setQuery( $q );
		$group_id = $db->loadResult();
		if (!$group_id) {		
			// DEBUG
			AltaUserPointsHelper::displayMessageSystem("erreur pas de groupe assigné.");
			// ----------
			return _MINIMUM_LEVEL;
		} else {		
			// select parent group
			$q = "SELECT `parent_id` FROM `#__usergroups` WHERE `id`= ".$db->quote($group_id)." ";
			$db->setQuery( $q );
			$parentUserGroup = $db->loadResult();
			if ($parentUserGroup){
				return $parentUserGroup;		
			} else return _MINIMUM_LEVEL;
		}		
		// DEBUG
		//AltaUserPointsHelper::displayMessageSystem("groupe actuel: ".$actualgroup);		
		
	}
	
	
	public static function refreshSession ($userid) 
	{
			// refresh session if user online
			$temp = JFactory::getUser((int) $userid);
			$temp->groups = $user->groups;
			$temp = JFactory::getUser((int) $userid);
			if ($temp->id == $userid)
			{
				$temp->groups = $user->groups;
			}
	}
	
	public static function checkChangeLevel( $referrerid, $newtotal )
	{
		$app  = JFactory::getApplication();
		$db	  = JFactory::getDBO();
		
		$ok = 0;
		
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
		
		$resultChangeLevel1 = AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel1', 0, $referrerid );
		$resultChangeLevel2 = AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel2', 0, $referrerid );
		$resultChangeLevel3 = AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_changelevel3', 0, $referrerid );		
		
		if ($resultChangeLevel1) $checkAlreadyDone1  = explode(',', $resultChangeLevel1[0]->exclude_items);
		if ($resultChangeLevel2) $checkAlreadyDone2  = explode(',', $resultChangeLevel2[0]->exclude_items);
		if ($resultChangeLevel3) $checkAlreadyDone3  = explode(',', $resultChangeLevel3[0]->exclude_items);
		
		$userid 			= AltaUserPointsHelper::getUserID( $referrerid );
	
		// get actual group fot this user
		jimport('joomla.user.helper');
		
		$authorizedLevels = JAccess::getAuthorisedViewLevels($userid);

		$result = array_keys(JUserHelper::getUserGroups($userid));
		$actualgroup = end($result);		
		
		// UP		
		if ( $resultChangeLevel1 && $newtotal >= $resultChangeLevel1[0]->points2 
			&& !in_array(intval($resultChangeLevel1[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone1) )
		{			
			// delete old group
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();
	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel1[0]->content_items));
			//$user = JUser::getInstance((int) $userid);

			$ok = 1;
			$resultChangeLevel = $resultChangeLevel1;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			
			// insert done for this user in this rule			
			if ( $resultChangeLevel1[0]->exclude_items!='' )
			{
				$insertUserId = $resultChangeLevel1[0]->exclude_items . ',' . $userid;
			} else {
				$insertUserId = $userid;
			}
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel1[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );	
					
		} 
		
		if ( $ok )
		{
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}
		
		if ( $resultChangeLevel2 && $newtotal >= $resultChangeLevel2[0]->points2 && !in_array(intval($resultChangeLevel2[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone2) )
		{			
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel2[0]->content_items));
			//$user = JUser::getInstance((int) $userid);
			$ok = 1;
			$resultChangeLevel = $resultChangeLevel2;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			// insert done for this user in this rule			
			if ( $resultChangeLevel2[0]->exclude_items!='' )
			{
				$insertUserId = $resultChangeLevel2[0]->exclude_items . ',' . $userid;
			} else {
				$insertUserId = $userid;
			}
			
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel2[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );
			
		} 
		
		if ( $ok )
		{
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}

		if ( $resultChangeLevel3 && $newtotal >= $resultChangeLevel3[0]->points2 && !in_array(intval($resultChangeLevel3[0]->content_items), $authorizedLevels) && !in_array($userid, $checkAlreadyDone3) )
		{			
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();	
			JUserHelper::addUserToGroup($userid, intval($resultChangeLevel3[0]->content_items));
			//$user = JUser::getInstance((int) $userid);
			$ok = 1;
			$resultChangeLevel = $resultChangeLevel3;
			$result = JUserHelper::getUserGroups($userid);
			$actualnamegroup = end($result);
			// insert done for this user in this rule			
			if ( $resultChangeLevel3[0]->exclude_items!='' )
			{
				$insertUserId = $resultChangeLevel3[0]->exclude_items . ',' . $userid;
			} else {
				$insertUserId = $userid;
			}

			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel3[0]->id) );
			$row->exclude_items = $insertUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );
			
		}
		
		if ( $ok )
		{
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}
		
		// DOWN
 		if ( $resultChangeLevel3 && $newtotal < $resultChangeLevel3[0]->points2 && in_array($userid, $checkAlreadyDone3) ) {
		
			$idParentGroup = AltaUserPointsHelper::checkParentLevel ( $userid );
			
			// delete to list checkAlreadyDone
			unset($checkAlreadyDone3[array_search($userid, $checkAlreadyDone3)]);
			$setWithoutUserId = implode (',',$checkAlreadyDone3);
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel3[0]->id) );
			$row->exclude_items = $setWithoutUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );						
			
			// delete actual group to return to parent			
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();
	
			JUserHelper::addUserToGroup($userid, intval($idParentGroup));
			//$user = JUser::getInstance((int) $userid);
			$ok = 1;
		}	
		
		if ( $ok )
		{
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}
		
		if ( $resultChangeLevel2 && $newtotal < $resultChangeLevel2[0]->points2 && in_array($userid, $checkAlreadyDone2)  ) {
		
			$idParentGroup = AltaUserPointsHelper::checkParentLevel ( $userid );
			
			// delete to list checkAlreadyDone
			unset($checkAlreadyDone2[array_search($userid, $checkAlreadyDone2)]);
			$setWithoutUserId = implode (',',$checkAlreadyDone2);
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel2[0]->id) );
			$row->exclude_items = $setWithoutUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );						
			
			// delete actual group to return to parent			
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();
	
			JUserHelper::addUserToGroup($userid, intval($idParentGroup));
			//$user = JUser::getInstance((int) $userid);
			$ok = 1;
		}
		
		if ( $ok )
		{
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}
		
		if ( $resultChangeLevel1 && $newtotal < $resultChangeLevel1[0]->points2 && in_array($userid, $checkAlreadyDone1)  ) {
		
			$idParentGroup = AltaUserPointsHelper::checkParentLevel ( $userid );
			
			// delete to list checkAlreadyDone
			unset($checkAlreadyDone1[array_search($userid, $checkAlreadyDone1)]);
			$setWithoutUserId = implode (',',$checkAlreadyDone1);
			$row = JTable::getInstance('rules');
			$row->load( intval($resultChangeLevel1[0]->id) );
			$row->exclude_items = $setWithoutUserId;
			$db->updateObject( '#__alpha_userpoints_rules', $row, 'id' );
			
			// delete actual group to return to parent			
			$q = "DELETE FROM `#__user_usergroup_map` WHERE `user_id`=".$db->quote($userid)." ";
			$db->setQuery( $q );
			$db->execute();
	
			JUserHelper::addUserToGroup($userid, intval($idParentGroup));
			//$user = JUser::getInstance((int) $userid);
			$ok = 1;
		}		
		
		if ( $ok )
		{		
			// prevent if minimum level (registered)
			/*
			$q = "SELECT `user_id` FROM `#__user_usergroup_map` WHERE `user_id`='$userid'";
			$db->setQuery( $q );
			$useridGroupExist = $db->loadResult();
			if (!$useridGroupExist)
			{
				JUserHelper::addUserToGroup($userid, 2);
				//$user = JUser::getInstance((int) $userid);
			}	
			*/		
			// refresh session if user online
			AltaUserPointsHelper::refreshSession ($userid);
		}
		
		// new name group
		$result = JUserHelper::getUserGroups($userid);
		$actualnamegroup = end($result);
		$q = "SELECT `title` FROM `#__usergroups` WHERE `id`=".$db->quote($actualnamegroup)." ";
		$db->setQuery( $q );
		$nameUserGroup = $db->loadResult();
		
		// show message only for current user and if frontend site
		if ( $referrerid == @$_SESSION['referrerid'] && $app->isSite() && $ok )
		{
			// display message for the current user
			if ( $resultChangeLevel[0]->displaymsg && $resultChangeLevel[0]->msg!='' )
			{
				$msg = str_replace('{username}',$user->username,$resultChangeLevel[0]->msg);
				$msg = str_replace('{points}',AltaUserPointsHelper::getFPoints($resultChangeLevel[0]->points),$msg);
				$msg = str_replace('{newtotal}',AltaUserPointsHelper::getFPoints($newtotal),$msg);
				AltaUserPointsHelper::displayMessageSystem( $msg );
			} 
			elseif ( $resultChangeLevel[0]->displaymsg && $resultChangeLevel[0]->msg=='' )		
			{
				AltaUserPointsHelper::displayMessageSystem( sprintf (JText::_('AUP_MSG_YOUHAVENEWUSERRIGHTS'), AltaUserPointsHelper::getFPoints($resultChangeLevel[0]->points2), $nameUserGroup) );
			} 
		}
		
		if ( $ok ) 	
		{		
			// get params definitions
			$params = JComponentHelper::getParams( 'com_altauserpoints' );		
			$insertAllActivities = $params->get( 'insertAllActivities', 0 );
			// insert this new activity in database			
			$datareference = sprintf (JText::_('AUP_DESCRIPTIONACTIVITYONCHANGELEVEL'), AltaUserPointsHelper::getFPoints($resultChangeLevel[0]->points2), $nameUserGroup);
			if ( $insertAllActivities ) {
				AltaUserPointsHelper::insertUserPoints( $referrerid, $resultChangeLevel[0], 0, '', $datareference );
			}
			
			//Send notification
			if ( $resultChangeLevel[0]->notification )
			{
				AltaUserPointsHelper::sendnotification ( $referrerid, $resultChangeLevel[0]->points2, $newtotal, $resultChangeLevel[0] );
				// load external plugins
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('altauserpoints');
				$rule_name = JText::_($resultChangeLevel[0]->rule_name);
				$results = $dispatcher->trigger( 'onSendNotificationAltaUserPoints', array(&$resultChangeLevel[0], $rule_name, $resultChangeLevel[0]->points2, $newtotal, $referrerid, $userid ) );
			}
			
			// load external plugins
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('altauserpoints');
			$results = $dispatcher->trigger( 'onChangeLevelAltaUserPoints', array(&$resultChangeLevel[0], $actualnamegroup, $userid, $referrerid ) );

		}
	}
	
	public static function getUserID( $referrerid )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `userid` FROM #__alpha_userpoints WHERE `referreid`=".$db->quote($referrerid)." ";
		$db->setQuery( $q );
		$userID = $db->loadResult();
		return	$userID;	
	}
	
	public static function getRuleID( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `id` FROM #__alpha_userpoints_rules WHERE `plugin_function`=".$db->quote($plugin_function)." ";
		$db->setQuery( $q );
		$rule_id = $db->loadResult();
		return intval($rule_id);
	}
	
	public static function getPointsRule( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `points` FROM #__alpha_userpoints_rules WHERE `plugin_function`=".$db->quote($plugin_function)." ";
		$db->setQuery( $q );
		$numpoints = $db->loadResult();
		return	$numpoints;	
	}
	
	public static function getPointsRule2( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `points2` FROM #__alpha_userpoints_rules WHERE `plugin_function`=".$db->quote($plugin_function)." ";;
		$db->setQuery( $q );
		$numpoints = $db->loadResult();
		return	$numpoints;	
	}
	
	public static function getNameRule( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `rule_name` FROM #__alpha_userpoints_rules WHERE `plugin_function`=".$db->quote($plugin_function)." ";
		$db->setQuery( $q );
		$rule_name = $db->loadResult();
		return	$rule_name;	
	}
	
	public static function getPluginFunction( $id_rule )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `plugin_function` FROM #__alpha_userpoints_rules WHERE `id`=".$db->quote($id_rule)." ";
		$db->setQuery( $q );
		$plugin_function = $db->loadResult();
		return	$plugin_function;	
	}
	
	public static function getDatareference( $idActivity )
	{
		if ( $idActivity ) return;		
		$db	   = JFactory::getDBO();
		$q = "SELECT `datareference` FROM #__alpha_userpoints_details WHERE `id`=".$db->quote($idActivity)." ";
		$db->setQuery( $q );
		$datareference = $db->loadResult();
		return	$datareference;
	}
	
	public static function getMethod( $plugin_function )
	{
		$db	   = JFactory::getDBO();
		$q = "SELECT `method` FROM #__alpha_userpoints_rules WHERE `plugin_function`=".$db->quote($plugin_function)." AND `published`='1'";
		$db->setQuery( $q );
		$method = $db->loadResult();
		return $method;	
	}	
	
	public static function buildKeyreference( $plugin_function, $spc='', $userID=0 )
	{		
		$user =  JFactory::getUser();
		
		$method = AltaUserPointsHelper::getMethod( $plugin_function );
		
		$jnow	= JFactory::getDate();
		$date 	= $jnow->format('Y-m-d');
		$week	= $jnow->format('W');	
		$month	= $jnow->format('m');
		$year	= $jnow->format('Y');
		
		$keyreference = '';
		
		if (!$userID) $userID = $user->id;
		
		if ( $spc!='' ) $spc .= '|';
		
		switch ( $method )
		{
			case '1':		// ONCE PER USER
				$keyreference = $spc . $userID;
				break;
			case '2':		// ONCE PER DAY AND PER USER'		
				$keyreference = $spc . $userID .'|'.$date;
				break;
			case '3':		// ONCE A DAY FOR A SINGLE USER ON ALL USERS
				$keyreference = $spc . $date;
				break;
			case '5':       // ONCE PER USER PER WEEK
				$keyreference = $spc . $userID .'|'.$week.'|'.$year;
				break;
			case '6':       // ONCE PER USER PER MONTH
				$keyreference = $spc . $userID .'|'.$month.'|'.$year;
				break;
			case '7':       // ONCE PER USER PER YEAR
				$keyreference = $spc . $userID .'|'.$year;
				break;
			case '4':       // WHENEVER
			case '0':
			default:
				$keyreference = $spc . uniqid ( '', false );
		}
		
		return $keyreference;
		
	}
	
	public static function checkExcludeUsers($referreID)
	{	
		$db	   = JFactory::getDBO();
		$q = "SELECT referreid FROM #__alpha_userpoints 
		WHERE `referreid`=".$db->quote($referreID)." AND `published`='1'";
		$db->setQuery( $q );
		$result  = $db->loadResult();		
		if ( $result )	
			return 1;	
		return 0;
	}
	
	public static function checkRuleEnabled( $plugin_function='', $force=0, $referrerid='' )
	{	
		if ( !$plugin_function )
			return false;	
		$user 		= JFactory::getUser();	
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$userID		= 0;
		$accessrule = "";
		
		if ( !$referrerid )
		{
			$referrerid = @$_SESSION['referrerid'];
			if ( !$referrerid ) {
				$userID = $user->id;
			} else {
				$userID = AltaUserPointsHelper::getUserID( $referrerid );
			}
		} else {
			$userID = AltaUserPointsHelper::getUserID( $referrerid );
		}

		$authorizedLevels = JAccess::getAuthorisedViewLevels( $userID );
		
		switch ( $plugin_function )
		{		
			case 'sysplgaup_referralpoints': // assign to all referral users level
			case 'sysplgaup_emailnotification':
			case 'sysplgaup_winnernotification':
			case 'sysplgaup_newregistered':
			case 'sysplgaup_invitewithsuccess':
			case 'sysplgaup_archive':
			case 'sysplgaup_changelevel1':
			case 'sysplgaup_changelevel2':
			case 'sysplgaup_changelevel3':
				$accessrule = "";
				break;
			default:
				$accessrule = "AND `access` IN (" . implode ( ",", $authorizedLevels ) . ")";
				break;		
		}		
		if ( $force )
		{
			$accessrule = "";
		} 
		$db	   = JFactory::getDBO();		
		$q = "SELECT * FROM #__alpha_userpoints_rules 
		WHERE `plugin_function`=".$db->quote($plugin_function)." AND `published`='1' $accessrule 
		AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
		$db->setQuery( $q );
		$result  = $db->loadObjectList();
		return $result;
	}
	
	public static function checkReference( $referrerid, $keyreference, $ruleid )
	{	
		$db	   = JFactory::getDBO();
		
		$plugin_function = AltaUserPointsHelper::getPluginFunction( $ruleid );
		$method = AltaUserPointsHelper::getMethod( $plugin_function );
		
		switch ( $method )
		{
			case '3':
				$q = "SELECT count(*) FROM #__alpha_userpoints_details 
				WHERE `keyreference`=".$db->quote($keyreference)." AND `rule`=".$db->quote($ruleid)." AND enabled='1'";
				break;			
			default:
				$q = "SELECT count(*) FROM #__alpha_userpoints_details 
				WHERE `referreid`=".$db->quote($referrerid)." AND `keyreference`=".$db->quote($keyreference)." 
				AND `rule`=".$db->quote($ruleid)." AND enabled='1'";		
		}		
		$db->setQuery( $q );
		$resultKeyReference = $db->loadResult();
		// Build Key Reference change on format date version 1.9.3
		$resultKeyReference = str_replace('%', '', $resultKeyReference);
		return $resultKeyReference;	
	}
	
	public static function getReferreid( $userID )
	{	
		if ( !$userID ) return;	
		// get referre ID on login	
		$db	   = JFactory::getDBO();
		$q = "SELECT referreid FROM #__alpha_userpoints 
		WHERE `userid`=".$db->quote($userID);
		$db->setQuery( $q );
		$referreid = $db->loadResult();
		if ( $referreid )
		{		
			@session_start('altauserpoints');	
			$_SESSION['referrerid'] = $referreid;
			$expire=time()+60*60*24*30; //expires in 30 days
			setcookie("referrerid", $referreid, $expire);
		}	
	}
	
	public static function getAnyUserReferreID( $userID )
	{	
		// get referre ID for an author etc...
		$db	   = JFactory::getDBO();
		$q = "SELECT referreid FROM #__alpha_userpoints
		 WHERE `userid`=".$db->quote(intval($userID));
		$db->setQuery( $q );
		$referreid = $db->loadResult();		
		return  $referreid;		
	}
	
	public static function getUserInfo ( $referrerid='', $userid='' )
	{	
		if ( !$referrerid && !$userid ) return;	
		$db	   = JFactory::getDBO();
		if ( $referrerid ) {
			$where = "a.referreid=".$db->quote($referrerid)." ";		
		} elseif ( $userid ){		
			$where = "a.userid=".$db->quote($userid)." ";
		}			
		
		$q = "SELECT a.userid, a.referreid, a.upnid, a.points, a.max_points, a.last_update, " .
				 "a.referraluser, a.referrees, a.blocked, a.avatar, a.levelrank, a.leveldate, " .
				 " a.profileviews, a.shareinfos, a.id AS rid, u.* " .
				 "FROM #__alpha_userpoints AS a, #__users AS u " .
				 "WHERE $where AND a.userid=u.id";
		$db->setQuery( $q );
		$userinfo = $db->loadObjectList();
		return @$userinfo[0];
	}	
	
	public static function sendnotification ( $referrerid, $assignpoints, $newtotal, $result, $force=0 )
	{
		$app = JFactory::getApplication();	
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		if ( !$referrerid || $referrerid=='GUEST') return;
		$db	   = JFactory::getDBO();
		jimport( 'joomla.mail.helper' );		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		$jsNotification = $params->get('jsNotification', 0);
		$jsNotificationAdmin = $params->get('fromIdUddeim', 0);		
		
		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		$sef		= $app->getCfg('sef');		
		$userinfo   = AltaUserPointsHelper::getUserInfo( $referrerid );
		$email	    = $userinfo->email;
		
		$rule_name	 = $result->rule_name;
		$subject	 = $result->emailsubject;
		$body		 = $result->emailbody;
		$formatMail	 = $result->emailformat;
		$bcc2admin	 = $result->bcc2admin;
			
		if ( !$userinfo->block || $force )
		{		
			if ( $subject!='' && $body!='' )
			{
				$subject = str_replace('{username}', $userinfo->username, $subject);
				$subject = str_replace('{name}', $userinfo->name, $subject);
				$subject = str_replace('{email}', $userinfo->email, $subject);
				$subject = str_replace('{points}', AltaUserPointsHelper::getFPoints(abs($assignpoints)), $subject);
				$subject = str_replace('{newtotal}', AltaUserPointsHelper::getFPoints($newtotal), $subject);
				$body 	 = str_replace('{username}', $userinfo->username, $body);
				$body 	 = str_replace('{name}', $userinfo->name, $body);
				$body 	 = str_replace('{email}', $userinfo->email, $body);
				$body 	 = str_replace('{points}', AltaUserPointsHelper::getFPoints(abs($assignpoints)), $body);
				$body 	 = str_replace('{newtotal}', AltaUserPointsHelper::getFPoints($newtotal), $body);
				$body 	 = str_replace('{datareference}', $result->datareference, $body);
			} 
			else
			{ 
				// default message
				if ( $assignpoints>0 ) 
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG'), $SiteName, AltaUserPointsHelper::getFPoints($assignpoints), AltaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );	
				}
				 elseif ( $assignpoints<0 )
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT_ACCOUNT_UPDATED');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_REMOVE_POINTS'), $SiteName, AltaUserPointsHelper::getFPoints(abs($assignpoints)), AltaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );
				}
			}
			
			$subject = JMailHelper::cleanSubject($subject);		
			//$body    = JMailHelper::cleanBody($body);
			
			if ( !$jsNotification )
			{
				$mailer = JFactory::getMailer();
				$mailer->setSender( array( $MailFrom, $FromName ) );
				$mailer->setSubject( $subject);
				$mailer->isHTML((bool) $formatMail);
				if ( $formatMail ) {
					$mailer->Encoding = 'base64';
				}
				$mailer->CharSet = "utf-8";
				$mailer->setBody($body);
				$mailer->addRecipient( $email );
				if ( $formatMail ) {
					$mailer->Encoding = 'base64';
				}
				if ( $bcc2admin ) 
				{			
					// get all users allowed to receive e-mail system
					$q = "SELECT email" .
							" FROM #__users" .
							" WHERE sendEmail='1' AND block='0'";
					$db->setQuery( $q );
					$rowsAdmins = $db->loadObjectList();		
					
					foreach ( $rowsAdmins as $rowsAdmin ) {
						$mailer->addBCC( $rowsAdmin->email );
					}
				}		
				$send = $mailer->Send();
				
			}
			else
			{
			    require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
				//$actor = CFactory::getUser();
				$params = new CParameter('');
				CNotificationLibrary::add( 'system_messaging' , $jsNotificationAdmin , $userinfo->id , $subject , $body , '' , $params );			
				if ( $bcc2admin ) 
				{			
					// get all users allowed to receive e-mail system
					$q = "SELECT id" .
							" FROM #__users" .
							" WHERE sendEmail='1' AND block='0'";
					$db->setQuery( $q );
					$rowsAdmins = $db->loadObjectList();		
					
					foreach ( $rowsAdmins as $rowsAdmin ) {
						//$mailer->addBCC( $rowsAdmin->id );
						CNotificationLibrary::add( 'system_messaging' , $userinfo->id , $rowsAdmin->id , $subject , $body , '' , $params );
					}
				}		
			}
			
		}		
	}
	
	public static function sendUddeimNotification ( $referrerid, $assignpoints, $newtotal, $rule_name )
	{
		$app = JFactory::getApplication();
		
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		// integration Uddeim		
		if ( !$referrerid || $referrerid=='GUEST') return;
		
		// check if component installed
		$uddeim_exist = JPATH_ADMINISTRATOR.'/components/com_uddeim/admin.uddeimlib15.php';
		if ( !file_exists ($uddeim_exist) ) return;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		
		$SiteName	= $app->getCfg('sitename');			
		$userinfo   = AltaUserPointsHelper::getUserInfo( $referrerid );		
		$fromIdUddeim = intval($params->get('fromIdUddeim'));
			
		if ( !$userinfo->block && $fromIdUddeim>0 )
		{
			require_once JPATH_SITE ."/components/com_uddeim/uddeim.api.php" ;
			
			if ( $assignpoints>0 ) 
			{
				$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG'), $SiteName, AltaUserPointsHelper::getFPoints($assignpoints), AltaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );	
			}
			 elseif ( $assignpoints<0 )
			{
				$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_REMOVE_POINTS'), $SiteName, AltaUserPointsHelper::getFPoints(abs($assignpoints)), AltaUserPointsHelper::getFPoints($newtotal), JText::_($rule_name) );
			}
			$uddeimapi = new uddeIMAPI();
			$uddeimapi->sendNewMessage( $fromIdUddeim, $userinfo->userid , $message );
		}		
	}
	
	public static function sendUddeimWinnerNotification ( $referrerid, $assignpoints, $newtotal )
	{
		$app = JFactory::getApplication();
		
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		
		if ( !$referrerid || $referrerid=='GUEST') return;
		
		// check if component installed
		$uddeim_exist = JPATH_ADMINISTRATOR.'/components/com_uddeim/admin.uddeimlib15.php';
		if ( !file_exists ($uddeim_exist) ) return;
			
		$FromName	= $app->getCfg('fromname');		
		$userinfo 	= AltaUserPointsHelper::getUserInfo( $referrerid );
		$name 		= $userinfo->name;
		$fromIdUddeim = intval($params->get('fromIdUddeim'));

		if ( !$userinfo->block && $fromIdUddeim>0 )
		{	
			require_once JPATH_SITE . "/components/com_uddeim/uddeim.api.php";
			// send notification to winner
			$message = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_USER'), $name, AltaUserPointsHelper::getFPoints($newtotal) );		
			$uddeimapi = new uddeIMAPI();
			$uddeimapi->sendNewMessage( $fromIdUddeim, $userinfo->userid , $message );
		}
	
	}

	
	public static function sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins='' )
	{
		$app = JFactory::getApplication();
		jimport( 'joomla.mail.helper' );	
		
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		if ( !$referrerid || $referrerid=='GUEST') return;		
		$MailFrom	= $app->getCfg('mailfrom'); 	
		$FromName	= $app->getCfg('fromname');		
		$userinfo 	= AltaUserPointsHelper::getUserInfo( $referrerid );
		$name 		= $userinfo->name;
		$email	 	= $userinfo->email;
		$formatMail = 1;
		if ( !$userinfo->block )
		{				
			// send notification to winner
			$subject = JText::_('AUP_EMAILWINNERNOTIFICATION_SUBJECT_MSG_USER');
			$body = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_USER'), $name, AltaUserPointsHelper::getFPoints($newtotal) );		
			
			$mailer = JFactory::getMailer();
			$mailer->setSender( array( $MailFrom, $FromName ) );
			$mailer->setSubject( $subject);
			$mailer->isHTML((bool) $formatMail);
			if ( $formatMail ) {
					$mailer->Encoding = 'base64';
			}
			$mailer->CharSet = "utf-8";
			$mailer->setBody($body);
			$mailer->addRecipient( $email );
			if ( $emailadmins ) 
			{			
				$mailer->addBCC( $emailadmins );
			}		
			$send = $mailer->Send();		
		}
	
	}

	public static function getCurrentTotalPoints ( $referreid='', $userid=0, $formatted=0 )
	{	
		$db	   = JFactory::getDBO();
		$currenttotalpoints = 0;
		if ( $referreid!='' ) 
		{
			$q = "SELECT points FROM #__alpha_userpoints WHERE `referreid`=".$db->quote($referreid)." AND `blocked`='0'";
			$db->setQuery( $q );
			$currenttotalpoints = $db->loadResult();
		} elseif ( $userid>=1 ) {
			$q = "SELECT points FROM #__alpha_userpoints WHERE `userid`=".$db->quote(intval($userid))." AND `blocked`='0'";
			$db->setQuery( $q );
			$currenttotalpoints = $db->loadResult();
		} else 
			return;
		if ( $formatted ) $currenttotalpoints = AltaUserPointsHelper::getFPoints($currenttotalpoints);
		
		return $currenttotalpoints;		
	}
	
	public static function operationIsFeasible ( $referreid='', $numpoints )
	{
		if ( !$referreid ) return;
		$currentpoints = AltaUserPointsHelper::getCurrentTotalPoints ( $referreid );
		$newtotal = $currentpoints + $numpoints;
		if ( $newtotal >=0 ) 
			return true;
		else
			return false;	
	}
	
	public static function checkMaxDailyPoints( $referreid, $numpoints, $plugin_function='' )
	{	
		if ( !$referreid ) return;
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );
		
		// except for raffles, coupons code, bonus points, custom rule, profile etc...
		switch ( $plugin_function ) {
			case 'sysplgaup_raffle':
			case 'sysplgaup_couponpointscodes':
			case 'sysplgaup_buypointswithpaypal':
			case 'sysplgaup_bonuspoints':
			case 'sysplgaup_custom':
				return $numpoints;
				break;			
			default:
				$maxdailypoints = $params->get('limit_daily_points');		
		}
		
		if ( $maxdailypoints )
		{
			$dateofday = date("Y-m-d"); 
			
			$db	   = JFactory::getDBO();
				
			// Except Raffle rule...
			$q = "SELECT id FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_raffle'";
			$db->setQuery( $q );
			$idraffle = $db->loadResult();
			
			// select only positive points earned per day
			$q = "SELECT SUM(points) FROM #__alpha_userpoints_details 
			WHERE `referreid`=".$db->quote($referreid)." AND `points` >= '1' 
			AND `insert_date` LIKE '".$dateofday."%' AND `rule`!=".$db->quote($idraffle)." AND `enabled`='1'";
			$db->setQuery( $q );
			$totaldailypoints = $db->loadResult();
			
			$sptotal = $totaldailypoints + $numpoints;
			
			if ( $sptotal > $maxdailypoints ) 
			{
				$sbtotal = $sptotal - $maxdailypoints;
				$numpoints = $numpoints - $sbtotal;
			}
			if ($numpoints<0) $numpoints=0;
		}		
		
		return $numpoints;
	
	}
	
	public static function getUserRank ( $referrerid='', $userid=0 )
	{	
		if ( !$referrerid && !$userid ) return;	
		$db	   = JFactory::getDBO();		
		$q = "SELECT lv.*, aup.leveldate FROM #__alpha_userpoints_levelrank AS lv, #__alpha_userpoints AS aup 
		WHERE lv.id=aup.levelrank AND (aup.referreid=".$db->quote($referrerid)." OR aup.userid=".$db->quote($userid).")";
		$db->setQuery( $q );
		$userrankinfo = $db->loadObjectList();
		return @$userrankinfo[0];
		
		// detail/explain
		// return $userrankinfo->id
		// return $userrankinfo->rank (name of rank)
		// return $userrankinfo->description (description of rank)		
		// return $userrankinfo->levelpoints (level points to reach this rank)
		// return $userrankinfo->typerank (return 0)
		// return $userrankinfo->icon (name file icon)
		// return $userrankinfo->image (name file image)
	}
	
	public static function getUserMedals ( $referrerid='', $userid=0 )
	{	
		if ( !$referrerid && !$userid ) return;	
		$db	   = JFactory::getDBO();
		$q = "SELECT m.id, m.medaldate, m.reason, lv.rank, lv.description, lv.icon, lv.image "
				. "\nFROM #__alpha_userpoints_medals AS m, #__alpha_userpoints_levelrank AS lv, #__alpha_userpoints AS aup "
				. "\nWHERE m.medal=lv.id AND aup.id=m.rid AND (aup.referreid=".$db->quote($referrerid)." OR aup.userid=".$db->quote($userid).") "
				. "\nORDER BY m.medaldate DESC";
		$db->setQuery( $q );
		$medalslistuser = $db->loadObjectList();
		
		return @$medalslistuser;
		
		// detail/explain
		// sample -> for each ( $medalslistuser as medallistuser ) {
		// return $medallistuser->id
		// return $medallistuser->rank (name of medal)
		// return $medallistuser->description (reason for awarded)		
		// return $medallistuser->icon (name file icon)
		// return $medallistuser->image (name file image)
	}
	
	public static function checkRankMedal( $referrerid='', $rule_id=0 ) 
	{		
		$db	   = JFactory::getDBO();
		
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		
		$q = "SELECT COUNT(*) FROM #__alpha_userpoints_levelrank WHERE `levelpoints`>='0'";
		$db->setQuery( $q );
		$rankmedalexist = $db->loadResult();
		
		// check level/rank/ medals by level points
		if ( !$rankmedalexist || !$referrerid ) return;
		
		$userinfo = AltaUserPointsHelper::getUserInfo ( $referrerid );
		// $userinfo->rid = id of table #__alpha_userpoints
		$rid = $userinfo->rid;
		
		// check if current rule is assigned to a rank or medal
		$currentpoints = $userinfo->points;		

		// check current rank to reuse after	
		$q = "SELECT levelrank FROM #__alpha_userpoints WHERE referreid = ".$db->quote($userinfo->referreid);
		$db->setQuery( $q );
		$oldrank = $db->loadResult();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');				
		$row = JTable::getInstance('userspoints');

		// check level/rank
		$q = "SELECT * FROM #__alpha_userpoints_levelrank " .
		         "WHERE `levelpoints`>0 AND `typerank`='0' ORDER BY `levelpoints` ASC";
		$db->setQuery( $q );
		$ranks = $db->loadObjectList();
		
		$previousrank = 0;
		
		for ($i=0, $n=count( $ranks ); $i < $n; $i++)
		{
			$rank 	= $ranks[$i];
			
			if ( $rank->ruleid>0 ) {
				// recalculate actual total points for this rule
				$q = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
						 "WHERE rule=".$db->quote($rank->ruleid)." AND approved='1' AND referreid=".$db->quote($referrerid)." 
						 AND (`expire_date`>'$now' OR `expire_date`='0000-00-00 00:00:00') AND `enabled`='1'";
				$db->setQuery( $q );
				$currentpoints = $db->loadResult();
			} else $currentpoints = $userinfo->points;
			
			if ( $currentpoints < $rank->levelpoints )
			{
				// update user rank into alpha_userpoints table
				$row->load( $rid );
				$row->levelrank = $previousrank;
				$row->leveldate = $now;				
				$db->updateObject( '#__alpha_userpoints', $row, 'id' );
				break;
			}
			 else
			{
				// update new user rank into alpha_userpoints table
				$row->load( $rid );
				$row->levelrank = $rank->id;
				$row->leveldate = $now;				
				$db->updateObject( '#__alpha_userpoints', $row, 'id' );	
			}			
			
			$previousrank = $rank->id;
		}
	
		// check medal(s)
		$q = "SELECT * FROM #__alpha_userpoints_levelrank " .
				 "WHERE `levelpoints`>0 AND `typerank`='1' ORDER BY `levelpoints` DESC";
		$db->setQuery( $q );
		$medals = $db->loadObjectList();

		for ($i=0, $n=count( $medals ); $i < $n; $i++)
		{
			$medal = $medals[$i];
			
			if ( $medal->ruleid>0 )
			{
				// recalculate
				$q = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
						 "WHERE rule=".$db->quote($medal->ruleid)." AND approved='1' AND referreid=".$db->quote($referrerid)." 
						 AND (`expire_date`>".$db->quote($now)." OR `expire_date`='0000-00-00 00:00:00') AND `enabled`='1'";
				$db->setQuery( $q );
				$currentpoints = $db->loadResult();
			} else $currentpoints = $userinfo->points;
		
			if ( $medal->levelpoints <= $currentpoints )
			{			
				// check if user medal not already awarded
				$q = "SELECT COUNT(*) FROM #__alpha_userpoints_medals 
				WHERE `rid`=".$db->quote($rid)." AND `medal`=".$db->quote($medal->id);
				$db->setQuery( $q );
				$alreadyexist = $db->loadResult();
				if ( !$alreadyexist ) 
				{
					// add medal
					$q = "INSERT INTO #__alpha_userpoints_medals (`id`, `rid`, `medal`, `medaldate`, `reason`) 
					VALUES ( '', ".$db->quote($rid).", ".$db->quote($medal->id).", ".$db->quote($now).", ".$db->quote($medal->description).");";
					$db->setQuery( $q );
					$db->execute();					
					
					// load external plugins
					$results = $dispatcher->trigger( 'onUnlockMedalAltaUserPoints', array(&$userinfo, &$medal) );
		
					break;
				}				
			}
			elseif ( $medal->levelpoints > $currentpoints )
			{			
				// check if user medal not already awarded
				$q = "SELECT COUNT(*) FROM #__alpha_userpoints_medals 
				WHERE `rid`=".$db->quote($rid)." AND `medal`=".$db->quote($medal->id);
				$db->setQuery( $q );
				$allreadyexist = $db->loadResult();
				if ( $allreadyexist ) //if he has, we delete it as he should not have it
				{
					// delete medal					
					$q = "DELETE FROM #__alpha_userpoints_medals 
					WHERE `rid`=".$db->quote($rid)." AND `medal`=".$db->quote($medal->id);
					$db->setQuery( $q );
					$db->execute();
					break;
				}
			}			
		}
		// check new rank for JomSocial Activity Stream
		$q = "SELECT levelrank FROM #__alpha_userpoints WHERE referreid = ".$db->quote($userinfo->referreid);
		$db->setQuery( $q );
		$newrrank = $db->loadResult();

		$q = "SELECT * FROM #__alpha_userpoints_levelrank WHERE id = ".$db->quote($newrrank);
		$db->setQuery( $q );		
		$rankdetails = $db->loadObject();
		
		if( $oldrank !== $newrrank )
		{		
			// load external plugins
			$results = $dispatcher->trigger( 'onGetNewRankAltaUserPoints', array(&$userinfo, &$rankdetails ) );
		}
	
	}
	
	public static function getNextUserRank ( $referrerid='', $userid=0, $currentrank )
	{	
		if ( !$referrerid && !$userid ) return;
		//get all ranks info order by levelpoints	
		$db	   = JFactory::getDBO();		
		$q = "SELECT * FROM #__alpha_userpoints_levelrank WHERE `typerank`=0 ORDER BY `levelpoints` ASC;";
		$db->setQuery( $q );
		$nextrankinfo = $db->loadObjectList();
		//create a single level index array
		$rankindex = array();
		foreach ($nextrankinfo as $onerank) {
			//copy just rank to search for index
			$rankindex[]=$onerank->rank;
		}
		//find the index of the current rank
		$index = array_search( $currentrank, $rankindex );
		//increase index to next
		$index = $index + 1;

		return @$nextrankinfo[$index];
		

		// detail/explain
		// return $nextrankinfo->id
		// return $nextrankinfo->rank (name of rank)
		// return $nextrankinfo->description (description of rank)		
		// return $nextrankinfo->levelpoints (level points to reach this rank)
		// return $nextrankinfo->typerank (return 0)
		// return $nextrankinfo->icon (name file icon)
		// return $nextrankinfo->image (name file image)
	}
	
	public static function getAupLinkToProfil( $userid, $Itemid='', $xhtml=true )
	{
		if ( !$userid ) return;
		
		($Itemid!='') ? $theItemid = "&amp;Itemid=".$Itemid : $theItemid = AltaUserPointsHelper::getItemidAupProfil();
		$profile 	  = AltaUserPointsHelper::getUserInfo('', $userid);
		$linktoprofil = "index.php?option=com_altauserpoints&amp;view=account&amp;userid=" . $profile->referreid . $theItemid;
		$linktoprofil = JRoute::_($linktoprofil, $xhtml);
		return $linktoprofil;
		
		// USAGE
		// $linktoAUPprofil = AltaUserPointsHelper::getAupLinkToProfil($userid);   OR  $linktoAUPprofil = AltaUserPointsHelper::getAupLinkToProfil($userid, $YourItemid); 
		// Think to call and include this API helper.php in your script
	}
	
	public static function getAupUsersURL($xhtml = true)
    {
        $db       = JFactory::getDBO();
        $q = "SELECT id FROM #__menu 
		WHERE `link`='index.php?option=com_altauserpoints&view=users' 
		AND `type`='component' AND `published`='1'";
        $db->setQuery( $q );
        $AupItemidUsers = $db->loadResult();
        $AupUsersURL = "index.php?option=com_altauserpoints&amp;view=users&amp;Itemid=" . $AupItemidUsers;
        $AupUsersURL = JRoute::_($AupUsersURL, $xhtml);  
        return $AupUsersURL;
    }
	
	public static function getItemidAupProfil()
	{
		$db	= JFactory::getDBO();
		$q 	= "SELECT id FROM #__menu 
		WHERE `link`='index.php?option=com_altauserpoints&view=account' 
		AND `type`='component' AND `published`='1'";
		$db->setQuery( $q );
		$AupItemidProfile = $db->loadResult();	
		return $AupItemidProfile;
	}
	
	public static function displayMessageSystem($message) 
	{
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_($message) );
		return true;
	}
	
	public static function getAupVersion()
	{
		require_once JPATH_ROOT.'/administrator/components/com_altauserpoints/assets/includes/version.php';
		return _ALTAUSERPOINTS_NUM_VERSION;
	}
	
	// get list of activities 
	// $params  $type = all | positive | negative
	// $params  $userid = all or unique userID
	// $params  $limit = int (0 by default)  
	// example-1 -> --------------------------------------------------------------------------
	// example-1 -> $listActivity = AltaUserPointsHelper::getListActivity('all', 'all');
	// example-1 SAME AS -> $listActivity = AltaUserPointsHelper::getListActivity();
	// example-1 -> show all acivities with pagination, positive and negative points of activity for all users
	// ------------------------------------------------------------------------------------
	// example-2 -> --------------------------------------------------------------------------
	// example-2 -> $user =  JFactory::getUser();
	// example-2 -> $userID = $user->id;
	// example-2 -> $listActivity = AltaUserPointsHelper::getListActivity('positive', $userID, 20);
	// example-2 -> show only positive points of activity for the current user logged in and show only 20 rows of recent activities
	//
	// return an array or $rows
	//
	// list of available fields :
	// insert_date
	// referreid
	// points (of this activity)
	// datareference
	// rule_name
	// plugin_function
	// category	
	
	public static function getListActivity($type='all', $userid='all', $numrows=0)
	{
		$app = JFactory::getApplication();
		$db	 = JFactory::getDBO();
		$nullDate	= $db->getNullDate();
		$date 		= JFactory::getDate();
		$now  		= $date->toSql();
		$typeActivity = "";
		$userID = "";
		
		switch ( $type )
		{			
			case 'positive':
				$typeActivity = " AND a.points >= 0";
				break;
			case 'negative':
				$typeActivity = " AND a.points <= 0";
				break;
			case 'all':
			default:
				$typeActivity = "";
		}
		
		switch ( $userid )
		{	
			case ( $userid>0 && $userid!='all' ):
				$userID = " AND aup.userid=".$db->quote($userid);
				break;
			case ( $userid=0 ):
				return;
			case 'all':
			default:
				$userID = "";
		}
		
		$q = "SELECT a.insert_date, a.referreid, a.points, aup.points AS last_total_points, a.datareference, 
		r.rule_name, r.plugin_function, r.category 
		FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r 
		WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.enabled='1' 
		AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule AND r.displayactivity='1'"
			   . $typeActivity
			   . $userID 
			   . " ORDER BY a.insert_date DESC"
			   ;
			   
		$totalquery = "SELECT COUNT(a.referreid) 
		FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r 
		WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.enabled='1' 
		AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule AND r.displayactivity='1'"
			   . $typeActivity
			   . $userID 
			   ;
			   
		$db->setQuery( $totalquery );
		$total = $db->loadResult();			
		
		if( $numrows )
		{				
			$q .= " LIMIT ". intval($numrows);
			$db->setQuery( $q );
			$result = $db->loadObjectList();			
			return $result;
		}
		else
		{
				// Get the pagination request variables
			$limit = JFactory::getApplication()->input->get('limit', $app->getCfg('list_limit'), 'int');
			$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
								
			// In case limit has been changed, adjust limitstart accordingly
			$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);		
			$q .= " LIMIT ". intval($limitstart) . ", " . intval($limit);
			$db->setQuery( $q );
			$result = $db->loadObjectList();
			return array($result, $total, $limit, $limitstart);
		}
	}
	
	 public static function getFPoints( $points )
	 {	 
		$params = JComponentHelper::getParams( 'com_altauserpoints' );
		$formatPoints = $params->get( 'formatPoints', '0' );
		switch( $formatPoints )
		{
			case "1":
				$fpoints = number_format($points, 2, '.', ','); // english format
				break;
			case "2":
				$fpoints = number_format($points, 2, ',', '');
				break;
			case "3":
				$fpoints = number_format($points, 2, ',', ' '); // french format
				break;
			case "4":
				$fpoints = number_format($points, 0);
				break;
			case "5":
				$fpoints = number_format($points, 0, '', ' ');
				break;
			case "6":
				$fpoints = number_format($points, 0, '', ',');
				break;
			case "7":				
				$fpoints = number_format(floor($points), 0);
				break;
			case "8":
				$fpoints = number_format(floor($points), 0, '', ' ');
				break;
			case "9":
				$fpoints = number_format(floor($points), 0, '', ',');
				break;		
			case "0":
			default:
				$fpoints = $points; 
		}		
	 	return $fpoints;
	}
}
?>