<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table class="noshow">
	<tr>
		<td width="100%" valign="top">
		<form enctype="multipart/form-data" action="index.php" method="post" name="filename">
		<table class="adminheading">
		<tr>
			<th class="install"><?php echo JText::_('AUP_IMPORT_FILE');?></th>
		</tr>
		</table>
		<table class="adminform">
		<tr>
			<th><?php echo JText::_('AUP_UPLOADFILE');?></th>
		</tr>
		<tr>
			<td align="left"><?php echo JText::_('AUP_FILENAME');?>:
			<input class="text_area" name="userfile" type="file" size="70"/>
			<input class="button" type="submit" value="<?php echo JText::_('AUP_UPLOADANDINSTALL');?>" />
			</td>
		</tr>
		</table>
		<input type="hidden" name="task" value="uploadImportFile"/>
		<input type="hidden" name="option" value="com_altauserpoints"/>
		</form>
		</td>
	</tr>
</table>
<table class="noshow">
	<tr>
		<td width="100%" valign="top">
		<form action="index.php" method="post" name="export">
		<table class="adminheading">
		<tr>
			<th class="install"><?php echo JText::_('AUP_EXPORT_FILE');?></th>
		</tr>
		</table>
		<table class="adminform">
		<tr>
			<th><?php //echo JText::_('AUP_UPLOADFILE');?></th>
		</tr>
		<tr>
			<td align="left">
			<input name="checkbox" type="checkbox" value="checkbox" checked disabled>
			<?php echo JText::_('AUP_DETAIL_ACTIVITY_TABLE');?><br />
			<input class="button" type="submit" value="<?php echo JText::_('AUP_EXPORT_FILE');?>" />
			</td>
		</tr>
		</table>
		<input type="hidden" name="task" value="ExportFileActivities"/>
		<input type="hidden" name="option" value="com_altauserpoints"/>
		</form>
		</td>
	</tr>
</table>


