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
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('reportsystem-form'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<script type="text/javascript">
	window.addEvent('domready', function(){
		$('link_sel_all').addEvent('click', function(e){
			$('reportsystem').select();
		});
	});
</script>
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="adminForm" class="form-validate">
		<legend><?php echo JText::_( 'AUP_REPORT_SYSTEM' ); ?></legend>
			<div class="alert alert-info" role="alert"><?php echo JText::_('AUP_REPORT_SYSTEM_DESCRIPTION'); ?></div>
		  <div class="panel panel-default">
  			<div class="panel-heading">
				<a href="#" id="link_sel_all" ><?php echo JText::_('AUP_REPORT_SELECT_ALL'); ?></a>
			</div>
			<div class="panel-body">
				<div class="control-group">
				<div class="controls"><textarea id="reportsystem" name="reportsystem" rows="15" style="width:65%;"><?php echo $this->reportsystem; ?></textarea></div>
				</div>
			</div>			
		</div>						
	</fieldset>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="cpanel" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<div class="clr"></div>