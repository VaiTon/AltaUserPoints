<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined('_JEXEC') or die('Restricted access');
defined('JPATH_BASE') or die();

class com_AltaUserPointsInstallerScript
{
        function postflight($type, $parent) 
        {
				require_once JPATH_ADMINISTRATOR.'/components/com_altauserpoints/assets/includes/version.php';
				require_once JPATH_ADMINISTRATOR.'/components/com_altauserpoints/assets/includes/functions.php';
		
				echo '<div class="well"><img src="'.JURI::base().'components/com_altauserpoints/assets/images/aup_logo.png" alt="" align="left" />
                <h1> AltaUserPoints Installation '._ALTAUSERPOINTS_NUM_VERSION.'</h1>';
				$app = JFactory::getApplication();
				
				$error = 0;
				
				$cache =  JFactory::getCache();
				$cache->clean( null, 'com_altauserpoints' );
				
				$db	= JFactory::getDBO();
				
				jimport('joomla.filesystem.folder');
				jimport('joomla.filesystem.file');
				
				
				/************************************************************************
				 *
				 *                              START INSTALL
				 *
				 *************************************************************************/
				$install = "";
				
				// copy example of Joomla plugin for AltaUserPoints
				$pathPluginsAUP = JPATH_SITE.'/plugins/altauserpoints';
				if (!JFolder::exists($pathPluginsAUP)) JFolder::create($pathPluginsAUP);
				$src = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/example';
				$dest = $pathPluginsAUP .'/example';
				JFolder::copy($src, $dest, '', true);
				
				// Disabled old module mod_aupadmin
				$query = "SELECT id FROM #__modules WHERE module='mod_aupadmin' AND published='1'";
				$db->setQuery( $query );
				$idmodule = $db->LoadResult();
				if ($idmodule)
				{	
					$query = "UPDATE #__modules SET published='0' 
					WHERE `module`='mod_aupadmin'";
					$db->setQuery( $query );
					$db->execute();	
				}
				
				// Install plugins
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/altauserpointsicon';
				if( $plugin_installer->install($file_origin) ) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE `element`='altauserpointsicon' AND `type`='plugin'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints Quick Icon <b>Button</b><br/>';
				}  else $error++;
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/altauserpoints';
				if( $plugin_installer->install($file_origin) ) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE `element`='altauserpoints' AND `type`='plugin' AND folder='system'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints <b>System</b> Plugin <br/>';
				}  else $error++;
				
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/sysplgaup_newregistered';
				if($plugin_installer->install($file_origin)) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1', ordering='999' 
					WHERE element='sysplgaup_newregistered' AND `type`='plugin' AND folder='user'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints Registering <b>User</b> Plugin <br/>';
				} else $error++;
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/sysplgaup_raffle';
				if($plugin_installer->install($file_origin)) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE element='sysplgaup_raffle' AND `type`='plugin' AND folder='content'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints Raffle <b>Content</b> Plugin <br/>';
				} else $error++;
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/sysplgaup_reader2author';
				if($plugin_installer->install($file_origin)) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE element='sysplgaup_reader2author' AND `type`='plugin' AND folder='content'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt=""  align="absmiddle" /> Installing AltaUserPoints Reader to Author <b>Content</b> Plugin <br/>';
				} else $error++;
				
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/sysplgaup_content';
				if($plugin_installer->install($file_origin)) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE element='sysplgaup_content' AND `type`='plugin' AND folder='content'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints system <b>Content</b> Plugin <br/>';
				} else $error++;
				
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/plg_editors-xtd_raffle';
				if($plugin_installer->install($file_origin)) {
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' 
					WHERE element='raffle' AND `type`='plugin' AND folder='editors-xtd'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle"/> Installing AltaUserPoints Raffle Editor Button <b>Editor</b> Plugin <br/>';
				} else $error++;
				
				
				$plugin_installer = new JInstaller;
				$file_origin = JPATH_ADMINISTRATOR.'/components/com_altauserpoints/install/plugins/notification_rank_medal';
				if( $plugin_installer->install($file_origin) )
				{
					// publish plugin
					$query = "UPDATE #__extensions SET enabled='1' WHERE `element`='notification_rank_medal' 
					AND `type`='plugin' AND folder='altauserpoints'";
					$db->setQuery( $query );
					$db->execute();
					$install .= '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
					alt="" align="absmiddle" /> Installing AltaUserPoints <b>Notification on update rank and medal</b> Plugin <br/>';
				}  else $error++;



				if ( $error )
				{
			     JControllerLegacy::setRedirect('index.php?option=com_altauserpoints',
				 'NOTICE: AltaUserPoints plugins are not successfully installed. Make sure that the plugins directory is writeable'  );
			     JControllerLegacy::redirect(); 
					
				} else {
				
					// Insert rules and Guest user on fresh install
					$query = "SELECT id FROM #__alpha_userpoints WHERE `userid`='0' AND `referreid`='GUEST'";
					$db->setQuery( $query );
					$result = $db->loadResult();
					if ( !$result ) {
						// This GUEST user is used by AUP system, don't remove!
						$query = "INSERT INTO #__alpha_userpoints (`id`, `userid`, `referreid`, `points`, 
						`max_points`, `last_update`, `referraluser`, `referrees`, `blocked`, `levelrank`) 
						VALUES ('', '0', 'GUEST', '0', '0', '0000-00-00 00:00:00', '', '0', '0', '0');";
						$db->setQuery( $query );
						$db->execute();
					}
					
					$query = "SELECT count(*) FROM #__alpha_userpoints_rules";
					$db->setQuery( $query );
					$result = $db->loadResult();
					if ( !$result ) {		
						// Insert default rules on fresh install
						$query = "INSERT INTO #__alpha_userpoints_rules (`id`, `rule_name`, `rule_description`, `rule_plugin`, `plugin_function`, `access`, `component`, `calltask`, `taskid`, `points`, `points2`, `percentage`, `rule_expire`, `sections`, `categories`, `content_items`, `exclude_items`, `published`, `system`, `duplicate`, `blockcopy`, `autoapproved`, `fixedpoints`, `category`, `displaymsg`, `msg`, `method`, `notification`, `emailsubject`, `emailbody`, `emailformat`, `bcc2admin`, `type_expire_date`, `chain`,`linkup`, `displayactivity`) VALUES
							('', 'AUP_NEWUSER', 'AUP_NEWUSERDESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_newregistered', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 1, 1, 0, 1, 1, 1, 'us', '0', '', '1', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_INVITE', 'AUP_INVITE_A_USER', 'AUP_SYSTEM', 'sysplgaup_invite', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 're', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_INVITESUCCES', 'AUP_INVITE_A_USERSUCCESS', 'AUP_SYSTEM', 'sysplgaup_invitewithsuccess', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 're', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_READTOAUTHOR', 'AUP_READTOAUTHORDESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_reader2author', '0', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'ar', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_REFERRALPOINTS', 'AUP_REFERRALPOINTSDESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_referralpoints', '1', '', '', '', 0, 0, 1, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'co', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_BONUSPOINTS', 'AUP_BONUSPOINTSDESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_bonuspoints', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 1, 0, 1, 1, 'ot', '0', '', '4', '0', '', '', '0', '0', '0', '1', '0', '1'),
							('', 'AUP_WINNERNOTIFICATION', 'AUP_WINNERNOTIFICATIONDESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_winnernotification', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 0, 'sy', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_COUPON_POINTS_CODES', 'AUP_COUPON_POINTS_CODES_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_couponpointscodes', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 0, 'cd', '1', '', '1', '0', '', '', '0', '0', '0', '0', '0', '1'),			
							('', 'AUP_RAFFLE', 'AUP_RAFFLE_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_raffle', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 0, 'ot', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_CUSTOM', 'AUP_CUSTOM_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_custom', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 0, 1, 0, 'ot', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_PROFILE_VIEW', 'AUP_PROFILE_VIEW_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_profile_view', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'co', '1', '', '1', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_CHANGE_LEVEL_1', 'AUP_CHANGE_LEVEL_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_changelevel1', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'us', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_CHANGE_LEVEL_2', 'AUP_CHANGE_LEVEL_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_changelevel2', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'us', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_CHANGE_LEVEL_3', 'AUP_CHANGE_LEVEL_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_changelevel3', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 1, 0, 1, 1, 1, 'us', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),			
							('', 'AUP_COMBINED_ACTIVITIES', 'AUP_COMBINE_ACTIVITIES_DESCRIPTION', 'AUP_SYSTEM', 'sysplgaup_archive', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 1, 1, 0, 1, 1, 0, 'sy', '0', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_KU_NEW_TOPIC', 'AUP_KU_NEW_TOPIC_DESCRIPTION', 'AUP_KUNENA_FORUM', 'plgaup_kunena_topic_create', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 0, 0, 0, 1, 1, 'fo', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_KU_REPLY_TOPIC', 'AUP_KU_REPLY_DESCRIPTION', 'AUP_KUNENA_FORUM', 'plgaup_kunena_topic_reply', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 0, 0, 0, 1, 1, 'fo', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_KU_THANKYOU', 'AUP_KU_THANKYOU_DESCRIPTION', 'AUP_KUNENA_FORUM', 'plgaup_kunena_message_thankyou', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 0, 0, 0, 1, 1, 'fo', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1'),
							('', 'AUP_KU_DELETE_POST', 'AUP_KU_DELETE_POST_DESCRIPTION', 'AUP_KUNENA_FORUM', 'plgaup_kunena_message_delete', '1', '', '', '', 0, 0, 0, '0000-00-00 00:00:00', '', '', '', '', 0, 0, 0, 0, 1, 1, 'fo', '1', '', '4', '0', '', '', '0', '0', '0', '0', '0', '1');";
				
						$db->setQuery( $query );
						if ( $db->execute() ) {
							// default
							$install .=  '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
							alt="" align="absmiddle" /> 17 default rules installed<br/>';
							// Kunena
							$install .=  '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
							alt="" align="absmiddle" /> 4 default rules for Kunena installed<br/>';
						}
						
						// Insert version on fresh install
						$query = "SELECT version FROM #__alpha_userpoints_version WHERE 1";
						$db->setQuery( $query );
						$result = $db->loadResult();
						if ( !$result ) {
							$query = "INSERT INTO #__alpha_userpoints_version (`version`) VALUES ('AUP11');";
							$db->setQuery( $query );
							$db->execute();
						}
						
						
						// fresh install or update -> update table version
						aup_update_db_version ();				
							
					}	
						
					$query = "SELECT COUNT(*) FROM #__alpha_userpoints_levelrank";
					$db->setQuery( $query );
					$nblevelrank = $db->loadResult();
					
					if ( !$nblevelrank ) {
					
						// insert sample ranks and medals on fresh install
						$query = "INSERT INTO `#__alpha_userpoints_levelrank` (`id`, `rank`, `description`, 
						`levelpoints`, `typerank`, `icon`, `image`, `gid`, `category`) VALUES
								('', 'Gold member', 'Gold member', 10000, 0, 'icon_gold.png', 'gold.png', 0, 0),
								('', 'Silver member', 'Silver member', 6000, 0, 'icon_silver.png', 'silver.png', 0, 0),
								('', 'Bronze member', 'Bronze member', 3000, 0, 'icon_bronze.png', 'bronze.png', 0, 0),			
								('', 'Honor Medal ', 'Honor Medal for best activities on this site', 1000, 1, 'award_small_gold.png', 'award_big_gold.png', 0, 0);";
						$db->setQuery( $query );
						$db->execute();
						
						$install .=  '<img src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
						alt="" align="absmiddle" /> Sample ranks/medals installed<br/>';
						
					}
				
				// install default modules
				// =======================
				$status = new stdClass;
				$status->modules = array();
				$status->plugins = array();
				$src = $parent->getParent()->getPath('source');
				$manifest = $parent->getParent()->manifest;
				$modules = $manifest->xpath('modules/module');
				foreach ($modules as $module)
				{
					$name = (string)$module->attributes()->module;
					$client = (string)$module->attributes()->client;
					$modulePublished = (string)$module->attributes()->publish;
					$modulePosition = (string)$module->attributes()->position;
					if (is_null($client))
					{
						$client = 'site';
					}
					$path = $src.'/modules/'.$name;
					$installer = new JInstaller;
					$result = $installer->install($path);
		
					if($client == 'administrator') {
							//auto publish the admin modules
							$sql = $db->getQuery(true)
									->update($db->qn('#__modules'))
									->set($db->qn('position').' = '.$db->q($modulePosition))
									->where($db->qn('module').' = '.$db->q($name));
								if($modulePublished) {
									$sql->set($db->qn('published').' = '.$db->q('1'));
								}
								$db->setQuery($sql);
								$db->execute();
		
								// Link to all pages
								$query = $db->getQuery(true);
								$query->select('id')->from($db->qn('#__modules'))
									->where($db->qn('module').' = '.$db->q($name));
								$db->setQuery($query);
								$moduleid = $db->loadResult();
		
								$query = $db->getQuery(true);
								$query->select('*')->from($db->qn('#__modules_menu'))
									->where($db->qn('moduleid').' = '.$db->q($moduleid));
								$db->setQuery($query);
								$assignments = $db->loadObjectList();
								$isAssigned = !empty($assignments);
								if(!$isAssigned) {
									$o = (object)array(
										'moduleid'	=> $moduleid,
										'menuid'	=> 0
									);
									$db->insertObject('#__modules_menu', $o);
								}
		
					}
		
					$status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
					
				}
				
				if (count($status->modules))
				{				 	
					foreach ($status->modules as $module) 
					{
						$install.=  ($module['result'])?'<img 
						src="components/com_altauserpoints/assets/images/icon-16-allow.png" 
						alt="" align="absmiddle" /> Module '.$module['name']. ' installed<br/>':'<img 
						src="components/com_altauserpoints/assets/images/publish_x.png" alt="" 
						align="absmiddle" /> Module '.$module['name'].' not installed !!!<br/>';
					}
				}		
		
				// Upgrade field in database on update version
				// -------------------------------------------
					
				// version 1.9.9	
				//$test199 = "SELECT `notification` FROM #__alpha_userpoints_levelrank";
				$test199 = "SHOW COLUMNS FROM #__alpha_userpoints_levelrank LIKE 'notification'";
				$db->setQuery( $test199 );
				//if ( !$db->query() ) {		
				if ( !$db->loadResult() ) {	
					$q = "ALTER TABLE #__alpha_userpoints_levelrank ADD `notification` tinyint(1) NOT NULL DEFAULT '0', ADD `emailsubject` varchar(255) NOT NULL DEFAULT '', ADD `emailbody` text NOT NULL DEFAULT '', ADD `emailformat` tinyint(1) NOT NULL DEFAULT '0', ADD `bcc2admin` tinyint(1) NOT NULL DEFAULT '0';";
					$db->setQuery( $q );
					$db->execute();
					
					$q = "ALTER TABLE #__alpha_userpoints_raffle_inscriptions ADD `ticket` varchar(30) NOT NULL DEFAULT '', ADD `referredraw` int(11) NOT NULL DEFAULT '0', ADD `inscription` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
					$db->setQuery( $q );
					$db->execute();
					
					$q = "ALTER TABLE #__alpha_userpoints_rules ADD `chain` tinyint(1) NOT NULL DEFAULT '0', ADD `linkup` int(11) NOT NULL DEFAULT '0';";
					$db->setQuery( $q );
					$db->execute();

					$q = "UPDATE #__alpha_userpoints_rules SET `duplicate`='1', `blockcopy`='0', `chain`='1' WHERE `plugin_function`='sysplgaup_bonuspoints';";
					$db->setQuery( $q );
					$db->execute();
				}		
				
				// version 2.0.2 	
				//$test202 = "SELECT `displayactivity` FROM #__alpha_userpoints_rules";
				$test202 = "SHOW COLUMNS FROM #__alpha_userpoints_rules LIKE 'displayactivity'";
				$db->setQuery( $test202 );
				//if ( !$db->query() ) {	
				if ( !$db->loadResult() ) {
					$q = "ALTER TABLE #__alpha_userpoints_rules ADD `displayactivity` tinyint(1) NOT NULL DEFAULT '1';";
					$db->setQuery( $q );
					$db->execute();
				}			
				
				// version 2.0.4 	
				//$test204 = "SELECT `enabled` FROM #__alpha_userpoints_details";
				$test204 = "SHOW COLUMNS FROM #__alpha_userpoints_details LIKE 'enabled'";
				$db->setQuery( $test204 );		
				if ( !$db->loadResult() )
				{
					$q = "ALTER TABLE #__alpha_userpoints_details ADD `enabled` tinyint(1) NOT NULL DEFAULT '1';";
					$db->setQuery( $q );
					$db->execute();
				}		
							
					
				echo "<p>&nbsp;</p><p>&nbsp;</p><p>" . $install . "</p>";				
				echo '<p>&nbsp;</p><p><a href="index.php?option=com_altauserpoints" class="btn btn-primary">Go to AltaUserPoints</a></p>';
				echo "<p>" . aup_CopySite ('left') . "</p>";				
				echo'<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FNordmograph-Web-marketing-and-Joomla-expertise%2F368385633962&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=739550822721946" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>';		
				echo "<p></p>";
				echo "</div>";
				
			}

        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
                // $parent is the class calling this method
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
                // $parent is the class calling this method	
				
				$this->install($parent);		
				
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                //echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        }
		 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */

}
?>