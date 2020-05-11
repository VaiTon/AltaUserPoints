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

class plgContentSysplgaup_reader2author extends JPlugin
{

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=0)
	{
		$app = JFactory::getApplication();
		
		$user =  JFactory::getUser();	
		
		if(isset($article->created_by)){
			$authorid  = $article->created_by;
		} else {$authorid = null;}		
	 
		if(isset($article->id)){
			$articleid  = $article->id;
		} else {$articleid = null;}		

		if ($app->isAdmin() || $user->id==$authorid || !$articleid || !$authorid ) return;		
		
		$option = JFactory::getApplication()->input->get('option', '');
		$view   = JFactory::getApplication()->input->get('view',   '');		
		
    $lang = JFactory::getLanguage();
    $lang->load( 'com_altauserpoints', JPATH_SITE);

		switch ( $view ) {
			case 'article' :
				if ( $option=='com_content' && $limitstart==0 ) {
					
					require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
					
					// Rule reader to author (guest and registered)
					$authorarticle = ($article->created_by_alias) ? $article->created_by_alias : $article->author;
					
					$uri = JURI::getInstance();
					
					$uri->delVar('invitekey');      // remove var used by alpharecommend pro -> no need in the url in data reference
					$uri->delVar('referreruser');   // remove var used by altauserpoints    -> no need in the url in data reference
					$uri->delVar('keyreference');   // remove var used by altauserpoints    -> no need in the url in data reference
					$uri->delVar('datareference');  // remove var used by altauserpoints    -> no need in the url in data reference
					
					$url = $uri->toString();
					
					$this->reader2author($authorid, $authorarticle, $articleid, $article->title, $url);					
				}
				break;
			default:					
		}	
	}
	
	public function reader2author ( $authorid=0, $author='', $articleid=0, $title='', $url='' )
	{	
		$app = JFactory::getApplication();
		
		require_once (JPATH_SITE.'/components/com_altauserpoints/helper.php');
		
		if ( !$authorid || !$articleid ) return;
		
		// get referrerid of author
		$referrerUserAuthor = AltaUserPointsHelper::getAnyUserReferreID( $authorid );
		if ( !AltaUserPointsHelper::checkExcludeUsers( $referrerUserAuthor ) ) return ;
		
		$ip 		= getenv('REMOTE_ADDR');				
		$db	        = JFactory::getDBO();
		
		$keyreference = $articleid . "|" . $ip;		
		$keyreference = AltaUserPointsHelper::buildKeyreference('sysplgaup_reader2author', $keyreference);
		
		// check if not already view by active user
		$query = "SELECT `id` FROM #__alpha_userpoints_details WHERE `keyreference`='" . $keyreference . "' AND enabled='1'";
		$db->setQuery( $query );
		$alreadyView = $db->loadResult();
		if ( !$alreadyView )
		{	
			$user 		=  JFactory::getUser();			
			$jnow		=  JFactory::getDate();
			$now		= $jnow->toSql();		
			$authorizedLevels = JAccess::getAuthorisedViewLevels($user->id);			
			
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_reader2author' AND `published`='1' AND `access` IN (" . implode ( ",", $authorizedLevels ) . ") AND (`rule_expire`>'$now' OR `rule_expire`='0000-00-00 00:00:00')";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			if ( $result && $referrerUserAuthor )
			{
				$datareference = '<a href="' . $url . '">' . $title . '</a> ('.$author.')' ;
				AltaUserPointsHelper::insertUserPoints( $referrerUserAuthor, $result[0], 0, $keyreference, $datareference );
			}
		}		
	}

}
?>