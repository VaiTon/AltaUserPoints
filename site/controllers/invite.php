<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerInvite extends JControllerForm
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()
	{
		parent::__construct( );
	}
	
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		$return = parent::getModel($name, $prefix, array('ignore_request' => false));
		//var_dump($return);
		return $return;
	}
	
	
	
	
	/*public function _display($cachable = false, $urlparams = false) 
	{
	
		
		$user =  JFactory::getUser();		
		if ( $user->id )		
			$user_name = $user->name;			
		else
			$user_name = "";	
		
		$model      = $this->getModel ( 'invite' );
		$view       = $this->getView  ( 'invite','html' );		
		
		$referrerid = $model->_getReferreid();		
		$params 	= $model->_getParamsAUP();
		
		$cparams = JComponentHelper::getParams( 'com_altauserpoints' );
		
		if ( $referrerid )				
			$referrer_link = getLinkToInvite( $referrerid, $cparams->get('systemregistration') );
		else
			$referrer_link = getLinkToInvite( '', $cparams->get('systemregistration') );
		
		//$view->assign('model', $model ); //?
		$view->assign('params', $params );
		$view->assign('user_name', $user_name );
		$view->assign('referreid', $referrerid );	
		$view->assign('referrer_link', $referrer_link );		
		
		// Display
		if ( JFactory::getApplication()->input->get('task', '', 'cmd')=='addressbook')
			 $view->_display_addressbook();
		else
			$view->display();
	
	}*/
	
	public function sendinvite () 
	{

		$app = JFactory::getApplication();
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model      = $this->getModel ( 'invite' );
		$view       = $this->getView  ( 'invite','html' );	
		$data  		= $this->input->post->get('jform', array(), 'array');

		$other_emails	= $data['other_recipients'];
		$sender 		= $data['sender'];
		$custommessage 	= $data['custommessage']; 
		$cparams = JComponentHelper::getParams( 'com_altauserpoints' );
		
		$params 	= $model->_getParamsAUP();
		//$referreid = $model->_getReferreid();
		$referreid = $app->input->post->get('referreid','','raw');
		

		$form = $model->getForm();
		if (!$form)
		{
			$app->enqueueMessage( $model->getError(), 'error' );
			return false;
		}
		
		$validate = $model->validate($form, $data);
		if ($validate === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 5; $i++)
			{
				if ($errors[$i] instanceof Exception)
					$app->enqueueMessage( $errors[$i]->getMessage(), 'warning');
				else
					$app->enqueueMessage($errors[$i], 'warning');
			}
			// Save the data in the session.
			$app->setUserState('com_altauserpoints.invite.data', $data);
			// Redirect back to the form.
			$app->redirect(JRoute::_('index.php?option=com_altauserpoints&view=invite&Itemid='.$app->input->getInt('Itemid'), false ));
			return false;
		}
		
		// active user
		$user =  JFactory::getUser();		
		$db	= JFactory::getDBO();

		jimport( 'joomla.mail.helper' );
		
			

		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();		

		$uri        = JURI::getInstance();
		$base    	= $uri->toString( array('scheme', 'host', 'port'));	
		
		if ( $referreid )	
		{
			$link = getLinkToInvite( $referreid, $cparams->get('systemregistration') );
		} 
		else 
		{
			$link = $base.JRoute::_('');
		}

		// An array of e-mail headers we do not want to allow as input
		$headers = array (	'Content-Type:',
							'MIME-Version:',
							'Content-Transfer-Encoding:',
							'bcc:',
							'cc:');

		// An array of the input fields to scan for injected headers
		$fields = array ('mailto',
						 'sender',
						 'from',
						 'subject',
						 );

		/*
		 * Here is the meat and potatoes of the header injection test.  We
		 * iterate over the array of form input and check for header strings.
		 * If we fine one, send an unauthorized header and die.
		 */
		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos(@$_POST[$field], $header) !== false)
				{
					$app->enqueueMessage(  'Err0r','error');
				}
			}
		}

		/*
		 * Free up memory
		 */
		unset ($headers, $fields);
		
		$imported_emails	= $_POST['importedemails'];
		
		// Check for a valid to address
		$errorMail	= false;		
				
		// build list emails
		if($imported_emails=='' && $other_emails!='') {
			$emails = $other_emails;
		} elseif($other_emails=='' && $imported_emails!='') {
			$emails = $imported_emails;
		} elseif ( $imported_emails!='' && $other_emails!='') {
			$emails = $imported_emails . "," . $other_emails;
		} else {
			$emails = "";
			$errorMail	=  JText::_( 'AUP_EMAIL_INVALID' );
			$app->enqueueMessage(  $errorMail ,'warning');
		}
	
		$emails = @explode( ',', $emails );

		// Check for a valid from address
		if ( ! $MailFrom || ! JMailHelper::isEmailAddress($MailFrom) )
		{
			$errorMail	= JText::sprintf('AUP_EMAIL_INVALID', $MailFrom);
			$app->enqueueMessage(  $errorMail ,'warning');
		}

		if ( $errorMail ) return $this->display ();

		// Build the message to send
		$msg			= JText:: _('AUP_EMAIL_MSG_INVITE');	

		$formatMail 	= '0';
		$bcc2admin		= '0';
	
		if ( $params->get( 'templateinvite', 0 ) )
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');				
			$row = JTable::getInstance('template_invite');				
			$row->load( intval($params->get( 'templateinvite' )));			
			$subject        = $row->emailsubject;
			$body           = $row->emailbody;		
			$body           = str_replace('{name}', $sender, $body);
			$body           = str_replace('{custom}', $custommessage, $body);
			$body           = str_replace('{link}', $link, $body);
			$bcc2admin		= $row->bcc2admin;
			$formatMail     = $row->emailformat;
		} 
		else 
		{		
			$subject		= JText::_( 'AUP_YOUAREINVITEDTOREGISTERON' ) . " " . $SiteName;
			$body			= sprintf( $msg, $SiteName, $sender, $link) . " \n" . $custommessage;			
		}
		

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
				
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
		
		// Limit
		$max 		= $params->get( 'maxemailperinvite'   );
		$maxperday  = $params->get( 'maxinvitesperday'    );
		$delay 		= intval($params->get( 'delaybetweeninvites' ));
		
		$counter 	= 0;
		
		$rule_ID = $model->_getRuleID ( 'sysplgaup_invite' );
		
		$refer_ID = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
		
		$numpoints4invite = AltaUserPointsHelper::getPointsRule( 'sysplgaup_invite' );
		$totalpointsearned= 0;
		
		$currentmaxperday = $model->_checkCurrentMaxPerDay( $rule_ID, $user->id, $referrerid, $_SERVER["REMOTE_ADDR"] );
		
		$checkdelay = 1;
		if ( $delay ) {
			$checkdelay = $model->_checkLastInviteForDelay( $rule_ID, $user->id, $referrerid, $_SERVER["REMOTE_ADDR"], $delay );
		}
		
		if ( !$checkdelay ) {
			$errorTime = JText :: _('AUP_DELAY_BETWEEN_INVITES_INVALID');
			$app->enqueueMessage(  $errorTime ,'warning');
			return $this->display ();
		} 
				
		if ( $currentmaxperday < $maxperday ) {
		
			$mailer = JFactory::getMailer();
			
		
		
			foreach ($emails as $email) {
				$aEmails[0] = $model->_extractEmailsFromString($email);
				$email= $aEmails[0][0];
				if ( JMailHelper::isEmailAddress($email) ) {
					
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
						$q = "SELECT email" .
								" FROM #__users" .
								" WHERE sendEmail='1' AND block='0'";
						$db->setQuery( $q );
						$rowsAdmins = $db->loadObjectList();		
						
						foreach ( $rowsAdmins as $rowsAdmin ) {
							$mailer->addBCC( $rowsAdmin->email );
						}
					}		
					
					if ( $mailer->Send() === true ) {
						if ( $user->id ) {				
							if ( AltaUserPointsHelper::checkRuleEnabled('sysplgaup_invite') ) {						
								// insert email for tracking
								$email2 = str_replace("@" ," [at] ", $email); // change @ because can be display on frontend in latest activity
								$keyreference = AltaUserPointsHelper::buildKeyreference( 'sysplgaup_invite', $email );
								AltaUserPointsHelper::userpoints( 'sysplgaup_invite', $refer_ID, 0, $keyreference, $email2  );
								$totalpointsearned = $totalpointsearned + $numpoints4invite;
							}
						} else {
							// guest user : Insert IP and email fortracking							
							JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');							
							$row = JTable::getInstance('userspointsdetails');
							$row->id				= NULL;
							$row->referreid			= 'GUEST';
							$row->points			= 0;
							$row->insert_date		= $now;
							$row->expire_date 		= '';		
							$row->rule				= $rule_ID;
							$row->approved			= 1;
							$row->status			= 1;
							$row->keyreference		= $_SERVER["REMOTE_ADDR"];
							$row->datareference		= $email;										
							$row->enabled			= 1;
							
							if ( !$row->store() )
							{
								$app->enqueueMessage(  $row->getError(),'error');
							}							
						}
						$counter++;
						$currentmaxperday++;
					}
					if ( $counter==$max || $currentmaxperday==$maxperday )	break;
				}
			}
			if ( $totalpointsearned )
			{
				if($totalpointsearned>1)
					$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_PLURAL'), $totalpointsearned );
				else
					$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_SINGULAR'), $totalpointsearned );
				$app->enqueueMessage($msg ,'message');
			}
		} else {
			$maxperdaylimit = JText :: _('AUP_MAXINVITESPERDAY') . " " . $maxperday ;
			$app->enqueueMessage( $maxperdaylimit ,'message');			
		}
		
		switch ( $counter ) {		
			case '0':
				$message = JText :: _('AUP_NO_EMAIL_HAS_BEEN_SENT');				
				break;				
			case '1':
				$message = JText :: _('AUP_EMAIL_SENT');		
				break;			
			default:
				$message = JText :: _('AUP_EMAILS_SENT');
				$message = sprintf( $message, $counter);
				break;				
		}	
		        
		$app->enqueueMessage( $message ,'message');
		$app->redirect(JRoute::_('index.php?option=com_altauserpoints&view=invite&Itemid='.$app->input->getInt('Itemid'), false ));       		

	}

}
?>