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
$db	   		= JFactory::getDBO();
$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
$user 		= JFactory::getUser();
$show_avatar = $params->get('showavatar', 0);

if ( $list ) {

	if (!class_exists('AltaUserPointsHelper')) {
		require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
	}

	$com_params 	= JComponentHelper::getParams( 'com_altauserpoints' );
	$useAvatarFrom 	= $com_params->get('useAvatarFrom');
		
?>
<table class="table table-striped table-hover table-condensed">
	<thead>
    	<tr>
        	<th><?php echo JText::_('MODAUP_RANK'); ?></th>
            <?php		
				if( $show_avatar ){ 
				?>
				<th ></th>
				<?php } ?>
            <th><?php echo JText::_('MODAUP_USER'); ?></th>
            <th><?php echo JText::_('MODAUP_POINTS'); ?></th>
        </tr>
    </thead>
    <tbody>
	<?php
	$i = 1;

	foreach ($list as $item) { 
	
		$usrname = htmlspecialchars($item->usrname, ENT_QUOTES, 'UTF-8');
		$userinfo = AltaUserPointsHelper::getUserInfo($item->referreid) ;
		$profil = getProfileLink( $com_params->get('linkToProfile', ''), $item );		
		
		?>
		<tr>
        	<td>
            	<?php echo '<div class="badge badge-info">#'.$i.'</div>'; ?>
            </td>
		
		<?php		
		if( $show_avatar ){ 
		?>
				<td><div style="width:32px;padding:2px;border:solid 1px #ccc;">
				<?php
				echo getAvatar( $useAvatarFrom, $userinfo, 32 );
				?>
				</div>
                </td>			   
		<?php } ?>
        <td>	
				<?php
				if ( strpos($profil, 'com_users')==false
					&& ( $user->id || ( !$user->id 
						&& $com_params->get( 'allowGuestUserViewProfil', 1) )
					 ))
				{
						
					echo '<a href="'.$profil.'">'. $usrname . '</a>';
				}
				else {
					echo $usrname;
				}			 
				 ?>

         </td>
         <td><?php echo getFormattedPoints( $item->points );?></td>
		</tr>		
		<?php
		$i++;		
		}	 
	?>
    </tbody>
	</table>
	<?php
} 
?>