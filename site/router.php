<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
function AltaUserPointsBuildRoute(&$query) 
{
	$segments = array();
	$db		  =  JFactory::getDBO();
	if(isset($query['view']))
	{
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		if($query['view'] == 'medals' ) {
			$segments[] = $query['view'];
		}
		if($query['view'] == 'account' ) {
			$segments[] = 'profile';
		}
		unset($query['view']);
	}
	
	if(isset($query['task']))
	{
		if($query['task'] == 'detailsmedal') {
			$segments[] = $query['task'];
			unset($query['task']);
			if(isset($query['cid']))
			{
				$q = "SELECT `rank` FROM `#__alpha_userpoints_levelrank`
						WHERE `id` = " . $db->quote($query['cid']) . " LIMIT 1";
				$db->setQuery($q);				
				$segments[] = urlencode($db->loadResult());
				unset($query['cid']);
			}
		}
		elseif ( $query['task'] == 'downloadactivity' )
		{
			$segments[] = $query['task'];
			unset($query['task']);
			if(isset($query['userid']))
			{
				$segments[] = $query['userid'];
				unset($query['userid']);
			}	
		}
	}
	if(isset($query['userid']))
	{			
		$q = "SELECT u.username " .
		"FROM #__users AS u, #__alpha_userpoints AS a " .
		"WHERE a.referreid=".$db->quote($query['userid'])." AND a.userid=u.id LIMIT 1";
		$db->setQuery($q);				
		$segments[] = urlencode($db->loadResult());		
		unset($query['userid']);
	}
	return $segments;
}
function AltaUserPointsParseRoute($segments)
{
	$vars = array();
	$db	=  JFactory::getDBO();
	$count = count($segments);	
	if ( $count )
	{
		if($segments[0] == 'profile')
		{
			$vars['view'] = 'account';
			$q = "SELECT a.referreid " .
			"FROM #__alpha_userpoints AS a, #__users AS u " .
			"WHERE u.username='".urldecode($segments[$count-1])."' AND a.userid=u.id LIMIT 1";
			$db->setQuery($q);
			$vars['userid'] = $db->loadResult();
			if ( !empty($segments[$count-2]) ) {
			  $vars['task'] = $segments[$count-2];	   
			  if($vars['task'] == 'downloadactivity') {
				  $vars['userid'] = $segments[$count-1];
			  }
			} 	
			return $vars;
		}
		if($segments[0] == 'medals')
		{
			$vars['view'] = 'medals';
			$vars['task'] = $segments[$count-2];
			$segments[$count-1] = str_replace( '.html', '', $segments[$count-1] );
			$q = "SELECT `id` FROM `#__alpha_userpoints_levelrank` 
			WHERE `rank`='" . urldecode($segments[$count-1]) . "' LIMIT 1";		
			$db->setQuery($q);			
			$vars['cid'] = $db->loadResult();			
			return $vars;
		}	
	}
}
?>