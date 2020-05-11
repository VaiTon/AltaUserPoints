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
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
function generatecode() {
   
    var length=8;
    var sCode = "";
   
    for (i=0; i < length; i++) {    
        numI = getRandomNum();
        while (checkPunc(numI))	{ numI = getRandomNum(); }        
        sCode = sCode + String.fromCharCode(numI);
    }
    
    document.adminForm.couponcode.value = sCode.toUpperCase();;    
    return true;
}

function getRandomNum() {        
    // between 0 - 1
    var rndNum = Math.random()
    // rndNum from 0 - 1000    
    rndNum = parseInt(rndNum * 1000);
    // rndNum from 33 - 127        
    rndNum = (rndNum % 94) + 33;            
    return rndNum;
}

function checkPunc(num) {
    
    if ((num >=33) && (num <=47)) { return true; }
    if ((num >=58) && (num <=64)) { return true; }    
    if ((num >=91) && (num <=96)) { return true; }
    if ((num >=123) && (num <=126)) { return true; }
    
    return false;
}

Joomla.submitbutton = function(task)
{
	//if (task == 'cpanel' || document.formvalidator.isValid(document.id('couponcodes-form'))) {
		Joomla.submitform(task, document.getElementById('couponcode-form'));
	//}
	//else {
		//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	//}
}

</Script>
<form action="index.php?option=com_altauserpoints" method="post" name="adminForm" id="couponcode-form" class="form-validate">
<div class="form-horizontal">
<fieldset>
	  <legend><?php echo JText::_( 'AUP_DETAILS' ); ?></legend>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_CODE' ); ?>::<?php echo JText::_('AUP_CODE'); ?>">
						<?php echo JText::_( 'AUP_CODE' ); ?>:
					</span>
				</div>
				<div class="controls">
					<div class="input-append">
						<input class="inputbox" type="text" name="couponcode" id="couponcode" size="20" maxlength="20" value="<?php echo $row->couponcode; ?>" />
						<a name="autogenerate" id="autogenerate" class="btn btn-primary" onclick="javascript:generatecode()"><i class="icon-wand"></i></a>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>::<?php echo JText::_('AUP_DESCRIPTION'); ?>">
						<?php echo JText::_( 'AUP_DESCRIPTION' ); ?>:
					</span>
				</div>
				<div class="controls">
				<input class="inputbox" type="text" name="description" id="description" size="100" maxlength="255" value="<?php echo $row->description; ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'JCATEGORIES' ); ?>::<?php echo JText::_('JCATEGORIES'); ?>">
						<?php echo JText::_( 'JCATEGORIES' ); ?>:
					</span>
				</div>
				<div class="controls">
					<select name="category" class="inputbox">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('category.categories', 'com_altauserpoints'), 'value', 'text', $row->category);?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_POINTS' ); ?>::<?php echo JText::_('AUP_POINTS'); ?>">
						<?php echo JText::_( 'AUP_POINTS' ); ?>:
					</span>
				</div>
				<div class="controls">
					<input class="inputbox" type="text" name="points" id="points" size="20" value="<?php echo $row->points; ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_EXPIRE' ); ?>::<?php echo JText::_('AUP_EXPIRE'); ?>">
						<?php echo JText::_( 'AUP_EXPIRE' ); ?>:
					</span>
				</div>
				<div class="controls">
				<?php echo JHTML::_('calendar', $row->expires, 'expires', 'expires', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_PUBLIC' ); ?>::<?php echo JText::_('AUP_PUBLIC'); ?>">
						<?php echo JText::_( 'AUP_PUBLIC' ); ?>:
					</span>
				</div>
				<div class="controls">
					<fieldset id="jform_public" class="radio btn-group"><?php echo $lists['public']; ?></fieldset>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'AUP_PRINTABLE' ); ?>::<?php echo JText::_('AUP_PRINTABLE'); ?>">
						<?php echo JText::_( 'AUP_PRINTABLE' ); ?>:
					</span>
				</div>
				<div class="controls">
				<fieldset id="jform_printable" class="radio btn-group"><?php echo $lists['printable']; ?></fieldset>
				</div>
			</div>
			 <?php if ( $row->couponcode && $row->printable ) { ?>
			<div class="control-group">
				<div class="control-label">
				</div>
				<div class="controls">
				
							<?php 
							$QRcode250 = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/assets/coupons/QRcode/250/'. strtoupper($row->couponcode) .'.png';
							if ( file_exists($QRcode250)) {
							?>
				<div>
							<img src="<?php echo JURI::base(); ?>components/com_altauserpoints/assets/coupons/QRcode/250/<?php echo strtoupper($row->couponcode); ?>.png" alt="" align="absmiddle" />
				</div>
				<div>
							<a href="<?php echo JURI::base()."components/com_altauserpoints/assets/coupons/QRcode/250/".strtoupper($row->couponcode).".png";?>"><?php echo JURI::base(); ?>components/com_altauserpoints/assets/coupons/QRcode/250/<?php echo strtoupper($row->couponcode); ?>.png</a>
				</div>
							<?php } ?>
				</div>
			</div>
         <?php } ?>
			
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="redirect" value="couponcodes" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</fieldset>
</div>
</form>
<div class="clr"></div>