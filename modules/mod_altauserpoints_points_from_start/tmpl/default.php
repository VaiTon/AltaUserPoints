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

$f = $params->get('format', '1');
$show = $params->get('show', '3');

if ( $points[0] && ($show=='1' || $show=='3') ) 
{		
	echo "<p>".JText::_('MODAUP_PFS_EARNED_POINTS')."</p>";	
	echo "<h".$f.">".getFormattedPoints( $points[0] )."</h".$f.">"; 	
}

if ( $points[1] && ($show=='2' || $show=='3')  ) 
{	
	echo "<p>".JText::_('MODAUP_PFS_SPENT_POINTS')."</p>";	
	echo "<h".$f.">".getFormattedPoints( $points[1] )."</h".$f.">"; 	
}

?>