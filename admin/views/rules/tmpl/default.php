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
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('rules-form'))) {
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
	<div class="pull-right">
		<?php echo JText::_('AUP_CATEGORY') . " " . $this->lists['filter_category']; ?>
	</div>
</div>
	<div class="clr"></div>
	<hr>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="2%" class="title" nowrap="nowrap">&nbsp;
					<?php echo JText::_('AUP_ID'); ?>
				</th>
				<th width="3%" class="title" nowrap="nowrap">&nbsp;
					
				</th>
				<th width="3%" class="title" nowrap="nowrap">&nbsp;
					
				</th>
				<th width="12%" class="title">
					<?php echo JText::_('AUP_RULENAME'); ?>
				</th>
				<th class="title" >
					<?php echo JText::_('AUP_DESCRIPTION'); ?>
				</th>
				<th width="6%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_PLUGIN' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_USERLEVEL' ); ?>
				</th>
				<th width="8%" class="title">
					<div align="right"><?php echo JText::_( 'AUP_POINTS' ); ?></div>
				</th>				
				<th width="5%" class="title">
					<div align="center"><?php echo JText::_( 'AUP_MESSAGE' ); ?></div>
				</th>
				<th width="5%" class="title">
					<div align="center"><?php echo JText::_( 'AUP_EMAILNOTIFICATION' ); ?></div>
				</th>
				<th width="10%" class="title">
					<?php echo JText::_( 'AUP_EXPIRE' ); ?>
				</th>
				<th width="5%" class="title" nowrap="nowrap">
					<div align="center"><?php echo JText::_( 'AUP_PUBLISHED' ); ?></div>
				</th>
				<th width="5%" class="title" nowrap="nowrap">
					<div align="center"><?php echo JText::_( 'AUP_AUTOAPPROVED' ); ?></div>
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
			for ($i=0, $n=count( $this->rules ); $i < $n; $i++)
			{
				$row 	=& $this->rules[$i];
				
				$access = $row->groupname;
				
				$prefix = "";

				$img 	= $row->published ? 'publish_x.png' : 'tick.png';
				$link 	= 'index.php?option=com_altauserpoints&amp;task=editrule&amp;cid[]='. $row->id. '';
				
				$locked = $row->blockcopy ? '<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/locked.png" alt="'.JText::_( 'AUP_THISRULECANTBECOPIED' ).'" />' : '';
				
				$published 	= JHTML::_('grid.published', $row, $i );
				
				$imgA 	 = $row->autoapproved ? 'featured.png' :'disabled.png';
				$taskA 	 = $row->autoapproved ? 'unautoapprove' : 'autoapprove';
				$altA	 = $row->autoapproved ? JText::_( 'AUP_AUTOAPPROVED' ) : JText::_( 'AUP_NOTAUTOAPPROVED' );
				$actionA = $row->autoapproved ? JText::_( 'AUP_UNAUTOAPPROVEITEM' ) : JText::_( 'AUP_AUTOAPPROVEITEM' );
		
				$autoapproved = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$taskA .'\')" title="'. $actionA .'">
				<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/'. $imgA .'" border="0" alt="'. $altA .'" /></a>'
				;
				
				$showmessage = $row->displaymsg ? '<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/megaphone.png" border="0" alt="'.JText::_( 'AUP_DISPLAY_MESSAGE_ON_FRONTEND' ).'" />' : '' ;
				$sendnotification = $row->notification ? '<img src="'.JURI::base(true).'/components/com_altauserpoints/assets/images/send_mail.png" border="0" alt="'.JText::_( 'AUP_EMAILNOTIFICATIONDESCRIPTION' ).'" />' : '' ;
				
				$db = JFactory::getDBO();

				$nullDate 		= $db->getNullDate();
				
				$category		= ($row->category!='') ? '<img src="../components/com_altauserpoints/assets/images/categories/'.$row->category.'.gif" alt="" />' : '';
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="center">
					<div align="center"><?php echo $row->id; ?></div>
				</td>	
				<td align="center" class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('AUP_THISRULECANTBECOPIED');?>">
					<?php echo $locked; ?></span>
				</td>
				<td align="center">
					<?php echo $category; ?>
				</td>	
				<td>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					<a href="<?php echo $link; ?>">
				<?php } ?>
						<?php echo JText::_( $row->rule_name ); ?>
				<?php 
				if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
				?>
					</a>
					<?php } ?>
				</td>
				<td>
					<?php echo JText::_( $row->rule_description ); ?>
				</td>
				<td>
					<?php echo JText::_( $row->rule_plugin ); ?>
				</td>
				<td align="right">									
					<?php
					
					switch ( $row->plugin_function ) {
					
						case 'sysplgaup_newregistered':
						case 'sysplgaup_referralpoints':						
						case 'sysplgaup_bonuspoints':
						case 'sysplgaup_emailnotification':
						case 'sysplgaup_winnernotification':
						case 'sysplgaup_archive':		
						case 'sysplgaup_changelevel1':		
						case 'sysplgaup_changelevel2':		
						case 'sysplgaup_changelevel3':										
							echo "<font color=\"grey\">-</font>";				
							break;							
						default:
							echo $access;		
					}					
					?>					
				</td>
				<td align="center">
				<div align="right">
					<?php
					switch ( $row->fixedpoints ) {
					
						case '0':
							echo  "-";
							break;
						case '1':
							if ($row->plugin_function!='sysplgaup_changelevel1' && $row->plugin_function!='sysplgaup_changelevel2' && $row->plugin_function!='sysplgaup_changelevel3'){
								echo getFormattedPointsAdm($row->points); 
								if ( $row->percentage ) echo "%";			
							}else{
								echo getFormattedPointsAdm($row->points2); 
							}		
					}
					
					?>
					</div>
				</td>
				
				<td align="center">
					<div align="center"><?php echo $showmessage; ?></div>
				</td>
				<td align="center">
					<div align="center"><?php echo $sendnotification; ?></div>
				</td>
				
				<td align="center">
					<?php 
					if ( $row->rule_expire == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date',  $row->rule_expire,  JText::_('DATE_FORMAT_LC') );
					}
					?>
				</td>
				<td align="center">
					<div align="center"><?php echo $published; ?></div>
				</td>
				<td align="center">
				<div align="center">
					<?php
					switch ( $row->plugin_function ) {
						case 'sysplgaup_newregistered':
						case 'sysplgaup_emailnotification':
						case 'sysplgaup_winnernotification':
						case 'sysplgaup_archive':
							echo  "<img src=\"".JURI::base(true)."/components/com_altauserpoints/assets/images/checked_out.png\" border=\"0\" title=\"".JText::_('AUP_NOT_AVAILABLE')."\" alt=\"".JText::_('AUP_NOT_AVAILABLE')."\" /></a>";
							break;						
						default:
							echo $autoapproved;
					} 
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
	<input type="hidden" name="task" value="rules" />
	<!--
	<input type="hidden" name="system" value="<?php echo $row->system; ?>" />
	<input type="hidden" name="duplicate" value="<?php echo $row->duplicate; ?>" />
	<input type="hidden" name="blockcopy" value="<?php echo $row->blockcopy; ?>" />	
	<input type="hidden" name="content_items" value="<?php echo $row->content_items; ?>" />	
	<input type="hidden" name="exclude_items" value="<?php echo $row->exclude_items; ?>" />
	-->
	<input type="hidden" name="table" value="alpha_userpoints_rules" />
	<input type="hidden" name="redirect" value="rules" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>