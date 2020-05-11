<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<br />
<div class="printcoupon"><a href="javascript:window.print()"><?php echo  JText::_( 'AUP_PRINT' ) ; ?></a></div>
<br /><br />
<div class="cut"></div>
<div class="coupon"><div class="couponcolor">
	<div class="infocoupon">	
	<h1><?php echo $this->couponcode; ?></h1>
	<h2><?php echo getFormattedPointsAdm($this->points) . ' ' . JText::_( 'AUP_POINTS' ); ?> </h2>
	</div>
	<div class="qrcode"><img src="<?php echo JURI::base(); ?>components/com_altauserpoints/assets/coupons/QRcode/250/<?php echo strtoupper($this->couponcode); ?>.png" alt="" align="absmiddle" /></div>
	<div class="infosite">
	<?php echo $this->sitename; ?> - <?php echo JURI::root(); ?>
	</div>
</div></div>