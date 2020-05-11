<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$row = $this->row;
$rulename = $this->rulename;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || task == 'canceluser' || document.formvalidator.isValid(document.id('user-form'))) {
			Joomla.submitform(task, document.getElementById('user-form'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="user-form" class="form-validate">
<div class="form-horizontal">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'AUP_DETAILS' ); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_REFERREID' ); ?>::<?php echo JText::_('AUP_REFERREID'); ?>">
					<?php echo JText::_( 'AUP_REFERREID' ); ?>:
				</span>
			</div>
			<div class="controls">
				<?php echo "<font color='green'>" . JText::_($row->referreid) . "</font>"; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_RULE' ); ?>::<?php echo JText::_('AUP_RULE'); ?>">
					<?php echo JText::_( 'AUP_RULE' ); ?>:
				</span>
			</div>
			<div class="controls">
				<?php echo "<font color='green'>" . JText::_($rulename) . "</font>"; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DATE' ); ?>::<?php echo JText::_('AUP_DATE'); ?>">
					<?php echo JText::_( 'AUP_DATE' ); ?>:
				</span>
			</div>
			<div class="controls">
				    <?php echo JHTML::_('calendar', $row->insert_date, 'insert_date', 'insert_date', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_POINTS'); ?>">
					<?php echo JText::_( 'AUP_POINTS' ); ?>:
				</span>
			</div>
			<div class="controls">
				<input class="inputbox" type="text" name="points" id="points" size="20" maxlength="255" value="<?php echo $row->points; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_EXPIRE' ); ?>::<?php echo JText::_('AUP_EXPIRE'); ?>">
					<?php echo JText::_( 'AUP_EXPIRE' ); ?>:
				</span>
			</div>
			<div class="controls">
				    <?php echo JHTML::_('calendar', $row->expire_date, 'expire_date', 'expire_date', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DATA' ); ?>::<?php echo JText::_('AUP_DATA'); ?>">
					<?php echo JText::_( 'AUP_DATA' ); ?>:
				</span>
			</div>
			<div class="controls">
				<input class="inputbox" type="text" name="datareference" id="datareference" size="80" maxlength="255" value="<?php echo $row->datareference; ?>" />
			</div>
		</div>

	</fieldset>
	</div>
</div>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="referreid" value="<?php echo $row->referreid; ?>" />	
	<input type="hidden" name="status" value="<?php echo $row->status; ?>" />
	<input type="hidden" name="rule" value="<?php echo $row->rule; ?>" />
	<input type="hidden" name="approved" value="<?php echo $row->approved; ?>" />
    <input type="hidden" name="enabled" value="<?php echo $row->enabled; ?>" />
	<input type="hidden" name="keyreference" value="<?php echo $row->keyreference; ?>" />	
	<input type="hidden" name="redirect" value="showdetails&amp;cid=<?php echo $row->referreid; ?>&amp;name=<?php echo $this->name; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php //echo JHTML::_( 'form.token' ); ?>
</form>
</div>
<div class="clr"></div>