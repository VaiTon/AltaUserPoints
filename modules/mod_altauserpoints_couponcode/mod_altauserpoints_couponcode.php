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
// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');
$jinput = JFactory::getApplication()->input;
if ( $jinput->post->getString('modAUP_CPsCouponValue' ) ) {
	$coupon = trim( $jinput->post->getString('modAUP_CPsCouponValue'));
	if ( $coupon ) modAltaUserPointsCouponCodeHelper::checkcoupon($params, $coupon);
}
require(JModuleHelper::getLayoutPath('mod_altauserpoints_couponcode'));

?>