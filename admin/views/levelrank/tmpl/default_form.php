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

$pathimagedefault = JURI::root() ;

$pathicon = JURI::root() . 'components/com_altauserpoints/assets/images/awards/icons/';
$pathiconbase = 'components/com_altauserpoints/assets/images/awards/icons';
$javascript  = 'onchange="changeDisplayIcon();"';

$pathimage = JURI::root() . 'components/com_altauserpoints/assets/images/awards/large/';
$pathimagebase = 'components/com_altauserpoints/assets/images/awards/large';
$javascript2  = 'onchange="changeDisplayImage();"';

JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
<!--
function changeDisplayIcon() {
	if (document.adminForm.icon.value !='') {
		document.adminForm.imagelib.src='<?php echo $pathicon; ?>' + document.adminForm.icon.value;
	} else {
		document.adminForm.imagelib.src='<?php echo $pathimagedefault; ?>images/blank.png';
	}
}
function changeDisplayImage() {
	if (document.adminForm.image.value !='') {
		document.adminForm.imagelib2.src='<?php echo $pathimage; ?>' + document.adminForm.image.value;
	} else {
		document.adminForm.imagelib2.src='<?php echo $pathimagedefault; ?>images/blank.png';
	}
}
Joomla.submitbutton = function(task)
{
	//if (task == 'cpanel' || document.formvalidator.isValid(document.id('levelrank-form'))) {
		Joomla.submitform(task, document.getElementById('levelrank-form'));
	//}
	//else {
		//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	//}
}
//-->
</script>
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="levelrank-form" class="form-validate">
<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'page-details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-details', JText::_('AUP_DETAILS', true)); ?>

<div class="width-100 fltrt">
	<fieldset class="adminform">
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
			<input class="inputbox" type="text" name="rank" id="rank" size="40" maxlength="50" value="<?php echo $row->rank; ?>" />			
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>::<?php echo JText::_('AUP_DESCRIPTION'); ?>">
					<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
				</span>
			</td>
			<td>
			<input class="inputbox" type="text" name="description" id="description" size="100" maxlength="255" value="<?php echo $row->description; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'JCATEGORIES' ); ?>::<?php echo JText::_('JCATEGORIES'); ?>">
					<?php echo JText::_( 'JCATEGORIES' ); ?>:
				</span>
			</td>
			<td>		
				<select name="category" class="inputbox">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.categories', 'com_altauserpoints'), 'value', 'text', $row->category);?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_TYPE' ); ?>::<?php echo JText::_('AUP_TYPE_RANK_EXPLAIN'); ?>">
					<?php echo JText::_( 'AUP_TYPE' ); ?>:
				</span>
			</td>
			<td>
			<?php echo $lists['typerank']; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_DESCRIPTION_POINTS_ON_RANK'); ?>">
					<?php echo JText::_( 'AUP_POINTS' ); ?>:
				</span>
			</td>
			<td>
				<input class="inputbox" type="text" name="levelpoints" id="levelpoints" size="20" value="<?php echo $row->levelpoints; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_ATTACH_TO_A_RULE' ); ?>::<?php echo JText::_('AUP_ATTACH_TO_A_RULE_DESC'); ?>">
					<?php echo JText::_( 'AUP_ATTACH_TO_A_RULE' ); ?>:
				</span>
			</td>
			<td>
				<?php echo $lists['rules']; ?>			
			</td>
		</tr>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_ICON' ); ?>::<?php echo JText::_('AUP_ICON_DESC'); ?>">
					<?php echo JText::_( 'AUP_ICON' ); ?>:
				</span>
			</td>
			<td>
				<?php echo JHTML::_( 'list.images', 'icon', $row->icon , $javascript, $pathiconbase); ?>&nbsp;&nbsp;
				<img src="<?php echo $pathicon; echo $row->icon;?>" name="imagelib" width="16" height="16" border="0" alt=""  style="vertical-align:middle" />
			</td>
		</tr>
		<tr>
          <td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_IMAGE' ); ?>::<?php echo JText::_('AUP_IMAGE_DESC'); ?>"> <?php echo JText::_( 'AUP_IMAGE' ); ?>: </span> </td>
          <td><?php echo JHTML::_( 'list.images', 'image', $row->image , $javascript2, $pathimagebase); ?> </td>
		  </tr>
		<tr>
			<td class="key">&nbsp;
			</td>
			<td><br />				
				<img src="<?php echo $pathimage; echo $row->image;?>" name="imagelib2" border="0" alt="" style="vertical-align:middle" />
			</td>
		</tr>
		</tbody>
		</table>
	</fieldset>
</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-notification', JText::_('AUP_EMAILNOTIFICATION', true)); ?>
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_EMAILNOTIFICATIONDESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_EMAILNOTIFICATION' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_notification" class="radio btn-group">		
					<?php echo $this->lists['notification']; ?>
					</fieldset>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_SUBJECT_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_SUBJECT' ); ?>:
					</span>
				</div>
				<div class="controls">
				  <input name="emailsubject" type="text" class="inputbox" id="emailsubject" value="<?php echo JText::_($row->emailsubject); ?>" size="68" maxlength="255">
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_MESSAGE_BODY_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_MESSAGE_BODY' ); ?>:
					</span>
				</div>
				<div class="controls">
				  <!--<textarea name="emailbody" cols="100" rows="5" class="inputbox" id="emailbody"><?php echo JText::_($row->emailbody); ?></textarea>-->
				  <?php            			
					$editor		=  JFactory::getEditor();
					echo $editor->display( 'emailbody',  $row->emailbody , '100%', '200', '75', '20', false );
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_FORMAT_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_FORMAT' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_emailformat" class="radio">		
						<?php echo $this->lists['emailformat']; ?>
					</fieldset>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_SEND_COPY_ADMIN_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_SEND_COPY_ADMIN' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_bcc2admin" class="radio btn-group">		
						<?php echo $this->lists['bcc2admin']; ?>
					</fieldset>
				</div>
			</div>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>


	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="redirect" value="levelrank" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clr"></div>
<?php echo JHtml::_('bootstrap.endTabSet'); ?>

</div>


<div class="clr"></div>
<!-- File Upload Form -->
<form action="<?php echo JURI::base(); ?>index.php?option=com_altauserpoints&amp;task=upload&amp;tmpl=component&amp;<?php echo JSession::getFormToken();?>=1" id="uploadForm" method="post" enctype="multipart/form-data" >
<div class="width-100 fltrt">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Upload File' ); ?> [ <?php echo JText::_( 'Max' ); ?>&nbsp;<?php echo (10000000 / 1000000); ?>M ]</legend>
		<fieldset class="actions">
			<input type="file" id="file-upload" name="Filedata[]" />
			<fieldset id="jform_folder" class="radio"><?php echo $this->lists['folder'] . "&nbsp;" ; ?></fieldset>
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
			<span id="upload-clear"></span>
		</fieldset>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
	</fieldset>
</div>
	<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_altauserpoints&task=editlevelrank&cid[]='.$row->id); ?>" />
</form>
