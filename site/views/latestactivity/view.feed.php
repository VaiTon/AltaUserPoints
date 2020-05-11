<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');
/**
 * RSS View class for the HelloWorld Component
 */
class AltauserpointsViewLatestactivity extends JViewLegacy
{ 
    public function display($tpl = null)
    {
        $app	= JFactory::getApplication();
        $doc    = JFactory::getDocument();
		
		$app 	= JFactory::getApplication();
		$menus 	= $app->getMenu();
		$menu   = $menus->getActive();
		$menuid = $menu->id;
		$params = $menus->getParams($menuid);
		

     	$activity_type 	= $params->get('activity','1');
	 	$usrname 		= $params->get('usrname','username');
	  	$app->input->set('limit', $params->get('count','20') );
        
        $siteEmail        = $app->get('mailfrom');
		$fromName         = $app->get('fromname');
		$feedEmail        = $app->get('feed_email', 'author');
		$doc->editor 		= $fromName;
		

		if ($feedEmail != "none")
		{
			$doc->editorEmail = $siteEmail;
		}
        // Get some data from the model
        $act = $this->get('LatestActivity');
        foreach ($act[0] as $item)
        {
            $date        = ($item->insert_date ? date('r', strtotime($item->insert_date)) : '');
            // Load individual item creator class
            $feeditem              = new JFeedItem;
            $feeditem->title       = $item->usrname;
            $feeditem->link        = '';
            $feeditem->description = '<![CDATA['.JText::_($item->rule_name).' / '.$item->last_points.']]>';
            $feeditem->date        = $date;
          
            $item->authorEmail = $siteEmail;
            
            // Loads item info into RSS array
			$doc->addItem($feeditem);
        }
    }
}