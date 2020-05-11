<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !@$this->points ) {
?>
<script type="text/javascript">
<!--
	function validateForm( frm ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			// do field validation
			if (frm.login.invalid) {
				alert( "<?php echo JText::_( 'AUP_USERNAME', true ); ?>" );
			}
			return false;
		} else {
			frm.submit();
		}
	}
// -->
</script>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="registerQRcode" id="registerQRcode" class="form-validate form-horizontal">
<fieldset>
		<div class="control-group">
			<div class="control-label">			
			<label data-original-title="<strong><?php echo JText::_( 'AUP_USERNAME' ); ?></strong><br /><?php echo JText::_( 'AUP_USERNAME' ); ?>" id="jform_login-lbl" for="login" class="hasTooltip required" title=""><?php echo JText::_( 'AUP_USERNAME' ); ?><span class="star">&nbsp;*</span></label>
			</div>
			<div class="controls">
				<input type="text" name="login" id="login" value="" class="validate-username" aria-required="true" /> <span id="statusUSR"></span>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary validate"><?php echo JText::_('AUP_SEND');?></button>
			<a class="btn" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
		</div>		
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="view" value="registerqrcode" />
	<input type="hidden" name="couponCode" value="<?php echo $this->couponCode; ?>" />
	<input type="hidden" name="trackID" value="<?php echo $this->trackID; ?>" />
	<input type="hidden" name="task" value="attribqrcode" />
	</fieldset>
</form>
<?php 
}
else
{
	$app = JFactory::getApplication();
	if( $this->points > 0 )
	{	
		if( getFormattedPoints( $this->points ) >1)
			$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_PLURAL'), getFormattedPoints( $this->points ) );
		else
			$msg = sprintf ( JText::_('AUP_CONGRATULATIONS_SINGULAR'), getFormattedPoints( $this->points ) );
		$app->enqueueMessage( $msg );
	} 
	elseif( $this->points < 0 )
	{
		if( getFormattedPoints( abs($this->points) ) >1)
			$msg = sprintf ( JText::_('AUP_X_POINTS_DEDUCTED_FROM_YOUR_ACCOUNT_PLURAL'), getFormattedPoints( abs($this->points) ) );
		else
			$msg = sprintf ( JText::_('AUP_X_POINTS_DEDUCTED_FROM_YOUR_ACCOUNT_SINGULAR'), getFormattedPoints( abs($this->points) ) );
		
		$app->enqueueMessage($msg);
	}
}
?>