<?php
/*
 * @component AltaUserPoints, Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel, https://www.nordmograph.com/extensions
 * @copyright Copyright (C) 2011 Mike Gusev (migus) - Updated by Bernard Gilly - Adrien Roussel for full compatibility Joomla 3.1.x on June 2013
 * @license : GNU/GPL
 * @Website : http://migusbox.com
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<div class="table-responsive">
	<table class="category table table-striped table-bordered table-hover">
	<thead>
			<tr>
<?php if ( $this->params->get( 'show_caticon', 1 ) ) { ?>
				<th id="caticon_header_title">
				</th>
<?php } ?>
				<th id="rulename_header_title">
					<?php echo JText::_('AUP_RULE_NAME'); ?>
				</th>
<?php if ( $this->params->get( 'show_desc', 1 ) ) { ?>
				<th id="ruledesc_header_title">
					<?php echo JText::_( 'AUP_RULE_DESC' ); ?>
				</th>
<?php } ?>
				<th id="points_header_title">
					<?php echo JText::_( 'AUP_POINTS' ); ?>
				</th>
<!--
<?php	if ( $this->params->get( 'show_published', 1 ) ) {?>
				<th id="published_header_title">
					<?php echo JText::_( 'AUP_RULEACTIVITY' ); ?>
				</th>
<?php } ?>
-->
<?php	if ( $this->params->get( 'show_approve', 1 ) ) {?>
		<th id="approve_header_title">
					<?php echo JText::_( 'AUP_RULEAPPROVAL' ); ?>
		</th>
<?php } ?>
		</tr>
	</thead>
		<tbody>
		<?php 

			for ($i=0, $n=count( $this->rules ); $i < $n; $i++)			
			{
				$row 	=& $this->rules[$i];			

			?>
			<tr class="cat-list-row<?php echo $i % 2; ?>" >
			
<?php if ( $this->params->get( 'show_caticon', 1 ) ) { ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo _getIconCategoryRule( $row->category ); ?>
				</td>
<?php } ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo JText::_( $row->rule_name ); ?>
				</td>
<?php if ( $this->params->get( 'show_desc', 1 ) ) { ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo JText::_( $row->rule_description ); ?>
				</td>
<?php } ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php					
					if ( $row->points || $row->points2 ) {				
						$points = $row->points;
						$points2 = $row->points2;
						if ($row->points >0|| $row->points2 >0)
						$points_color = 'badge badge-info';
						if ($row->points <0 || $row->points2 <0)
						$points_color = 'badge badge-warning';
						if ($row->points==0 && $row->points2==0)
						$points_color = 'badge';
					?>
					<span class="<?php echo $points_color ; ?>">
					<?php switch ( $row->plugin_function ) {
						case 'sysplgaup_referralpoints':
							echo getFormattedPoints( $points ) . " %";
							break;
						case 'sysplgaup_unlockmenus':
						case (substr($row->plugin_function, 0, 21) == 'sysplgaup_unlockmenus'):
							echo getFormattedPoints( $points2 );
							break;	
						default:
							echo getFormattedPoints( $points ); ?>
					<?php } ?>
					</span>
					<?php
					} else 	echo '-';
					?>
					<?php if( !$row->published ) { ?>
					<span class="badge badge-warning">
					<?php echo JText::_( 'AUP_RULEINACTIVE' ); ?>
					</span>
					<?php } ?>
				</td>
<!--
<?php	if ( $this->params->get( 'show_published', 1 ) ) {?>
				<td headers="categorylist_header_title" class="list-title">
					<?php					
						$icon = ( $row->published )? 'tick.png' : 'publish_x.png' ;
						$alt = ( $row->published )? 'AUP_RULEACTIVE' : 'AUP_RULEINACTIVE' ;	 
					?>
					<img src="components/com_altauserpoints/assets/images/<?php echo $icon; ?>" border="0" title="<?php echo JText::_( $alt ); ?>" alt="<?php echo JText::_( $alt ); ?>" />
				</td>
<?php } ?>
-->
<?php	if ( $this->params->get( 'show_approve', 1 ) ) {?>
				<td headers="categorylist_header_title" class="list-title">
					<?php
						switch ( $row->plugin_function ) {
						case 'sysplgaup_excludeusers':
						case 'sysplgaup_emailnotification':
						case 'sysplgaup_winnernotification':
						case 'sysplgaup_changelevel1':		
						case 'sysplgaup_changelevel2':		
						case 'sysplgaup_changelevel3':										
						case 'sysplgaup_archive':		
							$imgA =  'publish_y.png';
							$altA = JText::_('AUP_NOTAPPROVEDRULE');
							break;						
						default:
							$imgA = $row->autoapproved ? 'publish_g.png' :'publish_r.png';
							$altA = $row->autoapproved ? JText::_( 'AUP_AUTOAPPROVEDRULE' ) : JText::_( 'AUP_ADMINAPPROVEDRULE' );
						}
						?>
						<img src="components/com_altauserpoints/assets/images/<?php echo $imgA; ?>" border="0" title="<?php echo JText::_( $altA ); ?>" alt="<?php echo JText::_( $altA ); ?>" />
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