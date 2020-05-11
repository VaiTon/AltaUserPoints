<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$row = $this->row;
$listrank = $this->listrank;
$medalsexist = $this->medalsexist;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || task == 'cancelrule' || document.formvalidator.isValid(document.id('statistic-form'))) {
			Joomla.submitform(task, document.getElementById('statistic-form'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<div class="span2">

</div>

<div class="form-horizontal span10">
<form action="index.php?option=com_altauserpoints" method="post" name="statistic-form" id="statistic-form" class="form-validate">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('AUP_DETAILS', true)); ?>		
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_NAME'); ?>">
						<?php echo JText::_( 'AUP_NAME' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
						echo "<font color='green'>" . JText::_($row->name) . "</font>";
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_USERNAME'); ?>">
						<?php echo JText::_( 'AUP_USERNAME' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
						echo "<font color='green'>" . JText::_($row->username) . "</font>"; 
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_REFERREID'); ?>">
						<?php echo JText::_( 'AUP_REFERREID' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
						echo "<font color='green'>" . JText::_($row->referreid) . "</font>"; 
					?>				
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_POINTS'); ?>">
						<?php echo JText::_( 'AUP_POINTS' ); ?>:
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="points" id="points" size="20" maxlength="255" value="<?php echo $row->points; ?>"  readonly="readonly"/><br /><br />
					<a href="index.php?option=com_altauserpoints&task=showdetails&cid=<?php echo $row->referreid; ?>&name=<?php echo $row->name; ?>"><?php echo JText::_('AUP_SHOW_DETAIL_ACTIVITIES'); ?></a>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_MAXPOINTS'); ?>">
						<?php echo JText::_( 'AUP_MAXPOINTS' ); ?>:
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="max_points" id="max_points" size="20" maxlength="255" value="<?php echo $row->max_points; ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_EXCLUDESPECIFICUSERSDESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_ENABLED' ); ?>:
					</span>
				</div>
				<div class="controls">
					<fieldset id="jform_published" class="radio btn-group">
						<?php echo JHTML::_('select.booleanlist', 'published', '', $row->published); ?>
					</fieldset>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_RANK'); ?>">
						<?php echo JText::_( 'AUP_RANK' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php echo $listrank; ?>
				</div>
			</div>
			<?php 
				if ( $row->leveldate != '0000-00-00' ) {
			?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_DATE'); ?>">
						<?php echo JText::_( 'AUP_DATE' ); ?>:
					</span>
				</div>
				<div class="controls">
				<?php
					echo JHTML::_('date',  $row->leveldate,  JText::_('DATE_FORMAT_LC') );
				?>
				</div>
			</div>
			<?php } ?>
			
		</fieldset>
		<div class="clr"></div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'profile', JText::_('AUP_PROFILE', true)); ?>

		<fieldset class="adminform">
		<div class="span6">
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_PROFILE_VIEWS'); ?>">
					<?php echo JText::_( 'AUP_PROFILE_VIEWS' ); ?>:
				</span>
			</div>
			<div class="controls">
				<?php 
					echo "<span class='badge badge-success'>" . $row->profileviews . "</span>"; 
					if ( $row->profileviews )
					{
					?>
					<a class="btn btn-mini" href="index.php?option=com_altauserpoints&task=resetprofilviews&id=<?php echo $row->id; ?>"><i class="icon-arrow-left"></i> <?php echo JText::_( 'AUP_RESET' ); ?> <i class="icon-cancel"></i></a>							
					<?php
					}
				?>
			</div>
		</div>
		<?php if ( $row->referraluser ) {?>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_REFERRALUSER'); ?>">
					<?php echo JText::_( 'AUP_REFERRALUSER' ); ?>:
				</span>
			</div>
			<div class="controls">
				<?php 
					echo "<span class='label label-success'>" . $row->referraluser . "</span> ";
					if ( $row->referralusername ) 
					{
						echo "(" . $row->referralusername . ")"; 
					}

					?>
					<a class="btn btn-mini" href="index.php?option=com_altauserpoints&task=resetreferraluser&id=<?php echo $row->id; ?>"><i class="icon-arrow-left"></i> <?php echo JText::_( 'AUP_RESET' ); ?> <i class="icon-cancel"></i></a>							
			</div>
		</div>
		<?php } ?>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_REFERREES'); ?>">
					<?php echo JText::_( 'AUP_REFERREES' ); ?>:
				</span>
			</div>
			<div class="controls">
				<?php 
					echo "<span class='badge badge-success'>" . $row->referrees . "</span>"; 
					if ( $row->referrees )
					{
					?>
					<a class="btn btn-mini" href="index.php?option=com_altauserpoints&task=resetreferrees&id=<?php echo $row->id; ?>"><i class="icon-arrow-left"></i> <?php echo JText::_( 'AUP_RESET' ); ?> <i class="icon-cancel"></i></a>							
					<?php
					}
				?>
			</div>
		</div>

		</div>
		</fieldset>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="userid" value="<?php echo $row->userid; ?>" />
	<input type="hidden" name="referreid" value="<?php echo $row->referreid; ?>" />
	<!--<input type="hidden" name="referraluser" value="<?php echo $row->referraluser; ?>" />-->	
	<input type="hidden" name="oldrank" value="<?php echo $row->levelrank; ?>" />	
	<input type="hidden" name="upnid" value="<?php echo $row->upnid; ?>" />
	<input type="hidden" name="referrees" value="<?php echo $row->referrees; ?>" />
	<input type="hidden" name="blocked" value="<?php echo $row->blocked; ?>" />	
	<input type="hidden" name="avatar" value="<?php echo $row->avatar; ?>" />
	
	<input type="hidden" name="profileviews" value="<?php echo $row->profileviews; ?>" />
	<input type="hidden" name="shareinfos" value="<?php echo $row->shareinfos; ?>" />	
	<input type="hidden" name="redirect" value="statistics" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
		<div class="clr"></div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php if ( $medalsexist ) { ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'addnewmedal', JText::_('AUP_AWARDED_NEW_MEDAL', true)); ?>
		<?php if ( $this->medalslistuser ) { ?>	
			<legend><?php echo JText::_( 'AUP_MEDALS' ); ?></legend>
			<table class="table table-bordered table-striped tabel-hover">
			<tbody>
			<?php foreach ( $this->medalslistuser as $medaluser ) { ?>
			<tr>
				<td width="15%">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_NAME'); ?>">
						<i class="icon-calendar visible-desktop"></i> <?php echo $medaluser->medaldate; ?>
					</span>
				</td>
				<td width="20%">
					<?php 
						echo $medaluser->rank;
					?>
				</td>
				<td>
					<?php 
						$linkdelete = '<a  class="btn btn-small btn-danger pull-right" href="index.php?option=com_altauserpoints&amp;task=removemedaluser&amp;cid='.$medaluser->id.'&amp;rid='.$row->id.'"><i class="icon-delete"></i> '.JText::_( 'AUP_DELETE' ).'</a>';
						echo $medaluser->reason;
						echo $linkdelete;
					?>
				</td>
			</tr>
			<?php } ?>
			</tbody>
			</table>
		<?php } ?>
<p>&nbsp;</p>
<p>&nbsp;</p>	
		<form action="index.php?option=com_altauserpoints" method="post" name="adminForm2" id="adminForm2">
		<div class="form-horizontal span12">
				<table class="admintable" width="100%">
				<tbody>
				<div class="control-group">
					<div class="control-label">
						<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_MEDAL'); ?>">
							<?php echo JText::_( 'AUP_MEDAL' ); ?>
						</span>				
					</div>
					<div class="controls">
					  <?php echo $this->listmedals; ?>
				  </div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_OPTIONAL' ). ' - ' . JText::_( 'AUP_DESCRIPTION_MEDAL_BY_DEFAULT' ) ; ?>">
							<?php echo JText::_( 'AUP_DESCRIPTION' ) .  ' <i>(' . JText::_( 'AUP_OPTIONAL' ) . ')</i>'; ?>
						</span>				
					</div>
					<div class="controls">
					  <input type="text" id="reason" name="reason" class="inputbox" value="" size="60" />
				  </div>
				</div>		
				<div class="control-group">
					<div class="control-label">&nbsp;
					</div>
					<div class="controls">
					  <input type="submit" name="Submit" class="btn btn-primary" value="<?php echo  JText::_('AUP_AWARDED_NEW_MEDAL'); ?>" />
				  </div>
				</div>		
				</tbody>
				</table>			
			<input type="hidden" name="option" value="com_altauserpoints" />
			<input type="hidden" name="task" value="awardedmedal" />
			<input type="hidden" name="rid" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="userid" value="<?php echo $row->userid; ?>" />
			<input type="hidden" name="referreid" value="<?php echo $row->referreid; ?>" />
			<input type="hidden" name="referraluser" value="<?php echo $row->referraluser; ?>" />	
			<input type="hidden" name="redirect" value="edituser" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			</div>
		</form>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php } // end if medal exist ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'latestactivity', JText::_('AUP_LASTACTIVITY', true)); ?>	
			<div class="">
				<?php echo $this->loadTemplate('activity'); ?>
			</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
</div>
<?php echo JHtml::_('bootstrap.endTabSet'); ?>
<div class="clr"></div>
</div>