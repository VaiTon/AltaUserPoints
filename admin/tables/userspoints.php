<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTableuserspoints extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var int */
	var $userid = '';
	/** @var string */
	var $referreid = '';
	/** @var string */
	var $upnid = '';
	/** @var string */
	var $points = '';
	/** @var string */
	var $max_points = '';
	/** @var datetime */
	var $last_update = '';
	/** @var string */
	var $referraluser = '';
	/** @var int */	
	var $referrees = '';
	/** @var int */
	var $blocked = '';
	/** @var string */
	var $avatar = '';	
	/** @var int */
	var $levelrank = '';
	/** @var date */
	var $leveldate = '';
	
	var $profileviews = '';
	var $published = 1;
	var $shareinfos = 1;	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints', 'id', $db);
	}	

}
?>
