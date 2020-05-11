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
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('raffles-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="adminForm" class="form-validate">
<div id="filter-bar" class="btn-toolbar">
	<table>
		<tr>
			<td align="left" width="100%">&nbsp;
				<?php //echo JText::_( 'Filter' ); ?>
				<!--<input type="text" name="search" id="search" value="<?php echo @$this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php //echo JText::_( 'Reset' ); ?></button>-->
			</td>
			<td nowrap="nowrap" align="right">
				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_altauserpoints'), 'value', 'text', $this->lists['filter_category_id']);?>
				</select>
			</td>
		</tr>
	</table>
</div>
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
				<th width="2%" class="title" >
					<?php echo JText::_('AUP_ID'); ?>
				</th>
				<th class="title" >
					<?php echo JText::_('AUP_DESCRIPTION'); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'JCATEGORY' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_REGISTRATION' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_POINTS_TO_PARTICIPATE' ); ?>
				</th>
				<th width="8%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_REGISTERED' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_NUM_OF_WINNER' ); ?>
				</th>
				<th width="10%" class="title">
					<?php echo JText::_( 'AUP_RAFFLE_SYSTEM' ); ?>
				</th>
				<th width="10%" class="title">
					<?php echo JText::_( 'AUP_RAFFLE_DATE' ); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_WINNERS' ); ?>
				</th>
				<th width="5%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_PUBLISHED' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_RAFFLE' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="14">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->raffle ); $i < $n; $i++)
			{
				$row 	=& $this->raffle[$i];
			
				$prefix = "";

				$img 	= $row->published ? 'publish_x.png' : 'tick.png';
				$link 	= 'index.php?option=com_altauserpoints&amp;task=editraffle&amp;cid[]='. $row->id. '';
				$link2  = 'index.php?option=com_altauserpoints&amp;task=makeraffle&amp;cid[]='. $row->id. '';
		
				$published 	= JHTML::_('grid.published', $row, $i );
				
				$imgA 	 = $row->inscription ? JURI::base(true).'/components/com_altauserpoints/assets/images/tick.png':JURI::base(true).'/components/com_altauserpoints/assets/images/publish_x.png'  ;
				//$imgA 	 = $row->inscription ? 'publish_x.png' : 'tick.png';
				$taskA 	 = $row->inscription ? 'unregistration' : 'registration';
				$altA	 = $row->inscription ? JText::_( 'AUP_REGISTRATION' ) : JText::_( 'AUP_REGISTRATION' );
				$actionA = $row->inscription ? JText::_( 'AUP_REGISTRATION' ) : JText::_( 'AUP_REGISTRATION' );
		
				$registration = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$taskA .'\')" title="'. $actionA .'">
				<img src="'. $imgA .'" border="0" alt="'. $altA .'" /></a>'
				;				
				
				$db = JFactory::getDBO();		

				$nullDate 		= $db->getNullDate();				
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<b><?php echo $row->id; ?></b>		
				</td>
				<td>
				<?php
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link; ?>">
				<?php 
				}
				?>
						<?php echo JText::_( $row->description ); ?>
				<?php
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					</a>
				<?php 
				}
				?>
				</td>
				<td>
					<div align="center">
					<?php echo JText::_( $row->category_title ); ?>
					</div>
				</td>				
				<td align="center">
					<?php echo $registration; ?>				
				</td>
				<td align="center">
					<?php echo getFormattedPointsAdm($row->pointstoparticipate); ?>
				</td>
				<td align="center">
					<?php 
					if ( !$row->inscription ) {
						echo "-"; 
					} else {
						if ( $row->numregistered>=1 ) {

							if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
								echo "<a href=\"index.php?option=com_altauserpoints&amp;task=exportListUsersRaffle&amp;id=".$row->id."\">".$row->numregistered."</a>";
							} else echo $row->numregistered;
							
						} else echo $row->numregistered;
					}
					?>
				</td>				
				<td align="center">
					<?php echo $row->numwinner; ?>
				</td>
				<td align="center">
					<?php 					
					switch ( $row->rafflesystem ) {
						case '3':						
							$rafflesystem =  JText::_( 'AUP_EMAIL_ONLY' );
							break;					
						case '2':						
							$rafflesystem =  JText::_( 'AUP_EMAIL_WITH_A_LINK_TO_DOWNLOAD' );
							break;
						case '1':						
							$rafflesystem =  JText::_( 'AUP_COUPON_CODES' );
							break;
						case '0':
						default:		
							$rafflesystem =  JText::_( 'AUP_POINTS' );					
					}					
					echo $rafflesystem; 					
					?>
				</td>
				<td align="center">
					<?php 
					if ( $row->raffledate == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date',  $row->raffledate,  JText::_('DATE_FORMAT_LC2')) . '<br />' . nicetimeAdm($row->raffledate, 0);
					}
					?>
				</td>
				<td align="center">
					<?php			
					if ( $row->winner1 ) {					
						$db			    = JFactory::getDBO();
						
						$query = "SELECT u.*, aup.referreid FROM #__users AS u, #__alpha_userpoints AS aup"
								. "\n WHERE u.id = $row->winner1 AND u.id = aup.userid";
								;
						$db->setQuery($query);
						$result = $db->loadObjectList();
						if ( $result ) {
							foreach ( $result as $winner ) {
								$linkuser1 = "index.php?option=com_altauserpoints&task=showdetails&cid=$winner->referreid&name=$winner->name";
								echo "<a href=\"$linkuser1\">";
								echo $winner->name;	
								echo "</a>";				
							}
						}
						
						$query = "SELECT u.*, aup.referreid FROM #__users AS u, #__alpha_userpoints AS aup"
								. "\n WHERE u.id = $row->winner2 AND u.id = aup.userid";
								;
						$db->setQuery($query);
						$result2 = $db->loadObjectList();
						if ( $result2 ) {
							foreach ( $result2 as $winner ) {
								$linkuser2 = "index.php?option=com_altauserpoints&task=showdetails&cid=$winner->referreid&name=$winner->name";
								echo "<a href=\"$linkuser2\">";
								echo ", " . $winner->name;
								echo "</a>";			
							}
						}
						
						$query = "SELECT u.*, aup.referreid FROM #__users AS u, #__alpha_userpoints AS aup"
								. "\n WHERE u.id = $row->winner3 AND u.id = aup.userid";
								;
						$db->setQuery($query);
						$result3 = $db->loadObjectList();
						if ( $result3 ) {
							foreach ( $result3 as $winner ) {
								$linkuser3 = "index.php?option=com_altauserpoints&task=showdetails&cid=$winner->referreid&name=$winner->name";
								echo "<a href=\"$linkuser3\">";
								echo ", " . $winner->name;
								echo "</a>";
							}
						}
									
					} else echo "<i>". JText::_('AUP_PENDING') . "</i>";
					?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td align="center">
				<?php if ( !$row->winner1 ) { ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link2; ?>">
				<?php } ?>
						<?php echo JText::_( 'AUP_MAKE_THE_RAFFLE_NOW' ); ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
				?>
					</a>
					<?php } ?>
				<?php } else echo JText::_( 'AUP_THIS_RAFFLE_HAS_BEEN_PROCEEDED' ); ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="raffle" />
	<input type="hidden" name="table" value="alpha_userpoints_raffle" />
	<input type="hidden" name="redirect" value="raffle" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php //echo JHTML::_( 'form.token' ); ?>
</form>
</div>