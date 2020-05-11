<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$columns = 7;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cancelrule' || document.formvalidator.isValid(document.id('listinglevelranks-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="adminForm" class="form-validate">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="5%" class="title">&nbsp;
					
				</th>
				<th width="12%" class="title">
					<?php echo JText::_('AUP_NAME'); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_USERNAME' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_('AUP_DATE'); ?>
				</th>
				<th width="50%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_REASON_FOR_AWARD' ); ?>
				</th>
				<th class="title" nowrap="nowrap">&nbsp;
										
				</th>		
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $columns ; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			
			for ($i=0, $n=count( $this->detailrank ); $i < $n; $i++)			
			{
				$row 	=& $this->detailrank[$i];
				
				if ($row->icon ) {
					$pathicon = JURI::root() . 'components/com_altauserpoints/assets/images/awards/icons/';
					$icone = '<img src="'.$pathicon . $row->icon.'" width="16" height="16" border="0" alt="" />';
				} else $icone = '';
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td>
					<div align="center">
					<?php echo $icone; ?>
					</div>
				</td>
				<td>					
					<?php echo JText::_( $row->name ); ?>
				</td>
				<td>
					<?php echo JText::_( $row->username ); ?>
				</td>
				<td>
					<div align="center">
					<?php 
					echo JHTML::_('date',  $row->dateawarded,  JText::_('DATE_FORMAT_LC') );
					?>
					</div>
				</td>
				<td>					
					<?php 
					echo JText::_( $row->rank );
					if ( $row->reason ) echo ' - ' . JText::_( $row->reason ); 
					?>
				</td>
				<td>&nbsp;
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="detailrank" />
	<input type="hidden" name="cid" value="<?php echo $row->cid ; ?>" />
	<input type="hidden" name="typerank" value="<?php echo $row->typerank ; ?>" />	
	<input type="hidden" name="table" value="" />
	<input type="hidden" name="redirect" value="detailrank" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>