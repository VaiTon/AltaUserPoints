<?php
/**
* @version 1.6.0
* @package Raffle
* @copyright (C) 2011 migusbox.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPLv2
*
* editor-xtd-button for raffle 
*
*/

defined( '_JEXEC' ) or die;
jimport( 'joomla.plugin.plugin' );

class plgButtonRaffle extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onDisplay($name) {
		$app = JFactory::getApplication();
		$template = $app->getTemplate();
		$doc = JFactory::getDocument();

        //$lang = JFactory::getLanguage();
		//$lang->load('plg_editors-xtd_raffle', JPATH_ADMINSTRATOR);

        $doc->addStyleSheet( JURI::root().'plugins/editors-xtd/raffle/raffle.css', 'text/css', null, array() );

		JHtml::_('behavior.modal');
		$link = 'index.php?option=com_altauserpoints&task=editorInsertRaffle&amp;tmpl=component&amp;e_name='.$name;
		
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_RAFFLE_BUTTON_RAFFLE'));
		$button->set('name', 'raffle');
		$button->set('options', "{handler: 'iframe', size: {x: 500, y: 580}}");
		if (!$app->isAdmin()) {
			$button = null;
		}
		return $button;
	}
}
?>