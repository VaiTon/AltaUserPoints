<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerAddressbook extends AltauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
	
		parent::__construct( );
		
	}	

	/**
	* Show import contacts
	*/
	public function display() {
		
		$view       = $this->getView  ( 'invite','html' );
		
		$view->_display_addressbook();
	}
	
}
?>