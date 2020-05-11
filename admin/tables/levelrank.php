<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTablelevelrank extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var string */
	var $rank = '';
	/** @var string */
	var $description = '';
	/** @var int */
	var $levelpoints = '';
	/** @var int */
	var $typerank = '';
	/** @var string */
	var $icon = '';
	/** @var string */
	var $image = '';
	/** @var int */
	var $gid = '';
	/** @var int */
	var $ruleid = '';
	/** @var int */
	var $ordering = '';	
	var $category = '1';
    var $notification = '';
	/** @var string */
    var $emailsubject = '';
    var $emailbody ='';
	/** @var int */
    var $emailformat = '';
    var $bcc2admin = '';
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints_levelrank', 'id', $db);
	}	

}
?>
