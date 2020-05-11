<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class JTableRules extends JTable
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id = null;
	/** @var string */
	var $rule_name = '';
	/** @var string */
	var $rule_description = '';
	/** @var string */
	var $rule_plugin = '';
	/** @var string */
	var $plugin_function = '';
	/** @var int */
	var $access = '';
	/** @var string */
	var $component = '';
	/** @var string */
	var $calltask = '';
	/** @var string */
	var $taskid = '';
	/** @var int */
	var $points = '';
	var $points2 = '';
	var $percentage = '';
	/** @var datetime */
	var $rule_expire = '';
	/** @var string */
	var $sections = '';
	var $categories = '';
	var $content_items = '';
	var $exclude_items = '';
	/** @var int */
	var $published = '';
	var $system = '';
	var $duplicate = '';
	var $blockcopy = '';
	var $autoapproved = '';
	var $fixedpoints = '';
	/** @var string */
	var $category = '';
	/** @var int */
	var $displaymsg = '';
	/** @var string */
	var $msg = '';
	/** @var int */
	var $method = '';
	var $notification = '';
	/** @var string */
	var $emailsubject = '';
	var $emailbody = '';
	/** @var int */
	var $emailformat = '';
	var $bcc2admin = '';
	var $type_expire_date = '';
	var $chain = '';
	var $linkup = '';
	var $displayactivity = '';
	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__alpha_userpoints_rules', 'id', $db);
	}
}
?>
