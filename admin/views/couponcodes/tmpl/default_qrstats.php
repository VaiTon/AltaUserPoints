<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.formvalidation');
?>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="adminForm" class="form-validate">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( '#' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->couponcodes); ?>);" />
				</th>
				<th width="20%" class="title">
					<?php echo JText::_('AUP_DATE'); ?>
				</th>
				<th width="15%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_COUNTRY' ); ?>
				</th>
				<th width="15%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_CITY' ); ?>
				</th>
				<th class="title" >
					<?php echo JText::_('AUP_DEVICE'); ?>
				</th>
				<th width="20%" class="title" >
					<?php echo JText::_('AUP_IP_ADDRESS'); ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JText::_('AUP_CONFIRMED'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php		
			$db = JFactory::getDBO();
				
			$k = 0;
			
			$img = '<img src="components/com_altauserpoints/assets/images/tick.png" border="0" alt="'. JText::_('AUP_CONFIRMED') .'" />';
			
			for ($i=0, $n=count( $this->qrcodestats ); $i < $n; $i++)
			{
				$row 	= $this->qrcodestats[$i];
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php 
						echo JHTML::_('grid.id', $i, $row->id );						
					?>
				</td>
				<td>		
				<?php echo JHTML::_('date',  $row->trackdate,  JText::_('DATE_FORMAT_LC') ); ?>							
				</td>
				<td>
					<?php echo $row->country ; ?>
				</td>
				<td>
					<?php echo $row->city; ?>				
				</td>
				<td>		
					<?php echo $row->device; ?>							
				</td>
				<td>
					<?php echo $row->ip ; ?>	
				</td>
				<td>
					<div align="center">
					<?php					
					$confirmed = ( $row->confirmed ) ? $img : '';
					echo $confirmed; 					
					?>
					</div>						
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="qrcodestats" />
	<input type="hidden" name="table" value="alpha_userpoints_qrcodetrack" />
	<input type="hidden" name="redirect" value="couponcodes" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>