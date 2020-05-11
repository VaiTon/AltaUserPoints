<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );


$row = $this->row;
$lists = $this->lists;

JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	//if (task == 'cpanel' || document.formvalidator.isValid(document.id('couponcodes-form'))) {
		Joomla.submitform(task, document.getElementById('templateinvite-form'));
	//}
	//else {
		//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	//}
}

</Script>
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="templateinvite-form" class="form-validate">
<fieldset>
	  <legend><?php echo JText::_( 'AUP_DETAILS' ); ?></legend>
		<table class="admintable">
		<tbody>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_NAME' ); ?>::<?php echo JText::_('AUP_NAME'); ?>">
					<?php echo JText::_( 'AUP_NAME' ); ?>:
				</span>
			</td>
		  <td>
				<input class="inputbox" type="text" name="template_name" id="template_name" size="80" maxlength="100" value="<?php echo JText::_($row->template_name); ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_SUBJECT'); ?>">
					<?php echo JText::_( 'AUP_SUBJECT' ); ?>:
				</span>
			</td>
			<td>
			  <input name="emailsubject" type="text" class="inputbox" id="emailsubject" value="<?php echo JText::_($row->emailsubject); ?>" size="80" maxlength="255">
		</td>
		</tr>
		<tr>
			<td class="key">
			  <span class="editlinktip hasTip" title="<?php echo JText::_('AUP_MESSAGE_BODY'); ?>">
					<?php echo JText::_( 'AUP_MESSAGE_BODY' ); ?>:
					<br/>
					<br/>
					{name}
					<br/>
					{custom}
					<br/>
					{link}
					<br/>					
				  <?php
					$javascript = "onclick=\"Joomla.popupWindow('components/com_altauserpoints/help/en-GB/screen.altauserpoints.html#templateinvite', 'Help', 640, 480, 1)\"";
					?>
			    <a href="#" <?php echo $javascript;?>><?php echo JText::_('HELP');?></a></span>
			</td>
			<td>	
			  <?php            			
			    $editor		=  JFactory::getEditor();
				$paramsEditor = array('relative_urls' => '0');
				echo $editor->display( 'emailbody',  $row->emailbody , '100%', '200', '80', '20', true, null, null, null, $paramsEditor );
				//echo $editor->display( 'emailbody',  $row->emailbody , '100%', '200', '80', '20', false );
				?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_FORMAT_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_FORMAT' ); ?>:
				</span>
			</td>
			<td><div class="control-group"><fieldset id="jform_emailformat" class="controls">		
			<?php echo $this->lists['emailformat']; ?></fieldset></div>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_SEND_COPY_ADMIN_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_SEND_COPY_ADMIN' ); ?>:
				</span>
			</td>
			<td><fieldset id="jform_bcc2admin" class="radio btn-group">		
			<?php echo $this->lists['bcc2admin']; ?></fieldset>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_PUBLISHED'); ?>">
					<?php echo JText::_( 'AUP_PUBLISHED' ); ?>:
				</span>
			</td>
			<td><fieldset id="jform_bcc2admin" class="radio btn-group">		
			<?php echo $this->lists['published']; ?></fieldset>
			</td>
		</tr>	

		</tbody>
		</table>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="redirect" value="templateinvite" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="category" value="<?php echo $row->category; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
<div class="clr"></div>