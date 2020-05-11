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

$document	= JFactory::getDocument();
$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
$user = JFactory::getUser();

if ( $list ) {

	if (!function_exists('getAvatar')) {
		require_once (JPATH_SITE.'/components/com_altauserpoints/helpers/helpers.php');
	}
	
	$com_params 	= JComponentHelper::getParams( 'com_altauserpoints' );
	$useAvatarFrom 	= $com_params->get('useAvatarFrom');
		
?>
<div>
<ul style="padding:0;margin:0;list-style: none;">
	<?php
	$i = 1;
	foreach ($list as $item) { 
	
		$usrname = htmlspecialchars($item->usrname, ENT_QUOTES, 'UTF-8');
		
		$userinfo = AltaUserPointsHelper::getUserInfo($item->referreid) ;
		
		$profil = getProfileLink( $com_params->get('linkToProfile', ''), $item );		
		
		?>
		<li style="background:none;padding:5px 0;border-bottom:solid 1px #ccc;list-style: none;">
		<?php		
		if( $params->get('showavatar', 0) ){ 
		?>
			<div style="float:left;width:38px;">						  
				<div style="padding:2px;border:solid 1px #ccc;">
				<?php
				echo getAvatar( $useAvatarFrom, $userinfo, 32 );
				?>						
				</div>	
			</div>					   
		<?php
			$margin = 42;
		} else	$margin = 10;
	?>		
			<div style="margin-left:<?php echo $margin; ?>px;margin-top:3px;">
				<?php
				if ( $user->id || !$user->id && $com_params->get( 'allowGuestUserViewProfil', 1) ){
					echo '<a href="'.$profil.'">'. $usrname . '</a>';
				} else echo $usrname;				 
				 ?>
				<div class="small">
				<?php 
				echo getFormattedPoints( $item->points ) . ' ' . JText::_('MODAUP_POINTS') ;
				?>
				</div>		
			</div>
		</li>		
		<?php
		$i++;
		}	 
	?>	
	</ul><div style="clear: both;">&nbsp;</div></div>
	<?php
} else {

	echo JText::_('MODAUP_NO_MEMBER_ONLINE');
	
}
?>