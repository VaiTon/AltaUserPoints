<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 - Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
$doc	=  JFactory::getDocument();	
$app      = JFactory::getApplication();	

//$cparams 			= JComponentHelper::getParams( 'com_altauserpoints' );
//$showinformations 	= $caprams->get('showinformations',1);	
	
	
	?>
	
	<div class="page-header">
			<h1><?php //echo $this->escape($params['page_heading']); ?></h1>
		</div>

	<div id="invite-form">
		<form action="<?php echo JRoute::_( 'index.php?option=com_altauserpoints&view=invite&Itemid='.$app->input->get('Itemid', '') , false );?>" method="post" name="inviteForm" id="inviteForm" class="form-validate form-horizontal">
		<fieldset>
		<legend><?php echo JText::_( 'AUP_INVITEYOURFRIENDSTOSIGNUP' ); ?></legend>
		<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php if ( $this->params[ 'showinformations' ] ) { ?>
			<span style="text-decoration: underline;"><?php echo JText::_( 'AUP_INFORMATION' ); ?></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_MAXEMAILPERINVITE' ); ?> <b><?php echo $this->params[ 'maxemailperinvite']; ?></b></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_DELAYBETWEENINVITES' ); ?> <b><?php echo $this->params[ 'delaybetweeninvites' ]; ?> <?php echo JText::_( 'AUP_SECONDS' ); ?></b></span><br/>
			<span class="small"><?php echo JText::_( 'AUP_MAXINVITESPERDAY' ); ?> <b><?php echo $this->params['maxinvitesperday' ]; ?></b></span><br/>
			<?php if ( $this->referreid && $this->points ) { ?>
			<span class="small"><?php echo JText::_( 'AUP_POINTSEARNEDPERSUCCESSFULLINVITE' ); ?> <b><?php echo $this->points ; ?></b></span>
			<?php } ?>
		<?php } ?>
		</div>
	
    
		<div class="control-group">
			<div class="control-label">             
					<div class="control-label">
					 <?php  echo $this->form->getLabel('other_recipients') ?>                
					</div>
					<div class="controls">
					 <?php  echo $this->form->getInput('other_recipients')  	?>
					</div>
				   </div>
		</div>
		<div class="control-group">
			<div class="control-label">             
					<div class="control-label">
					 <?php  echo $this->form->getLabel('sender') ?>                
					</div>
					<div class="controls">
					 <?php  echo $this->form->getInput('sender')  	?>
					</div>
				   </div>
		</div>
		<div class="control-group">
			<div class="control-label">             
					<div class="control-label">
					 <?php  echo $this->form->getLabel('custommessage') ?>                
					</div>
					<div class="controls">
					 <?php  echo $this->form->getInput('custommessage')  	?>
					</div>
				   </div>
		</div>
		<?php 
				JPluginHelper::importPlugin('captcha');
				?>
                <div class="control-group">
				<div class="control-label">             
					<div class="control-label">
					 <?php  //echo $this->form->getLabel('captcha') ?>                
					</div>
					<div class="controls">
					 <?php  echo $this->form->getInput('captcha')  	?>
					</div>
				   </div>
                    </div>
			
		<div class="form-actions">
			<button type="submit" class="btn btn-primary validate"><?php echo JText::_('AUP_SEND');?></button>
			<a class="btn" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
		</div>		
		<?php if ( $this->referrer_link && $this->user_name!='' ) { ?>		 
		
		<p>&nbsp;</p>
		<div class="alert alert-info"><span aria-hidden="true" class="icon-info-circle"></span>&nbsp;&nbsp;<?php echo JText::_( 'AUP_INVITATION_LINK' ); ?><br /><input type="text" name="referrer_link" id="referrer_link" onfocus="select();" readonly="readonly" class="inputbox" value="<?php echo $this->referrer_link; ?>" /></div>		 
		<?php } 
		
		?>
		<input type="hidden" name="option" value="com_altauserpoints" />
		<input type="hidden" name="controller" value="invite" />
		<input type="hidden" name="referreid" value="<?php echo $this->referreid; ?>" />
		<input type="hidden" name="task" value="sendinvite" />
		<?php echo JHtml::_('form.token'); ?>
		</fieldset>	
	</form>		
	</div>

<?php 
	/** 
	*
	*  Provide copyright on frontend
	*  If you remove or hide this line below,
	*  please make a donation if you find AltaUserPoints useful
	*  and want to support its continued development.
	*  Your donations help by hardware, hosting services and other expenses that come up as we develop,
	*  protect and promote AltaUserPoints and other free components.
	*  You can donate on https://www.nordmograph.com/extensions
	*
	*/	
	getCopyrightNotice ();
?>