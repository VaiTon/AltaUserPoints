<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2015-2016. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if ( $params->get( 'textbefore' ) )
{
	echo $params->get( 'textbefore' ); 
}
?>
<form name="modAUP_CPsCouponForm" method="post" action="">
<div class="control-group">
	<div class="controls">
<div class="input-prepend input-append">
						<span class="add-on">
							<span class="icon-pencil tip" title="<?php echo JText::_( 'MODAUP_CP_LABEL_INPUT' ) ; ?>">
							</span><label for="modAUP_CPsCouponValue" class="element-invisible"><?php echo JText::_( 'MODAUP_CP_LABEL_INPUT' ) ; ?></label>
						</span>
	  <input name="modAUP_CPsCouponValue" type="text" id="modAUP_CPsCouponValue" value="<?php echo JText::_( 'MODAUP_CP_LABEL_INPUT' ) ; ?>" onfocus="this.value='';" class="input-small<?php echo $params->get( 'pageclass_sfx' ); ?>" />
	</div></div>
</div>
<div class="control-group">
	<div class="controls">
		<button type="submit" class="btn btn-primary"><?php echo JText::_('MODAUP_CP_LABEL_BUTTON'); ?></button>
	</div>
</div>
</form>
<?php 
if ( $params->get( 'textbefore' ) )
{
	echo $params->get( 'textafter' ); 
}
?>