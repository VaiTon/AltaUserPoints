<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import file dependencies
require_once JPATH_COMPONENT.'/helpers/helpers.php';
// Require the base controller
require_once JPATH_COMPONENT.'/controller.php';

$doc = JFactory::getDocument();
$direction = $doc->direction;
// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $direction);

$jinput = JFactory::getApplication()->input;

// Require specific controller if requested
if ( $jinput->get('task', 'display', 'cmd')=='showRSSAUPActivity')
{
	$jinput->input->set( 'view', 'rssactivity');
}

if( $controller = $jinput->getCmd('controller', ''))
{
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path))
		require_once $path;
}
else
{
	$controller = $jinput->getCmd( 'view', '' );
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path))
		require_once $path;	
} 

// Create the controller
$classname	= 'AltauserpointsController'.ucfirst($controller);
$controller	= new $classname( );

// Perform the Request task
$controller->execute( $jinput->getCmd( 'task', 'display' ) );
$controller->redirect();
?>