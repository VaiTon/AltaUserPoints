<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerAccount extends AltauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
		parent::__construct( );
	}
	
	public function display ($cachable = false, $urlparams = false) 
	{
		$app = JFactory::getApplication();
		
		require_once JPATH_ROOT.'/components/com_altauserpoints/helper.php';
		
		$com_params = JComponentHelper::getParams( 'com_altauserpoints' );		
	
		$model      = $this->getModel ( 'altauserpoints' );
		$view       = $this->getView  ( 'account','html' );
		
		// current user
		$user =  JFactory::getUser();
		
		// profil request 
		$userid      = JFactory::getApplication()->input->getInt( 'userid');
		
		if ( !$user->id && !$com_params->get( 'allowGuestUserViewProfil', 1 ) ) {		
			$msg = JText::_('ALERTNOTAUTH' );
			$app->redirect('index.php', $msg);
			
		}			
		
		// check referre ID
		if ( ! $userid ) {
			$referrerid = $model->_checkUser();
		} else  {
			$referrerid = $userid;
		}
		
		// Rule Profile View
		if ( $referrerid != @$_SESSION['referrerid'] ) 
		{
			$keyreference  = AltaUserPointsHelper::buildKeyreference( 'sysplgaup_profile_view', $user->id );
			$username = ( $user->username ) ? $user->username : JText::_('AUP_GUEST');
			$datareference = JText::_('AUP_PROFILE_VIEW_REFERENCE') . ' ' . $username;
			AltaUserPointsHelper::userpoints ( 'sysplgaup_profile_view' , $referrerid, 0, $keyreference, $datareference );
		}
		// End rule Profile View
		
		// Get the parameters of the active menu item
		$params = $model->_getParamsAUP();
		
		$num_item_activities = $params->get( 'num_item_activities', 10 );
	
		$_get_last_points   	= $model->_get_last_points ( $referrerid, $num_item_activities );
		$_listing_last_points	= $_get_last_points[0];
		$_listing_total			= $_get_last_points[1];
		$_listing_limit			= $_get_last_points[2];
		$_listing_limitstart	= $_get_last_points[3];		
		
		$rowsreferrees			= $model->_get_referrees ( $referrerid );
		
		$pointsearned 			= $model->_pointsearned(); // users points earned TOP 10
		$totalpoints			= $model->_totalpoints(); // entire community
		$mypointsearned 		= $model->_mypointsearned($referrerid);
		$mypointsspent 			= $model->_mypointsspent($referrerid);		
		$mypointsearnedthismonth= $model->_mypointsearnedthismonth($referrerid);
		$mypointsspentthismonth	= $model->_mypointsspentthismonth($referrerid);
		$mypointsearnedthisday	= $model->_mypointsearnedthisday($referrerid);
		$mypointsspentthisday	= $model->_mypointsspentthisday($referrerid);
	
		$_user_info = AltaUserPointsHelper::getUserInfo ( $referrerid );
		
		$currenttotalpoints    	= $_user_info->points;
		$lastupdate 			= $_user_info->last_update;
		$referraluser 			= $_user_info->referraluser;
		
		$myname = $_user_info->name;		
		$myusername = $_user_info->username;
		
		$referralname = "";
		if ( $referraluser ) {
			$referralinfo = AltaUserPointsHelper::getUserInfo ( $referraluser );
			$referralname = $referralinfo->username;
		}
		
		// get level/rank if exist
		$userrankinfo = AltaUserPointsHelper::getUserRank ( $referrerid );
		// get medals if exist
		$medalslistuser = AltaUserPointsHelper::getUserMedals ( $referrerid );		

		// load avatar		
		$useAvatarFrom = $com_params->get('useAvatarFrom');
		$height = 100;
		if ( $useAvatarFrom=='altauserpoints' ) {
    
		  $lang = JFactory::getLanguage();
		  $lang->load( 'com_media', JPATH_ADMINISTRATOR);
		
		}	
		
		$avatar = getAvatar( $useAvatarFrom, $_user_info, $height, $height, 'class=""' );		
		
		// Get coupons code
		$resultCoupons = $model->_getMyCouponCode( $referrerid );		
						
		$view->assign('params', $params );
		$view->assign('cparams', $com_params );
		$view->assign('referreid', $referrerid );
		$view->assign('currenttotalpoints', $currenttotalpoints );
		
		$view->assign('rowslastpoints', $_listing_last_points );
		$view->assign('total', $_listing_total );
		$view->assign('limit', $_listing_limit );
		$view->assign('limitstart', $_listing_limitstart );
			
		$view->assign('lastupdate', $lastupdate );		
		$view->assign('referraluser', $referraluser );
		$view->assign('referralname', $referralname );
		$view->assign('rowsreferrees', $rowsreferrees );	
		$view->assign('userid', $user->id);
		$view->assign('userrankinfo', $userrankinfo);
		$view->assign('medalslistuser', $medalslistuser);
		$view->assign('pointsearned', $pointsearned);
		$view->assign('totalpoints', $totalpoints);
		$view->assign('mypointsearned', $mypointsearned);
		$view->assign('mypointsspent', $mypointsspent);	
		$view->assign('mypointsearnedthismonth', $mypointsearnedthismonth);
		$view->assign('mypointsspentthismonth', $mypointsspentthismonth);
		$view->assign('mypointsearnedthisday', $mypointsearnedthisday);
		$view->assign('mypointsspentthisday', $mypointsspentthisday);
		$view->assign('myname', $myname);
		$view->assign('myusername', $myusername);
		$view->assign('avatar', $avatar);
		$view->assign('user_info', $_user_info);
		$view->assign('useAvatarFrom', $useAvatarFrom);
		$view->assign('mycouponscode', $resultCoupons);
		$view->assign('userinfo', $_user_info);	

		// Display
		$view->_display();
	}
	
	public function saveprofile() {
	
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$referreid 		= JFactory::getApplication()->input->get('referreid', '', 'string');// referreid is not an INTeger, string filter is required
		$model      = $this->getModel ( 'altauserpoints' );
		$model->_save_profile();
		JFactory::getApplication()->input->set( 'userid', $referreid );
		
		return $this->display();
	
	}
	
	public function downloadactivity()
	{
	
			$userid = JFactory::getApplication()->input->get( 'userid', '', 'int' );
			
			require_once JPATH_ROOT.'/components/com_altauserpoints/helper.php';
			
			$referrerid = AltaUserPointsHelper::getAnyUserReferreID( $userid );
			
			if ( !$referrerid || $referrerid!=@$_SESSION['referrerid'] ) return;			
			
			$db = JFactory::getDBO();
			$nullDate = $db->getNullDate();
			
			$model        = $this->getModel ( 'altauserpoints' );
			$lastpoints   = $model->_get_last_points ( $referrerid, 'nolimit' );			

			$fileName     = "completeactivity_" . uniqid(rand(), true) . ".csv";	
			$filepath     = JPATH_SITE . '/tmp/' . $fileName;
			
			$handler = fopen($filepath,"a");
			$header = JText::_('AUP_DATE') . ";" . JText::_('AUP_ACTION') . ";" . JText::_('AUP_POINTS') . ";" . JText::_('AUP_EXPIRE') . ";" .JText::_('AUP_DETAIL') . ";" . JText::_('AUP_APPROVED');
			fwrite($handler, $header ."\n");

			$total = count( $lastpoints );
			for ($i=0;$i< $total;$i++)
			{
			
				$date_insert = JHTML::_('date',  $lastpoints[$i]->insert_date,  JText::_('DATE_FORMAT_LC2') );
			
				if ( $lastpoints[$i]->expire_date == $nullDate ) {
					$date_expire =  '';
				} else {
					$date_expire = JHTML::_('date',  $lastpoints[$i]->expire_date,  JText::_('DATE_FORMAT_LC') );
				}	
				
				$approved = ( $lastpoints[$i]->approved )?  JText::_('AUP_APPROVED') :  JText::_('AUP_PENDINGAPPROVAL') ;	 					 

				fwrite( $handler, $date_insert . ";" . JText::_($lastpoints[$i]->rule_name) . ";" . $lastpoints[$i]->points . ";" . $date_expire . ";" . $lastpoints[$i]->datareference . ";" . $approved . "\n" );
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
}
?>