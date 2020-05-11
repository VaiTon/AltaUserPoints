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
 * AltaUserPoints Content Plugin
 *
 * @package		Joomla
 * @subpackage	AltaUserPoints
 * @since 		1.6
 */

class plgContentsysplgaup_raffle extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$db	   						= JFactory::getDBO();
		$user 						= JFactory::getUser();
		
		$print  					= JFactory::getApplication()->input->get('print', '');
		$format 					= JFactory::getApplication()->input->get('format', '');
		
		$aupraffleid 				= JFactory::getApplication()->input->get('aupraffleid', 0, 'int');		
		$aupraffleuserid 			= JFactory::getApplication()->input->get('aupraffleuserid', 0, 'int');
		$auprafflepoints			= JFactory::getApplication()->input->get('auprafflepoints', 0, 'FLOAT');
		$auprafflepointsremove  	= JFactory::getApplication()->input->get('auprafflepointsremove', 0, 'FLOAT');
		$multipleentries  			= JFactory::getApplication()->input->get('multipleentries', 0, 'int');	
		// ---
		// if referral draw system
		$referredraw2  				= JFactory::getApplication()->input->get('referredraw2', 0, 'int');	
		$draw2  					= JFactory::getApplication()->input->get('draw2', 0, 'int');	
		//$showlink					= JFactory::getApplication()->input->get('showlink', 0, 'int');	
		// ---
		
		$uri = JURI::getInstance();		
		@$uri->delVar('referrer');
		@$uri->delVar('draw');
		$url = $uri->toString();
		
		$inscription 				= 0;
		$pointstoparticipate 		= 0;
		$removepointstoparticipate 	= 0;

		if ( $print || $format=='pdf' ) {
			$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
			return;
		}
		
		if ($app->isAdmin()) return;
		
    	$lang = JFactory::getLanguage();
    	$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();		
		
		$api_AUP = JPATH_SITE.'/components/com_altauserpoints/helper.php';
		require_once ($api_AUP);		
		
		if ( preg_match('#{AUP::RAFFLE=(.*)}#Uis', $article->text, $m) && !$aupraffleid ) // form not sent
		{
			$raffleid = $m[1];
			
			if ( $raffleid=='ID' ) {
				// default sample
				return;
			}
			
			$query = "SELECT * FROM #__alpha_userpoints_raffle WHERE `id`='$raffleid' AND `published`='1'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			
			if ( $result ) 
			{
				$inscription = $result[0]->inscription;
				$pointstoparticipate = $result[0]->pointstoparticipate;
				$removepointstoparticipate = $result[0]->removepointstoparticipate;
				$multipleentries  = $result[0]->multipleentries;
				$alreadyProceeded = $result[0]->winner1;
				$limitdate = $result[0]->raffledate;
				if ( $now>$limitdate ) $alreadyProceeded = 1;				
			}			
			
			// You can choose number subscriptions members
			//$query = "SELECT COUNT(DISTINCT userid) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$raffleid'";
			
			// You can choose number of tickets sold!
			$query = "SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$raffleid'";
			$db->setQuery( $query );
			$num_subscription = $db->loadResult();			
			$article->text .= "\n<p>".JText::_('AUP_NUMBER_SUBSCRIPTION_CURRENT_RAFFLE'). " " . $num_subscription . "</p>\n";
			
			if ( $inscription && $user->id && $alreadyProceeded==0) 
			{	

				if ( !$multipleentries ) {										
					$query = "SELECT userid FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='$user->id' AND `raffleid`='$raffleid'";
					$db->setQuery( $query );
					$userid = $db->loadResult();					
				} else $userid=0;
				
				
				if ( !$userid )
				{		
					$referredraw = '';
					$draw = 0;
					if ( isset($_COOKIE['referredraw']) ){
						$referredraw = $_COOKIE['referredraw'];					
						$referredraw = AltaUserPointsHelper::getUserID( $referredraw );		
					}											
					if ( isset($_COOKIE['draw'])) {
						$draw = $_COOKIE['draw'];
					}
								
					$registrationForm = "\n<form action=\"$url\" method=\"post\" name=\"RaffleForm\">\n"
										. "<input type=\"hidden\" name=\"aupraffleid\" id=\"aupraffleid\" value=\"".$raffleid."\" />\n"
										. "<input type=\"hidden\" name=\"aupraffleuserid\" id=\"aupraffleuserid\" value=\"".$user->id."\" />\n"
										. "<input type=\"hidden\" name=\"auprafflepoints\" id=\"auprafflepoints\" value=\"".$pointstoparticipate."\" />\n"
										. "<input type=\"hidden\" name=\"auprafflepointsremove\" id=\"auprafflepointsremove\" value=\"".$removepointstoparticipate."\" />\n"
										. "<input type=\"hidden\" name=\"multipleentries\" id=\"multipleentries\" value=\"".$multipleentries."\" />\n"										
										. "<input type=\"hidden\" name=\"referredraw2\" id=\"referredraw2\" value=\"".$referredraw."\" />\n"
										. "<input type=\"hidden\" name=\"draw2\" id=\"draw2\" value=\"".$draw."\" />\n"		
										//. "<input type=\"hidden\" name=\"showlink\" id=\"showlink\" value=\"".$showlink."\" />\n"																			
										. "<input class=\"button\" type=\"submit\" name=\"Submit\" value=\"".JText::_('AUP_SIGNUP_FOR_THIS_RAFFLE_NOW')."\" />\n"
										."</form>\n";
		
					if ( $pointstoparticipate )
					{
						$referreid = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
						$currentpoints = AltaUserPointsHelper::getCurrentTotalPoints ( $referreid );
						if ( $currentpoints>=$pointstoparticipate )						
						{
							$article->text .= $registrationForm;
						}
						else
						{
							$article->text .= "<p>".JText::_('AUP_YOUDONOTHAVEENOUGHPOINTSTOPERFORMTHISOPERATION')."</p>";
						}
					}
					else
					{					
						$article->text .= $registrationForm;
					}									
				}
				else
				{
					$article->text .= "<p>".JText::_('AUP_ALREADY_REGISTERED_FOR_THIS_RAFFLE')."</p>";
				}
			
			}
			elseif ( $inscription && $user->id && $alreadyProceeded>0)
			{
				$article->text .= "<p>".JText::_('AUP_DRAW_HAS_BEEN_MADE_YOU_CANT_REGISTER')."</p>";
				$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
				return;				
			} else 	{
				$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
				return;
			}			
		} 
		elseif ( preg_match('#{AUP::RAFFLE=(.*)}#Uis', $article->text, $m) && $aupraffleid && $aupraffleuserid )  // form has been sent
		{				
			if ( !$multipleentries ) {			
				$query = "SELECT userid FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='$aupraffleuserid' AND `raffleid`='$aupraffleid'";
				$db->setQuery( $query );
				$alreadyregister = $db->loadResult();		
			} else $alreadyregister=0;
			
			if ( !$alreadyregister )
			{
				// Save registration				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
				$row = JTable::getInstance('raffle_inscriptions');
				$row->id				= NULL;
				$row->raffleid			= $aupraffleid;
				$row->userid			= $aupraffleuserid;
				$row->ticket			= $this->createRandomTicket(3,2,3,4);
				$row->referredraw		= $referredraw2;
				$row->inscription		= $now;	
				
				if ( !$row->store() )
				{
					JFactory::getApplication()->enqueueMessage(  $row->getError(),'error');
				} 
				else 
				{

					// if referral draw system -> attribs one additional ticket to the referral user if already register to this raffle
					if ( $draw2 && $referredraw2 && ($draw2==$aupraffleid) && ($referredraw2!=$aupraffleuserid) )
					{
											
						// check if the referral user have a first registration to this raffle
						$query="SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid' AND `userid`='$referredraw2'";
						$db->setQuery( $query );
						$n_subscription = $db->loadResult();
						
						// if first registration check if not already done by this user
						$query="SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid' AND `userid`='$referredraw2' AND `referredraw`='$aupraffleuserid'";
						$db->setQuery( $query );
						$already_done_subscription = $db->loadResult();
						
						if ( !$already_done_subscription && $n_subscription ) {						
							// Save additional registration for the referral user
							JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
							$row2 = JTable::getInstance('raffle_inscriptions');
							$row2->id				= NULL;
							$row2->raffleid			= $aupraffleid;
							$row2->userid			= $referredraw2;	
							$row2->ticket			= $this->createRandomTicket(3,3,3,4);
							$row2->referredraw		= $aupraffleuserid;
							$row2->inscription		= $now;
							
							if ( !$row2->store() )
							{
								JFactory::getApplication()->enqueueMessage(  $row2->getError(),'error');
							}							
							// delete cookies for draw
							setcookie ("referredraw");
							setcookie ("draw");
							unset($_COOKIE['referredraw']);
							unset($_COOKIE['draw']);
						}
											
					}					
				
					// remove points if necessary
					if ( $auprafflepointsremove && $auprafflepoints ) {
						AltaUserPointsHelper::newpoints( 'sysplgaup_raffle', '', '', JText::_('AUP_LABEL_REGISTRATION_RAFFLE'), (abs($auprafflepoints)*(-1)) );
					}					
					$app->enqueueMessage( JText::_('AUP_CONFIRM_RAFFLE_REGISTRATION') );
					
					// You can choose number subscriptions members					
					// $query = "SELECT COUNT(DISTINCT userid) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid'";		
					// You can choose number of total tickets sold!
					if ( $multipleentries ) {		
						$query = "SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid' AND `userid`='$aupraffleuserid'";
					} else {					
						$query = "SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid'";
					}					
					
					$db->setQuery( $query );
					$num_subscription = $db->loadResult();		
					$article->text .= "<p>".JText::_('AUP_NUMBER_SUBSCRIPTION_CURRENT_RAFFLE'). " " . $num_subscription . "</p>";

					$article->text .= "<p>".JText::_('AUP_YOUR_SUBSCRIPTION_HAS_BEEN_REGISTERED')."</p>";
					// check new total points to play again
					$referreid = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
					$currentpoints = AltaUserPointsHelper::getCurrentTotalPoints ( $referreid );
					

					if ( ($currentpoints>=$auprafflepoints || !auprafflepointsremove ) && $multipleentries )						
					{
						$article->text .= "<p><a href=\"$url\">".JText::_('AUP_SUBSCRIBE_AGAIN')."</a></p>";
					}
					
				}			
			} else $article->text .= "<p>".JText::_('AUP_ALREADY_REGISTERED_FOR_THIS_RAFFLE')."</p>";
		
		}
		
		// Show link to invite
		if ( preg_match('#{AUP::RAFFLEINVITE=(.*)}#Uis', $article->text, $m) )
		{
			$showinvite = $m[1];
			if ( $showinvite && $user->id ) 
			{
				$referreid = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
				$urlinvite = $url . '/?referrer='. $referreid . '&draw=' . $showinvite ;
				// show invite link to register to this raffle and allow to the referral earn an additional free ticket
				$urlinvite = '<input type="text" name="referredraw_link" id="referredraw_link" onfocus="select();" readonly="readonly" class="inputbox" value="' . $urlinvite . '" />';
				$article->text = preg_replace( " |{AUP::RAFFLEINVITE=(.*)}| ", $urlinvite, $article->text );	
			}
		}		
		
		// show tickets subscription
		if ( preg_match('#{AUP::RAFFLETICKETS=(.*)}#Uis', $article->text, $m) && $user->id )
		{
			$showTicketsList = "";
			$showtickets = $m[1];
			if ( $showtickets ) 
			{
				$query = "SELECT `ticket` FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$showtickets' AND `userid`='".$user->id."'";
				$db->setQuery( $query );
				$tickets = $db->loadObjectList();
				if ($tickets)
				{
					$showTicketsList = "<ul>";
					foreach($tickets as $ticket)
					{							
						$showTicketsList .= "<li>".$ticket."</li>";
					} 
					$showTicketsList .= "</ul>";
					$article->text = preg_replace( " |{AUP::RAFFLETICKETS=(.*)}| ", $showTicketsList, $article->text );
				}
			}
		}

		//  article text updated
		$article->text = preg_replace( " |{AUP::RAFFLE(.*)}| ", "", $article->text );
	}
	
	private function createRandomTicket($nChars=3,$nChars1=2,$nNum1=3,$nNum2=4) {
	
		// provide serial ticket number with 3 letters +  2 letters + space + 3 numbers + space + 4 numbers
	
		$chars 	= "ABCDEFGHIJKLMNPQRSTUVWXYZ";	
		$numm	= "0123456789";
		
		$code = "";	
		$code1 = "";
		$code2 = "";
		$code3 = "";
		$nChars = $nChars - 1;
		$nChars1 = $nChars1 - 1;
		$nNum1  = $nNum1 - 1;
		$nNum2	= $nNum2 - 1;		
		
		$i = 0;	
		// letters
		while ($i <= $nChars) {	
			$num = rand(0, 24);	
			$tmp = substr($chars, $num, 1);	
			$code = $code . $tmp;	
			$i++;	
		}	
		
		$i = 0;	
		// letters
		while ($i <= $nChars1) {	
			$num = rand(0, 24);	
			$tmp = substr($chars, $num, 1);
			$code1 = $code1 . $tmp;	
			$i++;	
		}	
		
		$i = 0;
		//  nums
		while ($i <= $nNum1) {	
			$num1 = rand(0, 9);	
			$tmp = substr($numm, $num1, 1);	
			$code2 = $code2 . $tmp;	
			$i++;	
		}	
		
		$i = 0;
		//  nums
		while ($i <= $nNum2) {	
			$num2 = rand(0, 9);	
			$tmp = substr($numm, $num2, 1);	
			$code3 = $code3 . $tmp;
			$i++;	
		}	
	
		$ticket = $code . " " . $code1. " " . $code2 . " " . $code3;
		
		return $ticket;	
	
	}

}
?>