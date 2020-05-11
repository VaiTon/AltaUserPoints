<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * extension menu created by Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerRegisterqrcode extends AltauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
		parent::__construct( );
	}
	
	//function registerQRcode()
	public function display($cachable = false, $urlparams = false)
	{

		$model      = $this->getModel ( 'registerqrcode' );
		$view       = $this->getView  ( 'registerqrcode','html' );
		
		$couponCode = JFactory::getApplication()->input->get('QRcode', '', 'string');
		$trackIDNew = uniqid('', true);		
		$trackID = JFactory::getApplication()->input->get('trackID', $trackIDNew, 'string');
		$model->trackQRcode($trackID, $couponCode);
		
		$view->assign( 'couponCode', $couponCode );
		$view->assign( 'trackID', $trackID );
		
		$view->display();
	}
	
	public function attribqrcode() 
	{

		$model      = $this->getModel ( 'registerqrcode' );
		$view       = $this->getView  ( 'registerqrcode','html' );
	
		$points = $model->attribPoints();
		
		$view->assign( 'points', $points );
		
		$view->displayResult();
	}


}
?>