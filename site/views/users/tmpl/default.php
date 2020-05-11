<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

$colspan = 2;
$user =  JFactory::getUser();

if ( $this->useAvatarFrom ) $colspan++;

?>
<script language="javascript" type="text/javascript">
	function tableOrdering( order, dir, task ) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}
</script>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace($this->action); ?>" method="post" name="adminForm" class="form-inline">
<?php if ($this->params->def('show_limit', 1)) { ?>
<fieldset class="filters btn-toolbar clearfix">
	<div class="btn-group pull-right">
<label for="limit" class="element-invisible"><?php echo JText::_('AUP_DISPLAY_NUM') .'&nbsp;'; ?></label>
<?php
echo $this->pagination->getLimitBox(); ?>
</div>
</fieldset>
<?php 
} 
?>
<div class="table-responsive">
	<table class="category table table-striped table-bordered table-hover">
		<thead>
			<?php if ( $this->params->def( 'show_headings', 1 ) ) { ?>
			<tr>
				<?php if ( $this->params->get( 'show_num_cols' ) ) { 
				$colspan++;
				?>			
				<th id="categorylist_header_num">
					<?php echo JText::_( 'AUP_NUM' ); ?>
				</th>
				<?php } ?>		
				<?php if ( $this->useAvatarFrom ) { ?>
				<th id="categorylist_header_avatar">
				<?php echo JText::_( 'AUP_AVATAR' ); ?>
				</th>
				<?php } ?>
				<?php if ( $this->params->get( 'show_rank_icon_cols' ) ) { 
				$colspan++;
				?>				
				<th id="categorylist_header_rank">
				<?php echo JText::_( 'AUP_RANK' ); ?>
				</th>
				<?php } ?>				
				<?php if ( $this->params->get( 'show_nummedals_cols' ) ) { 
				$colspan++;
				?>				
				<th id="categorylist_header_medals">
					<?php echo JText::_( 'AUP_MEDALS' ); ?>
				</th>
				<?php } ?>					
				<?php if ( $this->params->get( 'show_name_cols' ) ) { 
				$colspan++;
				?>				
				<th id="categorylist_header_name">					
					<a data-original-title="<strong><?php echo JText::_( 'AUP_NAME' ); ?></strong><br /><?php echo JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ); ?>" href="#" onclick="tableOrdering('usr_name','asc','');return false;" class="hasTooltip" title=""><?php echo JText::_( 'AUP_NAME' ); ?></a>
				</th>
				<?php } ?>
				<?php if ( $this->params->get( 'show_username_cols' ) ) { 
				$colspan++;
				?>
				<th id="categorylist_header_username">
					<a data-original-title="<strong><?php echo JText::_( 'AUP_USERNAME' ); ?></strong><br /><?php echo JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ); ?>" href="#" onclick="tableOrdering('usr_username','asc','');return false;" class="hasTooltip" title=""><?php echo JText::_( 'AUP_USERNAME' ); ?></a>
				</th>
				<?php } ?>
				<?php if ( $this->params->get( 'show_referreid_cols' ) ) { 
				$colspan++;
				?>				
				<th id="categorylist_header_referreid">
					<a data-original-title="<strong><?php echo JText::_( 'AUP_REFERREID' ); ?></strong><br /><?php echo JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ); ?>" href="#" onclick="tableOrdering('aup.referreid','asc','');return false;" class="hasTooltip" title=""><?php echo JText::_( 'AUP_REFERREID' ); ?></a>
				</th>
				<?php } ?>
				<th id="categorylist_header_points">
					<a data-original-title="<strong><?php echo JText::_( 'AUP_POINTS' ); ?></strong><br /><?php echo JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ); ?>" href="#" onclick="tableOrdering('aup.points','desc','');return false;" class="hasTooltip" title=""><?php echo JText::_( 'AUP_POINTS' ); ?></a>
				</th>				
				<?php if ( $this->params->get( 'show_referral_user_cols' ) ) { 
				$colspan++;
				?>
				<th id="categorylist_header_referraluser">
					<a data-original-title="<strong><?php echo JText::_( 'AUP_REFERRALUSER' ); ?></strong><br /><?php echo JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ); ?>" href="#" onclick="tableOrdering('aup.referraluser','asc','');return false;" class="hasTooltip" title=""><?php echo JText::_( 'AUP_REFERRALUSER' ); ?></a>
				</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php } ?>
		<?php
			require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
			
			$db = JFactory::getDBO();
			$nullDate 		= $db->getNullDate();

			for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
			{
				$row 	=& $this->rows[$i];
				
				$_user_info   = AltaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
				
				$pathicon = JURI::root() . 'components/com_altauserpoints/assets/images/awards/icons/';
				$icone = "";
				$nummedals = "";
				
				if ( $row->levelrank && $this->params->get( 'show_rank_icon_cols' ) ) {
					
					$q = "SELECT icon FROM #__alpha_userpoints_levelrank WHERE id=".$db->quote($row->levelrank);
					$db->setQuery( $q );
					$icon = $db->loadResult();
					if ( $icon ) {
						$icone = '<img src="'.$pathicon . $icon .'" width="16" height="16" border="0" style="vertical-align:middle" alt="" /> ';
					}
				}
				
				if ( $this->params->get( 'show_nummedals_cols' ) ) {
				
					$q = "SELECT COUNT(*) FROM #__alpha_userpoints_medals WHERE rid=".$db->quote($row->rid);
					$db->setQuery( $q );
					$medals = $db->loadResult();
					if ( $medals ) {
						$nummedals = $medals;
					}
				
				} 			
			?>
			<tr class="cat-list-row<?php echo $i % 2; ?>">
				<?php if ( $this->params->get( 'show_num_cols' ) ) { ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo $i+1+$this->pagination->limitstart; ?>
				</td>
				<?php } ?>
				<?php
					// load avatar if need
					if ( $this->useAvatarFrom ) {
						$avatar = getAvatar( $this->useAvatarFrom, $_user_info, $this->params->get( 'heightAvatar', '48' ) );							
						// add link to profil if need
						$startprofil = '<a href="#" >';
						$endprofil  = '</a>';
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$startprofil =  '<a href="' . $linktoprofil . '" >';
							$endprofil   = '</a>';
						}					
						echo '<td headers="categorylist_header_title" class="list-title">'
						.$startprofil
						.$avatar
						.$endprofil
						.'</td>';
				}			
				?>				
				<?php if ( $this->params->get( 'show_rank_icon_cols' ) ) { ?>		
				<td headers="categorylist_header_title" class="list-title">
					<?php echo $icone ; ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_nummedals_cols' ) ) { ?>		
				<td headers="categorylist_header_title" class="list-title">
				 	<?php if ($nummedals) { ?>
					<span class="badge badge-info"><?php echo $nummedals ; ?></span>
					<?php } ?>	
				</td>
				<?php } ?>			
				<?php if ( $this->params->get( 'show_name_cols' ) ) { ?>		
				<td headers="categorylist_header_title" class="list-title">
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->usr_name . "</a>";
					} else $profil = $row->usr_name ;
					echo $profil;			 
					 ?>
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_username_cols' ) ) { ?>
					<td headers="categorylist_header_title" class="list-title">				
						<?php
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$profil =  "<a href=\"" . $linktoprofil . "\">" . $row->usr_username . "</a>";
						} else $profil = $row->usr_username ;						
						 echo $profil;				 
						 ?>			
					</td>
				<?php } ?>
				<?php if ( $this->params->get( 'show_referreid_cols' ) ) { ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo $row->referreid; ?>
				</td>
				<?php } ?>
				<td headers="categorylist_header_title" class="list-title">	
					<span class="badge badge-info"><?php echo getFormattedPoints( $row->points ); ?></span>
				</td>
				<?php if ( $this->params->get( 'show_referral_user_cols' ) ) { ?>
				<td headers="categorylist_header_title" class="list-title">		
					<?php echo $row->referraluser; ?>
				</td>
				<?php } ?>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>	
	<div class="pagination">
	<?php 
	echo $this->pagination->getPagesCounter() . "<br />" ;
	echo $this->pagination->getPagesLinks(); 
	?>
	</div>
	<input type="hidden" name="filter_order" value="<?php echo @$this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->lists['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="users" />
</form>
<?php
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
