<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2015-2016. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import file dependencies if needed
if (!function_exists('getProfileLink')) {
	require_once (JPATH_ROOT.'/components/com_altauserpoints/helpers/helpers.php');
}

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$list = modAltaUserPointsMostActiveUsersHelper::getList($params);
require(JModuleHelper::getLayoutPath('mod_altauserpoints_mostactiveusers'));

?>