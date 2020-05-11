<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class altauserpointsModelReportsystem extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _generate_report()
	{
		$app = JFactory::getApplication();
		
		$db = JFactory::getDBO();
		
		$JVersion = new JVersion();
		$jversion = $JVersion->PRODUCT .' '. $JVersion->RELEASE .'.'. $JVersion->DEV_LEVEL .' '. $JVersion->DEV_STATUS.' [ '.$JVersion->CODENAME .' ] '. $JVersion->RELDATE;

		if($app->getCfg('legacy' )) {
			$jconfig_legacy = '[color=#FF0000]Enabled[/color]';
		} else {
			$jconfig_legacy = 'Disabled';
		}
		if(!$app->getCfg('smtpuser' )) {
			$jconfig_smtpuser = 'Empty';
		}
		if($app->getCfg('ftp_enable' )) {
			$jconfig_ftp = 'Enabled';
		} else {
			$jconfig_ftp = 'Disabled';
		}
		if($app->getCfg('sef' )) {
			$jconfig_sef = 'Enabled';
		} else {
			$jconfig_sef = 'Disabled';
		}
		if($app->getCfg('sef_rewrite' )) {
			$jconfig_sef_rewrite = 'Enabled';
		} else {
			$jconfig_sef_rewrite = 'Disabled';
		}
		
		if (file_exists(JPATH_ROOT. '/.htaccess')) {
			$htaccess = 'Exists';
		} else {
			$htaccess = 'Missing';
		}
		
		if(ini_get('register_globals')) {
			$register_globals = '[u]register_globals:[/u] [color=#FF0000]On[/color]';
		} else {
			$register_globals = '[u]register_globals:[/u] Off';
		}
		if(ini_get('safe_mode')) {
			$safe_mode = '[u]safe_mode:[/u] [color=#FF0000]On[/color]';
		} else {
			$safe_mode = '[u]safe_mode:[/u] Off';
		}
		if(extension_loaded('mbstring')) {
			$mbstring = '[u]mbstring:[/u] Enabled';
		} else {
			$mbstring = '[u]mbstring:[/u] [color=#FF0000]Not installed[/color]';
		}
		if(extension_loaded('gd')) {
			$gd_info = gd_info ();
			$gd_support = '[u]GD:[/u] '.$gd_info['GD Version'] ;
		} else {
			$gd_support = '[u]GD:[/u] [color=#FF0000]Not installed[/color]';
		}
		$maxExecTime = ini_get('max_execution_time');
		$maxExecMem = ini_get('memory_limit');
		$fileuploads = ini_get('upload_max_filesize');
		
		$aupversion = _ALTAUSERPOINTS_NUM_VERSION;
		
		//test on each table if the collation is on utf8
		$tableslist = $db->getTableList();
		$collation = '';
		foreach($tableslist as $table) {
			if ( preg_match('_alpha_', $table) ) {
				$db->setQuery("SHOW FULL FIELDS FROM " .$table. "");
				$fullfields = $db->loadObjectList ();
				if ($db->getErrorMsg()) JFactory::getApplication()->enqueueMessage( $db->getErrorMsg(),'error') ;
		
				$fieldTypes = array('tinytext','text','char','varchar');
		
				foreach ($fullfields as $row) {
					$tmp = strpos ( $row->Type , '(' );
		
					if ($tmp) {
						if ( in_array(substr($row->Type,0,$tmp),$fieldTypes) ) {
							if(!empty($row->Collation) && !preg_match('`utf8`',$row->Collation)) {
								$collation .= $table.' [color=#FF0000]have wrong collation of type '.$row->Collation.' [/color] on field '.$row->Field.'  ';
							}
						}
					} else {
						if ( in_array($row->Type,$fieldTypes) ) {
							if(!empty($row->Collation) && !preg_match('`utf8`',$row->Collation)) {
								$collation .= $table.' [color=#FF0000]have wrong collation of type '.$row->Collation.' [/color] on field '.$row->Field.'  ';
							}
						}
					}
				}
			}
		}
		if(empty($collation)) {
			$collation = 'The collation of your table fields are correct';
		}
		
		$aupconfigsettings = JComponentHelper::getParams( 'com_altauserpoints' );
	
		$report = '[confidential][b]Joomla! version:[/b] '.$jversion.' [b]Platform:[/b] '.$_SERVER['SERVER_SOFTWARE'].' ('
			.$_SERVER['SERVER_NAME'].') [b]PHP version:[/b] '.phpversion().' | '.$safe_mode.' | '.$register_globals.' | '.$mbstring
			.' | '.$gd_support.' | [b]MySQL version:[/b] '.$db->getVersion() . ' | [b]Mailer:[/b] '.$app->getCfg('mailer' ).' | [b]Mail from:[/b] '.$app->getCfg('mailfrom' ).' | [b]From name:[/b] '.$app->getCfg('fromname' ).' | [b]SMTP Secure:[/b] '.$app->getCfg('smtpsecure' ).' | [b]SMTP Port:[/b] '.$app->getCfg('smtpport' ).' | [b]SMTP User:[/b] '.$jconfig_smtpuser.' | [b]SMTP Host:[/b] '.$app->getCfg('smtphost' ).'[/confidential]'
			.'[quote][b]Database collation check:[/b] '.$collation .'[/quote]'
			.'[quote][b]Legacy mode:[/b] '.$jconfig_legacy.' | [b]Joomla! SEF:[/b] '.$jconfig_sef.' | [b]Joomla! SEF rewrite:[/b] '
			. $jconfig_sef_rewrite.' | [b]FTP layer:[/b] '.$jconfig_ftp.' | [b]htaccess:[/b] ' . $htaccess
			.' | [b]PHP environment:[/b] [u]Max execution time:[/u] '.$maxExecTime.' seconds | [u]Max execution memory:[/u] '
			.$maxExecMem.' | [u]Max file upload:[/u] '.$fileuploads.' [/quote]'
			.'[quote][b]AltaUserPoints version:[/b] [u]Installed version:[/u] '.$aupversion
			.chr(13).'[b]AltaUserPoints detailled configuration:[/b][spoiler]'			
			.chr(13).'[u]allowGuestUserViewProfil:[/u]'.$aupconfigsettings->get('allowGuestUserViewProfil')
			.chr(13).'[u]prefix_selfregister:[/u]'.$aupconfigsettings->get('prefix_selfregister')
			.chr(13).'[u]prefix_referralregister:[/u]'.$aupconfigsettings->get('prefix_referralregister')
			.chr(13).'[u]referralIDtype:[/u]'.$aupconfigsettings->get('referralIDtype')
			.chr(13).'[u]limit_daily_points:[/u]'.$aupconfigsettings->get('limit_daily_points')
			.chr(13).'[u]insertAllActivities:[/u]'.$aupconfigsettings->get('insertAllActivities')
			.chr(13).'[u]useAvatarFrom:[/u]'.$aupconfigsettings->get('useAvatarFrom')
			.chr(13).'[u]linkToProfile:[/u]'.$aupconfigsettings->get('linkToProfile')
			.chr(13).'[u]showUpdateCheck:[/u]'.$aupconfigsettings->get('showUpdateCheck')
			.chr(13).'[u]showUddeimTab:[/u]'.$aupconfigsettings->get('showUddeimTab')
			.chr(13).'[u]sendMsgUddeim:[/u]'.$aupconfigsettings->get('sendMsgUddeim')
			.chr(13).'[u]fromIdUddeim:[/u]'.$aupconfigsettings->get('fromIdUddeim')			
			.'[/spoiler][/quote]'
			.'[quote][b]AltaUserPoints installed rules:[/b][spoiler]'	
			
			. $this->_checkRules()
			
			.'[/spoiler][/quote]'
			
			.'[quote][b]AltaUserPoints installed menus:[/b][spoiler]'	
			
			. $this->_checkMenus()
			
			.'[/spoiler][/quote]'

			;
		
		return $report;

	}	
	
	function _checkRules()
	{	
		$db = JFactory::getDBO();
		
		$listRules = '';
		
		$query = "SELECT * FROM #__alpha_userpoints_rules";
		$db->setQuery( $query );
		$listingRules = $db->loadObjectList();
		
		foreach ($listingRules as $rule)
		{
			$listRules .= chr(13).'[u]name:[/u] ' . JText::_($rule->rule_name) .' | [u]plugin function:[/u] '.$rule->plugin_function .' | [u]access:[/u] '.$rule->access .' | [u]published:[/u] '. $rule->published .' | [u]points:[/u] '. $rule->points .' | [u]optional points:[/u] '. $rule->points2;
		}		
		
		return $listRules;	
	
	}
	
	function _checkMenus()
	{	
		$db = JFactory::getDBO();
		
		$listMenus = '';		
		
		$query = "SELECT * FROM #__menu WHERE `link` LIKE '%option=com_altauserpoints&view=%'";
		$db->setQuery( $query );
		$listingMenus = $db->loadObjectList();
		
		foreach ($listingMenus as $menu)
		{
			$listMenus .= chr(13).'[u]link:[/u] ' . JText::_($menu->link) .' | [u]published:[/u] '.$menu->published .' | [u]access:[/u] '.$menu->access .' | [u]params:[/u] '. $menu->params;
		}		
		
		return $listMenus;
	
	}
	
}
?>