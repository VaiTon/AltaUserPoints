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
<div id="filter-bar" class="btn-toolbar">
	<table>
		<tr>
			<td align="left" width="100%">&nbsp;
				<?php //echo JText::_( 'Filter' ); ?>
				<!--<input type="text" name="search" id="search" value="<?php echo @$this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>-->
			</td>
			<td nowrap="nowrap" align="right">
				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_altauserpoints'), 'value', 'text', $this->lists['filter_category_id']);?>
				</select>
				<?php
				echo $this->lists['filter_state'];
				?>
			</td>
		</tr>
	</table>
</div>
	<div class="clearfix"> </div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="20%" class="title">
					<?php echo JText::_('AUP_CODE'); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_POINTS' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'JCATEGORY' ); ?>
				</th>				
				<th width="15%" class="title" nowrap="nowrap">
					<?php echo JText::_( 'AUP_EXPIRE' ); ?>
				</th>
				<th class="title" >
					<?php echo JText::_('AUP_DESCRIPTION'); ?>
				</th>
				<th width="5%" class="title" >
					<?php echo JText::_('AUP_PUBLIC'); ?>
				</th>
				<th width="60" class="title" >
					<?php echo JText::_('AUP_PRINTABLE'); ?>
				</th>
				<th width="50" class="title" >
					<?php echo JText::_('AUP_SCANNED'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php		
			$db = JFactory::getDBO();
				
			$k = 0;
			
			$img = '<img src="images/tick.png" border="0" alt="'. JText::_('AUP_PUBLIC') .'" /></a>';
			
			$img = '<img src="components/com_altauserpoints/assets/images/tick.png" border="0" alt="'. JText::_('AUP_PUBLIC') .'" /></a>';
			
			for ($i=0, $n=count( $this->couponcodes ); $i < $n; $i++)
			{
				$row 	=& $this->couponcodes[$i];
				
				$link 	= 'index.php?option=com_altauserpoints&amp;task=editcoupon&amp;cid[]='. $row->id. '';
				
				//$db = JFactory::getDBO();		

				$nullDate 		= $db->getNullDate();				
				
				// check if the coupon is already awarded		
				if ( $row->public ) {
					$where =  "d.keyreference LIKE '".strtoupper($row->couponcode)."##%'";
				} else $where = "d.keyreference='".strtoupper($row->couponcode)."'";
				
				$query = "SELECT d.* FROM #__alpha_userpoints_details AS d, #__alpha_userpoints_rules AS r WHERE $where AND d.enabled='1' AND r.id=d.rule AND r.plugin_function='sysplgaup_couponpointscodes'";
				$db->setQuery( $query );
				$resultCoupons = $db->loadObjectList();
				
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
						if ( $resultCoupons ) {
							if ( !$row->public ) {
						 		echo "<b><s>" . JText::_( strtoupper($row->couponcode)) . "</s></b><br />";
							} else {
								if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
									echo '<b><a href="'.$link.'">';
								}
								echo JText::_( strtoupper($row->couponcode) );
								if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
									echo '</a></b>';
								}
							}
							// show list user(s)
							echo '<br /><span class="small">' . JText::_( 'AUP_AWARDED' ) . ': ';
							foreach ( $resultCoupons as $awardedcoupon ) {
								echo '<br />&nbsp;&nbsp;-&nbsp;' . $awardedcoupon->referreid . '&nbsp;('.JHTML::_('date',  $awardedcoupon->insert_date,  JText::_('DATE_FORMAT_LC2') ).')';										
							}
							echo '</span>';
						} else {
							if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
								echo '<a href="'.$link.'">';
							}
							
							echo JText::_( strtoupper($row->couponcode) );
							
							if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
								echo '</a>';
							}
							
						}
						?>					
				</td>
				<td>
					<div align="right">
					<?php echo getFormattedPointsAdm( $row->points ); ?>
					</div>
				</td>
				<td>
					<div align="center">
					<?php echo JText::_( $row->category_title ); ?>
					</div>
				</td>				
				<td>
					<div align="center">
					<?php 
					if ( $row->expires == $nullDate ) {
						echo '-';
					} else {
						echo JHTML::_('date',  $row->expires,  JText::_('DATE_FORMAT_LC') );
					}
					?>
					</div>
				</td>
				<td>		
					<?php echo JText::_( $row->description ); ?>							
				</td>
				<td>
					<div align="center">
					<?php					
					$public = ( $row->public ) ? $img : '';
					echo $public; 					
					?>
					</div>						
				</td>
				<td>
					<?php 
					if ( $row->printable )
					{
						$QRcode50 = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/assets/coupons/QRcode/50/'. strtoupper($row->couponcode) .'.png';
						if ( file_exists($QRcode50)) {
						?>
						<a href="<?php echo JURI::base()."components/com_altauserpoints/assets/coupons/QRcode/250/".strtoupper($row->couponcode).".png" ; ?>"><img src="<?php echo JURI::base(); ?>components/com_altauserpoints/assets/coupons/QRcode/50/<?php echo strtoupper($row->couponcode); ?>.png" alt="" align="absmiddle" /></a>
					<?php 
						}
					} 
					?>
				</td>
				<td>
					<?php 
					if ( $row->printable )
					{
						$db = JFactory::getDBO();
						$query = "SELECT COUNT(*) FROM #__alpha_userpoints_qrcodetrack WHERE couponid='".$row->id."'";
						$db->setQuery( $query );
						$resultQRstats = $db->loadResult();
						if ($resultQRstats) {
							$linkQRcodestats = 'index.php?option=com_altauserpoints&amp;task=qrcodestats&amp;id='. $row->id. '';
							echo '<a href="'.$linkQRcodestats.'" title="'.JText::_('AUP_TRACK').'">';
							echo $resultQRstats;
							echo '</a>';						
						} else echo '0';
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
	<input type="hidden" name="task" value="couponcodes" />
	<input type="hidden" name="table" value="alpha_userpoints_coupons" />
	<input type="hidden" name="redirect" value="couponcodes" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>
<!-- Modal Form Coupons Generator-->
<div class="modal hide fade" id="generatorModal" data-toggle="modal">
	<form action="index.php" method="post" name="adminFormGenerator" id="adminFormGenerator" autocomplete="off" style="margin:0;">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">&times;</button>
			<h3><?php echo JText::_('AUP_COUPONS_GENERATOR');?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<fieldset class="adminform">
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_NUMBER' ); ?>::<?php echo JText::_('AUP_NUMBER_COUPON'); ?>">
								<?php echo JText::_( 'AUP_NUMBER' ); ?>:
							</span>
						</div>
						<div class="controls">
							<input class="inputbox" type="text" name="numbercouponcode" id="numbercouponcode" size="20" maxlength="20" value="20" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_PREFIXE' ); ?>::<?php echo JText::_('AUP_PREFIXE'); ?>">
								<?php echo JText::_( 'AUP_PREFIXE' ); ?>:
							</span>
						</div>
						<div class="controls">
							<input class="inputbox" type="text" name="prefixcouponcode" id="prefixcouponcode" size="20" maxlength="20" value="ABC-" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_LENGTH' ); ?>::<?php echo JText::_('AUP_NUMBER_RANDOM_CHARS'); ?>">
								<?php echo JText::_( 'AUP_LENGTH' ); ?>:
							</span>
						</div>
						<div class="controls">
							<select name="numrandomchars" id="numrandomchars">
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8" selected>8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="12">12</option>
							</select>
						</div>
					</div>
					
					
					<!--<div class="control-group">
							<span class="editlinktip hasTip" title="<?php //echo JText::_( 'AUP_SUFFIXE' ); ?>::<?php //echo JText::_('AUP_SUFFIXE'); ?>">
								<?php //echo JText::_( 'AUP_SUFFIXE' ); ?>:
							</span>
						<label class="checkbox">
						<div class="controls">
						<input type="checkbox" class="checkbox" name="enabledincrement" id="enabledincrement" value="1" />
							<?php echo JText::_( 'AUP_ENABLED_INCREMENT' ); ?>
						</div>
						</label>
					</div>-->					
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_ENABLED_INCREMENT' ); ?>::<?php echo JText::_('AUP_ENABLED_INCREMENT'); ?>">
								<?php echo JText::_( 'AUP_ENABLED_INCREMENT' ); ?>:
							</span>
						</div>
						<div class="controls">
							<fieldset id="jform_public" class="radio btn-group"><?php echo $this->lists['enabledincrement']; ?></fieldset>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>::<?php echo JText::_('AUP_DESCRIPTION'); ?>">
								<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
							</span>
						</div>
						<div class="controls">
							<input class="inputbox" type="text" name="description" id="description" size="100" maxlength="255" value="" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_POINTS'); ?>">
								<?php echo JText::_( 'AUP_POINTS' ); ?>:
							</span>
						</div>
						<div class="controls">
							<input class="inputbox" type="text" name="points" id="points" size="20" value="" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_EXPIRE' ); ?>::<?php echo JText::_('AUP_EXPIRE'); ?>">
								<?php echo JText::_( 'AUP_EXPIRE' ); ?>:
							</span>
						</div>
						<div class="controls">
							<?php echo JHTML::_('calendar', '', 'expires', 'expires', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_PUBLIC' ); ?>::<?php echo JText::_('AUP_PUBLIC'); ?>">
								<?php echo JText::_( 'AUP_PUBLIC' ); ?>:
							</span>
						</div>
						<div class="controls">
							<fieldset id="jform_public" class="radio btn-group"><?php echo $this->lists['public']; ?></fieldset>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_PRINTABLE' ); ?>::<?php echo JText::_('AUP_PRINTABLE'); ?>">
								<?php echo JText::_( 'AUP_PRINTABLE' ); ?>:
							</span>
						</div>
						<div class="controls">
						<fieldset id="jform_printable" class="radio btn-group"><?php echo $this->lists['printable']; ?></fieldset>
						</div>
					</div>
				</fieldset>
				<input type="hidden" name="option" value="com_altauserpoints" />
				<input type="hidden" name="couponcode" value=""/>
				<input type="hidden" name="task" value="savecoupongenerator"/>	
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" type="button" data-dismiss="modal">
				<?php echo JText::_( 'Cancel' ); ?>
			</button>
			<button class="btn btn-primary" type="submit" onclick="submit('savecoupongenerator');">
				<?php echo JText::_( 'Save' ); ?>
			</button>

		</div>
	</form>
</div>