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
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('archive-form'))) {
			Joomla.submitform(task, document.getElementById('archive-form'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div id="j-main-container">
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="form-horizontal">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'AUP_COMBINE_ACTIVITIES_DESCRIPTION' ); ?></legend>
			<div class="control-group">
				<div class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_COMBINE_ACTIVITIES' ); ?>::<?php echo JText::_('AUP_COMBINE_ACTIVITIES_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_COMBINE_ACTIVITIES' ); ?>:
				</span>
				</div>
				<div class="controls">
					<?php echo JHTML::_('calendar', '', 'datestart', 'datestart', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
				</div>
				<div class="controls">
					<input class="btn btn-primary" type="submit" name="Submit" value="<?php echo JText::_( 'AUP_COMBINE_ACTIVITIES' ); ?>">
				</div>
			</div>
	</fieldset>
</div>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="processarchive" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>
<div class="clr"></div>