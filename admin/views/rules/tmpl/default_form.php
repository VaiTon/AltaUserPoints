<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$row = $this->row;
$pos = strpos( $row->plugin_function, 'sysplgaup_' );
$disabled = ( $pos === false ) ? 0 : 1;
$duplicate = $row->duplicate;
$system = $row->system;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{		
		Joomla.submitform(task, document.getElementById('rule-form'));
	}
</script>
<div id="j-main-container">
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="rule-form" class="form-validate">
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'page-details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-details', JText::_('AUP_DETAILS', true)); ?>
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_ID'); ?>">
						<?php echo JText::_( 'AUP_ID' ); ?>:
					</span>
				</div>
				<div class="controls">
				<?php echo "<font color='green'>" . $row->id . "</font>"; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_CATEGORY'); ?>">
						<?php echo JText::_( 'AUP_CATEGORY' ); ?>:
					</span>
				</div>
				<div class="controls">
				<?php echo $this->lists['category']; ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php if ( $disabled ) : echo JText::_('AUP_THISFIELDCANBEMODIFIEDINLANGUAGEFILE'); else : echo JText::_('AUP_RULENAME'); endif; ?>">
						<?php echo JText::_( 'AUP_RULENAME' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
					if ( !$disabled || $duplicate )  { ?>
					<input class="inputbox" type="text" name="rule_name" id="rule_name" size="80" maxlength="255" value="<?php echo JText::_($row->rule_name); ?>" />
					<?php
					} else {
						echo "<font color='green'>" . JText::_($row->rule_name) . "</font>"; 
						?>
						<input type="hidden" name="rule_name" value="<?php echo $row->rule_name; ?>" />
						<?php
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php if ( $disabled ) : echo JText::_('AUP_THISFIELDCANBEMODIFIEDINLANGUAGEFILE'); else : echo JText::_('AUP_DESCRIPTION'); endif; ?>">
						<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
					if ( !$disabled || $duplicate )  { ?>
					<input class="inputbox" type="text" name="rule_description" id="rule_description" size="80" maxlength="255" value="<?php echo JText::_($row->rule_description); ?>" />
					<?php
					} else {
						echo "<font color='green'>" . JText::_($row->rule_description) . "</font>"; 
						?>
						<input type="hidden" name="rule_description" value="<?php echo $row->rule_description; ?>" />
						<?php
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php if ( $disabled ) : echo JText::_('AUP_THISFIELDCANBEMODIFIEDINLANGUAGEFILE'); else : echo JText::_('AUP_PLUGIN_TYPE_DESCRIPTION'); endif; ?>">
						<?php echo JText::_( 'AUP_PLUGIN_TYPE' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
					if ( !$disabled )  { ?>
					<input class="inputbox" type="text" name="rule_plugin" id="rule_plugin" size="20" maxlength="50" value="<?php echo JText::_($row->rule_plugin); ?>" />
					<?php
					} else {
						echo "<font color='green'>" . JText::_($row->rule_plugin) . "</font>"; 
						?>
						<input type="hidden" name="rule_plugin" value="<?php echo $row->rule_plugin; ?>" />
						<?php
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php if ( $disabled ) : echo JText::_('AUP_UNIQUE_NAME_FUNCTION_RULE_DESCRIPTION'); else : echo JText::_('AUP_UNIQUE_NAME_FUNCTION_RULE_DESCRIPTION'); endif; ?>">
						<?php echo JText::_( 'AUP_UNIQUE_NAME_FUNCTION_RULE' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php 
					if ( !$disabled )  { ?>
					<input class="inputbox" type="text" name="plugin_function" id="plugin_function" size="20" maxlength="50" value="<?php echo $row->plugin_function; ?>" />
					<?php
					} else {
						echo "<font color='green'>" . $row->plugin_function . "</font>"; 
						?>
						<input type="hidden" name="plugin_function" value="<?php echo $row->plugin_function; ?>" />
						<?php
					}
					?>
				</div>
			</div>
			<?php		
			 if (   $row->plugin_function=='sysplgaup_changelevel1' 
					   || $row->plugin_function=='sysplgaup_changelevel2' 
						|| $row->plugin_function=='sysplgaup_changelevel3' 				 
						) {
			 
			 ?>		
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_CHANGELEVELTO'); ?>">
						<?php echo JText::_( 'AUP_CHANGELEVELTO' ); ?>:
					</span>
				</div>
				<div class="controls">	
					<?php echo $this->lists['groups']; ?>
					
				</div>
			</div>
			<?php } ?>

			<?php		
			 if ( $row->plugin_function!='sysplgaup_newregistered'
				&& $row->plugin_function!='sysplgaup_referralpoints' 
				 && $row->plugin_function!='sysplgaup_excludeusers' 
				  && $row->plugin_function!='sysplgaup_emailnotification' 
				   && $row->plugin_function!='sysplgaup_winnernotification' 
					 && $row->plugin_function!='sysplgaup_changelevel1' 
					  && $row->plugin_function!='sysplgaup_changelevel2' 
					   && $row->plugin_function!='sysplgaup_changelevel3' ) {	 
			 ?>		
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_USERLEVEL'); ?>">
						<?php echo JText::_( 'AUP_USERLEVEL' ); ?>:
					</span>
				</div>
				<div class="controls">
					<?php echo JHtml::_('access.level', 'access', $row->access);//Migus changed instead JHTML::_('list.accesslevel',  $row); ?>
				</div>
			</div>
			<?php 		
			} else { 		
			?>
			<input type="hidden" name="access" value="2" />		
			<?php				
			}		
			
			// show categories field for rule plgaup_readarticle_by_cat (added in 1.8.1)
			if ( substr($row->plugin_function, 0, 25) == 'plgaup_readarticle_by_cat' ) {
			$categories = array();
			$categories = explode(',',$row->categories);
			?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'JCATEGORIES' ); ?>::<?php echo JText::_('JCATEGORIES'); ?>">
						<?php echo JText::_( 'JCATEGORIES' ); ?>:
					</span>
				</div>
				<div class="controls">
					<select name="categories[]" class="inputbox" multiple="multiple">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('category.categories', 'com_content'), 'value', 'text', $categories);?>
					</select>
				</div>
			</div>
			<?php
			} 
			?>		
			<?php		
			 if ( $row->plugin_function=='sysplgaup_changelevel1' 
					|| $row->plugin_function=='sysplgaup_changelevel2' 
					  || $row->plugin_function=='sysplgaup_changelevel3' 		
						|| $row->plugin_function=='sysplgaup_unlockmenus'
						  || substr($row->plugin_function, 0, 22) == 'sysplgaup_unlockmenus_'	 
						) {
			  
			 ?>		
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_NUMBEROFPOINTSNECESSARY'); ?>">
						<?php echo JText::_( 'AUP_POINTSREACHED' ); ?>
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="points2" id="points2" size="10" maxlength="30" value="<?php echo $row->points2; ?>" /> <?php echo  JText::_( 'AUP_NUMBEROFPOINTSNECESSARY' ); ?>	
				</div>
			</div>
			<?php
			} elseif ( $row->plugin_function=='plgaup_kunena_message_thankyou' )  {
			?>		
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_KU_POINT_USER_TARGET' ); ?>::<?php echo JText::_('AUP_KU_POINT_USER_TARGET_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_KU_POINT_USER_TARGET' ); ?>:
					</span>
				</div>
				<div class="controls">
				<input class="inputbox" type="text" name="points2" id="points2" size="10" maxlength="30"   value="<?php echo $row->points2; ?>" /> <?php echo JText::_('AUP_KU_POINT_USER_TARGET_DESCRIPTION'); ?>
				</div>
			</div>
			<?php
			} else {
			?>		
			<input type="hidden" name="points2" value="<?php echo $row->points2; ?>" />
			<?php
			} 
			?>		
			<?php
			if ( ( $row->fixedpoints || $row->plugin_function=='' ) && $row->plugin_function!='sysplgaup_changelevel1' && $row->plugin_function!='sysplgaup_changelevel2' && $row->plugin_function!='sysplgaup_changelevel3' ) {    // $row->plugin_function = '' if new rule
			?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_ATTRIB_X_POINTS_TO_THIS_RULE'); ?>">
						<?php echo JText::_( 'AUP_POINTS' ); if ($row->percentage) echo " (" . JText::_( 'AUP_PERCENTAGE' ) . ")"; ?> :
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="points" id="points" size="10" maxlength="30" value="<?php echo $row->points; ?>" />
					<?php if ($row->percentage) echo " <b> %</b>" ; ?>
				</div>
			</div>
			<?php
			} else {
			?>		
			<input type="hidden" name="points" value="<?php echo $row->points; ?>" />
			<?php
			} 
			?>
			
			<?php if ( !$disabled || substr($row->plugin_function, 0, 17) == 'sysplgaup_custom_' )  { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_FIXED_POINTS_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_FIXED_POINTS' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_fixedpoints" class="radio btn-group">		
						<?php echo $this->lists['fixedpoints']; ?>
					</fieldset>
				</div>
			</div>
			<?php } else { ?>
				<input type="hidden" name="fixedpoints" value="<?php echo $row->fixedpoints; ?>" />
			<?php } ?>
			
			<?php if ( !$disabled )  { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_PERCENTAGE'); ?>">
						<?php echo JText::_( 'AUP_PERCENTAGE' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_percentage" class="radio btn-group">	
						<?php echo $this->lists['percentage']; ?>
					</fieldset>
				</div>
			</div>
			<?php } else { ?>
				<input type="hidden" name="percentage" value="<?php echo $row->percentage; ?>" />
			<?php } ?>
			
			<?php
			if ( $row->plugin_function!='sysplgaup_newregistered'
				 && $row->plugin_function!='sysplgaup_excludeusers'
				  && $row->plugin_function!='sysplgaup_emailnotification'
				   && $row->plugin_function!='sysplgaup_winnernotification'
					  && $row->plugin_function!='sysplgaup_changelevel1' 
					   && $row->plugin_function!='sysplgaup_changelevel2' 
						&& $row->plugin_function!='sysplgaup_changelevel3' 
						 && $row->plugin_function!='sysplgaup_archive') {
			?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_EXPIRE_DESC'); ?>">
						<?php echo JText::_( 'AUP_EXPIRE' ); ?>:
					</span>
				</div>
				<div class="controls">
						<?php echo JHTML::_('calendar', $row->rule_expire, 'rule_expire', 'rule_expire', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
						<?php echo "<BR /><BR />" . $this->lists['type_expire_date'] ; ?>
				</div>
			</div>
			<?php 
			} else { 
			?>
			<input type="hidden" name="rule_expire" value="0000-00-00 00:00:00" />		
			<?php 
			}
			?>
			<?php if ( $row->plugin_function=='sysplgaup_excludeusers' ) { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_EXCLUDEUSERIDDESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_EXCLUDEUSERID' ); ?>:
					</span>
				</div>
				<div class="controls">
				<input class="inputbox" type="text" name="exclude_items" id="exclude_items" size="100" value="<?php echo $row->exclude_items; ?>" />
				</div>
			</div>
			<?php }?>		
			<?php if ( $row->plugin_function=='sysplgaup_winnernotification' ) { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_EMAILDAMINSNOTIFICATION'); ?>">
						<?php echo JText::_( 'AUP_EMAILDAMINS' ); ?>:
					</span>
				</div>
				<div class="controls">
				<input class="inputbox" type="text" name="content_items" id="content_items" size="100" value="<?php echo $row->content_items; ?>" />
				</div>
			</div>
			<?php }?>		
			<?php if ( $row->plugin_function=='sysplgaup_inactiveuser' ) { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_INACTIVE_PERIOD'); ?>">
						<?php echo JText::_( 'AUP_INACTIVE_PERIOD' ); ?>:
					</span>
				</div>
				<div class="controls">
				<?php echo $this->lists['inactive_preset_period'] ; ?>
				</div>
			</div>
			<?php }?>	
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_DISPLAYACTIVITYDESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_DISPLAYACTIVITY' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_displayactivity" class="radio btn-group">	
						<?php echo $this->lists['displayactivity']; ?>
					</fieldset>
				</div>
			</div>	
			<?php if ( $row->plugin_function!='sysplgaup_newregistered' ) { ?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_PUBLISHED'); ?>">
						<?php echo JText::_( 'AUP_PUBLISHED' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_published" class="radio btn-group">	
						<?php echo $this->lists['published']; ?>
					</fieldset>
				</div>
			</div>
			<?php } else { ?>
			<input type="hidden" name="published" value="1" />		
			<?php }?>
			<?php
			switch ( $row->plugin_function ) {
				case 'sysplgaup_newregistered':
				case 'sysplgaup_excludeusers':
				case 'sysplgaup_emailnotification':
				case 'sysplgaup_winnernotification':
				case 'sysplgaup_archive':
				case 'sysplgaup_unlockmenus':
				case (substr($row->plugin_function, 0, 22) == 'sysplgaup_unlockmenus_'):
					echo "<input type=\"hidden\" name=\"autoapproved\" value=\"1\" />";
					break;										
				default:
					?>
				<div class="control-group">
					<div class="control-label">
						<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_AUTOAPPROVED'); ?>">
							<?php echo JText::_( 'AUP_AUTOAPPROVED' ); ?>:
						</span>
					</div>
					<div class="controls" style="margin-left:0px;">
						<fieldset id="jform_autoapproved" class="radio btn-group">		
							<?php echo $this->lists['autoapproved']; ?>
						</fieldset>
					</div>
				</div>
			<?php
			}					 

			switch ( $row->plugin_function ) {
				case 'sysplgaup_newregistered':
				case 'sysplgaup_excludeusers':
				case 'sysplgaup_archive':
				case 'sysplgaup_couponpointscodes':
				case 'sysplgaup_changelevel1' :
				case 'sysplgaup_changelevel2' :
				case 'sysplgaup_changelevel3' :
				case 'sysplgaup_winnernotification' :
				case 'sysplgaup_unlockmenus':
				case (substr($row->plugin_function, 0, 22) == 'sysplgaup_unlockmenus_'):
					echo '<input type="hidden" name="method" value="4" />';
					break;
				default:
			?>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_METHOD_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_METHOD' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_methods" class="radio btn-group">
						<?php echo $this->lists['methods']; ?> 
					 </fieldset>
				</div>
			</div>
			<?php			
			}			
			switch ( $row->plugin_function ) {
				case 'sysplgaup_excludeusers':
				case 'sysplgaup_raffle':
				case 'sysplgaup_archive':
				case 'sysplgaup_couponpointscodes':
				case 'sysplgaup_winnernotification' :
					// this rules no needs linkup
					echo '<input type="hidden" name="linkup" value="0" />';
					break;
				default:
			?>			
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_LINK_UP_DESC'); ?>">
						<?php echo JText::_( 'AUP_LINK_UP' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_linkup" class="radio btn-group">
						<?php echo $this->lists['linkup']; ?> 
					 </fieldset>
				</div>
			</div>

			<?php  } ?>		
		</fieldset>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php if (  $row->plugin_function != 'sysplgaup_referralpoints'  ) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-message', JText::_('AUP_MESSAGE', true)); ?>
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_DISPLAY_MSG'); ?>">
						<?php echo JText::_( 'AUP_DISPLAY_MSG' ); ?>:
					</span>
				</div>
				<div class="controls" style="margin-left:0px;">
					<fieldset id="jform_displaymsg" class="radio btn-group">
						<?php echo $this->lists['displaymsg']; ?>
					</fieldset>
				</div>
			</div>		
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_('AUP_MESSAGE_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_MESSAGE' ); ?>:
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="msg" id="msg" size="68" maxlength="255" value="<?php echo $row->msg; ?>" />
				</div>
			</div>		
		</fieldset>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>
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
				    <?php            			
					$editor		=  JFactory::getEditor();
					$paramsEditor = array('relative_urls' => '0');
					echo $editor->display( 'emailbody',  $row->emailbody , '100%', '200', '75', '20', true, null, null, null, $paramsEditor );
					//echo $editor->display( 'emailbody',  $row->emailbody , '100%', '200', '75', '20', true );
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

	<?php 
	if ( substr($row->plugin_function, 0, 21) == 'sysplgaup_unlockmenus' || substr($row->plugin_function, 0, 19) == 'plgaup_clickonmenus' ) :

    $lang = JFactory::getLanguage();
    $lang->load( 'com_modules', JPATH_ADMINISTRATOR);		
		
	?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-assignment', JText::_('COM_MODULES_MENU_ASSIGNMENT', true)); ?>
			<div class="">
				<?php echo $this->loadTemplate('assignment'); ?>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="option" value="com_altauserpoints" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="system" value="<?php echo $row->system; ?>" />
		<input type="hidden" name="duplicate" value="<?php echo $row->duplicate; ?>" />
		<input type="hidden" name="blockcopy" value="<?php echo $row->blockcopy; ?>" />
		<input type="hidden" name="redirect" value="rules" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="chain" value="<?php echo $row->chain; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
	</div><!-- form -->
<div class="clr"></div>
</div><!-- row-fluid -->