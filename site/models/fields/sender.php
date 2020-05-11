<?php 
/*
 * @component com_vmvendor
 * @copyright Copyright (C) 2010-2015 Adrien ROUSSEL Nordmograph.com
 * @license GNU/GPL Version 3
 * @Website : https://www.nordmograph.com/extensions
 */
  
 defined('JPATH_BASE') or die; 
 /* 
 jimport('joomla.html.html'); 
 jimport('joomla.form.formfield'); 
 jimport('joomla.form.helper'); 
 */ 
 JFormHelper::loadFieldClass('sql'); 
  
 /** 
  * Supports an HTML select list of options driven by SQL 
  */ 
 class JFormFieldSender extends JFormFieldSQL 
 { 
     /** 
      * The form field type. 
      */ 
     public $type = 'sender'; 
  
     /** 
      * Overrides parent's getinput method 
      */ 
     protected function getInput() 
     { 
         // Initialize variables. 
        $html = ''; 
        // Load user 
     	$user = JFactory::getUser(); 
		$html .= '<input type="text" size="5" name= "'.$this->name.'"  id="'.$this->id.'" value="'.$user->name.'" class="required" required="required" />';	

         // return the HTML 
         return $html; 
     } 
 } 
 ?> 