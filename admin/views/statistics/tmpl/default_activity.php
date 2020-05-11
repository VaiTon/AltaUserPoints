<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$row = $this->row;
?>
<div id="j-main-container">
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<!--<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>-->
				<th width="20%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_DATE' ); ?>
				</th>
				<th width="20%" class="title">
					<?php echo JText::_('AUP_RULE'); ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JText::_('AUP_POINTS'); ?>
				</th>
				<th width="20%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_EXPIRE' ); ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JText::_('AUP_APPROVED'); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_DATA' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php //echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->userDetails ); $i < $n; $i++)
			{
				$row2 	= $this->userDetails[$i];
				
				$link 	= 'index.php?option=com_altauserpoints&amp;task=edituserdetails&amp;cid[]='. $row2->id. '&amp;name=' . $row->name . '';
	
				$db = JFactory::getDBO();
				
				$prefix = "";

				$nullDate 		= $db->getNullDate();
				
				$imgA 	 = $row2->approved ? 'icon-16-add.png' :'publish_x.png';
				$taskA 	 = $row2->approved ? 'unapprove' : 'approve';
				$altA	 = $row2->approved ? JText::_( 'AUP_APPROVE' ) : JText::_( 'AUP_NOTAPPROVE' );
				$actionA = $row2->approved ? JText::_( 'AUP_NOTAPPROVE' ) : JText::_( 'AUP_APPROVE' );
		
				$approved = '<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/'. $imgA .'" border="0" alt="'. $altA .'" />'
				;
			
				if ( $row2->status ) {
					// already approved !
					$approved = "<img src=\"".JURI::base(true)."/components/com_altauserpoints/assets/images/icon-16-allowinactive.png\" border=\"0\" title=\"".JText::_('AUP_ALREADY_APPROVED')."\" alt=\"".JText::_('AUP_ALREADY_APPROVED')."\" /></a>";
				}
				
				$style  = ( $row2->points < 0 ) ? 'style="color:red;"' : '' ;
				
			?>
			<tr class="<?php echo "row$k"; ?>" <?php echo "$style"; ?>>
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<!--<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row2->id ); ?>
				</td>-->
				<td>				
				<i class="icon-calendar"></i>
					<?php 
					if ( $row2->insert_date == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date', $row2->insert_date,  JText::_('DATE_FORMAT_LC2') );
					}
					?>				
				</td>				
				<td>
					<?php echo JText::_( $row2->rule_name ); ?>					
				</td>
				<td align="center">
					<?php echo getFormattedPointsAdm( $row2->points ); ?>
				</td>
				<td align="center">
				<i class="icon-calendar"></i>
					<?php 
					if ( $row2->expire_date == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date', $row2->expire_date,  JText::_('DATE_FORMAT_LC2') );
					}
					?>
				</td>
				<td align="center">
					<?php echo $approved; ?>
				</td>
				<td>				
					<?php echo $row2->datareference; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="edituser" />
	<input type="hidden" name="cid" value="<?php echo $row2->id; ?>" />
	<input type="hidden" name="c2id" value="<?php echo $row2->referreid; ?>" />
	<input type="hidden" name="name" value="<?php echo $row->name; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<a href="index.php?option=com_altauserpoints&task=showdetails&cid=<?php echo $row->referreid; ?>&name=<?php echo $row->name; ?>"><?php echo JText::_('AUP_SHOW_DETAIL_ACTIVITIES'); ?></a>
</div>