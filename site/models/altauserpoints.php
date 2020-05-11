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

class AltauserpointsModelaltauserpoints extends JmodelLegacy {

	function __construct(){
		parent::__construct();
		
	}
	
	function _getParamsAUP() {
	
		// Get the parameters of the active menu item
		$app = JFactory::getApplication();
		$menus = $app->getMenu();		
		$menu       = $menus->getActive();
		$menuid     = @$menu->id;
		$params     = $menus->getParams($menuid);
		
		return $params;
	
	}	

	function _get_last_points( $referrerid, $limit=10 ) {
	
		$app = JFactory::getApplication();
		
		$db	   = JFactory::getDBO();
		
		if ( $limit=='all') {
			// Get the pagination request variables
			$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
			$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
			// In case limit has been changed, adjust limitstart accordingly
			$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);		
			$q = "SELECT a.*, r.rule_name, r.plugin_function FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules as r "
					."\nWHERE a.referreid=".$db->quote($referrerid)." AND a.rule=r.id AND r.displayactivity='1' AND a.enabled='1' "
					."\nORDER BY a.insert_date DESC";
			$total = @$this->_getListCount($q);
			$result = $this->_getList($q, $limitstart, $limit);
			return array($result, $total, $limit, $limitstart);
			
		} 
		elseif ( $limit=='nolimit' )
		{		// used for export CSV
			$q = "SELECT a.*, r.rule_name, r.plugin_function FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules as r "
					."\nWHERE a.referreid=".$db->quote($referrerid)." AND a.rule=r.id AND r.displayactivity='1' AND a.enabled='1' "
					."\nORDER BY a.insert_date DESC";
			$db->setQuery( $q );
			$rowslastpoints = $db->loadObjectList();
			return $rowslastpoints;				
		} 
		else 
		{		
			$limit = "LIMIT " . $limit ;
			$q = "SELECT a.*, r.rule_name, r.plugin_function FROM #__alpha_userpoints_details AS a, #__alpha_userpoints_rules as r "
					."\nWHERE a.referreid=".$db->quote($referrerid)." AND a.rule=r.id AND r.displayactivity='1' AND a.enabled='1' "
					."\nORDER BY a.insert_date DESC $limit";
			$db->setQuery( $q );
			$rowslastpoints = $db->loadObjectList();
			return array($rowslastpoints, null, null, null);
		}	
	
	}
	
	function _get_referrees ( $referrerid ) {
		
		$db	   = JFactory::getDBO();
		$q = "SELECT * FROM #__alpha_userpoints WHERE referraluser='$referrerid'";
		$db->setQuery( $q );
		$rowsreferrees = $db->loadObjectList();
		
		if ( $rowsreferrees )
		{
			require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
			for ($i=0, $n=count( $rowsreferrees ); $i < $n; $i++)
			{				
				$UserInfo = AltaUserPointsHelper::getUserInfo ( $rowsreferrees[$i]->referreid );
				$username = $UserInfo->username;
				$name = $UserInfo->name;
				$rowsreferrees[$i]->username = $username;	
				$rowsreferrees[$i]->name = $name;		
			}		
		}

		return $rowsreferrees;
	}
	
	function _checkCurrentMaxPerDay( $ruleid, $userid, $referrerid, $ip ) {	
	
		$db	= JFactory::getDBO();
		
		$curdate = date( "Y-m-d" );
		
		if ( $userid ) {			
			// count invite sent this day
			$q = "SELECT count(*) FROM #__alpha_userpoints_details 
			WHERE rule=".$db->quote($ruleid)." AND referreid=".$db->quote($referrerid)." AND `insert_date` LIKE '$curdate%' AND enabled='1'";
		} else {
			// count guest invite sent this day
			$q = "SELECT count(*) FROM #__alpha_userpoints_details WHERE rule=".$db->quote($ruleid)." AND referreid='GUEST' 
			AND `insert_date` LIKE '$curdate%' AND keyreference=".$db->quote($ip)." AND enabled='1'";
		}	
		$db->setQuery( $q );
		$result = $db->loadResult();
		
		return $result;
	
	}
	
	function _checkLastInviteForDelay( $ruleid, $userid=0, $referrerid, $ip, $delay ) {	
	
		$db	= JFactory::getDBO();
		
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$ts 		= strtotime( $now );		
				
		$checkdelay = 1;
		
		if ( $userid ) {			
			$q = "SELECT `insert_date` 
			FROM #__alpha_userpoints_details 
			WHERE rule='$ruleid' AND referreid=".$db->quote($referrerid)." AND enabled='1' ORDER BY `insert_date` DESC LIMIT 1";
		} else {
			$q = "SELECT `insert_date` FROM #__alpha_userpoints_details WHERE rule=".$db->quote($ruleid)." AND referreid='GUEST' 
			AND keyreference=".$db->quote($ip)." AND enabled='1' ORDER BY `insert_date` DESC LIMIT 1";
		}	
		$db->setQuery( $q );
		$result = $db->loadResult();
		
		// if exist -> compare
		if ( $result ) {				
			$lasttime = strtotime($result) + $delay;						
			if ( $lasttime > $ts ){
				$checkdelay = 0;
			}	
		}
		
		return $checkdelay;
	
	}
	
	function _extractEmailsFromString($sChaine) {	 
		if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', $sChaine, $aEmails)) {
			if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
				return array_unique($aEmails[0]);
			}
		}		 
		return null;
	}	
	
	function _checkUser()  {
		$app = JFactory::getApplication();
		
		// active user
		$user =  JFactory::getUser();	
		
		// check referre ID
		$referrerid = @$_SESSION['referrerid'];		
		
		if ( !$user->id || !$referrerid ) {		
			$msg = JText::_('ALERTNOTAUTH' );			
			$app->redirect('index.php', $msg);
		} else return $referrerid;
		
	}

	function _getReferreid()  {
		
		// check referre ID
		$referrerid = @$_SESSION['referrerid'];		
		
		return $referrerid;
		
	}
	
	function _getRuleID ( $plugin_function ) {
	
		$db	= JFactory::getDBO();
		 
		$q = "SELECT id FROM #__alpha_userpoints_rules WHERE plugin_function=".$db->quote($plugin_function)."";
		$db->setQuery( $q );
		$result = $db->loadResult();
		
		return $result;	
	}
	
	
	function _getUsersList(){
	
		$app = JFactory::getApplication();
		
		$db			        = JFactory::getDBO();		

		$filter_order		= $app->getUserStateFromRequest( "com_altauserpoints.filter_order",		'filter_order',		'aup.points',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( "com_altauserpoints.filter_order_Dir",	'filter_order_Dir',	'desc',			'word' );

		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		$orderby = " ORDER BY " . $filter_order . " " . $filter_order_Dir;
			
		$q = "SELECT aup.id AS rid, aup.points, aup.referreid, aup.last_update, aup.referraluser, u.name AS usr_name, u.username AS usr_username, aup.levelrank"
		. "\n FROM #__alpha_userpoints AS aup, #__users AS u"
		. "\n WHERE aup.userid=u.id AND aup.published='1'"
		. $orderby
		;				
		$total  = @$this->_getListCount($q);

		$rows = $this->_getList( $q, $limitstart , $limit );			
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		
		return array ( $rows, $total, $limit, $limitstart, $lists );
	
	}
	
	function _getArticleDescription ( $idArticle ){
	
		$db	= JFactory::getDBO();
		
		$q = "SELECT id, title, introtext FROM #__content WHERE id=".$db->quote($idArticle)." ";
		$db->setQuery( $q );
		$result = $db->loadObjectList();
		
		return $result;	
	
	}	
	
	function _pointsearned() { // function for TOP 10 in statistics tab
	
		$db = JFactory::getDBO();
		
		$q = "SELECT a.referreid, SUM(a.points) AS sumpoints, u.username AS username, u.name AS name"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND a.approved='1' AND a.status='1' AND a.enabled='1' AND aup.published='1'"
			   . " GROUP BY a.referreid"
			   . " ORDER BY sumpoints DESC"
			   . " LIMIT 10"
			   ;

		$db->setQuery( $q );
		$result = $db->loadObjectList();

		return $result;
	}
	
	function _totalpoints() {
	
		$db = JFactory::getDBO();
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1'"
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}	
	
	function _mypointsearned($referreid) {	
		
		$db = JFactory::getDBO();
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points>=1 AND a.referreid=" . $db->quote($referreid) . "";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}

	function _mypointsspent($referreid) {	
		
		$db = JFactory::getDBO();
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points<0 AND a.referreid=" . $db->quote($referreid) . "";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}
	
	function _mypointsearnedthismonth($referreid) {	
		
		$db = JFactory::getDBO();
		
		$curmonth = date( "Y-m-" );
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points>=1 AND a.referreid=" . $db->quote($referreid) . " AND insert_date LIKE '".$curmonth."%'";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}

	function _mypointsspentthismonth($referreid) {	
		
		$db = JFactory::getDBO();
		
		$curmonth = date( "Y-m-" );
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points<0 AND a.referreid=" . $db->quote($referreid) . " AND insert_date LIKE '".$curmonth."%'";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}

	function _mypointsearnedthisday($referreid) {	
		
		$db = JFactory::getDBO();
		
		$curday = date( "Y-m-d" );
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points>=1 AND a.referreid=" . $db->quote($referreid) . " AND insert_date LIKE '".$curday."%'";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}

	function _mypointsspentthisday($referreid) {	
		
		$db = JFactory::getDBO();
		
		$curday = date( "Y-m-d" );
		
		$q = "SELECT SUM(a.points)"
			   . " FROM #__alpha_userpoints_details AS a"
			   . " WHERE a.approved='1' AND a.status='1' AND a.enabled='1' AND a.points<0 AND a.referreid=" . $db->quote($referreid) . " AND insert_date LIKE '".$curday."%'";
			   ;

		$db->setQuery( $q );
		$result = $db->loadResult();

		return $result;
	
	}
	
	function _save_profile()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		//$post	= $app->input->post->get('post');
		$post	= JRequest::get( 'post' );
		
		if ( $post['referreid']=='' ) {
			echo "<script>window.history.go(-1);</script>\n";
			exit();
		} 
	
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
		$row = JTable::getInstance('userspoints');
		

		
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$app->enqueueMessage( JText::_('AUP_CHANGE_SAVED') );
	}
	
	function checkNewTotal( $referreid, $rule_id )
	{
		$db			= JFactory::getDBO();
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();
		
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
		
		// recalculate for this user 
		$q = "SELECT SUM(points) FROM #__alpha_userpoints_details WHERE `referreid`=" . $db->quote($referreid) . " AND `approved`='1' AND (`expire_date`>'$now' OR `expire_date`='0000-00-00 00:00:00') AND `enabled`='1'";
		$db->setQuery($q);
		$newtotal = $db->loadResult();

		$q = "UPDATE #__alpha_userpoints SET `points`=" . $db->quote($newtotal) . ", `last_update`='$now' WHERE `referreid`=" . $db->quote($referreid) . "";
		$db->setQuery( $q );
		$db->execute();
		
		// update Ranks / Medals if necessary		
		AltaUserPointsHelper::checkRankMedal ( $referreid, $rule_id );
	
	}	
	
	function _getMyCouponCode( $referreid )
	{
		$db = JFactory::getDBO();		
		$q = "SELECT d.* FROM #__alpha_userpoints_details AS d, #__alpha_userpoints_rules AS r 
		WHERE d.referreid='$referreid' AND d.enabled='1' AND r.id=d.rule 
		AND r.plugin_function='sysplgaup_couponpointscodes'";
		$db->setQuery( $q );
		$resultCoupons = $db->loadObjectList();
		return $resultCoupons;		
	
	}	
	
}
?>