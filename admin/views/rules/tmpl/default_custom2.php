<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JFactory::getApplication()->input->set('hidemainmenu', 1);
JToolBarHelper::title(   JText::_( 'AUP_CUSTOM_POINTS' ), 'searchtext' );
JToolBarHelper::custom( 'cpanel', 'default.png', 'default.png', JText::_('AUP_CPANEL'), false );
JToolBarHelper::back( 'Back' );
JToolBarHelper::save( 'savecustomrulepoints' );
JToolBarHelper::help( 'screen.altauserpoints', true );
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('custom-form'))) {
			Joomla.submitform(task, document.getElementById('custom-form'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<form action="index.php" method="post" name="adminForm" autocomplete="off" id="custom-form" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_( 'AUP_CUSTOM_POINTS' ); ?></legend>
		<table class="admintable">
		<tbody>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_CUSTOM_POINTS_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_POINTS' ); ?>:
				</span>
			</td>
			<td>				
				<input class="inputbox" type="text" name="points" id="points" size="20" maxlength="255" value="" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>::<?php echo JText::_('AUP_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
				</span>
			</td>
			<td>
				<input class="inputbox" type="text" name="reason" id="reason" size="80" maxlength="255" value="" />
			</td>
		</tr>
		</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="savecustompoints" />
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
</form><br /><br />