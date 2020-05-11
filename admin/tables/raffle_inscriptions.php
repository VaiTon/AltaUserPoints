<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTableRaffle_inscriptions extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var int */
	var $raffleid = '';
	/** @var int */
	var $userid = '';
	/** @var string */
	var $ticket = '';
    var $referredraw = '';
	/** @var datetime */
    var $inscription = '';
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints_raffle_inscriptions', 'id', $db);
	}
}
?>