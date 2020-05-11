<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTableRaffle extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var string */
	var $description = '';
	/** @var int */
	var $inscription = '';
	/** @var int */
	var $rafflesystem = '';
	/** @var int */
	var $numwinner = '1';
	/** @var int */
	var $couponcodeid1 = '';
	/** @var int */
	var $couponcodeid2 = '';
	/** @var int */
	var $couponcodeid3 = '';
	/** @var int */
	var $sendcouponbyemail = '';
	/** @var int */
	var $pointstoparticipate = '';
	var $removepointstoparticipate = '';
	/** @var int */
	var $pointstoearn1 = '';
	/** @var int */
	var $pointstoearn2 = '';
	/** @var int */
	var $pointstoearn3 = '';
	/** @var datetime */
	var $raffledate = '';
	/** @var int */
	var $winner1 = '';
	var $winner2 = '';
	var $winner3 = '';
	/** @var int */
	var $published = '1';
	/** @var string */
	var	$link2download1 = '';
	var	$link2download2 = '';
	var	$link2download3 = '';
	/** @var int */
	var $multipleentries = '';
	var $category = '1';
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints_raffle', 'id', $db);
	}
}
?>
