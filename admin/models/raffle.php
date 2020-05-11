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

class altauserpointsModelRaffle extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _load_raffle() {
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
		
		$total 			= 0;
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		$filter_category_id = JFactory::getApplication()->input->get('filter_category_id', 0, 'int');	
		
		$filter = '';
		if ( $filter_category_id ) {			
			$filter .= " WHERE r.category='".$filter_category_id."'";		
		}
		
		/*
		$query = "SELECT r.*, COUNT(ri.id) AS numregistered, c.title AS category_title FROM #__alpha_userpoints_raffle AS r, #__categories AS c"
		. " LEFT JOIN #__alpha_userpoints_raffle_inscriptions AS ri ON ri.raffleid=r.id"
		. " WHERE c.id=r.category"
		. $filter
		. " GROUP BY r.id"
		. " ORDER BY r.id DESC";
		*/
		
		$query ="SELECT r . * , COUNT( ri.id ) AS numregistered, c.title AS category_title"
		. " FROM #__alpha_userpoints_raffle AS r"
		. " LEFT JOIN #__alpha_userpoints_raffle_inscriptions AS ri ON r.id = ri.raffleid"
		. " LEFT JOIN #__categories AS c ON r.category = c.id"
		. $filter
		. " GROUP BY r.id"
		. " ORDER BY r.id DESC";		
		$total = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		// message if rule disabled
		$query = "SELECT id FROM #__alpha_userpoints_rules WHERE plugin_function = 'sysplgaup_raffle' AND published = '1'";
		$db->setQuery($query);
		$alert = $db->loadResult();
		
		if ( !$alert ) $app->enqueueMessage( JText::_('AUP_THIS_RULE_IS_DISABLED'));		
		
		$lists = array();
		$lists['filter_category_id'] = $filter_category_id;
		
		return array($result, $total, $limit, $limitstart, $lists);
	
	}	
	
	function _edit_raffle() {
	
		$db     = JFactory::getDBO();

		$cid 	= JFactory::getApplication()->input->get('cid', array(0), 'array');
		$option = JFactory::getApplication()->input->get('option', '', 'cmd');
		
		if (!is_array( $cid )) {
			$cid = array(0);
		}

		$lists = array();

		$row = JTable::getInstance('raffle');
		$row->load( $cid[0] );
		
		$lists['filter_category_id'] = $row->category;
		
		return array($row, $lists);
	
	}	
	
	function _delete_raffle() {
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {		
			
			$query = "DELETE FROM #__alpha_userpoints_raffle"
					. "\n WHERE (`id` = " . implode(' OR `id` = ', $cid) . ")"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
			
			$query = "DELETE FROM #__alpha_userpoints_raffle_inscriptions"
					. "\n WHERE (`raffleid` = " . implode(' OR `raffleid` = ', $cid) . ")"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}

		}

		$app->redirect('index.php?option=com_altauserpoints&task=raffle', $msg, $msgType);
		//JControllerLegacy::redirect(); 				

	}
	
	function _save_raffle($apply=0) {
		$app = JFactory::getApplication();

		// initialize variables
		$db = JFactory::getDBO();
		//$post	= $app->input->post;
		$post	= JRequest::get( 'post' );
		$row = JTable::getInstance('raffle');
		
		$id= $app->input->get( 'id', 0, 'int' );

		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$msg = JText::_( 'AUP_DETAILSSAVED' );
		if (!$apply){
			$app->redirect('index.php?option=com_altauserpoints&task=raffle', $msg);
		} else {
			$app->redirect('index.php?option=com_altauserpoints&task=editraffle&cid[]='.$id, $msg);
		}
		//JControllerLegacy::redirect(); 
	}
	
	function _make_raffle_now() {
	
		$app = JFactory::getApplication();
		
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';	
		
		$db = JFactory::getDBO();
		
		// Proceed raffle now
		$cid 	= JFactory::getApplication()->input->get('cid', array(0), 'array');
		$rowRaffle = $this->_edit_raffle();
		$rowRaffle = $rowRaffle[0];
		
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
				
		// add offset
		$config = JFactory::getConfig();
		$tzoffset = $config->get('config.offset');
		$datetimestamp = strtotime($now);
		$now = date('Y-m-d H:i:s', $datetimestamp + ($tzoffset * 60 * 60));		
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );
		
		// Raffle date is not today ?
		if ( $rowRaffle->raffledate!='0000-00-00 00:00:00' && $now < $rowRaffle->raffledate ){
			echo "<script> alert('".JText::_( 'AUP_DATE_OF_RAFFLE_IS_NOT_TODAY' )."'); window.history.go(-1); </script>\n";
			exit();	
		}
		
		// load external plugins
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('altauserpoints');
		$results = $dispatcher->trigger( 'onBeforeMakeRaffleAltaUserPoints', array(&$rowRaffle, $now) ); 
		
		$winner1 = 0;
		$winner2 = 0;
		$winner3 = 0;
		$winner1_Referreid = 0;
		$winner2_Referreid = 0;
		$winner3_Referreid = 0;
		
		// exclude users if rule enabled
		$queryExclude =  " AND `published`='1'";		
	
		// first raffle
		// select all users registered or only users with registration for this raffle 
		if ( $rowRaffle->inscription ) 
		{			
			$query = "SELECT id, userid as uid FROM #__alpha_userpoints_raffle_inscriptions WHERE raffleid=" . $rowRaffle->id;	
		} 
		else 
		{
			$query = "SELECT userid as uid FROM #__alpha_userpoints WHERE blocked='0'".$queryExclude;
		}		
		$db->setQuery($query);
		$listParticipants = $db->loadObjectList();
		
		if ( !$listParticipants ) 
		{ 
			echo "<script> alert('".JText::_( 'AUP_NO_PARTICIPANT' )."'); window.history.go(-1); </script>\n";
			exit();
		}	
		
		$max = count($listParticipants)-1;		
		$choice = rand(0, $max);		
		$winner1 = $listParticipants[$choice]->uid;		

		if ( $rowRaffle->numwinner>1 )
		{
			// 2th raffle without first winner
			if ( $rowRaffle->inscription )
			{			
				$query = "SELECT userid as uid FROM #__alpha_userpoints_raffle_inscriptions WHERE raffleid=" . $rowRaffle->id . " AND userid!='$winner1'";
			} 
			else 
			{
				$query = "SELECT userid as uid FROM #__alpha_userpoints WHERE blocked='0' AND userid!='$winner1'".$queryExclude;
			}
			$db->setQuery($query);
			$listParticipants2 = $db->loadObjectList();
	
			$max2 = count($listParticipants2)-1;
			$choice2 = rand(0, $max2);
			$winner2 = @$listParticipants2[$choice2]->uid;
		}
		
		if ( $rowRaffle->numwinner==3 )
		{
			// 3th raffle without first and second winner
			if ( $rowRaffle->inscription ) 
			{			
				$query = "SELECT userid as uid FROM #__alpha_userpoints_raffle_inscriptions WHERE raffleid=" . $rowRaffle->id . " AND userid!='$winner1' AND userid!='$winner2'";		
			} 
			else 
			{
				$query = "SELECT userid as uid FROM #__alpha_userpoints WHERE blocked='0' AND userid!='$winner1' AND userid!='$winner2'".$queryExclude;
			}
			$db->setQuery($query);
			$listParticipants3 = $db->loadObjectList();
			
			$max3 = count($listParticipants3)-1;
			$choice3 = rand(0, $max3);
			$winner3 = @$listParticipants3[$choice3]->uid;
		}
		
		// Save winner(s)
		$row = JTable::getInstance('raffle');
		$row->load( $rowRaffle->id );
		
		$row->winner1 = $winner1;
		$row->winner2 = $winner2;
		$row->winner3 = $winner3;
		
		if (!$row->store()) {
			JFactory::getApplication()->enqueueMessage(  $row->getError(),'error');
		}	
		
		// attribs points or coupon code
		if ( $winner1 ) $winner1_Referreid = AltaUserPointsHelper::getAnyUserReferreID( intval($winner1) );	
		if ( $winner2 ) $winner2_Referreid = AltaUserPointsHelper::getAnyUserReferreID( intval($winner2) );	
		if ( $winner3 ) $winner3_Referreid = AltaUserPointsHelper::getAnyUserReferreID( intval($winner3) );
		
		switch ( $rowRaffle->rafflesystem )
		{
		case '1':
			// is coupon code ...				
			
			// send notification by email		
			if ( $rowRaffle->sendcouponbyemail )
			{
				if ( $winner1 && $rowRaffle->couponcodeid1 ) {
					$this->sendnotification4couponcode ( $winner1_Referreid, $this->_get_Coupon($rowRaffle->couponcodeid1) );
				}
				if ( $winner2 && $rowRaffle->couponcodeid2 ) {
					$this->sendnotification4couponcode ( $winner2_Referreid, $this->_get_Coupon($rowRaffle->couponcodeid2) );
				}
				if ( $winner3 && $rowRaffle->couponcodeid3 ) {
					$this->sendnotification4couponcode ( $winner3_Referreid, $this->_get_Coupon($rowRaffle->couponcodeid3) );
				}
			}
			
			// uddeim notification
			if ( $params->get( 'sendMsgUddeim', 0 ) && $params->get('fromIdUddeim') )
			{					
				// Uddeim notification integration
				require_once (JPATH_SITE.'/components/com_altauserpoints/helpers/uddeim.api.php');
				$SiteName	= $app->getCfg('sitename');				
				
				if ( $winner1 && $rowRaffle->couponcodeid1 ) {
					$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_COUPONCODE'), $SiteName, $this->_get_Coupon($rowRaffle->couponcodeid1) );
					uddeIMAPI::sendNewMessage( intval($params->get('fromIdUddeim')), intval($winner1), $message );
					$message = "";
				}
				if ( $winner2 && $rowRaffle->couponcodeid2 ) {
					$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_COUPONCODE'), $SiteName, $this->_get_Coupon($rowRaffle->couponcodeid2) );
					uddeIMAPI::sendNewMessage( intval($params->get('fromIdUddeim')), intval($winner2), $message );
					$message = "";
				}
				if ( $winner3 && $rowRaffle->couponcodeid3 ) {
					$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_COUPONCODE'), $SiteName, $this->_get_Coupon($rowRaffle->couponcodeid3) );
					uddeIMAPI::sendNewMessage( intval($params->get('fromIdUddeim')), intval($winner3), $message );
					$message = "";
				}					
			}	

			break;
		case '2':

			// e-mail with a download link as a price
			if ( $winner1 && $rowRaffle->link2download1 ) {
				$this->sendnotificationDownload ( $winner1_Referreid, $rowRaffle->link2download1 );
			}
			if ( $winner2 && $rowRaffle->link2download2 ) {
				$this->sendnotificationDownload ( $winner2_Referreid, $rowRaffle->link2download2 );
			}
			if ( $winner3 && $rowRaffle->link2download3 ) {
				$this->sendnotificationDownload ( $winner3_Referreid, $rowRaffle->link2download3 );
			}		
	
			break;
			
		case '3':
			// just simple e-mail
			if ( $winner1 ) {
				$this->sendSimpleEmail ( $winner1_Referreid, '1' );
			}
			if ( $winner2 ) {
				$this->sendSimpleEmail ( $winner2_Referreid, '2' );
			}
			if ( $winner3 ) {
				$this->sendSimpleEmail ( $winner3_Referreid, '3' );
			}
					
			break;
			
		default:

			// is points ...
			if ( $winner1 ) {
				AltaUserPointsHelper::newpoints ( 'sysplgaup_raffle', $winner1_Referreid, '', $rowRaffle->description, $rowRaffle->pointstoearn1 );
			}
			if ( $winner2 ) {			
				AltaUserPointsHelper::newpoints ( 'sysplgaup_raffle', $winner2_Referreid, '', $rowRaffle->description, $rowRaffle->pointstoearn2 );
			}
			if ( $winner3 ) {							
				AltaUserPointsHelper::newpoints ( 'sysplgaup_raffle', $winner3_Referreid, '', $rowRaffle->description, $rowRaffle->pointstoearn3 );
			}
			
		} // end switch
		
		$results = $dispatcher->trigger( 'onAfterMakeRaffleAltaUserPoints', array(&$rowRaffle, $now) );
		
		$redirecturl = "index.php?option=com_altauserpoints&task=raffle";
		$app->redirect($redirecturl);
		//JControllerLegacy::redirect();
	
	}
		
	function sendnotification4couponcode ( $referrerid, $couponcode ) 
	{
		$app = JFactory::getApplication();
		jimport( 'joomla.mail.helper' );	
		
		if ( !$referrerid ) return;
		
		// JomSocial notification
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		

		$jsNotification = $params->get('jsNotification', 0);
		$jsNotificationAdmin = $params->get('fromIdUddeim', 0);
		
		$MailFrom	= $app->getCfg('mailfrom');
		$FromName	= $app->getCfg('fromname');
		$SiteName	= $app->getCfg('sitename');
		$sef		= $app->getCfg('sef');
		
		$userinfo = $this->getUserInfo( $referrerid );
		$email	  = $userinfo->email;
		
		$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT_COUPONCODE');
		$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_COUPONCODE'), $SiteName, $couponcode );		
		
		if ( !$jsNotification )
		{			
			//JMail::sendMail( $MailFrom, $FromName, $email, $subject, $message );
			$mailer = JFactory::getMailer();
			$mailer->setSender( array( $MailFrom, $FromName ) );
			$mailer->setSubject( $subject);
			//$mailer->isHTML((bool) $formatMail);
			$mailer->CharSet = "utf-8";
			$mailer->setBody($message);
			$mailer->addRecipient( $email );
			$send = $mailer->Send();

		} 
		else 
		{
			require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
			$Cparams = new CParameter('');
			if ( $userinfo->id && $jsNotificationAdmin ) {
				CNotificationLibrary::add( 'system_messaging' , $jsNotificationAdmin , $userinfo->id, $subject , $message , '' , $Cparams );			
			}		
		}		
		
	}
	
	function sendnotificationDownload ( $referrerid, $linkToDownload )
	{
		$app = JFactory::getApplication();
		jimport( 'joomla.mail.helper' );	
		
		if ( !$referrerid ) return;
	
		// JomSocial notification
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		
		$jsNotification = $params->get('jsNotification', 0);
		$jsNotificationAdmin = $params->get('fromIdUddeim', 0);
		
		$MailFrom	= $app->getCfg('mailfrom');
		$FromName	= $app->getCfg('fromname');
		$SiteName	= $app->getCfg('sitename');
		$sef		= $app->getCfg('sef');
		
		$userinfo = $this->getUserInfo( $referrerid );		
		$email	  = $userinfo->email;
		
		$subject = JText::_('AUP_EMAILNOTIFICATION_SUBJECT_MAIL_WITH_DOWNLOAD');
		$message = sprintf ( JText::_('AUP_EMAILNOTIFICATION_MSG_MAIL_WITH_DOWNLOAD'), $SiteName, $linkToDownload );

		if ( !$jsNotification )
		{			
			//JMail::sendMail( $MailFrom, $FromName, $email, $subject, $message );
			$mailer = JFactory::getMailer();
			$mailer->setSender( array( $MailFrom, $FromName ) );
			$mailer->setSubject( $subject);
			//$mailer->isHTML((bool) $formatMail);
			$mailer->CharSet = "utf-8";
			$mailer->setBody($message);
			$mailer->addRecipient( $email );
			$send = $mailer->Send();

		} 
		else 
		{
			require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
			$Cparams = new CParameter('');
			if ( $userinfo->id && $jsNotificationAdmin ) {
				CNotificationLibrary::add( 'system_messaging' , $jsNotificationAdmin , $userinfo->id, $subject , $message , '' , $Cparams );			
			}		
		}		
	
	}
	
	function sendSimpleEmail ( $referrerid, $num )
	{
		$app = JFactory::getApplication();
		
		jimport( 'joomla.mail.helper' );	
		
		if ( !$referrerid ) return;
		
		// JomSocial notification
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );
		$jsNotification = $params->get('jsNotification', 0);
		$jsNotificationAdmin = $params->get('fromIdUddeim', 0);
		
		$MailFrom	= $app->getCfg('mailfrom');
		$FromName	= $app->getCfg('fromname');
		$SiteName	= $app->getCfg('sitename');
		$sef		= $app->getCfg('sef');
		
		$userinfo = $this->getUserInfo( $referrerid );		
		$email	  = $userinfo->email;
		
		$subject = JText::_('AUP_RF'.$num.'_SUBJECT_EMAIL');
		$message = JText::_('AUP_RF'.$num.'_BODY_EMAIL');
		
		if ( !$jsNotification )
		{			
			//JMail::sendMail( $MailFrom, $FromName, $email, $subject, $message );			
			$mailer = JFactory::getMailer();
			$mailer->setSender( array( $MailFrom, $FromName ) );
			$mailer->setSubject( $subject);
			//$mailer->isHTML((bool) $formatMail);
			$mailer->CharSet = "utf-8";
			$mailer->setBody($message);
			$mailer->addRecipient( $email );
			$send = $mailer->Send();
		} 
		else 
		{
			require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
			$Cparams = new CParameter('');
			if ( $userinfo->id && $jsNotificationAdmin ) {
				CNotificationLibrary::add( 'system_messaging' , $jsNotificationAdmin , $userinfo->id, $subject , $message , '' , $Cparams );			
			}		
		}		

	}

	
	function getUserInfo ( $referrerid='', $userid='' ) 
	{	
		if ( !$referrerid && !$userid ) return;
	
		$db	   = JFactory::getDBO();
		
		if ( $referrerid ) {
			$where = "a.referreid='$referrerid'";		
		} elseif ( $userid ){		
			$where = "a.userid='$userid'";
		}
		
		$query = "SELECT a.userid, a.referreid, a.upnid, a.points, a.max_points, a.last_update, " .
				 "a.referraluser, a.referrees, a.blocked, a.avatar, a.levelrank, a.leveldate, " .
				 " a.profileviews, a.shareinfos, a.id AS rid, u.* " .
				 "FROM #__alpha_userpoints AS a, #__users AS u " .
				 "WHERE $where AND a.userid=u.id";
		$db->setQuery( $query );
		$userinfo = $db->loadObjectList();
		return @$userinfo[0];
	}
	
	function _export_users_registration()
	{
		$raffleid	   = JFactory::getApplication()->input->get('id', 0, 'int');
		
		$db	   = JFactory::getDBO();
		$query = "SELECT ari.userid AS uid, u.name, u.username, u.email " .
				 "FROM #__alpha_userpoints_raffle_inscriptions AS ari " .				 
				 "LEFT JOIN #__users AS u ON ari.userid=u.id " .
				 "WHERE ari.raffleid='" . $raffleid . "' " .
				 "GROUP BY ari.userid "
				 ;				 
		$db->setQuery( $query );
		$userslist = $db->loadObjectList();
		return @$userslist;
	}
	
	function _get_Coupon( $idcoupon )
	{
		$db	   = JFactory::getDBO();
		$query = "SELECT couponcode " .
				 "FROM #__alpha_userpoints_coupons " .
				 "WHERE id='" . $idcoupon . "'";		
		$db->setQuery( $query );
		$couponcode = $db->loadResult();
		return $couponcode;	
	}
	
}
?>