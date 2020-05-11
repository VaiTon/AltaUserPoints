<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * @package AltaUserPoints
 */
class altauserpointsController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
		parent::__construct( );
	}	

	/**
	* Show Control Panel
	*/
	public function cpanel() {
	
		$synch				= JFactory::getApplication()->input->get( 'synch', '', 'cmd' );
		$recalculate		= JFactory::getApplication()->input->get( 'recalculate', '', 'cmd' );
		
		$_params 			= JComponentHelper::getParams( 'com_altauserpoints' );
	
		$model          	= $this->getModel ( 'statistics' );
		$model3         	= $this->getModel ( 'users' );
		$modelUpdate       	= $this->getModel ( 'upgrade' );
		
		$view  				= $this->getView ( 'cpanel','html');
		
		$_top10 	 		= $model->_load_top10 ();
		$_unapproved 		= $model->_load_unapproved ();
		$_needsync   		= $model->_needsync ();		
		$_last_Activities 	= $model3->_last_Activities ();	
		$_communitypoints 	= $model->_totalcurrentcommunitypoints();
		
		if ( $_params->get('showUpdateCheck', 0) ) 
		{

			$cache=  JFactory::getCache('com_altauserpoints');
			// save configured lifetime		 
			@$lifetime=$cache->lifetime; 
			$cache->setLifeTime(15 * 60); // 15 minutes to seconds		 
			// save cache conf		 
			$conf = JFactory::getConfig();		 
			// check if cache is enabled in configuration		 
			//$cacheactive = $conf->getValue('config.caching');		
			$cacheactive = $conf->get('config.caching'); 
			$cache->setCaching(1); //enable caching		 
			// if the cache expired, the method will be called again and the result will be stored for 'lifetime' seconds
			$_check = $cache->call( array( $modelUpdate, '_getUpdate') );
			// revert configuration
			$cache->setCaching($cacheactive);		
		} else $_check		= '';
		
		// check if general configuration is Ok
		$modelUpdate->_checkConfig();
		
		// Check new official rules installed
		$modelUpdate->_checkNewRule();
		
		$view->assign('top10', $_top10 );
		$view->assign('unapproved10', $_unapproved[0] );
		$view->assign('totalunapproved', $_unapproved[1] );
		$view->assign('needSync', $_needsync );
		$view->assign('check' , $_check);
		$view->assign('params' , $_params);
		$view->assign('lastactivities' , $_last_Activities);
		$view->assign('synch' , $synch);
		$view->assign('recalculate' , $recalculate);
		$view->assign('communitypoints',  $_communitypoints );		

		$view->show();
	}

	/**
	* Show listing of all activities
	*/
	public function activities() {
	
		$model     	= $this->getModel ( 'users' );
		$view       = $this->getView  ( 'activities','html' );
		
		$_activities = $model->_load_activities ();
		
		$view->assign('activities', $_activities[0] );
		$view->assign('total', $_activities[1] );
		$view->assign('limit', $_activities[2] );
		$view->assign('limitstart', $_activities[3] );
		$view->assign('lists', $_activities[4] );
	
		// Display
		$view->_displaylist();	
	}
	
	/**
	* Show About
	*/
	public function about() {
		$view  = $this->getView ( 'about','html');		
		$view->show();
	}
	
	/**
	* Set max points for all users
	*/
	public function setmaxpoints() {
		
		$view         = $this->getView  ( 'maxpoints','html' );	
		
		$setpoints	= JFactory::getApplication()->input->get( 'newmaxpoints', 0, 'int' );
		
		$view->assign('setpoints', $setpoints );
		$view->showform();
	
	}	
	
	public function savemaxpoints() {
		$app = JFactory::getApplication();
		
		$model        = $this->getModel ( 'users' );
		
		$view         = $this->getView  ( 'maxpoints','html' );		
		
		$_setmaxpoints = $model->_setmaxpoints ();
		
		$msg         = JText::_( 'AUP_NEWMAXPOINTS' ) . " " . $_setmaxpoints;
		$urlredirect = "index.php?option=com_altauserpoints&task=setmaxpoints&newmaxpoints=$_setmaxpoints";
    $this->setRedirect($urlredirect, $msg);
    $this->redirect();	
	}
		
	public function resetpoints() {
		$app = JFactory::getApplication();
		
		$model       = $this->getModel ( 'users' );
		
		$_resetpoints= $model->_resetpoints ();	
		
		$msg         = JText::_( 'AUP_SUCCESSFULLYRESETTOZERO' ) ;
		
		$urlredirect = "index.php?option=com_altauserpoints&task=cpanel";
    $this->setRedirect($urlredirect, $msg);
    $this->redirect();	
	}
	
	public function recalculate() {
		$app = JFactory::getApplication();
	
		$model       = $this->getModel ( 'users' );		
		
		$_recalculatepoints = $model->_recalculate_points ();
		
		$urlredirect = "index.php?option=com_altauserpoints&task=cpanel&recalculate=start";
    $this->setRedirect($urlredirect);
    $this->redirect();
	}
	
	public function purge() {
		$app = JFactory::getApplication();
		
		$model       = $this->getModel ( 'users' );
		
		$_purgeexpirepoints	= $model->_purge_expires ();
	
		$msg         = JText::_( 'AUP_SUCCESSFULLYPURGE' ) ;
		
		$urlredirect = "index.php?option=com_altauserpoints&task=cpanel";
	
    $this->setRedirect($urlredirect, $msg);
    $this->redirect();
	}
	
	
	/**
	* Show Rules
	*/
	public function rules() {
		
		$model        = $this->getModel ( 'rules' );
	
		$view         = $this->getView  ( 'rules','html' );

		// load rules
		$_rules = $model->_load_rules ();
		
		$view->assign('rules', $_rules[0] );
		$view->assign('total', $_rules[1] );
		$view->assign('limit', $_rules[2] );
		$view->assign('limitstart', $_rules[3] );
		$view->assign('lists', $_rules[4] );
		
		// Display
		$view->_displaylist();		
	}

	/**
	* Edit Rules
	*/
	public function editrule() {
	
		$model        = $this->getModel ( 'rules' );
		$modelHelper  = $this->getModel ( 'helper' );
		$view         = $this->getView  ( 'rules','html' );
		
		$_row = $model->_edit_rule ();
		$_rowChainedRules = $model->_getChainedRulesList ($_row->id);
		
		$_groups = $modelHelper->getGenrericGroupsList($_row->content_items);
		
		$view->assign('row', $_row );
		$view->assign('groups', $_groups );
		$view->assign('chainedrules', $_rowChainedRules );
		
		// Display
		$view->_edit_rule();				
	}	
	
	
	/**
	* Save Rule
	*/
	public function saverule() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'rules' );
		// save rule(s)
		$model->_save_rule ();	

	}	
	
	public function applyrule() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$model        = $this->getModel ( 'rules' );
		// save rule(s)
		$apply = 1;
		$model->_save_rule ($apply);	
	
	}
	
	/**
	* Delete Rules
	*/
	public function deleterule() {
		
		$model        = $this->getModel ( 'rules' );
		// delete rule(s)
		$model->_delete_rule ();	

	}	
	
	
	public function cancelrule() {
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=rules";		
    	$this->setRedirect($redirecturl);
    	$this->redirect();	
	}
	
	
	public function copyrule() {
	
		$model        = $this->getModel ( 'rules' );
		// copy rule(s)
		$model->_copy_rule ();	
	
	}
	
	public function statistics() {
	
		$model        = $this->getModel ( 'statistics' );
		$view         = $this->getView  ( 'statistics','html' );
		
		$_stats = $model->_load_users ();
		
		$view->assign('usersStats', $_stats[0] );
		$view->assign('total', $_stats[1] );
		$view->assign('limit', $_stats[2] );
		$view->assign('limitstart', $_stats[3] );
		$view->assign('lists', $_stats[4] );
		$view->assign('ranksexist', $_stats[5] );			
		$view->assign('medalsexist', $_stats[6] );				
		
		// Display
		$view->_displaylist();	
	}
	
	public function edituser () {
		
		$model        = $this->getModel ( 'statistics' );
		$view         = $this->getView  ( 'statistics','html' );
		
		$_row = $model->_edit_user ();	
		
		$view->assign('row', $_row[0] );
		$view->assign('listrank', $_row[1] );
		$view->assign('medalsexist', $_row[2] );
		$view->assign('medalslistuser', $_row[3] );
		$view->assign('listmedals', $_row[4] );
		
		$model2 = $this->getModel ( 'user' );
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$cid = $cid[0];
		
		// load user details	
		$userDetails = $model2->_load_details_user_edit_user ($cid);
		
		$view->assign('userDetails', $userDetails[0] );
		$view->assign('total', $userDetails[1] );
		$view->assign('limit', $userDetails[2] );
		$view->assign('limitstart', $userDetails[3] );
		$view->assign('lists', $userDetails[4] );
		//$view->assign('name', $_name );
		$view->assign('name', $_row[0]->name );
		$view->assign('cid', $cid );
		
		// Display
		$view->_edit_user();
	
	}
	
	public function awardedmedal () {
		
		$model        = $this->getModel ( 'statistics' );
		// save general medal
		$model->_save_medaluser ();	
	
	}
	
	public function removemedaluser () {
		
		$model        = $this->getModel ( 'statistics' );
		// delete user medal
		$model->_delete_medaluser ();	
	
	}
	
	
	public function saveuser() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$model        = $this->getModel ( 'statistics' );
		// save general user stats
		$model->_save_user ();	

	}	
	
	public function applyuser() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$model        = $this->getModel ( 'statistics' );
		// save general user stats
		$apply=1;
		$model->_save_user ($apply);	

	}	


	public function canceluser(){
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=statistics";		
    	$this->setRedirect($redirecturl);
    	$this->redirect();		
	}
	
	public function showdetails() {
	
		$_name		= JFactory::getApplication()->input->get( 'name', '', 'cmd' );
		$_cid		= JFactory::getApplication()->input->get( 'cid', '', '', 'cmd' );
	
		$model = $this->getModel ( 'user' );
		$view  = $this->getView ( 'user','html');
		
		// load user details
		$userDetails = $model->_load_details_user ();
		
		$view->assign('userDetails', $userDetails[0] );
		$view->assign('total', $userDetails[1] );
		$view->assign('limit', $userDetails[2] );
		$view->assign('limitstart', $userDetails[3] );
		$view->assign('lists', $userDetails[4] );
		$view->assign('name', $_name );
		$view->assign('cid', $_cid );
		
		// Display
		$view->_displaylist();

	}
		
	public function edituserdetails() {

		$_name		  = JFactory::getApplication()->input->get( 'name', '', 'cmd' );
		
		$model        = $this->getModel ( 'user' );
		$view         = $this->getView  ( 'user','html' );
		
		$_row = $model->_edit_pointsDetails ();
		$_rule_name = $model->_get_rule_name ($_row->rule);
		
		$view->assign('row', $_row );	
		$view->assign('name', $_name );
		$view->assign('rulename', $_rule_name );
		
		// Display
		$view->_edit_pointsDetails();				
	
	}
	
	public function saveuserdetails () {
		
		$model        = $this->getModel ( 'user' );
		// save user details
		$model->_save_user_details ();
	
	}
	
	public function canceluserdetails() {
		$app = JFactory::getApplication();
		
		$redirecturl = JFactory::getApplication()->input->get( 'redirect', '', 'string' );
		
		$redirecturl = "index.php?option=com_altauserpoints&task=" . $redirecturl ;		
    	$this->setRedirect($redirecturl);
    	$this->redirect();
	}
	

	public function deleteuserdetails () {	
		
		$model        = $this->getModel ( 'user' );
		// delete 
		$model->_delete_user_details ();
	}	
	
	public function deleteuserallactivities() {
	
		$model        = $this->getModel ( 'user' );
		// delete 
		$model->_delete_user_all_activities ();
	
	}
		
	
	/**
	* Export 50 most active users in CSV
	*/
	public function exportactiveusers() {
	
		$model        = $this->getModel ( 'exports' );
		$_row = $model->_export_most_active_users ();
		
		if ( $_row ) {		
			$totalRecords = 0;
			$fileName     = "mostactiveusers_" . uniqid(rand(), true) . ".csv";	
			$filepath     = JPATH_COMPONENT_ADMINISTRATOR . '/assets/csv/' . $fileName;
			
			$handler = fopen($filepath,"a");
			fwrite($handler,"#;USER ID;NAME;USERNAME;POINTS;ALTAUSERPOINTS ID"."\n");

			$total = count( $_row );
			$j = 0;
			for ($i=0;$i< $total;$i++) {
				if ( $_row[$i]->referreid != 'GUEST' ) {
					$j++;
					fwrite( $handler, $j . ";" . $_row[$i]->iduser . ";" . $_row[$i]->name . ";" . $_row[$i]->username . ";" . $_row[$i]->points . ";" . $_row[$i]->referreid . "\n" );
				}
			}
	
			header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
			header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-Type: text/x-comma-separated-values");
			header("Content-Disposition: attachment; filename=$fileName");
			
			readfile($filepath);
			
			exit;
		} else {
			$error = JText::_('AUP_NO_DATA');
			JFactory::getApplication()->enqueueMessage(  $error ,'warning');
			return $this->cpanel();
		} 
	
	}

	/**
	* Export all e-mails addresses sent in CSV
	*/
	public function exportemails() {
	
		jimport( 'joomla.mail.helper' );
	
		$model        = $this->getModel ( 'exports' );		
		$_row = $model->_export_emails ();	
		
		if ( $_row ) {
			$totalRecords = 0;
			$fileName     = "export_emails_" . uniqid(rand(), true) . ".csv";	
			$filepath     = JPATH_COMPONENT_ADMINISTRATOR . '/assets/csv/' . $fileName;
			
			$handler= fopen($filepath,"a");
			fwrite($handler,"EMAIL"."\n");
			
			$total = count( $_row );
			for ($i=0;$i< $total;$i++) {
				$_row[$i]->datareference = str_replace(" [at] ", "@", $_row[$i]->datareference);
				$aEmails[0] = $this->extractEmailsFromString($_row[$i]->datareference);
				$email= $aEmails[0][0];
				if ( JMailHelper::isEmailAddress($email) ) {				
					fwrite($handler,$email."\n");
				}				
			}
	
			header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
			header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-Type: text/x-comma-separated-values");
			header("Content-Disposition: attachment; filename=$fileName");
			
			readfile($filepath);
			
			exit;
		} else {
			$error = JText::_('AUP_NO_DATA');
			JFactory::getApplication()->enqueueMessage(  $error ,'warning');
			return $this->cpanel();
		} 
	
	}
	
	
	/**
	* Common publish/unpublish function
	*/
	public function publish() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd' );	
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'cmd' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_publish( $cid, 1, $option, $table, $redirect );
	
	}
	
	public function unpublish() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd');
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'cmd' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_publish( $cid, 0, $option, $table, $redirect );	

	}
	
	
	
	public function autoapprove() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd' );	
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'string' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_autoapprove( $cid, 1, $option, $table, $redirect );
	
	}
	
	public function unautoapprove() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd');
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'string' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_autoapprove( $cid, 0, $option, $table, $redirect );	

	}
	
	public function approve() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd' );	
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'string' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_approve( $cid, 1, $option, $table, $redirect );
	
	}
	
	public function unapprove() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd');
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'cmd' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_approve( $cid, 0, $option, $table, $redirect );	

	}
	
	public function deletependingapproval() {
	
		$cid		= JFactory::getApplication()->input->get( 'cid', 0, 'int' );
		$db			= JFactory::getDBO();
		$q = "DELETE FROM #__alpha_userpoints_details WHERE `id` = " . $db->quote($cid);
		$db->setQuery($q);
		
		if (!$db->query()) {
			JFactory::getApplication()->enqueueMessage(  $db->getErrorMsg() ,'error');
		}
		
		return $this->cpanel();
	}
	
	
	public function extractEmailsFromString($sChaine) {	 
		if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', $sChaine, $aEmails)) {
			if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
				return array_unique($aEmails[0]);
			}
		}		 
		return null;
	}
	
	public function applybonus() {
		
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		
		if (count($cid) < 1) {
			JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
			return false;
		}
		
		$model      = $this->getModel ( 'helper' );
		$model->_bonuspoints( $cid );
	
	}
	
	public function changeuserlevel() {
		$app = JFactory::getApplication();
		
		$model = $this->getModel( 'requests' );
		$model->_acceptlevel();
				
    	$this->setRedirect('index.php?option=com_altauserpoints&task=cpanel');
    	$this->redirect();

	}
	
	public function couponcodes() {
	
		$model = $this->getModel( 'couponcodes' );
		
		$view  = $this->getView  ( 'couponcodes','html' );

		// load coupons
		$_couponcodes = $model->_load_couponcodes ();
		
		$view->assign('couponcodes', $_couponcodes[0] );
		$view->assign('total', $_couponcodes[1] );
		$view->assign('limit', $_couponcodes[2] );
		$view->assign('limitstart', $_couponcodes[3] );
		$view->assign('lists', $_couponcodes[4] );
		
		// Display
		$view->_displaylist();		
	
	}	
	
	public function editcoupon() {
		
		$model        = $this->getModel ( 'couponcodes' );
		$view         = $this->getView  ( 'couponcodes','html' );
		
		$result = $model->_edit_coupon ();		
		
		$view->assign('row', $result[0] );
		$view->assign('lists', $result[1]);
		
		// Display
		$view->_edit_coupon();
	
	}
	
	public function savecoupon() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'couponcodes' );
		// save coupon(s)
		$model->_save_coupon ();

	}	
	
	public function applycoupon() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'couponcodes' );
		// save coupon(s)
		$apply=1;
		$model->_save_coupon ($apply);

	}	


	public function deletecoupon() {
		
		$model        = $this->getModel ( 'couponcodes' );
		// delete coupon(s)
		$model->_delete_coupon ();

	}	
	
	public function cancelcoupon() {
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=couponcodes";		

    	$this->setRedirect($redirecturl);
    	$this->redirect();
	
	}
	
	public function coupongenerator() {

		$view  = $this->getView  ( 'couponcodes','html' );
		
		$lists = array();		
		$lists['public'] 		= JHTML::_('select.booleanlist',  'public', 'class="inputbox"', 1 );
		$lists['printable'] 	= JHTML::_('select.booleanlist',  'printable', 'class="inputbox"', 0 );
		
		$view->assign( 'lists',  $lists );		
		
		$view->_generate_coupon();	
	
	}
	
	public function savecoupongenerator() {	

		$model        = $this->getModel ( 'couponcodes' );
		// save coupon(s)
		$model->_save_coupongenerator ();

	}
	
	// show tracking QR code coupon
	public function qrcodestats() {
	
		$model = $this->getModel( 'couponcodes' );
		
		$view         = $this->getView  ( 'couponcodes','html' );
		
		$id		= JFactory::getApplication()->input->get( 'id', 0, 'int' );

		// load coupons
		$_qrcodestats = $model->_load_qrcodestats ($id);
		
		$view->assign('qrcodestats', $_qrcodestats[0] );
		$view->assign('total', $_qrcodestats[1] );
		$view->assign('limit', $_qrcodestats[2] );
		$view->assign('limitstart', $_qrcodestats[3] );
		$view->assign('id', $id	);
		
		// Display
		$view->_displayQRcodestats();		
	
	
	}
	
	// show print coupon
	public function printcoupon() {	
	
		$app = JFactory::getApplication();
	
		$model = $this->getModel( 'couponcodes' );
		
		$view         = $this->getView  ( 'couponcodes','html' );
		
		$SiteName 	= $app->getCfg('sitename');
		
		// load info coupon
		$row = $model->_print_coupon ();
		
		$view->assign('couponcode', $row->couponcode );
		$view->assign('points', $row->points );
		$view->assign('sitename', $SiteName );
		
		// Display
		$view->_print_coupon();
	
	}
	
		
	
	public function stats() {
	
		$date_start = JFactory::getApplication()->input->get( 'date_start', '', 'string' );
		$date_end = JFactory::getApplication()->input->get( 'date_end', '', 'string' );
		$rule = JFactory::getApplication()->input->get( 'rule', '', 'int' );
	
		$model        = $this->getModel ( 'statistics' );		
		$view         = $this->getView  ( 'stats','html' );
		
		$result = $model->_pointsearned();
		$result2 = $model->_pointsspent();
		
		$average_points_earned_by_day = $model->_average_points_earned_by_day();
		$average_points_spent_by_day = $model->_average_points_spent_by_day();
		$numusers = $model->_get_num_users();
		$resultinactiveusers = $model->_get_inactive_members();
		$inactiveusers = $resultinactiveusers[0];
		$num_days_inactiveusers_rule = $resultinactiveusers[1];
		
		$communitypoints = $model->_totalcurrentcommunitypoints();
		
		$listRules = $model->_getListRules($rule);
	
		
		$view->assign('result', $result );
		$view->assign('result2', $result2 );
		$view->assign( 'date_start', $date_start );
		$view->assign( 'date_end',  $date_end );
		$view->assign( 'listrules',  $listRules );
		$view->assign( 'communitypoints',  $communitypoints );	
		$view->assign( 'average_points_earned_by_day', $average_points_earned_by_day );
		$view->assign( 'average_points_spent_by_day', $average_points_spent_by_day );	
		$view->assign( 'numusers', $numusers );	
		$view->assign( 'inactiveusers', $inactiveusers );
		$view->assign( 'num_days_inactiveusers_rule', $num_days_inactiveusers_rule );		
				
		// Display
		$view->_display();
	}
	
	public function raffle ()
	{	
		$model        = $this->getModel ( 'raffle' );
	
		$view         = $this->getView  ( 'raffle','html' );

		// load raffle
		$_raffle = $model->_load_raffle ();
		
		$view->assign('raffle', $_raffle[0] );
		$view->assign('total', $_raffle[1] );
		$view->assign('limit', $_raffle[2] );
		$view->assign('limitstart', $_raffle[3] );
		$view->assign('lists', $_raffle[4] );
		
		// Display
		$view->_displaylist();	
	}

	/**
	* Edit Raffle
	*/
	public function editraffle() {
		
		$model        = $this->getModel ( 'raffle' );
		$view         = $this->getView  ( 'raffle','html' );
		
		$_row = $model->_edit_raffle ();
		
		//$view->assign('row', $_row );
		$view->assign('row', $_row[0] );
		$view->assign('lists', $_row[1]);
		
		// Display
		$view->_edit_raffle();				
	}	
	
	
	/**
	* Save Raffle
	*/
	public function saveraffle() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'raffle' );
		// save raffle
		$model->_save_raffle ();	
	}	
	
	public function applyraffle() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'raffle' );
		// save raffle
		$apply=1;
		$model->_save_raffle ($apply);	
	}	
	
	
	/**
	* Delete Raffle
	*/
	public function deleteraffle() {
		
		$model        = $this->getModel ( 'raffle' );
		// delete raffle
		$model->_delete_raffle ();
	}
	
	public function registration() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd' );	
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'cmd' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_registration_raffle( $cid, 1, $option, $table, $redirect );
	
	}
	
	public function unregistration() {
	
		$option		= JFactory::getApplication()->input->get( 'option', 'com_altauserpoints', 'cmd' );
		$table  	= JFactory::getApplication()->input->get( 'table', '', 'cmd');
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$redirect	= JFactory::getApplication()->input->get( 'redirect', 'cpanel', 'cmd' );
		
		$model      = $this->getModel ( 'helper' );
		$model->_aup_registration_raffle( $cid, 0, $option, $table, $redirect );

	}	
	
	public function cancelraffle() {
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=raffle";
    	$this->setRedirect($redirecturl);
    	$this->redirect();
	}
	
	public function makeraffle() {
		
		$model        = $this->getModel ( 'raffle' );
		// launch raffle
		$model->_make_raffle_now ();
		
	}
	
	public function exportListUsersRaffle()
	{
		$model        = $this->getModel ( 'raffle' );
		$_row = $model->_export_users_registration ();
	
		$fileName     = "users_registration_" . uniqid(rand(), true) . ".csv";	
		$filepath     = JPATH_COMPONENT_ADMINISTRATOR . '/assets/csv/' . $fileName;
		
		$handler = fopen($filepath,"a");
		fwrite($handler,"#;USER ID;NAME;USERNAME\n");
		$j=0;
		$total = count( $_row );
		
		for ($i=0;$i< $total;$i++) {
			$j++;
			fwrite( $handler, $j . ";" . $_row[$i]->uid . ";" . $_row[$i]->name . ";" . $_row[$i]->username . ";" . $_row[$i]->email . "\n" );
		}
		
		header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
		header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/x-comma-separated-values");
		header("Content-Disposition: attachment; filename=$fileName");
		
		readfile($filepath);
		
		exit;

	}
	
	public function levelrank()
	{
		$model = $this->getModel( 'levelrank' );
		$view  = $this->getView  ( 'levelrank','html' );
		// load language for upload image
    	$lang = JFactory::getLanguage();
   		$lang->load( 'com_media');

		// load levelrank
		$_levelrank = $model->_load_levelrank ();
		
		$view->assign('levelrank', $_levelrank[0] );
		$view->assign('total', $_levelrank[1] );
		$view->assign('limit', $_levelrank[2] );
		$view->assign('limitstart', $_levelrank[3] );
		$view->assign('lists', $_levelrank[4] );
		
		// Display
		$view->_displaylist();		
	
	}	
	
	public function editlevelrank()
	{
		$model        = $this->getModel ( 'levelrank' );
		$view         = $this->getView  ( 'levelrank','html' );		
		// load language for upload image
    	$lang = JFactory::getLanguage();
    	$lang->load( 'com_media');
		$result = $model->_edit_levelrank ();		
		$view->assign('row', $result[0] );
		$view->assign('lists', $result[1]);
		// Display
		$view->_edit_levelrank();
	}
	
	public function savelevelrank()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model        = $this->getModel ( 'levelrank' );
		// save levelrank(s)
		$model->_save_levelrank ();
	}	


	public function deletelevelrank() {
		
		$model        = $this->getModel ( 'levelrank' );
		// delete levelrank(s)
		$model->_delete_levelrank ();

	}	

	
	public function cancellevelrank() {
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=levelrank";		
    	$this->setRedirect($redirecturl);
    	$this->redirect();	
	}
	
	public function detailrank() {
	
		$model = $this->getModel( 'levelrank' );
		
		$view  = $this->getView  ( 'levelrank','html' );

		// load levelrank
		$_detailrank = $model->_load_detailrank ();
		
		$view->assign('detailrank', $_detailrank[0] );
		$view->assign('total', $_detailrank[1] );
		$view->assign('limit', $_detailrank[2] );
		$view->assign('limitstart', $_detailrank[3] );
		
		// Display
		$view->_displaydetailrank();		
	
	}
	
	public function applycustom() {	// specific user
	
		$cid		= JFactory::getApplication()->input->get( 'cid', '', 'string' );
		$name		= JFactory::getApplication()->input->get( 'name', '', 'string' );
		
		if (!$cid) {
			return false;
		}
		
		$view  = $this->getView  ( 'rules','html' );
		
		$view->assign('cid', $cid );
		$view->assign('name', $name );
		
		$view->_displaycustompoints();	
	
	}
	
	public function savecustompoints() {	// specific user
	
		$app = JFactory::getApplication();
		
		$referrerid = JFactory::getApplication()->input->get( 'cid', '', 'string' );
		$name		= JFactory::getApplication()->input->get( 'name', '', 'string' );
		
		$points		= JFactory::getApplication()->input->get( 'points', 0, 'float' );
		$reason		= JFactory::getApplication()->input->get( 'reason', '', 'SAFE_HTML ' );		
		
		if ( $referrerid ) {
			require_once ( JPATH_SITE.'/components/com_altauserpoints/helper.php' );			
			AltaUserPointsHelper::userpoints ( 'sysplgaup_custom', $referrerid, 0, '', $reason, $points );
			AltaUserPointsHelper::checkRankMedal($referrerid);
		}
		   
	    $this->setRedirect('index.php?option=com_altauserpoints&task=showdetails&cid='.$referrerid.'&name='.$name);
	    $this->redirect();
	}
	
	public function applycustomrule() {	// several users
	
		$cid		= JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		
		if (count($cid) < 1) {
			JViewContent::displayError( JText::_('Select an item to') . ' ' . JText::_($action) );
			return false;
		}
		$cid		= implode(",", $cid);
		
		$view  = $this->getView  ( 'rules','html' );
		$view->assign('cid', $cid );
		$view->_displaycustomrulepoints();
	}

	public function savecustomrulepoints() {	// save several users for custom rule points
	
		$cid		= JFactory::getApplication()->input->get( 'cid', '', 'string' );
		$points		= JFactory::getApplication()->input->get( 'points', 0, 'float' );
		$reason		= JFactory::getApplication()->input->get( 'reason', '', 'SAFE_HTML ' );		
		
		if ( $cid ) {
			$cid = explode(",", $cid );
		}
				
		$model      = $this->getModel ( 'helper' );		
		$model->_customrulepoints( $cid, $reason, $points );
		
	}
	
	/**
	* Save the item(s) to the menu selected
	*/
	public function saveorder()
	{
		$cid	= JFactory::getApplication()->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel( 'levelrank' );
		if ($model->setOrder($cid)) {
			$msg = JText::_( 'AUP_NEW_ORDERING_SAVED' );
		}
		
		$this->setRedirect( 'index.php?option=com_altauserpoints&task=levelrank', $msg );
		$this->redirect();
	}
	
	/**
	* Save rank/medal(s) order
	*/
	public function orderup()
	{		
		$cid	= JFactory::getApplication()->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_altauserpoints&task=levelrank', JText::_('No Items Selected') );
			$this->redirect();
			return false;
		}

		$model = $this->getModel( 'levelrank' );
		if ($model->orderItem($id, -1)) {
			$msg = JText::_( 'AUP_ITEM_MOVED_UP' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( 'index.php?option=com_altauserpoints&task=levelrank', $msg );
		$this->redirect();
	}

	/**
	* Save rank/medal(s) order
	*/
	public function orderdown()
	{		
		$cid	= JFactory::getApplication()->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_altauserpoints&task=levelrank', JText::_('No Items Selected') );
			$this->redirect();
			return false;
		}

		$model = $this->getModel( 'levelrank' );
		if ($model->orderItem($id, 1)) {
			$msg = JText::_( 'AUP_ITEM_MOVED_DOWN' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( 'index.php?option=com_altauserpoints&task=levelrank', $msg );
		$this->redirect();
	}
	
	
	/* *
	 * upload image
	 */
	public function upload()
	{
		$app = JFactory::getApplication();

		// load language fo component media
    	$lang = JFactory::getLanguage();
    	$lang->load( 'com_media');
		$params = JComponentHelper::getParams('com_media');
		
		require_once( JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php' );		
		
		define('COM_AUP_MEDIA_BASE', JPATH_ROOT.'/components/com_altauserpoints/assets/images/awards');		

		// Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );


		$files 		= JFactory::getApplication()->input->files->get('Filedata', '', 'array');
		$file 		= $files[0];
		$folder		= JFactory::getApplication()->input->get( 'folder', 'icon', 'path' );
		$format		= JFactory::getApplication()->input->get( 'format', 'html', 'cmd');
		$return		= JFactory::getApplication()->input->get( 'return-url', null, 'base64' );
		$err		= null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$filepath = JPath::clean(COM_AUP_MEDIA_BASE.'/'.$folder.'/'.strtolower($file['name']));

			if (!MediaHelper::canUpload( $file, $err )) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					$app->enqueueMessage( JText::_($err) , 'notice');
					// REDIRECT
					if ($return) {
            			$this->setRedirect(base64_decode($return));
            			$this->redirect();						
					}
					return;
				}
			}

			if (JFile::exists($filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: '.$filepath));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					$app->enqueueMessage( JText::_('Error. File already exists'),'notice');
					// REDIRECT
					if ($return) {
            			$this->setRedirect(base64_decode($return));
            			$this->redirect();
					}
					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: '.$filepath));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				} else {
					JFactory::getApplication()->enqueueMessage(  JText::_('Error. Unable to upload file'),'warning');
					// REDIRECT
					if ($return) {
            			$this->setRedirect(base64_decode($return));
            			$this->redirect();
					}
					return;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance();
					$log->addEntry(array('comment' => $folder));
					jexit('Upload complete');
				} else {
					$app->enqueueMessage(JText::_('Upload complete'));
					// REDIRECT
					if ($return) {
            			$this->setRedirect(base64_decode($return));
            			$this->redirect();
					}
					return;
				}
			}
		} else {
	    	$this->setRedirect('index.php', 'Invalid Request', 'error');
	    	$this->redirect();
		}
	}
	
	// export activities of a specific user to CSV
	public function exportallactivitiesuser()
	{
	
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
	
		$model        = $this->getModel ( 'exports' );		
		$lastpoints = $model->_exportallactivitiesuser ();
	
		$fileName     = $referrerid . "_activities_" . uniqid(rand(), true) . ".csv";	
		$filepath     = JPATH_SITE . '/tmp/' . $fileName;
		
		$handler = fopen($filepath,"a");
		$header = JText::_('AUP_USERNAME') . ";" . JText::_('AUP_DATE') . ";" . JText::_('AUP_ACTIVITY') . ";" . JText::_('AUP_POINTS') . ";" . JText::_('AUP_EXPIRE') . ";" .JText::_('AUP_DETAILS') . ";" . JText::_('AUP_APPROVED') ."\n";
		fwrite($handler, $header ."\n");

		$total = count( $lastpoints );
		for ($i=0;$i< $total;$i++) {
		
			$date_insert = JHTML::_('date',  $lastpoints[$i]->insert_date,  JText::_('DATE_FORMAT_LC2') );
		
			if ( $lastpoints[$i]->expire_date == $nullDate ) {
				$date_expire =  '';
			} else {
				$date_expire = JHTML::_('date',  $lastpoints[$i]->expire_date,  JText::_('DATE_FORMAT_LC') );
			}	
			
			$approved = ( $lastpoints[$i]->approved )?  JText::_('AUP_APPROVED') :  JText::_('AUP_PENDING_APPROVAL') ;	 					 

			fwrite( $handler, $lastpoints[$i]->username . ";" . $date_insert . ";" . JText::_($lastpoints[$i]->rule_name) . ";" . $lastpoints[$i]->points . ";" . $date_expire . ";" . $lastpoints[$i]->datareference . ";" . $approved . "\n" );
		}

		header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
		header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/x-comma-separated-values");
		header("Content-Disposition: attachment; filename=$fileName");
		
		readfile($filepath);
		
		exit;
	}
	
	// Export all activities for all members to CSV
	public function exportallactivitiesallusers()
	{
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();

		$model        = $this->getModel ( 'exports' );		
		$lastpoints = $model->_exportallactivitiesallusers ();
	
		$fileName     = "all_activities_" . uniqid(rand(), true) . ".csv";	
		$filepath     = JPATH_SITE . '/tmp/'  . $fileName;
		
		$handler = fopen($filepath,"a");
		$header = JText::_('AUP_USERNAME') . ";" . JText::_('AUP_DATE') . ";" . JText::_('AUP_ACTIVITY') . ";" . JText::_('AUP_POINTS') . ";" . JText::_('AUP_EXPIRE') . ";" .JText::_('AUP_DETAILS') . ";" . JText::_('AUP_APPROVED') . "\n";
		fwrite($handler, $header ."\n");

		$total = count( $lastpoints );
		for ($i=0;$i< $total;$i++) {
		
			$date_insert = JHTML::_('date',  $lastpoints[$i]->insert_date,  JText::_('DATE_FORMAT_LC2') );
		
			if ( $lastpoints[$i]->expire_date == $nullDate ) {
				$date_expire =  '';
			} else {
				$date_expire = JHTML::_('date',  $lastpoints[$i]->expire_date,  JText::_('DATE_FORMAT_LC') );
			}	
			
			$approved = ( $lastpoints[$i]->approved )?  JText::_('AUP_APPROVED') :  JText::_('AUP_PENDING_APPROVAL') ;	 					 

			fwrite( $handler, $lastpoints[$i]->username . ";" . $date_insert . ";" . JText::_($lastpoints[$i]->rule_name) . ";" . $lastpoints[$i]->points . ";" . $date_expire . ";" . $lastpoints[$i]->datareference . ";" . $approved . "\n" );
		}

		header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
		header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/x-comma-separated-values");
		header("Content-Disposition: attachment; filename=$fileName");
		
		readfile($filepath);
		
		exit;	
	}
	
	
	public function archiveActivities()
	{
		// show form for archive
		$view  = $this->getView ( 'archive','html');		
		$view->show();	
	}
	
	
	public function processarchive()
	{
		$app = JFactory::getApplication();
		
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Purge old points expired before combine all activities
		$model       = $this->getModel ( 'users' );		
		$_purgeexpirepoints	= $model->_purge_expires ();		

		// process to combine the set of all actions in one activity from a specified date
		$modelArchive = $this->getModel ( 'archive' );
		
		// delete and combine activities(s)
		$combined = $modelArchive->_archive ();
		
		// Display
    	$this->setRedirect('index.php?option=com_altauserpoints&task=cpanel&recalculate=start');
   		$this->redirect();		
	}
	
	
	public function reportsystem ()
	{	
		$model       = $this->getModel ( 'reportsystem' );
		
		// show form for report
		$view  = $this->getView ( 'reportsystem','html');		
		
		// load report
		$_reportsystem = $model->_generate_report ();
		
		$view->assign('reportsystem', $_reportsystem );
		
		$view->show();	
	}
	
	
	public function exportcoupons ()
	{	
		$model        = $this->getModel ( 'exports' );
		$_row = $model->_export_coupons ();
	
		$fileName     = "coupons_" . uniqid(rand(), true) . ".csv";	
		$filepath     = JPATH_COMPONENT_ADMINISTRATOR . '/assets/csv/' . $fileName;
		
		$handler = fopen($filepath,"a");
		fwrite($handler,"#;ID;DESCRIPTION;COUPON CODE;POINTS;EXPIRE DATE;PUBLIC\n");
		$j=0;
		$total = count( $_row );
		
		for ($i=0;$i< $total;$i++) {
			$j++;
			fwrite( $handler, $j . ";" . $_row[$i]->id . ";" . $_row[$i]->description . ";" . $_row[$i]->couponcode . ";" . $_row[$i]->points . ";" . $_row[$i]->expires . ";" . $_row[$i]->public . "\n" );
		}
		
		header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
		header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/x-comma-separated-values");
		header("Content-Disposition: attachment; filename=$fileName");
		
		readfile($filepath);
		
		exit;	
	
	}
	
	
	/**
	* Show Form Install Plugins
	*/
	public function plugins() {
		
		$view  = $this->getView ( 'plugins','html');
		
		$view->show();
	}
	
	/**
	* Upload Plugin
	*/
	public function uploadfile() {
	
		$view  = $this->getView ( 'plugins','html');		
		
		$error = "";
		$msg = "";
	
		if (@is_uploaded_file($_FILES["userfile"]["tmp_name"])) {
		
			require_once (JPATH_COMPONENT_ADMINISTRATOR  .'/assets' . '/includes/altauserpoints.installer.php');
			
			$installer = new aupInstaller();
			
			$file = $installer->install( $_FILES["userfile"] );
						
			if ( !is_array($file) ) {
				// extract data of xml file
				$this->loadPluginElements( $file );
			} elseif ( is_array($file) ){
				foreach ( $file as $_file ) {
					$this->loadPluginElements( $_file );
				}			
			} else {
				$error = JText::_('AUP_FILEUPLOAD_ERROR');
				JFactory::getApplication()->enqueueMessage(  $error,'warning' );
				$view->show();
			}
			
			return $this->rules();
			
		} else {
			$error = JText::_('AUP_FILEUPLOAD_ERROR');
			JFactory::getApplication()->enqueueMessage(  $error ,'warning');
			$view->show();
		}
	
	}
	
	/**
	 * Loading of related XML files
	 *
	*/
	public function loadPluginElements( $xmlFile ) {

		$model = $this->getModel ( 'plugins' );		
		$model->_loadPluginElements( $xmlFile );

	}
	
	
	// import export table activity
	
	public function importexportTableActivities()
	{
	
		$view  = $this->getView ( 'export','html');
		
		$view->show();
	}
	
	public function uploadImportFile()
	{
		
		$view  = $this->getView ( 'export','html');		
		
		$error = "";
		$msg = "";
	
		if (@is_uploaded_file($_FILES["userfile"]["tmp_name"])) {
		
			$datafile = $_FILES["userfile"];
		
			//  TODO traitement pour import data
			$db	   = JFactory::getDBO();
			$q = "LOAD DATA INFILE '".$datafile."' REPLACE INTO TABLE #__alpha_userpoints_details
						FIELDS TERMINATED BY ';' ENCLOSED BY '\"'
						LINES TERMINATED BY '\r\n'
						IGNORE 1 LINES;";
			$db->setQuery( $q );
			$db->query();
			
			$view->show();
	
		} else {
			$error = JText::_('AUP_FILEUPLOAD_ERROR');
			JFactory::getApplication()->enqueueMessage(  $error , 'warning' );
			$view->show();
		}
		
	}
	
	public function templateinvite() {
	
		$model = $this->getModel( 'templateinvite' );
		
		$view  = $this->getView  ( 'templateinvite','html' );

		// load coupons
		$_templateinvite = $model->_load_templateinvite ();
		
		$view->assign('templateinvite', $_templateinvite[0] );
		$view->assign('total', $_templateinvite[1] );
		$view->assign('limit', $_templateinvite[2] );
		$view->assign('limitstart', $_templateinvite[3] );
		
		// Display
		$view->_displaylist();		
	
	}	
	
	public function edittemplateinvite() {
		
		$model        = $this->getModel ( 'templateinvite' );
		$view         = $this->getView  ( 'templateinvite','html' );
		
		$result = $model->_edit_templateinvite ();		
		
		$view->assign('row', $result );
		
		// Display
		$view->_edit_templateinvite();
	
	}
	
	public function savetemplateinvite() {
	
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model        = $this->getModel ( 'templateinvite' );
		// save coupon(s)
		$model->_save_templateinvite ();

	}	
	
	public function applytemplateinvite()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model        = $this->getModel ( 'templateinvite' );
		$apply=1;
		$model->_save_templateinvite ($apply);
	}	

	public function deletetemplateinvite()
	{
		$model        = $this->getModel ( 'templateinvite' );
		$model->_delete_templateinvite ();
	}	
	
	public function canceltemplateinvite() {
		$app = JFactory::getApplication();
		
		$redirecturl = "index.php?option=com_altauserpoints&task=templateinvite";		
	  	$this->setRedirect($redirecturl);
	  	$this->redirect();
	}
	
	public function resetuser()
	{	
	
			$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
			
			$cid 		= implode(',',$cid);
			
			JToolBarHelper::title(   JText::_('AUP_USERS'), 'user' );
			echo "<b>".JText::_( 'AUP_CONFIRM_RESET_USER' )."</b>";
			
			?>
			<p>
			<form action="index.php?option=com_altauserpoints&task=resetUserConfirmed" method="post" name="adminForm" id="confirm-form" class="form-validate">
			  <input type="hidden" name="cid" value="<?php echo $cid;?>">
			  <input type="submit" name="Submit" value="<?php echo  JText::_('JYes'); ?>">
              <input type="Submit" name="Reset" value="<?php echo  JText::_('JNO'); ?>" onClick="javascript: window.history.go(-1);">			
			</form>
			</p> 
			<?php
			
			
	}	
	
	public function resetUserConfirmed()
	{
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= $app->input->get('cid', '', 'string');
		$cid		= explode(',',$cid);
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {			
		
			$jnow		= JFactory::getDate();		
			$now		= $jnow->toSql();
			
			foreach ($cid as $id)
			{
				
				// main query
				$q = "UPDATE #__alpha_userpoints SET `points`='0', `max_points`='0', `last_update`='$now', `levelrank`='0', `leveldate`='$now', `profileviews`='0', `referraluser`='', `referrees`='' WHERE id=".$db->quote($id);
				$db->setQuery( $q );
				$db->execute();
				
				$referreid = getReferreidByID( $id );
				
				// main query
				$query = "DELETE FROM #__alpha_userpoints_details WHERE referreid='".$referreid."'";
				$db->setQuery( $query );
				$db->query();
				
				// main query
				$query = "DELETE FROM #__alpha_userpoints_medals WHERE rid='".$id."'";
				$db->setQuery( $query );
				$db->query();
					
				if (!$db->query()) {
					$msg = $db->getErrorMsg();
					$msgType = 'error';
				}
			}

		}

	  $this->setRedirect('index.php?option=com_altauserpoints&task=statistics', $msg, $msgType);
	  $this->redirect();
	
	}
	
	public function resetprofilviews()
	{
		$app = JFactory::getApplication();
		$cid		= JFactory::getApplication()->input->get('id', 0, 'int');
		
		$db = JFactory::getDBO();
		
		$query = "UPDATE #__alpha_userpoints SET `profileviews`='0' WHERE id='".$cid."'";
		$db->setQuery( $query );
		$db->query();		
		
	  	$this->setRedirect('index.php?option=com_altauserpoints&task=edituser&cid[]='.$cid);
	  	$this->redirect();				
	}

	public function resetreferraluser()
	{
		$app = JFactory::getApplication();
		$cid		= JFactory::getApplication()->input->get('id', 0, 'int');
		
		$db = JFactory::getDBO();
		
		$query = "UPDATE #__alpha_userpoints SET `referraluser`='' WHERE id='".$cid."'";
		$db->setQuery( $query );
		$db->query();		
		
    	$this->setRedirect('index.php?option=com_altauserpoints&task=edituser&cid[]='.$cid);
    	$this->redirect();		
	}
	
	public function resetreferrees()
	{
		$app = JFactory::getApplication();
		$cid		= JFactory::getApplication()->input->get('id', 0, 'int');
		
		$db = JFactory::getDBO();
		
		$query = "UPDATE #__alpha_userpoints SET `referrees`='' WHERE id='".$cid."'";
		$db->setQuery( $query );
		$db->query();		
		
    	$this->setRedirect('index.php?option=com_altauserpoints&task=edituser&cid[]='.$cid);
    	$this->redirect();					
	}
	
	//----------------------- plg_editor-xtd_raffle integration start -----------------------//
		
	public function editorInsertRaffle()
	{		
		//$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$doc =  JFactory::getDocument();
		$lang =  JFactory::getLanguage();
		$lang->load('plg_editors-xtd_raffle', JPATH_ADMINISTRATOR);
		
		// build raffles listbox
		$raffles_list = array();
		$query = "SELECT id, description, raffledate"
		. " FROM #__alpha_userpoints_raffle"
		. " WHERE published = '1'"
		. " AND winner1=0"
		. " ORDER BY id";
		// check if not winner (current raffles)		
		
		$db->setQuery( $query );
		$raffles = $db->loadObjectList();
		foreach ($raffles as $raffle) {
			$raffles_list[] = JHTML::_('select.option', $raffle->id, $raffle->description);
		}
		$raffles_listbox =  JHTML::_('select.genericlist', $raffles_list, 'id', 'class="inputbox" size="10"', 'value', 'text', '' );
		$eName    = JFactory::getApplication()->input->get('e_name','');

?>
<script type="text/javascript">
function insertRaffleId()
{
	var id = document.getElementById("id").value;
	var tag;
	if (id >0){
		tag = "\{AUP::RAFFLE="+id+"\}"; 
		window.parent.jInsertEditorText(tag, '<?php echo $eName; ?>');
	}
	window.parent.SqueezeBox.close();
	return false;
}
</script>

    <form id="insertRaffle" style="font-style: bold; font-family: Arial; font-size:12px; background-color: #FFFFCC">
    <table width="100%" cellpadding="2" cellspacing="2" border="0" style="padding: 10px;">
       <tr> 
         <td colspan="2">
			<img src="components/com_altauserpoints/assets/images/aup_logo.png" width="48px" height="48px" align="bottom" border="0"/>&nbsp;&nbsp;
            <strong><?php echo JText::_('PLG_RAFFLE_BUTTON_TITLE').''; ?></strong><br />
            <?php echo JText::_('PLG_RAFFLE_BUTTON_TITLE_DESC'); ?>
         </td>
       </tr>
       <tr>
          <td class="key" align="right" width="30%" valign="top">
              <label for="id">
                  <?php echo JText::_('PLG_RAFFLE_BUTTON_ID_TITLE'); ?>
              </label>
          </td>
          <td width="70%" align="left">
              <?php echo $raffles_listbox; ?>
          </td>
       </tr>
       <tr><td colspan="2"><font color="#red"><?php echo JText::_('PLG_RAFFLE_BUTTON_NOTE'); ?></font></td></tr>
       <tr><td colspan="2"><hr /></td></tr>
			<tr>
                <td class="key" align="right"></td>
                <td>
                    <button onclick="insertRaffleId();return false;"><?php echo JText::_('PLG_RAFFLE_BUTTON_INSERT'); ?></button>
                </td>
            </tr>
            <tr><td colspan="2"><?php echo JText::_('PLG_RAFFLE_BUTTON_INFO'); ?></td></tr>
      </table>
</form>
<?php
	}
	//------------------------ plg_editor-xtd_raffle integration end ------------------------//

	
} // end class
?>