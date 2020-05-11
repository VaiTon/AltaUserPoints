<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

$user =  JFactory::getUser();
?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<?php
if ( $this->latestactivity ) {
	require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
?>
	<table class="category table table-striped table-bordered table-hover">
	<tbody>
<?php 
		$i=0;
		foreach ($this->latestactivity as $activity) {		
			$_user_info = AltaUserPointsHelper::getUserInfo ( $activity->referreid );
			$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
			?>
		<tr class="cat-list-row<?php echo $i % 2; ?>">			
			<?php			
			if ( $this->useAvatarFrom && $this->params->get( 'showAvatar')) {				
				$avatar = getAvatar( $this->useAvatarFrom, $_user_info, $this->params->get( 'heightAvatar', '48' ) );							
				// add link to profil if need
				$startprofil = '<a href="#" class="thumbnail">';
				$endprofil   = '</a>';					
				if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
					$startprofil =  '<a href="' . $linktoprofil . '" class="thumbnail">';
					$endprofil   =  '</a>';
				}
				?>
				<td headers="categorylist_header_title" class="list-title">
				<?php		
				echo $startprofil.$avatar.$endprofil;
				?></td>
				<?php				
			}			
			?>
				<td headers="categorylist_header_title" class="list-title">
						<b>
						<?php
						// insert icon category if exist
						if ( $this->params->get( 'showIconCategory', 1) && $activity->category!='' ) {
							echo _getIconCategoryRule( $activity->category ) . '&nbsp;' ;
						}
						echo JText::_( $activity->rule_name);						
						?>
						</b>
						<br />
						<?php
						switch ( $activity->plugin_function ) {
							case 'sysplgaup_dailylogin':
								echo JHTML::_('date', $activity->datareference, JText::_('DATE_FORMAT_LC1') );
								break;
							case 'plgaup_getcouponcode_vm':
							case 'plgaup_alphagetcouponcode_vm':
							case 'sysplgaup_buypointswithpaypal':
								echo  ''; 
								break;
							default:
								echo $activity->datareference;
						}									
						?>
						<br /><b>
						<?php					
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$profil =  "<a href=\"" . $linktoprofil . "\">" . $activity->usrname . "</a>";
						} else $profil = $activity->usrname ;
						echo $profil;			 
						 ?>
						</b>
						<span>
						<?php echo nicetime($activity->insert_date); ?>
						</span>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<?php
						$points_color = '';
						if ($activity->last_points >0)
						$points_color = 'badge badge-info';
						if ($activity->last_points<0)
						$points_color = 'badge badge-warning';
						if ($activity->last_points==0)
						$points_color = 'badge';
					?>
						<span class="<?php echo $points_color ; ?>">
						<?php 
						$s = '';					
						if($activity->last_points > 1)
							$s = 'S';
						
						echo getFormattedPoints( $activity->last_points ) . " " . JText::_( 'AUP_POINT'.$s ); ?>
                        
                        </span>
				</td>
			</tr>
		<?php			
			$i++;
		} // end foreach		
		?>
			</tbody>
		</table>
		<?php		
		echo '<div class="pagination">';		
		echo $this->pagination->getPagesCounter().'<br />';
		echo $this->pagination->getPagesLinks();
		echo '</div>';
} // end if latest activities

	/** 
	*
	*  Provide copyright on frontend
	*  If you remove or hide this line below,
	*  please make a donation if you find AltaUserPoints useful
	*  and want to support its continued development.
	*  Your donations help by hardware, hosting services and other expenses that come up as we develop,
	*  protect and promote AltaUserPoints and other free components.
	*  You can donate on https://www.nordmograph.com/extensions
	*
	*/	
	getCopyrightNotice ();
?>