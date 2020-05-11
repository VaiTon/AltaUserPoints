<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Access check.
$app = JFactory::getApplication();
if (!JFactory::getUser()->authorise('core.manage', 'com_altauserpoints')) {
	return $app->enqueueMessage(  JText::_('JERROR_ALERTNOAUTHOR'),'warning');
}

// include version
require_once (JPATH_COMPONENT.'/assets/includes/version.php');
require_once (JPATH_COMPONENT.'/assets/includes/functions.php');
require_once (JPATH_COMPONENT.'/assets/includes/pane.php');



// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

// Create the controller
$controller = new altauserpointsController();

// Perform the Request task
$controller->execute( $app->input->get( 'task', 'cpanel', 'cmd' ) );
$controller->redirect();

aup_CopySite();

?>