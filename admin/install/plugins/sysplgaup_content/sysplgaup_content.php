<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport( 'joomla.plugin.plugin' );

/**
 * AltaUserPoints Content Plugin
 *
 * @package		Joomla
 * @subpackage	AltaUserPoints
 * @since 		1.6
 */

class plgContentsysplgaup_content extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$user 	=  JFactory::getUser();
		
		if(isset($article->id))
		{
			$articleid  = $article->id;
		} 
		else 
		{
			$articleid = null;
		}		
		
		$option = $app->input->get('option', '');
		$view   = $app->input->get('view',   '');
		$print	= $app->input->get('print', '');

		
		if ($app->isAdmin()) return;
		
		if ( !$user->id || !$articleid ) {
			$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
			return;
		}
		
    	$lang = JFactory::getLanguage();
    	$lang->load( 'com_altauserpoints', JPATH_SITE);
		
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
		
		// *******************************************
		// * show current points of the current user *
		// *******************************************
		if ( preg_match('#{AUP::SHOWPOINTS}#Uis', $article->text, $m) )
		{
			$show = $m[0];
			if ( $show && @$_SESSION['referrerid'] ) {
				$currentpoints = AltaUserPointsHelper::getCurrentTotalPoints( @$_SESSION['referrerid'] );
				$currentpoints = AltaUserPointsHelper::getFPoints($currentpoints);
				if ( !$article->title || $article->title==NULL ) $article->title = $option;					
				$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", $currentpoints, $article->text );
			} else $article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
		} 
	}
}
?>