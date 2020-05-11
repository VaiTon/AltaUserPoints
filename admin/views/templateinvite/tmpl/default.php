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
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('couponcodes-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="adminForm" class="form-validate">
<!--<div id="filter-bar" class="btn-toolbar">-->
	<!--<table>
		<tr>
			<td align="left" width="100%">&nbsp;
				<?php //echo JText::_( 'Filter' ); ?>
				<input type="text" name="search" id="search" value="<?php //echo @$this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap" align="right">
				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php //echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php //echo JHtml::_('select.options', JHtml::_('category.options', 'com_altauserpoints'), 'value', 'text', $this->lists['filter_category_id']);?>
				</select>
				<?php
				//echo $this->lists['filter_state'];
				?>
			</td>
		</tr>
	</table>-->
<!--</div>-->
	<div class="clearfix"> </div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="15%" class="title">
					<?php echo JText::_('AUP_NAME'); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_SUBJECT' ); ?>
				</th>
				<th width="8%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_FORMAT' ); ?>
				</th>
				<th width="8%"class="title" >
					<?php echo JText::_('AUP_SEND_COPY_ADMIN'); ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JText::_('AUP_PUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php		
			$db = JFactory::getDBO();
				
			$k = 0;
			
			
			
			for ($i=0, $n=count( $this->templateinvite ); $i < $n; $i++)
			{
				$row 	= $this->templateinvite[$i];
				
				$link 	= 'index.php?option=com_altauserpoints&amp;task=edittemplateinvite&amp;cid[]='. $row->id. '';
				//$img 	= $row->published ? 'publish_x.png' : 'tick.png';
				$published 	= JHTML::_('grid.published', $row, $i );				
				$format = $row->emailformat ? JText::_( 'AUP_HTML' ) : JText::_( 'AUP_PLAIN-TEXT' );
				$bcc2admin = $row->bcc2admin ? JText::_( 'JYes' ) : JText::_( 'AUP_NO' );
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
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link; ?>">	
				<?php } ?>			
					<?php echo $row->template_name; ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					</a>
				<?php } ?>		
				</td>
				<td>
					<?php echo $row->emailsubject; ?>
				</td>
				<td>
					<?php echo $format; ?>
				</td>				
				<td>
					<?php echo $bcc2admin; ?>
				</td>
				<td>
					<?php echo $published; ?>
				</td>	
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="templateinvite" />
	<input type="hidden" name="table" value="alpha_userpoints_template_invite" />
	<input type="hidden" name="redirect" value="templateinvite" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>