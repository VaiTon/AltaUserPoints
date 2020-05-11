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
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || task == 'cancelusers??? details???' || document.formvalidator.isValid(document.id('users-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
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
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->userDetails ); $i < $n; $i++)
			{
				$row 	=& $this->userDetails[$i];

				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
					$link 	= 'index.php?option=com_altauserpoints&amp;task=edituserdetails&amp;cid[]='. $row->id. '&amp;name=' . $this->name . '';
				} else $link 	= '';
				
				$db = JFactory::getDBO();
				
				$prefix = "";

				$nullDate 		= $db->getNullDate();
				
				$imgA 	 = $row->approved ? 'icon-16-add.png' :'publish_x.png';
				$taskA 	 = $row->approved ? 'unapprove' : 'approve';
				$altA	 = $row->approved ? JText::_( 'AUP_APPROVE' ) : JText::_( 'AUP_NOTAPPROVE' );
				$actionA = $row->approved ? JText::_( 'AUP_NOTAPPROVE' ) : JText::_( 'AUP_APPROVE' );
		
				$approved = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$taskA .'\')" title="'. $actionA .'">				
				<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/'. $imgA .'" border="0" alt="'. $altA .'" /></a>'
				;
			
				if ( $row->status ) {
					// already approved !
					$approved = "<img src=\"".JURI::base(true)."/components/com_altauserpoints/assets/images/icon-16-allowinactive.png\" border=\"0\" title=\"".JText::_('AUP_ALREADY_APPROVED')."\" alt=\"".JText::_('AUP_ALREADY_APPROVED')."\" /></a>";
				}
				
				$style  = ( $row->points < 0 ) ? 'style="color:red;"' : '' ;
				
			?>
			<tr class="<?php echo "row$k"; ?>" <?php echo "$style"; ?>>
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td>
				<a href="<?php echo $link; ?>">
				<i class="icon-calendar"></i>
					<?php 
					if ( $row->insert_date == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date', $row->insert_date,  JText::_('DATE_FORMAT_LC2') );
					}
					?>
				</a>
				</td>				
				<td>
					<?php echo JText::_( $row->rule_name ); ?>					
				</td>
				<td align="center">
					<?php echo getFormattedPointsAdm( $row->points ); ?>
				</td>
				<td align="center">
				<i class="icon-calendar"></i>
					<?php 
					if ( $row->expire_date == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date', $row->expire_date,  JText::_('DATE_FORMAT_LC2') );
					}
					?>
				</td>
				<td align="center">
					<?php echo $approved; ?>
				</td>
				<td>				
					<?php echo $row->datareference; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="showdetails" />
	<input type="hidden" name="c2id" value="<?php echo $row->referreid; ?>" />
	<input type="hidden" name="name" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="table" value="alpha_userpoints_details" />
	<input type="hidden" name="redirect" value="showdetails&cid=<?php echo $row->referreid; ?>&name=<?php echo $this->name; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>
<!-- Modal Form Custom Points-->
<div class="modal hide fade" id="generatorModal" data-toggle="modal">
	<form action="index.php" method="post" name="adminFormCustomPoints" id="adminFormCustomPoints" autocomplete="off" style="margin:0;">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">&times;</button>
			<h3><?php echo JText::_('AUP_CUSTOM_POINTS');?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<fieldset class="adminform">
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_CUSTOM_POINTS_DESCRIPTION'); ?>">
								<?php echo JText::_( 'AUP_POINTS' ); ?>:
							</span>
						</div>
						<div class="controls">							
							<input class="inputbox" type="text" name="points" id="points" size="20" maxlength="255" value="" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>::<?php echo JText::_('AUP_DESCRIPTION'); ?>">
								<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
							</span>
						</div>
						<div class="controls">							
							<input class="inputbox" type="text" name="reason" id="reason" size="80" maxlength="255" value="" />
						</div>
					</div>
				</fieldset>
				<input type="hidden" name="option" value="com_altauserpoints" />
				<input type="hidden" name="task" value="savecustompoints" />
				<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
				<input type="hidden" name="name" value="<?php echo $this->name; ?>" />
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" type="button" data-dismiss="modal">
				<?php echo JText::_( 'Cancel' ); ?>
			</button>
			<button class="btn btn-primary" type="submit" onclick="Joomla.submitform('savecustompoints', this.form);">
				<?php 				
					echo JText::_( 'Save' ); 
				?>
			</button>
		</div>
	</form>
</div>