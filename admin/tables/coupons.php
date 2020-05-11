<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTableCoupons extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var string */
	var $description = '';
	/** @var string */
	var $couponcode = '';
	/** @var int */
	var $points = '';
	/** @var datetime */
	var $expires = '';
	/** @var int */
	var $public = '1';
    var $category = '1';
    var $printable = '0';
  
  	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints_coupons', 'id', $db);
	}
}
?>
