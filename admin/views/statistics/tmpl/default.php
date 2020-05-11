<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$numberofcolumns = 13;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || task == 'cancelrule' || document.formvalidator.isValid(document.id('statistics-form'))) {
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
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div id="filter-bar" class="btn-toolbar">
		<div class="input-prepend input-append pull-left">
			<span class="add-on hasTip" title="<?php echo JText::_( 'JGLOBAL_FILTER_FIELD_LABEL' ); ?>"><i class="icon-filter"></i></span>
			<input type="text" name="search" id="search" value="<?php echo @$this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button class="btn" onclick="document.adminForm.submit();"><?php echo JText::_( 'JSEARCH_FILTER_SUBMIT' ); ?></button>
			<button class="btn" onclick="document.getElementById('search').value='';document.adminForm.submit();"><?php echo JText::_( 'JSEARCH_RESET' ); ?></button>
		</div>
		<div class="pull-right">
		<?php 
		if ( $this->ranksexist ) { 
			echo @$this->lists['levelrank']; 
		}
		?>
		</div>
	</div>
	<div class="clearfix"><hr></div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="3%" class="title">					
					<?php //echo JHTML::_('grid.sort',   'AUP_ID', 'a.userid', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.userid','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_ID');; ?></a>
				</th>
				<th width="3%" class="title">					
					<?php //echo JText::_( 'AUP_ENABLED' ); ?>
					<a href="javascript:tableOrder('a.published','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_ENABLED');; ?></a>
				</th>
				<?php 
				if ( $this->ranksexist ) { 
					$numberofcolumns++;
				?>
				<th width="3%" class="title">					
					<?php //echo JHTML::_('grid.sort',   'AUP_RANK', 'a.levelrank', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.levelrank','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_RANK');; ?></a>
				</th>
				<?php } ?>
				<?php 
				if ( $this->medalsexist ) { 
					$numberofcolumns++;
				?>
				<th width="3%" class="title">					
					<?php echo JText::_( 'AUP_MEDALS' ); ?>
				</th>
				<?php } ?>
				<th width="12%" class="title" >
					<?php //echo JHTML::_('grid.sort',   'AUP_NAME', 'u.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('u.name','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_NAME');; ?></a>
				</th>
				<th width="12%" class="title" nowrap="nowrap">
					<?php //echo JHTML::_('grid.sort',   'AUP_USERNAME', 'u.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('u.username','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_USERNAME');; ?></a>
				</th>
				<th width="12%" class="title" nowrap="nowrap">
                  <?php echo JText::_( 'AUP_REFERREID' ); ?> </th>
				<th width="6%" class="title">
					<?php //echo JHTML::_('grid.sort',   'AUP_POINTS', 'a.points', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.points','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_POINTS');; ?></a>
				</th>
				<th width="5%" class="title">
					<?php echo JText::_( 'AUP_MAXPOINTS' ); ?>
				</th>
				<th width="18%" class="title" nowrap="nowrap">
					<?php //echo JHTML::_('grid.sort',   'AUP_LASTUPDATE', 'a.last_update', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.last_update','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_LASTUPDATE');; ?></a>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php //echo JHTML::_('grid.sort',   'AUP_REFERRALUSER', 'a.referraluser', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<a href="javascript:tableOrder('a.referraluser','<?php echo @$this->lists['order_Dir'];?>','statistics');" title="Click to sort by this column"><?php echo JText::_('AUP_REFERRALUSER');; ?></a>
				</th>
				<th width="3%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_REFERREES' ); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_ACTIVITY' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $numberofcolumns ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			
			$pathicon = JURI::root() . 'components/com_altauserpoints/assets/images/awards/icons/';
			
			$db = JFactory::getDBO();
			$nullDate 		= $db->getNullDate();
						
			for ($i=0, $n=count( $this->usersStats ); $i < $n; $i++)
			{
				$row 	=& $this->usersStats[$i];				
				
				$img 	= $row->published ? 'publish_x.png' : 'tick.png';
				$published 	= JHTML::_('grid.published', $row, $i );
								
				$link 			= 'index.php?option=com_altauserpoints&amp;task=edituser&amp;cid[]='. $row->id. '';
				$link_details 	= 'index.php?option=com_altauserpoints&amp;task=showdetails&amp;cid='. $row->referreid. '&amp;name='. $row->name .'';	
				
				$queryicon = "SELECT icon FROM #__alpha_userpoints_levelrank WHERE id=".$row->levelrank;
				$db->setQuery( $queryicon );
				$icon = $db->loadResult();
				if ( $icon ) {
					$icone = '<img src="'.$pathicon . $icon .'" width="16" height="16" border="0" style="vertical-align:middle" alt="" /> ';
				} else $icone = "";
				
				$querymedals = "SELECT COUNT(*) FROM #__alpha_userpoints_medals WHERE rid=".$row->id;
				$db->setQuery( $querymedals );
				$medals = $db->loadResult();
				if ( $medals ) {
					$nummedals = $medals;
				} else $nummedals = "";
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<?php echo $row->userid;?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<?php 
				if ( $this->ranksexist ) { ?>
				<td align="center">					
					<?php echo $icone; ?> 
				</td>
				<?php } ?>
				<?php 
				if ( $this->medalsexist ) { ?>
				<td align="center">					
					<?php echo $nummedals; ?> 
				</td>
				<?php } ?>				
				<td>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
				<a href="<?php echo $link; ?>" title="<?php echo JText::_('AUP_USER_DETAILS'); ?>">
				<?php } ?>
					<?php echo $row->name; ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
				</a>
				<?php } ?>
				</td>
				<td>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_('AUP_USER_DETAILS'); ?>">
				<?php } ?>
					<?php echo $row->username; ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					</a>
				<?php } ?>
				</td>
				<td>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_('AUP_USER_DETAILS'); ?>">
				<?php } ?>
					<?php echo $row->referreid; ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					</a>
				<?php } ?>
				</td>
				<td align="right">				
					<?php echo getFormattedPointsAdm($row->points); ?>
				</td>
				<td align="right">
					<?php echo getFormattedPointsAdm($row->max_points); ?>
				</td>
				<td align="center">
					<?php 
					if ( $row->last_update == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date',  $row->last_update,  JText::_('DATE_FORMAT_LC2') );
					}
					?>
				</td>
				<td>				
					<?php echo $row->referraluser; ?>
				</td>
				<td align="center">				
					<?php 
					$nb_referrees = ( $row->referrees ) ? $row->referrees : "" ;
					echo $nb_referrees;					 
					?>
				</td>
				<td align="center">
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>

					<b><a href="<?php echo $link_details; ?>" title="<?php echo JText::_('AUP_DETAILS_ALL_POINTS'); ?>">
					<?php echo JText::_('AUP_DETAILS'); ?>
					</a></b>
				<?php } else { 
				echo JText::_('AUP_DETAILS');
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
	<input type="hidden" name="task" value="statistics" />
	<input type="hidden" name="table" value="alpha_userpoints" />
	<input type="hidden" name="redirect" value="statistics" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->lists['order_Dir']; ?>" />	
</form>
</div>