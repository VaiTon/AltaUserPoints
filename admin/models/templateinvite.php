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

class altauserpointsModelTemplateinvite extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _load_templateinvite()
	{
		$app = JFactory::getApplication();
		$db			    = JFactory::getDBO();
		$total 			= 0;
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->input->getInt('limitstart');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);
		$q = "SELECT * FROM #__alpha_userpoints_template_invite";
		$total = @$this->_getListCount($q);
		$result = $this->_getList($q, $limitstart, $limit);
		return array($result, $total, $limit, $limitstart);
	}	
	
	function _edit_templateinvite()
	{
		$app = JFactory::getApplication();
		$db     = JFactory::getDBO();
		$cid 	= $app->input->get('cid', array(0), 'array');
		$option = $app->input->get('option', '', 'cmd');
		if (!is_array( $cid ))
			$cid = array(0);
		$lists = array();
		$row = JTable::getInstance('template_invite');
		$row->load( $cid[0] );
		return $row;
	}	
	
	function _delete_templateinvite()
	{
		$app = JFactory::getApplication();
		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= $app->input->get('cid', array(), 'array');
		$msgType	= '';
		JArrayHelper::toInteger($cid);
		if (count($cid))
		{		
			$q = "DELETE FROM #__alpha_userpoints_template_invite"
					. "\n WHERE (`id` = " . implode(' OR `id` = ', $cid) . ")"
					;
			$db->setQuery($q);
			
			if (!$db->execute()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}
		$app->redirect('index.php?option=com_altauserpoints&task=templateinvite', $msg, $msgType);
	}
	
	function _save_templateinvite($apply=0)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$post = $app->input->getArray(array());	
		$raw_emailbody = $app->input->get('emailbody', '', 'raw' );
		
		$row = JTable::getInstance('template_invite');
		
		$post['emailbody']= $raw_emailbody;
		$id= $app->input->getInt( 'id' );
		
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$msg = JText::_( 'AUP_TEMPLATESAVED' );
		if (!$apply OR !$id ){
			$app->redirect('index.php?option=com_altauserpoints&task=templateinvite', $msg);
		} else {
			$app->redirect('index.php?option=com_altauserpoints&task=edittemplateinvite&cid[]='.$id, $msg);
		}		
	}
}
?>