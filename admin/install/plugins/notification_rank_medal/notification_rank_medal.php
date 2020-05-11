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
class plgAltauserpointsNotification_rank_medal extends JPlugin
{
	public function onUnlockMedalAltaUserPoints( &$userinfo, &$medal )
	{	
		if ( !$medal->notification ) return;
		$this->sendNotificationOnUpdateRank( $userinfo, $medal );		
	
	}
	
	
	public function onGetNewRankAltaUserPoints( &$userinfo, &$rankdetail )
	{	
		if ( !$rankdetail->notification ) return;
		
		$this->sendNotificationOnUpdateRank( $userinfo, $rankdetail );
			
	}
	private function sendNotificationOnUpdateRank ( $userinfo, $result )
	{
		$app 	= JFactory::getApplication();	
		$db 	= JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$lang->load( 'com_altauserpoints', JPATH_SITE);
	
		jimport( 'joomla.mail.helper' );		
		
		require_once JPATH_ROOT . '/components/com_altauserpoints/helper.php';
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		$jsNotification = $params->get('jsNotification', 0);
		$jsNotificationAdmin = $params->get('fromIdUddeim', 0);		
		
		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		$sef		= $app->getCfg('sef');		
		
		$email	    = $userinfo->email;
		
		$subject	= $result->emailsubject;
		$body		= $result->emailbody;
		$formatMail	= $result->emailformat;
		$bcc2admin	= $result->bcc2admin;
		
		
		$subject = str_replace('{username}', $userinfo->username, $subject);
		$subject = str_replace('{name}', $userinfo->name, $subject);
		$subject = str_replace('{email}', $userinfo->email, $subject);
		$subject = str_replace('{points}', AltaUserPointsHelper::getFPoints($userinfo->points), $subject);
		$body 	 = str_replace('{username}', $userinfo->username, $body);
		$body 	 = str_replace('{name}', $userinfo->name, $body);
		$body 	 = str_replace('{email}', $userinfo->email, $body);
		$body 	 = str_replace('{points}', AltaUserPointsHelper::getFPoints($userinfo->points), $body);
		
		$subject = JMailHelper::cleanSubject($subject);		
		
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
			$send = $mailer->Send();
			
		}
		else
		{
			require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
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
					$mailer->addBCC( $rowsAdmin->id );
					CNotificationLibrary::add( 'system_messaging' , $userinfo->id , $rowsAdmin->id , $subject , $body , '' , $params );
				}
			}		
		}
	}
}
?>