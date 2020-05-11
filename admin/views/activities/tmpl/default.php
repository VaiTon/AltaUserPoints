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
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('activities-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<script language="javascript" type="text/javascript">
function tableOrder(order, dir, task)
{
		if (dir=='desc')
		{
			dirn='asc';
		} 
		else
		{
			dirn='desc';
		}		
		Joomla.tableOrdering(order, dirn, task);
}
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;
 
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints&view=activities" method="post" name="adminForm" id="adminForm" class="form-validate">
<div id="filter-bar" class="btn-toolbar">
		<div class="input-prepend input-append pull-left">
			<span class="add-on hasTip" title="<?php echo JText::_( 'JGLOBAL_FILTER_FIELD_LABEL' ); ?>"><i class="icon-filter"></i></span>
			<input type="text" name="search" id="search" value="<?php echo @$this->lists['search'];?>" class="text_area" />
			<button class="btn" onclick="document.adminForm.submit();"><?php echo JText::_( 'JSEARCH_FILTER_SUBMIT' ); ?></button>
			<button class="btn" onclick="document.getElementById('search').value='';document.adminForm.submit();"><?php echo JText::_( 'JSEARCH_RESET' ); ?></button>
		</div>
		<div class="pull-right">
				<?php
				echo $this->lists['filter_state'];
				?>
		</div>
</div>
<div class="clearfix"> </div>
	<table class="table table-striped table-hover">
		<thead>
			<tr class="sortable">
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title" width="18%">
					<?php //echo JHTML::_('grid.sort', 'AUP_DATE', 'a.insert_date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.insert_date','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_DATE');; ?></a>
				</th>
				<th class="title" width="14%">
					<?php //echo JHTML::_('grid.sort', 'AUP_RULENAME', 'r.rule_name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				<a href="javascript:tableOrder('r.rule_name','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_RULENAME');; ?></a>
				</th>
				<th class="title">
					<?php //echo JHTML::_('grid.sort', 'AUP_NAME', 'u.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('u.name','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_NAME');; ?></a>
				</th>
				<th class="title">
					<?php //echo JHTML::_('grid.sort', 'AUP_USERNAME', 'u.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('u.username','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_USERNAME');; ?></a>
				</th>
				<th class="title" width="8%">
					<?php //echo JHTML::_('grid.sort', 'AUP_POINTS', 'a.points', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.points','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_POINTS');; ?></a>
				</th>
				<th class="title" width="12%">
					<?php //echo JHTML::_('grid.sort', 'AUP_APPROVED', 'a.approved', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.approved','<?php echo @$this->lists['order_Dir'];?>','activities');" title="Click to sort by this column"><?php echo JText::_('AUP_APPROVED');; ?></a>
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
			for ($i=0, $n=count( $this->activities ); $i < $n; $i++)
			{
				$row 	=& $this->activities[$i];
				$link 	= 'index.php?option=com_altauserpoints&amp;task=showdetails&amp;cid='. $row->referreid. '&amp;name='.$row->uname;
				$style  = ( $row->last_points < 0 ) ? 'style="color:red;"' : '' ;
			?>
			<tr class="<?php echo "row$k"; ?>" <?php echo "$style"; ?>>
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<i class="icon-calendar"></i> <?php echo JHTML::_('date',  $row->insert_date,  JText::_('DATE_FORMAT_LC2') ); ?>				
				</td>
				<td>
					<?php echo JText::_($row->rule_name); ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo htmlspecialchars(JText::_( $row->uname ), ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo htmlspecialchars(JText::_( $row->usrname ), ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</td>
				<td align="right">
				<?php echo getFormattedPointsAdm($row->last_points); ?>
				</td>
				<td align="center">
				<?php 
				if ( $row->approved=='1' ) {
					echo '<span class="label label-success">'.JText::_( 'AUP_APPROVED' ).'</label>';
				} elseif ( $row->approved=='0' ) {
					echo '<span class="label label-important">'.JText::_( 'AUP_UNAPPROVED' ).'</label>';
				}
				?>									
				</td>				
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="activities" />
	<input type="hidden" name="table" value="__alpha_userpoints_details" />
	<input type="hidden" name="redirect" value="activities" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->lists['order_Dir']; ?>" />	
</form>
</div>