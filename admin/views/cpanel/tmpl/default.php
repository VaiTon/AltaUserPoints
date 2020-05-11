<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$app = JFactory::getApplication();

$rows = $this->top10;
$rowsPA = $this->unapproved10;
$rowsLA = $this->lastactivities;

if ( $this->needSync && $this->synch!='start') {	
	$error = JText::_('AUP_MSG_NEEDSYNCUSERS');
	$app->enqueueMessage( $error ,'notice');
}

if ($this->synch=='end') {
	$app->enqueueMessage( JText::_( 'AUP_ALLUSERSSYNC' ) );		
} elseif ($this->synch!='start' && $this->synch!='end' && $this->synch!='') {
	$app->enqueueMessage( $this->synch );		
}

if ($this->recalculate=='end') {
	$app->enqueueMessage( JText::_( 'AUP_SUCCESSFULLYRECALCULATE' ) );		
} elseif ($this->recalculate!='start' && $this->recalculate!='end' && $this->recalculate!='') {
	$app->enqueueMessage( $this->recalculate );		
}

if (!curlDetect())$app->enqueueMessage( JText::_( 'AUP_CURLMUSTBEENABLE' ),'notice' );

$template	= $app->getTemplate();
?>
<script type="text/javascript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);


Joomla.submitbutton = function(task)
{
	//if (task == 'cancelrule' || document.formvalidator.isValid(document.id('cpanel-form'))) {
		Joomla.submitform(task, document.getElementById('cpanel-form'));
	//}
	//else {
		//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	//}
}
//-->
</script>
<?php
// force new style for icon
echo '<style type="text/css">
	a.thumbnail:hover {
    border-color: #59B8E8;
    box-shadow: 0 0 2px 2px rgba(0, 105, 214, 0.25);
}
</style>
';
?>
<div id="j-main-container">
<?php
if ( $this->synch=='start' || $this->recalculate=='start' ) {
	JFactory::getApplication()->input->set( 'hidemainmenu', 1 );	
	echo '<div class="alert alert-danger" role="alert">' . JText::_( 'AUP_NOTTOINTERRUPTPROCESS' ) . '</div>';
 ?>
<div class="alert alert-warning" role="alert"> 
	  <div class="media">
	  <img src="<?php echo JURI::base(true).'/components/com_altauserpoints/assets/images/working.gif'; ?>" alt="" />
		  <div class="media-body">
		  <?php if ( $this->synch=='start' ) { ?>	
		  <iframe src="components/com_altauserpoints/assets/synch/synch.php?start=0&run=1" width="100%" height="40" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" style="vertical-align:middle;"></iframe>
		  <?php } elseif ( $this->recalculate=='start' ) { ?>
		  <iframe src="components/com_altauserpoints/assets/recalculate/recalculate.php?start=0&run=1" width="100%" height="40" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" style="vertical-align:middle;"></iframe>
		  <?php } ?>
		  </div>
	  </div>
</div>
<?php
}
?>	
		<div class="span7">
			<div id="cpanel">
			<?php
			
					$javascript_alert_no_auth = "onclick=\"return alert('" . JText::_('JERROR_ALERTNOAUTHOR') . "')\"";
			
					if (JFactory::getUser()->authorise('core.rules', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=rules';
						aup_createIconPanel($link, 'aupbe-icon-list', JText::_('AUP_RULES'));
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-list', JText::_('AUP_RULES'), $javascript_alert_no_auth);
					}
						
					if (JFactory::getUser()->authorise('core.users', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=statistics';
						aup_createIconPanel($link, 'aupbe-icon-users', JText::_('AUP_USERS'));
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-users', JText::_('AUP_USERS'), $javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.activity', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=activities';
						aup_createIconPanel($link, 'aupbe-icon-activity', JText::_('AUP_ACTIVITY'));
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-activity', JText::_('AUP_ACTIVITY'), $javascript_alert_no_auth);
					}
						
						
					if (JFactory::getUser()->authorise('core.templatesinvite', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=templateinvite';
						aup_createIconPanel($link, 'aupbe-icon-templates', JText::_('AUP_TEMPLATES_INVITE'));		
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-templates', JText::_('AUP_TEMPLATES_INVITE'), $javascript_alert_no_auth);
					}
									
					if (JFactory::getUser()->authorise('core.usersynch', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=cpanel&synch=start';
						aup_createIconPanel($link, 'aupbe-icon-sync', JText::_('AUP_USERSYNC'));
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-sync', JText::_('AUP_USERSYNC'), $javascript_alert_no_auth);
					}					
					
					
					if (JFactory::getUser()->authorise('core.recalculate', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=recalculate';
						$javascript_recalculate_points = "onclick=\"return confirm('" . JText::_( 'AUP_CONFIRM_STARTPROCESS' ) . "')\"";	
						aup_createIconPanel($link, 'aupbe-icon-recalc', JText::_('AUP_RECALCULATE'), $javascript_recalculate_points);
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-recalc', JText::_('AUP_RECALCULATE'), $javascript_alert_no_auth);
					}					
						
					
					if (JFactory::getUser()->authorise('core.resetallpoints', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=resetpoints';
						$javascript_reset_points = "onclick=\"return confirm('" . JText::_( 'AUP_DO_YOU_WANT_RESET_ALL_POINTS' ) . "')\"";
						aup_createIconPanel($link, 'aupbe-icon-reset', JText::_('AUP_RESETPOINTS'), $javascript_reset_points);
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-reset', JText::_('AUP_RESETPOINTS'), $javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.setmaxpoints', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=setmaxpoints';
						aup_createIconPanel($link, 'aupbe-icon-maxpoints', JText::_('AUP_SETMAXPOINST'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-maxpoints', JText::_('AUP_SETMAXPOINST'), $javascript_alert_no_auth);					
					}
					
					
					if (JFactory::getUser()->authorise('core.purgeexpiredpoints', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=purge';
						aup_createIconPanel($link, 'aupbe-icon-purgexpire', JText::_('AUP_PURGEEXPIRESPOINTS'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-purgexpire', JText::_('AUP_PURGEEXPIRESPOINTS'), $javascript_alert_no_auth);
					}
					
					
					if (JFactory::getUser()->authorise('core.categories', 'com_altauserpoints')) {
						$link = "index.php?option=com_categories&view=categories&extension=com_altauserpoints";
						aup_createIconPanel($link, 'aupbe-icon-categories', JText::_('JCATEGORIES'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-categories', JText::_('JCATEGORIES'), $javascript_alert_no_auth);
					}
						
					if (JFactory::getUser()->authorise('core.couponcodes', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=couponcodes';
						aup_createIconPanel($link, 'aupbe-icon-coupon', JText::_('AUP_COUPON_CODES'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-coupon', JText::_('AUP_COUPON_CODES'), $javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.raffles', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=raffle';
						aup_createIconPanel($link, 'aupbe-icon-raffle', JText::_('AUP_RAFFLE'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-raffle', JText::_('AUP_RAFFLE'), $javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.levelrank', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=levelrank';
						aup_createIconPanel($link, 'aupbe-icon-ranks', JText::_('AUP_LEVEL_RANK'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-ranks', JText::_('AUP_LEVEL_RANK'), $javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.viewstats', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=stats';
						aup_createIconPanel($link, 'aupbe-icon-stats', JText::_('AUP_STATISTICS'));
					} else {
						$link = '';						
						aup_createIconPanel($link, 'aupbe-icon-stats', JText::_('AUP_STATISTICS'),$javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.exportactiveusers', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=exportactiveusers';
						aup_createIconPanel($link, 'aupbe-icon-exportusers', JText::_('AUP_EXPORTACTIVEUSERS'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-exportusers', JText::_('AUP_EXPORTACTIVEUSERS'),$javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.exportemails', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=exportemails';
						aup_createIconPanel($link, 'aupbe-icon-exportmail', JText::_('AUP_EXPORTEMAILS'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-exportmail', JText::_('AUP_EXPORTEMAILS'),$javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.combineactivities', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=archiveActivities';
						aup_createIconPanel($link, 'aupbe-icon-combine', JText::_('AUP_COMBINE_ACTIVITIES'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-combine', JText::_('AUP_COMBINE_ACTIVITIES'),$javascript_alert_no_auth);
					}
					
					if (JFactory::getUser()->authorise('core.reportsystem', 'com_altauserpoints')) {
						$link = 'index.php?option=com_altauserpoints&task=reportsystem';
						aup_createIconPanel($link, 'aupbe-icon-report', JText::_('AUP_REPORT_SYSTEM'));
					} else {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-report', JText::_('AUP_REPORT_SYSTEM'),$javascript_alert_no_auth);
					}
					if ( JFactory::getUser()->authorise('core.plugins', 'com_altauserpoints') && _ALTAUSERPOINTS_SHOW_DEPRECATED_PLUGIN_INSTALL == '1' ) {
						$link = 'index.php?option=com_altauserpoints&task=plugins';
						aup_createIconPanel($link, 'aupbe-icon-link', JText::_('AUP_PLUGINS'));
					} elseif ( !JFactory::getUser()->authorise('core.plugins', 'com_altauserpoints') && _ALTAUSERPOINTS_SHOW_DEPRECATED_PLUGIN_INSTALL == '1' ) {
						$link = '';
						aup_createIconPanel($link, 'aupbe-icon-link', JText::_('AUP_PLUGINS'),$javascript_alert_no_auth);
					}
					
					// auto-detect rules in third components, plugins and modules
					$msg = JText::_('AUP_NONE');
					if (JFactory::getUser()->authorise('core.autodetectplugins', 'com_altauserpoints')) {
						if ( !JFactory::getApplication()->input->get('scanAUPRules', 0, 'int') ) {
							$link = 'index.php?option=com_altauserpoints&task=cpanel&scanAUPRules=1';
							aup_createIconPanel($link, 'aupbe-icon-autodetect', JText::_('AUP_AUTODETECTPLUGINS'));
						} else {
							// Import file dependencies
							require_once JPATH_COMPONENT_ADMINISTRATOR.'/assets/includes/simplexml.php';
							$aup_rule_file = 'altauserpoints_rule.xml';
						
							jimport('joomla.filesystem.file');
							jimport('joomla.filesystem.folder');
						
							$pathToScan = array( 0 =>	'components',
												1 =>	'modules',
												2 =>	'plugins');	
							
							foreach($pathToScan as $scan)
							{
								$scan_path		= JPATH_ROOT . '/' .$scan;
								
								if(! JFolder::exists($scan_path))
									continue;
								
								$scan_folders	= JFolder::folders($scan_path, '.', true, true);
							
								$count = 0;
								
								foreach($scan_folders as $folder)
								{
									$xmlRuleFile = $folder .'/' . $aup_rule_file;
							
									if(JFile::exists($xmlRuleFile))
									{
										$parser = new JSimpleXML();
										$parser->loadFile($xmlRuleFile);
										
										$elementCom		= $parser->document->getElementByPath('component');
										$component 	= (empty($elementCom)) ? '' : $elementCom->data();
										
										$elementRoot = $parser->document->getElementByPath('rules');				
										
										if(! empty($elementRoot))
										{
											foreach($elementRoot->children() as $rule)
											{			
												$element 			= $rule->getElementByPath('name');
												$nameRule 			= (empty($element)) ? '' : addslashes( $element->data() );
												
												$element 			= $rule->getElementByPath('description');
												$descriptionRule 	= (empty($element)) ? '' : addslashes( $element->data() );
												
												$element 			= $rule->getElementByPath('plugin_function');
												$pluginRule 		= (empty($element)) ? '' : $element->data();
												
												$element 			= $rule->getElementByPath('fixed_points');
												$fixed_points 		= (empty($element)) ? 'true' : $element->data();
												$fixed_points 		= trim(strtolower($fixed_points));
												$fixed_points 		= ( $fixed_points==='true' ) ? 1 : 0;					
												
												$element 			= $rule->getElementByPath('category');
												$category 			= (empty($element)) ? 'ot' : $element->data();
												
												$element 			= $rule->getElementByPath('display_message');
												$display_message 	= (empty($element)) ? 1 : $element->data();
												
												$element 			= $rule->getElementByPath('email_notification');
												$email_notification	= (empty($element)) ? 0 : $element->data();
												
																
												// insert in AUP table if not exist
												if ( $nameRule!='' && $descriptionRule!='' && $pluginRule!='') {
												
													$db	= JFactory::getDBO();
													// check if already exist...					
													$query = "SELECT COUNT(*) FROM #__alpha_userpoints_rules WHERE `plugin_function`='$pluginRule'";
													$db->setQuery( $query );
													$resultCount = $db->loadResult();
													if ( !$resultCount ) {					
														$query = "INSERT INTO #__alpha_userpoints_rules VALUES ('', '".$nameRule."', '".$descriptionRule."', '".$component."', '".$pluginRule."', '1', '".$component."', '', '', '0', '0', '0', '0000-00-00 00:00:00', '', '', '', '', '1', '0', '0', '0', '1', '".$fixed_points."', '".$category."', '".$display_message."', '', '0', '".$email_notification."', '', '', '0', '0', '0', '0', '0', '1' )";
														$db->setQuery( $query );
														if ( $db->query() ) {							
															$count ++;
														}
													}
												}				
												
											}//end foreach
											
											
											if ( $count ) 
											{
												$msg = JText::_('AUP_NEW_RULE_INSTALLED_SUCCESSFULLY') . " (" . $count . ")";	
												$app->enqueueMessage( $msg );				
											} 
											else 
											{
												$msg = JText::_('AUP_NONE');
											}
											
										} 
									}					
								}		
							}
							
							$link = 'index.php?option=com_altauserpoints&task=cpanel&scanAUPRules=1';
							aup_createIconPanel($link, 'auto-detect-rules.png', $msg);
						} 
					} else {
						$link = '';
						aup_createIconPanel($link, 'auto-detect-rules_2.png', JText::_('AUP_AUTODETECTPLUGINS'),$javascript_alert_no_auth);
					}
					$msg = '';
					
					// TODO -> import export feature
					/*
					$link = 'index.php?option=com_altauserpoints&task=importexportTableActivities';
					aup_createIconPanel($link, 'icon-48-exportcsv.png', JText::_('AUP_IMPORT_EXPORT'));
					*/

					$link = 'index.php?option=com_altauserpoints&task=about';
					aup_createIconPanel($link, 'aupbe-icon-info', JText::_('AUP_ABOUT'));					
					
					$link = '#';
					if (file_exists( JPATH_COMPONENT . "/help/" . $this->tag . "/screen.how_create_plugin.html")){
						$javascript = "onclick=\"Joomla.popupWindow('components/com_altauserpoints/help/" . $this->tag . "/screen.how_create_plugin.html', 'Help', 640, 480, 1)\"";
					} else $javascript = "onclick=\"popupWindow('components/com_altauserpoints/help/en-GB/screen.how_create_plugin.html', 'Help', 640, 480, 1)\"";
					aup_createIconPanel($link, 'aupbe-icon-howto', JText::_('AUP_HOWTO'), $javascript);
					
					$link = 'https://www.nordmograph.com/extensions/index.php?option=com_virtuemart&view=category&virtuemart_category_id=8&Itemid=58';
					aup_createIconPanel($link, 'aupbe-icon-ruleslist', JText::_('AUP_MORERULES'));
					
					$javascript="";					
					$link = '#';
					if (file_exists( JPATH_COMPONENT . "/help/" . $this->tag . "/screen.altauserpoints.html")){
						$javascript = "onclick=\"Joomla.popupWindow('components/com_altauserpoints/help/" . $this->tag . "/screen.altauserpoints.html', 'Help', 640, 480, 1)\"";
					} else $javascript = "onclick=\"popupWindow('components/com_altauserpoints/help/en-GB/screen.altauserpoints.html', 'Help', 640, 480, 1)\"";
					aup_createIconPanel($link, 'aupbe-icon-help', JText::_('HELP'), $javascript);					
					
					
					// add new button module position
					$document = JFactory::getDocument();
					$renderer = $document->loadRenderer( 'modules' );
					$options = array( 'style' => 'none' );
					echo $renderer->render( 'AltaUserPoints CPanel', $options, null);									
					
					$link4welcome = "<a href=\"#\" $javascript>";
					$endlinkwelcome = "</a>";	
					
			?>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="span5">
			<div class="well"><h2><?php echo JText::_( 'AUP_TOTAL_COMMUNITY_POINTS' ) . ":&nbsp;&nbsp;" . getFormattedPointsAdm($this->communitypoints); ?></h2></div>
		<?php			
			echo $this->pane->startPane("content-pane");
			
			echo $this->pane->startPanel(JText::_('AUP_WELCOMETOALTAUSERPOINTS'), "cpanel-panel-welcome" );
			echo JText::sprintf('AUP_WELCOMEPANELTEXT', $link4welcome, $endlinkwelcome);
			
			
			echo $this->pane->endPanel();	
			
			echo $this->pane->startPanel(JText::_('AUP_HIGHT_SCORE'), "cpanel-panel-usersawards" );
			
			if ( $rows ) {
		?>
		<table class="table table-bordered table-striped table-hover table-condensed">
		<tr>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_NAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_USERNAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_POINTS' ); ?></strong>
			</td>
		</tr>
		<?php		
			foreach ($rows as $row)	{			
				?>
				<tr>
					<td>					
						<?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');?>
					</td>
					<td>
						<?php echo $row->username;?>
					</td>
					<td align="right">
						<?php echo getFormattedPointsAdm($row->points);?>
					</td>
				</tr>
				<?php
			}
			?>
		  </table>
		
		<?php
		}
			echo $this->pane->endPanel();
			
			// Last activity
			echo $this->pane->startPanel(JText::_('AUP_LASTACTIVITY'), "cpanel-panel-latest-activities" );
			
			if ( $rowsLA ) {
			?>
		<table class="table table-bordered table-striped table-hover table-condensed">
		<tr>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_DATE' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_RULENAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_NAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_USERNAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_POINTS' ); ?></strong>
			</td>
		</tr>		
			<?php
			
			foreach ($rowsLA as $rowLA)	{			
				?>
				<tr>
					<td>					
						<?php echo nicetimeAdm($rowLA->insert_date); ?>
					</td>
					<td>					
						<?php echo JText::_( $rowLA->rule_name ); ?>
					</td>
					<td>					
						<?php
						 $linkLA = "<a href=\"index.php?option=com_altauserpoints&task=showdetails&cid=".$rowLA->referreid."&name=".htmlspecialchars($rowLA->uname, ENT_QUOTES, 'UTF-8')."\">";
						 echo $linkLA . htmlspecialchars($rowLA->uname, ENT_QUOTES, 'UTF-8') . "</a>";					
						 ?>					
					</td>
					<td>
						<?php echo $rowLA->usrname;?>
					</td>
					<td align="right">
						<?php echo getFormattedPointsAdm($rowLA->last_points);?>
					</td>
				</tr>
				<?php
			} 
			?>
		  </table>
			<?php
			}
			
			echo $this->pane->endPanel();			
			
			// pending approval
			$nb = ( $rowsPA ) ? ' <span class="badge badge-important">'.$this->totalunapproved.'</span>' : ' <span class="badge badge-success">0</span>';
			
			echo $this->pane->startPanel(JText::_('AUP_PENDING_APPROVAL').$nb, "cpanel-panel-pending-approval" );
			
			if ( $rowsPA ) {
		?>
		<form action="index.php" method="post" name="adminForm">
		<table class="table table-bordered table-striped table-hover table-condensed">
		<tr>
			<td width="3%" class="title">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_DATE' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_RULENAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_NAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_USERNAME' ); ?></strong>
			</td>
			<td class="title">
				<strong><?php echo JText::_( 'AUP_POINTS' ); ?></strong>
			</td>
			<td class="title">&nbsp;
				
			</td>
		</tr>
		<?php		
			$k = 0;
			for ($i=0, $n=count( $rowsPA ); $i < $n; $i++)
			{
				$rowPA 	=& $rowsPA[$i];

				?>
				<tr>
					<td align="center">
						<?php echo JHTML::_('grid.id', $i, $rowPA->cid ); ?>
					</td>
					<td>					
						<?php echo $rowPA->insert_date; ?>
					</td>
					<td>					
						<?php echo JText::_( $rowPA->rule_name ); ?>
					</td>
					<td>					
						<?php
						 $linkPA = "<a href=\"index.php?option=com_altauserpoints&task=showdetails&cid=".$rowPA->referreid."&name=".htmlspecialchars($rowPA->name, ENT_QUOTES, 'UTF-8')."\">";
						
						 echo $linkPA . htmlspecialchars($rowPA->name, ENT_QUOTES, 'UTF-8') . "</a>";
						 
						 ?>
					</td>
					<td>
						<?php echo $rowPA->username;?>
					</td>
					<td align="right">
						<?php echo getFormattedPointsAdm($rowPA->pendingapprovalpoints);?>
					</td>
					<td>
						<?php 
						$linkDeletePA = '<a href="index.php?option=com_altauserpoints&task=deletependingapproval&cid='.$rowPA->cid.'">';
						echo $linkDeletePA . '<img src="'. JURI::base(true).'/components/com_altauserpoints/assets/images/trash-16.png" alt="" style="vertical-align:middle;" /></a>';
						?>
					</td>
				</tr>
				<?php
			} 
			?>
		  </table>
		  <table class="adminform">
		  	<tr>
		  		<td>
		    		<input type="submit" name="Submit" value="<?php echo JText::_( 'AUP_APPROVE' ); ?>">
		  		</td>
		  	</tr>		  
		  </table>
			<input type="hidden" name="option" value="com_altauserpoints" />
			<input type="hidden" name="task" value="approve" />
			<input type="hidden" name="table" value="alpha_userpoints_details" />
			<input type="hidden" name="redirect" value="cpanel" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>		
		<?php
		} else echo "<p>" . JText::_( 'AUP_NO_PENDING_APPROVAL' ) . "</p>";
			
			
			echo $this->pane->endPanel();
			
			
			// add new slider module position
			$document = JFactory::getDocument();
			$renderer = $document->loadRenderer( 'modules' );
			$options = array( 'style' => 'none' );
			echo $renderer->render( 'AltaUserPoints CPanel Slider', $options, null);
			
			
			echo $this->pane->endPane();
		

		?>					
		</div>
	<div class="clearfix"></div>

	<div align="center">
		<form action="index.php" method="post" name="cpanel-form" id="cpanel-form" class="form-validate">			
			<input type="hidden" name="option" value="com_altauserpoints" />
			<input type="hidden" name="task" value="cpanel" />
		</form>
	</div>
</div>