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

class altauserpointsModelRules extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _load_rules() {
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
		
		$total 			= 0;
		
		$filter_category = $app->getUserStateFromRequest( 'com_altauserpoints'.'.filter_category', 'filter_category',	'all', 'word' );
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		
		if ( $filter_category!='all' ) {
			$filter = "WHERE r.category = '$filter_category'";						
		} else {
			$filter = "";		
		}
		
		// check if Kunena forum is installed to show pre-installed rules for Kunena
		// Dectects Kunena 2.0+   
		if( class_exists('KunenaForum') && KunenaForum::enabled() ) {
		  ( $filter!='' ) ? $filter .= " AND " : $filter .= "WHERE ";
				$filter .= "(r.plugin_function!='plgaup_newtopic_kunena' AND r.plugin_function!='plgaup_reply_kunena' )";
		// Detects Kunena 1.6 and 1.7
		} elseif ( class_exists('Kunena') && Kunena::enabled () ) {
		  if ( substr(Kunena::version(), 0, 3) == '1.7' ) {
			( $filter!='' ) ? $filter .= " AND " : $filter .= "WHERE ";
				$filter .= "(r.plugin_function!='plgaup_newtopic_kunena' AND r.plugin_function!='plgaup_reply_kunena' )";
		  } else {
			( $filter!='' ) ? $filter .= " AND " : $filter .= "WHERE ";
				$filter .= "(r.plugin_function!='plgaup_kunena_topic_create' AND r.plugin_function!='plgaup_kunena_topic_reply' AND r.plugin_function!='plgaup_kunena_message_delete' AND r.plugin_function!='plgaup_kunena_message_thankyou')";
		  }
		}		
		// end check Kunena pre_installed rules
		
		$query = "SELECT r.*, g.title AS groupname FROM #__alpha_userpoints_rules AS r LEFT JOIN #__viewlevels AS g ON g.id=r.access " . $filter . " ORDER BY r.category";
		$total = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		$lists = array();
		$options = array();		
		$options[] = JHTML::_('select.option', '', JText::_( 'JNONE' ) );
		$options[] = JHTML::_('select.option', 'us', JText::_( 'AUP_CAT_USER' ) );
		$options[] = JHTML::_('select.option', 'co', JText::_( 'AUP_CAT_COMMUNITY' ) );
		$options[] = JHTML::_('select.option', 'ar', JText::_( 'AUP_CAT_ARTICLE' ) );
		$options[] = JHTML::_('select.option', 'li', JText::_( 'AUP_CAT_LINK' ) );
		$options[] = JHTML::_('select.option', 'po', JText::_( 'AUP_CAT_POLL_QUIZZ' ) );		
		$options[] = JHTML::_('select.option', 're', JText::_( 'AUP_CAT_RECOMMEND_INVITE' ) );
		$options[] = JHTML::_('select.option', 'fo', JText::_( 'AUP_CAT_COMMENT_FORUM' ) );
		$options[] = JHTML::_('select.option', 'vi', JText::_( 'AUP_CAT_VIDEO' ) );		
		$options[] = JHTML::_('select.option', 'ph', JText::_( 'CAT_CAT_PHOTO' ) );
		$options[] = JHTML::_('select.option', 'mu', JText::_( 'AUP_CAT_MUSIC' ) );
		$options[] = JHTML::_('select.option', 'sh', JText::_( 'AUP_CAT_SHOPPING' ) );	
		$options[] = JHTML::_('select.option', 'pu', JText::_( 'AUP_CAT_PURCHASING' ) );		
		$options[] = JHTML::_('select.option', 'cd', JText::_( 'AUP_CAT_COUPON_CODE' ) );
		$options[] = JHTML::_('select.option', 'su', JText::_( 'AUP_CAT_SUBSCRIPTION' ) );
		$options[] = JHTML::_('select.option', 'ga', JText::_( 'AUP_CAT_GAMING' ) );
		$options[] = JHTML::_('select.option', 'sy', JText::_( 'AUP_CAT_SYSTEM' ) );	
		$options[] = JHTML::_('select.option', 'ot', JText::_( 'AUP_CAT_OTHER' ) );
		$options[] = JHTML::_('select.option', 'all', JText::_( 'AUP_ALL' ) );
		$lists['filter_category'] = JHTML::_('select.genericlist', $options, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit();"' ,'value', 'text', $filter_category );		
		
		return array($result, $total, $limit, $limitstart, $lists);
	
	}
	
	
	function _edit_rule() {
	
		$db     = JFactory::getDBO();
		$input     = JFactory::getApplication()->input;

		$cid 	= $input->get('cid', array(0), 'array');
		$option = $input->get('option', '', 'cmd');
		
		if (!is_array( $cid )) {
			$cid = array(0);
		}

		$lists = array();

		$row = JTable::getInstance('rules');
		$row->load( $cid[0] );
		//echo  'CID= '. $cid[0];
		//exit();
		return $row;
	
	}
	
	
	function _delete_rule() {
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {		
			
			// are there one or more rows to delete?
			if (count($cid) == 1) {
				$row = JTable::getInstance('rules');
				$row->load($cid[0]);
				//if ($row->system == 0 || $row->blockcopy == 0) {
				if ( $row->system == 0 ) {
					$msg = JText::sprintf('AUP_MSGSUCCESSFULLYDELETED', JText::_('AUP_RULE'), JText::_($row->rule_name));
				} else {
					$msg = JText::_('AUP_SYSTEM');
					$msgType = 'error';
				}
			} else {
				$msg = JText::sprintf('AUP_MSGSUCCESSFULLYDELETED', JText::_('AUP_RULES'), '');
			}
		
			$query = "DELETE FROM #__alpha_userpoints_rules"
					. "\n WHERE (`id` = " . implode(' OR `id` = ', $cid) . ")"
					. "\n AND ((`system`=0) OR (`system`=1 AND `duplicate`=1))"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}

		}

		$app->redirect('index.php?option=com_altauserpoints&task=rules', $msg, $msgType);
		//JControllerLegacy::redirect(); 				
		
	}
	
	function _save_rule($apply=0) {
	
		$app = JFactory::getApplication();

		// initialize variables
		$db = JFactory::getDBO();
		//$post = $_POST;	
		$post = JFactory::getApplication()->input->getArray(array());	
		$id= JFactory::getApplication()->input->get( 'id', 0, 'int' );	
		$emailbody= JFactory::getApplication()->input->get( 'emailbody', '', 'raw' );	
		$post['emailbody']=$emailbody; 
		
		$row = JTable::getInstance('rules');
		
		if ($post['plugin_function']=='sysplgaup_unlockmenus' || substr($post['plugin_function'], 0, 22) == 'sysplgaup_unlockmenus_' ){
			$post['content_items'] = implode(',', $post['content_items']);
			
			$cache	=  JFactory::getCache();
			$cache->cleanCache();
		}
		
		if ($post['plugin_function']=='plgaup_clickonmenus' || substr($post['plugin_function'], 0, 20) == 'plgaup_clickonmenus_' ){
			$post['content_items'] = implode(',', $post['content_items']);
			
			$cache	=  JFactory::getCache();
			$cache->cleanCache();
		}		

		if ($post['plugin_function']=='plgaup_readarticle_by_cat' || substr($post['plugin_function'], 0, 26) == 'plgaup_readarticle_by_cat_' ){
			$post['categories'] = implode(',', $post['categories']);		
		}

		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$msg = JText::_( 'AUP_RULESAVED' );
		if (!$apply){
			$app->redirect('index.php?option=com_altauserpoints&task=rules', $msg);
		} else {
			$app->redirect('index.php?option=com_altauserpoints&task=editrule&cid[]='.$id, $msg);
		}
		//JControllerLegacy::redirect(); 				

	}
	
	function _copy_rule() {
		$app = JFactory::getApplication();
		
		// Initialize variables
		$db			=  JFactory::getDBO();
		$cid		=  JFactory::getApplication()->input->get( 'cid', array(), 'array' );
		
		JArrayHelper::toInteger($cid);
		
		$item	= null;
		
		$total = count($cid);
		$j = 0;
		for ($i = 0; $i < $total; $i ++)
		{			
			$row =  JTable::getInstance('rules');

			// main query
			$query = 'SELECT * FROM #__alpha_userpoints_rules' .
					' WHERE id = '.(int) $cid[$i];
			$db->setQuery($query, 0, 1);
			$item = $db->loadObject();
			
			$unique = uniqid('', false);
			
			if ( $item->blockcopy=='0' ) {

				// values loaded into array set for store
				$row->id						= NULL;
				$row->rule_name					= JText::_('AUP_COPYOF') . " " . JText::_( $item->rule_name );
				$row->rule_description			= "";
				$row->rule_plugin				= $item->rule_plugin;
				$row->plugin_function			= $item->plugin_function . '_' . $unique;
				$row->access					= $item->access;
				$row->component					= $item->component;
				$row->calltask					= $item->calltask;
				$row->taskid					= $item->taskid;
				$row->points					= $item->points;
				$row->points2					= $item->points2;
				$row->percentage				= $item->percentage;
				$row->rule_expire				= $item->rule_expire;
				$row->sections					= $item->sections;
				$row->categories				= $item->categories;
				$row->content_items				= $item->content_items;
				$row->exclude_items				= $item->exclude_items;
				$row->published					= 0;
				$row->system					= 0;
				$row->duplicate					= 1;
				$row->blockcopy					= 0;
				$row->autoapproved				= $item->autoapproved;
				$row->fixedpoints				= 1;
				$row->category					= $item->category;
				$row->displaymsg				= $item->displaymsg;
				$row->msg						= $item->msg;
				$row->method		 			= $item->method;
				$row->notification		 		= $item->notification;
				$row->emailsubject		 		= $item->emailsubject;
				$row->emailbody		 			= $item->emailbody;
				$row->emailformat		 		= $item->emailformat;
				$row->bcc2admin		 			= $item->bcc2admin;
				$row->type_expire_date 			= $item->type_expire_date;
				$row->chain 					= 0;
				$row->linkup 					= intval($item->linkup);
				
				
				if (!$row->store()) {
					JFactory::getApplication()->enqueueMessage(  $row->getError() ,'error');
					return false;
				}
				$j++;			
			} 
			
		}		
		
		$msg = JText::sprintf('AUP_XCOPYOFRULE', $j);		
		$app->redirect('index.php?option=com_altauserpoints&task=rules', $msg);
		//JControllerLegacy::redirect(); 	
	}
	
	/**
	* Set the access of selected rule
	*/
	function setAccess( $items, $access ) {

		$row =  JTable::getInstance('rules');		
		
		foreach ($items as $id)	{
			$row->load( $id );
			$row->access = $access;
	
			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				return false;
			}		
		}
		return true;
	}
	
	
	/**
	* Get chained rules list
	*/
	function _getChainedRulesList($currentrule=0)
	{
		$db			=  JFactory::getDBO();
		$query 		= "SELECT id, rule_name FROM #__alpha_userpoints_rules WHERE `chain`='1' AND `id`!='".$currentrule."' AND `published`='1'";
		$db->setQuery($query);
		$chainedrules = $db->loadObjectList();
		
		return $chainedrules;
	
	}

}
?>