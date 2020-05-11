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

class altauserpointsModelHelper extends JmodelLegacy {

	function __construct()
	{
		parent::__construct();
	}
	
	function _aup_publish( $cid=null, $publish=1, $option, $table, $redirect ) 
	{
		$app = JFactory::getApplication();
		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
		
			// initialize variables
			$db		=  JFactory::getDBO();
			
			if (count($cid) < 1) 
			{
				$action = ( $publish == 1 )? 'publish' : 'unpublish';
				JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
				return false;
			}
			
			$cids	= implode(',', $cid);
	
			$query = "UPDATE #__$table" .
			"\n SET published = $publish" .
			"\n WHERE id IN ( $cids )"
			;
			$db->setQuery( $query );
	
			if (!$db->query()) 
			{
				JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg() ,'error');
				return false;
			}		
			
			if ( $table == 'alpha_userpoints_rules' ) 
			{ 		
				// clean cache
				$cache	=  JFactory::getCache();
				$cache->cleanCache();
				
				// check new user rule is already enabled
				$query = "UPDATE #__$table" .
				"\n SET `published`= '1'" .
				"\n WHERE `plugin_function`='sysplgaup_newregistered'"
				;
				$db->setQuery( $query );
		
				if (!$db->query()) 
				{
					JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg(),'error' );
					return false;
				}		
			}
			
		} // end if authorize to change state
				
		$redirecturl = "index.php?option=$option&task=$redirect";		
		
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect(); 				

	}
	
	function _aup_autoapprove( $cid=null, $autoapprove=1, $option, $table, $redirect ) 
	{
		$app = JFactory::getApplication();
		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
		
			// initialize variables
			$db		=  JFactory::getDBO();		
			
			if (count($cid) < 1) 
			{
				$action = ( $autoapprove == 1 )? 'autoapprove' : 'unautoapprove';
				JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
				return false;
			}
			
			$cids	= implode(',', $cid);
	
			$query = "UPDATE #__$table" .
			"\n SET autoapproved = $autoapprove" .
			"\n WHERE id IN ( $cids )"
			;
			$db->setQuery( $query );
	
			if (!$db->query()) 
			{
				JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg(),'error' );
				return false;
			}		
			
			// check new user rule is already enabled
			$query = "UPDATE #__$table" .
			"\n SET `autoapproved`= '1'" .
			"\n WHERE `plugin_function`='sysplgaup_newregistered'"
			;
			$db->setQuery( $query );
	
			if (!$db->query()) 
			{
				JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg() , 'error' );
				return false;
			}	
			
		} // end if authorize to change state	
				
		$redirecturl = "index.php?option=$option&task=$redirect";		
		
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();
	}
	
	function _aup_approve( $cid=null, $approved=1, $option, $table, $redirect ) 
	{
		$app = JFactory::getApplication();
		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
		
			// pending approval		
			$db		=  JFactory::getDBO();
			
			if (count($cid) < 1) 
			{
				$action = ( $approved == 1 )? 'approve' : 'unapprove';
				JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
				return false;
			}
			
			$cids	= implode(',', $cid);
	
			$query = "UPDATE #__$table" .
			"\n SET approved = $approved" .
			"\n WHERE id IN ( $cids )"
			;
			$db->setQuery( $query );
	
			if (!$db->query())
			{
				JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg(),'error' );
				return false;
			}
			
			// Updade points member account and check the new status
			if ( $approved )  
			{
				foreach ( $cid as $id ) {
					$this->updateUserAccount($id, 0);
				}
			}
		
		} // end if authorize to change state
		
		$redirecturl = "index.php?option=$option&task=$redirect";
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();
		
	}
	
	function updateUserAccount($cid, $message=1) 
	{
		// cid is the ID of action with points stored in #__alpha_userpoints_details on pending approval
		$db	   = JFactory::getDBO();
		
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		
		// check status			
		$query = "SELECT a.*, r.*"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules AS r"
			   . " WHERE a.id='$cid' AND a.rule=r.id AND a.enabled='1'";
		$db->setQuery( $query );
		$details = $db->loadObjectList();
		
		if ( !$details ) return;
		
		// already approved -> exit
		if ( $details[0]->status ) return;
		
		$query = "SELECT id, referraluser FROM #__alpha_userpoints WHERE `referreid`='".$details[0]->referreid."'";
		$db->setQuery( $query );
		$aupUser = $db->loadObjectList();
		$referrerUser = $aupUser[0]->id;
		$referraluser = $aupUser[0]->referraluser;
	
		$row = JTable::getInstance('userspoints');
		
		// update points into alpha_userpoints table
		$row->load( intval($referrerUser) );
		
		$assignpoints = $details[0]->points;
		$referrerid   = $details[0]->referreid;
		
		$newtotal = $row->points + $assignpoints ;
		
		$row->last_update	= $now;
		
		if ( $details[0]->plugin_function=='sysplgaup_invitewithsuccess' )
		{
			// update number referrees
			$row->referrees = $row->referrees+1;
		}
		
		$checkWinner = 0;
		if ( $row->max_points >=1 && ( $newtotal > $row->max_points ) ) 
		{
			// Max total was reached !
			$newtotal = $row->max_points;
			// HERE YOU CAN ADD MORE FUNCTIONS! example call other component etc...
			if ( $this->checkRuleIsEnabled( 'sysplgaup_winnernotification' ) ) 
			{
				// get email admins in rule
				$query = "SELECT `content_items` FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_winnernotification'";
				$db->setQuery( $query );
				$emailadmins = $db->loadResult();
				$this->sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins );				
				$checkWinner = 1;
			}
		}		
								
		$row->points		= $newtotal;

		$db->updateObject( '#__alpha_userpoints', $row, 'id' );
		
		$userinfo = $this->getUserInfos ( $referrerid );
		
		$result = $this->checkRuleIsEnabled( $details[0]->plugin_function );
		if ( $result->notification && !$checkWinner ) $this->sendnotification ( $referrerid, $assignpoints, $newtotal, $result, 0, $userinfo->username );
		
		// Assign status = 1
		$query = "UPDATE #__alpha_userpoints_details" .
		"\n SET status='1'" .
		"\n WHERE id ='$cid'"
		;
		$db->setQuery( $query );

		if (!$db->query()) 
		{
			JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg(),'error' );
			return false;
		}
		
		require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
		
		// If referral user exist		
		if ( $referraluser!='' && $details[0]->plugin_function!='sysplgaup_buypointswithpaypal' && $details[0]->plugin_function!='sysplgaup_raffle' && $details[0]->plugin_function!='sysplgaup_referralpoints' ) {
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_referralpoints' AND `published`='1' AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
			$db->setQuery( $query );
			$referralpoints = $db->loadObjectList();
			if ( $referralpoints ){			
				$referraluserpoints = round(($details[0]->points*$referralpoints[0]->points)/100, 2) ;
				if ( $referraluserpoints>0 ) {					
					AltaUserPointsHelper::userpoints( 'sysplgaup_referralpoints', $referraluser, $referraluserpoints );
					$this->checkTotalAfterRecalculate( $referraluser, $details[0]->rule );
				}
			}			
		}

		// recalculate for this user 
		$query = "SELECT SUM(points) FROM #__alpha_userpoints_details WHERE `referreid`='" . $details[0]->referreid . "' AND `approved`='1' AND (`expire_date`>'$now' OR `expire_date`='0000-00-00 00:00:00') AND `enabled`='1'";
		$db->setQuery($query);
		$newtotal = $db->loadResult();

		$query = "UPDATE #__alpha_userpoints SET `points`='" . $newtotal . "', `last_update`='$now' WHERE `referreid`='" . $details[0]->referreid . "'";
		$db->setQuery( $query );
		$db->query();
		
		// update Ranks / Medals if necessary
		AltaUserPointsHelper::checkRankMedal ( $details[0]->referreid, $details[0]->rule );

	}
	
	function getUserInfos ( $referrerid='', $userid='' )
	{	
		if ( !$referrerid && !$userid ) return;
		
		$db	   = JFactory::getDBO();
		
		if ( $referrerid ) {
			$where = "a.referreid='$referrerid'";
		} elseif ( $userid ){		
			$where = "a.userid='$userid'";
		}		
		
		$query = "SELECT a.userid, a.referreid, a.upnid, a.points, a.max_points, a.last_update, " .
				 "a.referraluser, a.referrees, a.blocked,  a.avatar, a.levelrank, a.leveldate, " .
				 " a.profileviews," .
				 "a.id AS rid, u.* " .
				 "FROM #__alpha_userpoints AS a, #__users AS u " .
				 "WHERE $where AND a.userid=u.id";
		$db->setQuery( $query );
		$userinfo = $db->loadObjectList();
		return @$userinfo[0];
	}	

	
	function checkRuleIsEnabled( $plugin_function='' ) 
	{
	
		if ( !$plugin_function ) return false;
	
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		
		$db	   = JFactory::getDBO();		
		$query = "SELECT id FROM #__alpha_userpoints_rules WHERE `plugin_function`='$plugin_function' AND `published`='1' AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
		
		$db->setQuery( $query );
		$result  = $db->loadResult();
		return $result;
	
	}

	function sendnotification ( $referrerid, $assignpoints, $newtotal, $result, $force=0, $username='' )
	{
		$app = JFactory::getApplication();	
		
		if ( !$referrerid || $referrerid=='GUEST') return;	
		
		$db	   = JFactory::getDBO();
		$user  =  JFactory::getUser($username);		
		
		jimport( 'joomla.mail.helper' );
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		
		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		$sef		= $app->getCfg('sef');		
		$userinfo   = AltaUserPointsHelper::getUserInfo( $referrerid );
		$email	    = $userinfo->email;
		
		$rule_name	= $result->rule_name;
		$subject	= $result->emailsubject;
		$body		= $result->emailbody;
		$formatMail	= $result->emailformat;
		$bcc2admin	= $result->bcc2admin;
			
		if ( !$userinfo->block || $force )
		{		
		
			if ( $subject!='' && $body!='' ) {
				
				$subject = str_replace('{username}', $user->username, $subject);
				$subject = str_replace('{name}', $user->name, $subject);
				$subject = str_replace('{email}', $user->email, $subject);
				$subject = str_replace('{points}', abs($assignpoints), $subject);
				$subject = str_replace('{newtotal}', $newtotal, $subject);
				$body 	 = str_replace('{username}', $user->username, $body);
				$body 	 = str_replace('{name}', $user->name, $body);
				$subject = str_replace('{email}', $user->email, $subject);
				$body 	 = str_replace('{points}', abs($assignpoints), $body);
				$body 	 = str_replace('{newtotal}', $newtotal, $body);
				
			} else {
				if ( $assignpoints>0 ) 
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG'), $SiteName, $assignpoints, $newtotal, JText::_($rule_name) );	
				}
				 elseif ( $assignpoints<0 )
				{
					$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT_ACCOUNT_UPDATED');
					$body = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_REMOVE_POINTS'), $SiteName, abs($assignpoints), $newtotal, JText::_($rule_name) );
				}
			}
			
			$subject = JMailHelper::cleanSubject($subject);		
			$body    = JMailHelper::cleanBody($body);
			
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
			if ( $bcc2admin ) 
			{			
				// get all users allowed to receive e-mail system
				$query = "SELECT email" .
						" FROM #__users" .
						" WHERE sendEmail='1' AND block='0'";
				$db->setQuery( $query );
				$rowsAdmins = $db->loadObjectList();		
				
				foreach ( $rowsAdmins as $rowsAdmin ) {
					$mailer->addBCC( $rowsAdmin->email );
				}
			}		
			$send =& $mailer->Send();
			
		}		
	}

	
	function sendwinnernotification ( $referrerid, $assignpoints, $newtotal, $emailadmins='' ) 
	{
		$app = JFactory::getApplication();
		jimport( 'joomla.mail.helper' );	
		
		$MailFrom	= $app->getCfg('mailfrom'); 	
		$FromName	= $app->getCfg('fromname'); 
		
		$userinfo 	= $this->getUserInfos( $referrerid );
		$name 		= $userinfo->name;
		$email	 	= $userinfo->email;
		$formatMail = 1;

		if ( !$userinfo->block ) 
		{		
		
			// send notification to winner
			$subject = JText::_('AUP_EMAILWINNERNOTIFICATION_SUBJECT_MSG_USER');
			$body = sprintf ( JText::_('AUP_EMAILWINNERNOTIFICATION_MSG_USER'), $name, $newtotal );
			
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
	
	function _bonuspoints ( $cids ) 
	{
		$app = JFactory::getApplication();
	
		// initialize variables
		$db		=  JFactory::getDBO();	
		
		$query = "SELECT `id` FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_bonuspoints'";
		$db->setQuery( $query );
		$rule_id = $db->loadResult();

		JArrayHelper::toInteger($cids);
		
		if (count($cids)) 
		{		
			require_once ( JPATH_SITE.'/components/com_altauserpoints/helper.php' );			
			foreach( $cids as $cid ) 
			{			
				$query = "SELECT referreid FROM #__alpha_userpoints WHERE id=".$db->quote($cid)."";
				$db->setQuery( $query );
				$referrerid = $db->loadResult();
				if ( $referrerid )
				{
					AltaUserPointsHelper::userpoints ( 'sysplgaup_bonuspoints' , $referrerid, 0, '', JText::_('AUP_BONUSPOINTS') );
					$this->checkTotalAfterRecalculate( $referrerid, $rule_id );
					
				}
			}			
			$app->enqueueMessage( JText::_('AUP_RECALCULATION_MADE' ) );
		}
		$redirecturl = "index.php?option=com_altauserpoints&task=statistics";		
		
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();

	}
	
	function _aup_registration_raffle( $cid=null, $regitration=1, $option, $table, $redirect )
	{
		$app = JFactory::getApplication();
		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			
			// initialize variables
			$db		=  JFactory::getDBO();		
			
			if (count($cid) < 1) 
			{
				$action = ( $regitration == 1 )? 'regitration' : 'unregitration';
				JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
				return false;
			}
			
			$cids	= implode(',', $cid);
	
			$query = "UPDATE #__$table" .
			"\n SET inscription = $regitration" .
			"\n WHERE id IN ( $cids )"
			;
			$db->setQuery( $query );
	
			if (!$db->query()) 
			{
				JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg(),'error' );
				return false;
			}		
			
		} // end if authorize to change state
		
		$redirecturl = "index.php?option=$option&task=$redirect";		
		
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();
	}

	function _customrulepoints ( $cids, $reason, $points ) 
	{
		$app = JFactory::getApplication();
	
		// initialize variables
		$db		=  JFactory::getDBO();
		
		$query = "SELECT `id` FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_custom'";
		$db->setQuery( $query );
		$rule_id = $db->loadResult();

		JArrayHelper::toInteger($cids);
		
		if (count($cids)) 
		{		
			require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';			
			foreach( $cids as $cid ) 
			{			
				$query = "SELECT referreid FROM #__alpha_userpoints WHERE id=".$db->quote($cid)."";
				$db->setQuery( $query );
				$referrerid = $db->loadResult();
				if ( $referrerid )
				{					
					AltaUserPointsHelper::userpoints ( 'sysplgaup_custom', $referrerid, 0, '', $reason, $points );
					$this->checkTotalAfterRecalculate( $referrerid, $rule_id );
					
				}
			}			
			$app->enqueueMessage( JText::_('AUP_RECALCULATION_MADE' ) );
		}
		$redirecturl = "index.php?option=com_altauserpoints&task=statistics";		
		
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();

	}
	
	function checkTotalAfterRecalculate( $referrerid, $rule_id=0 )
	{
		$db			= JFactory::getDBO();
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();
		
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
		
		// recalculate for this user 
		$query = "SELECT SUM(points) FROM #__alpha_userpoints_details WHERE `referreid`='" . $referrerid . "' AND `approved`='1' AND (`expire_date`>'$now' OR `expire_date`='0000-00-00 00:00:00') AND `enabled`='1'";
		$db->setQuery($query);
		$newtotal = $db->loadResult();

		$query = "UPDATE #__alpha_userpoints SET `points`='" . $newtotal . "', `last_update`='$now' WHERE `referreid`='" . $referrerid . "'";
		$db->setQuery( $query );
		$db->query();
		
		// update Ranks / Medals if necessary		
		AltaUserPointsHelper::checkRankMedal ( $referrerid, $rule_id );
	
	}
	
	/**
	 * Get a list of the user groups for filtering.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	function getGroups()
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JFactory::getApplication()->enqueueMessage( $db->getErrorMsg(),'notice');
			return null;
		}

		foreach ($options as &$option) {
			$option->text = str_repeat('- ',$option->level).$option->text;
		}

		return $options;
	}
	
	function getGenrericGroupsList( $id=0 )
	{
		$list = '';
		$groups = $this->getGroups();
		
		foreach ($groups as &$group) {
			$options[] = JHTML::_('select.option', $group->value, $group->text );
		}
		
		$list = JHTML::_('select.genericlist', $options, 'content_items', 'class="inputbox" size="1"' ,'value', 'text', $id );
		
		return $list;
	}
		
}
?>