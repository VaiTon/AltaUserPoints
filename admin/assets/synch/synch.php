<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
define("_JEXEC", 1);
defined('_JEXEC') or die('Restricted access');

if (file_exists( dirname( dirname( dirname( dirname( dirname(__FILE__))))) .'/defines.php')) 
	include_once dirname( dirname( dirname( dirname( dirname(__FILE__))))) .'/defines.php';

if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname( dirname( dirname( dirname( dirname(__FILE__))))));
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';
$mainframe = JFactory::getApplication('administrator');
$mainframe->initialise();

jimport( 'joomla.plugin.plugin' );
$lang = JFactory::getLanguage();
$lang->load( 'com_altauserpoints', JPATH_ADMINISTRATOR);

$base = JURI::base();
$base = str_replace("components/com_altauserpoints/assets/synch/", "", $base);

// Time execution max (seconds)
$tempsExecMax = 1;
// Time delay between each step (milliseconds)
$tempsRepos = 100;
	
function Synchro( $start, $tempsExec )
{
	global $mainframe;
	global $base;
	
	// check time execute
	list($usec, $sec) = explode(' ', microtime());
	$start_time=(float)$usec + (float)$sec;
	$new_time = $start_time;

	$db			=  JFactory::getDBO();
	$jnow		=  JFactory::getDate();
	$now		=  $jnow->toSql();	
	
	// get params definitions
	$params = JComponentHelper::getParams( 'com_altauserpoints' );		

	$prefixNewReferreid = strtoupper($params->get('prefix_selfregister', ''));
	//$prefixNewReferreid = "AUPRS-";
	
	if ( $start ) {
		$i = $start;	
	} else $i = 0;
	
	$query = "SELECT id, username FROM #__users";
	$db->setQuery($query);
	$users = $db->loadObjectList();
	
	$numusers = count($users);

	// rule new user
	$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_newregistered' AND `published`='1'";
	$db->setQuery( $query );
	$ruleNewUser = $db->loadObjectList();
	
		if ( $users ) {
			//foreach ( $users as $user ) {
			for ($i, $n=$numusers; $i < $n; $i++) {			
					
				if ($new_time - $start_time < $tempsExec){
				
					$user = $users[$i];
					
					// check if user exist
					$q = "SELECT referreid FROM #__alpha_userpoints WHERE userid=".$db->quote($user->id)." ";
					$db->setQuery($q);
					$checkuser = $db->loadResult();
					// if not exist -> create
					if ( !$checkuser ) {
											
						/*if ( $params->get('referralIDtype')=='r' ) {
							$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));
						} elseif ( $params->get('referralIDtype')=='u' )
						{
							 $newreferreid = $prefixNewReferreid . strtoupper($user->username);
							 $newreferreid = str_replace( ' ', '-', $newreferreid );				
							$newreferreid = str_replace( ',', '-', $newreferreid );
							$newreferreid = str_replace( "'", "-", $newreferreid );	 
						}*/
						if ( !$params->get('referralIDtype') ) {
							$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));
						} else $newreferreid = $prefixNewReferreid . strtoupper($user->username);
						
						JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
						
						$row = JTable::getInstance('userspoints');
						
						// insert this new user into altauserpoints table
						$row->id			= NULL;
						$row->userid		= $user->id;
						$row->referreid		= $newreferreid;
						$row->points		= $ruleNewUser[0]->points;
						$row->max_points	= 0;
						$row->last_update	= $now;
						$row->referraluser	= '';						
						
						/*$query2 = "SELECT * FROM #__user_profiles WHERE user_id='".$user->id."'";
						$db->setQuery($query2);
						$user_profile = $db->loadObjectList();						
						
						if ($user_profile)
						{							
							foreach ( $user_profile as $jprofil)
							{
								$value = str_replace('"', '', $jprofil->profile_value);
								$row = checkProfileKey($jprofil->profile_key, $row, $value);
							}							
						}*/						
						
						if (!$row->store()) {
							$error = $row->getError();	
							echo '<script language="Javascript">
							<!--
							parent.document.location.replace("'.$base.'index.php?option=com_altauserpoints&task=cpanel&synch='.$error.'");				
							// -->
							</script>';
							exit();						
						}
						
						// save new points into alphauserpoints table details
						$row2 = JTable::getInstance('userspointsdetails');
						$row2->id				= NULL;
						$row2->referreid		= $newreferreid;
						$row2->points			= $ruleNewUser[0]->points;
						$row2->insert_date		= $now;
						$row2->expire_date 		= $ruleNewUser[0]->rule_expire;
						$row2->status			= $ruleNewUser[0]->autoapproved;
						$row2->rule				= $ruleNewUser[0]->id;
						$row2->approved			= $ruleNewUser[0]->autoapproved;
						$row2->datareference	= JText::_( 'AUP_WELCOME' );
						$row2->enabled			= 1;
								
						if (!$row2->store()) {
							$error = $row2->getError();
							echo '<script language="Javascript">
							<!--
							parent.document.location.replace("'.$base.'index.php?option=com_altauserpoints&task=cpanel&synch=$error");
							// -->
							</script>';
							exit();
						}
					}					
					
				} else {
				
					break;
				
				}
				
				list($usec, $sec) = explode(" ", microtime());
				$new_time=(float)$usec + (float)$sec;				
			
			} // for $i=

			if ($i==$numusers) {
				$start=-1;
			} else {
				$start=$i;
			}
		
			return $start;
			
		}  // if users
				
}

// Manage autorun page
header("Expires: Mon, 1 Dec 2003 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Synchronization Users</title>
	<meta http-equiv="CONTENT-TYPE" content="text/html; charset=iso-8859-1"/>
	<meta http-equiv="CONTENT-LANGUAGE" content="FR"/>
	<meta http-equiv="Cache-Control" content="no-cache/"/>
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="Expires" content="-1"/>
</head>
<body style="font-family: Verdana; font-size: 12px">
	<div>
		<p>
			
			<?php
				$run = 0;
				if (isset($_GET['run'])) $run = $_GET['run'];
				$start = 0;
				if (isset($_GET['start'])) $start = $_GET['start'];
				// Display running
				if ($run == 1)
				{
					if ($start>0 ) {			
						echo " " . $start . " " . strtolower(JText::_( 'AUP_USERS' )) . " ...";		
					} elseif ( $start==-1 ) {					
					    echo JText::_( 'AUP_PLEASE_WAIT' ); 				
					} else echo " " . JText::_( 'AUP_PLEASE_WAIT' );
					 
				}
				// call script
				$start = Synchro( $start, $tempsExecMax );
				// if end
				if (($run==1) and ($start == -1))
				{					
					echo '<script language="Javascript">
					<!--
					parent.document.location.replace("'.$base.'index.php?option=com_altauserpoints&task=cpanel&synch=end");
					console.log("'.$base.'index.php?option=com_altauserpoints&task=cpanel&synch=end");
					// -->
					</script>';
					exit();
				}
			?>
		</p>
		<?php
			if ($run == 1)
			{
				// If not end...
				if ($start > -1)
				{
					// Reload script
					echo ("<script language=\"JavaScript\"
						type=\"text/javascript\">window.setTimeout('location.href=\"".$_SERVER["PHP_SELF"]."?start=$start&run=1&tmpl=component\";',500+$tempsRepos);
						console.log(\"".$_SERVER["PHP_SELF"]."?start=$start&run=1\");
						</script>\n");
				}
			}
		?>
</div>	
</body>