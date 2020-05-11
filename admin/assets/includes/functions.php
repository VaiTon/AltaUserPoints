<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2016-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined("_ALTAUSERPOINTS_SHOW_DEPRECATED_PLUGIN_INSTALL")) {
   DEFINE( "_ALTAUSERPOINTS_SHOW_DEPRECATED_PLUGIN_INSTALL", "0" );
}
if(!defined("_ALTAUSERPOINTS_WIDTH_POPUP_CONFIG")) {
   DEFINE( "_ALTAUSERPOINTS_WIDTH_POPUP_CONFIG", "580" );
}
if(!defined("_ALTAUSERPOINTS_HEIGHT_POPUP_CONFIG")) {
   DEFINE( "_ALTAUSERPOINTS_HEIGHT_POPUP_CONFIG", "480" );
}
$document = JFactory::getDocument();
$style = '.icon-32-export { background-image: url(../administrator/components/com_altauserpoints/assets/images/icon-32-export.png); }';
$document->addStyleDeclaration( $style );
function curlDetect() {
	if (function_exists('curl_init')) {
		return true;
	} else return false;
}
 function getFormattedPointsAdm( $points ){
 
	// get params definitions
	$params = JComponentHelper::getParams( 'com_altauserpoints' );		
	$formatPoints = $params->get( 'formatPoints', 0 );
	
	switch( $formatPoints ){
		case "1":
			$fpoints = number_format($points, 2, '.', ',');
			break;
		case "2":
			$fpoints = number_format($points, 2, ',', '');
			break;
		case "3":
			$fpoints = number_format($points, 2, ',', ' ');
			break;
		case "4":
			$fpoints = number_format($points, 0);
			break;
		case "5":
			$fpoints = number_format($points, 0, '', ' ');
			break;
		case "6":
			$fpoints = number_format($points, 0, '', ',');
			break;
		case "7":				
			$fpoints = number_format(floor($points), 0);
			break;
		case "8":
			$fpoints = number_format(floor($points), 0, '', ' ');
			break;
		case "9":
			$fpoints = number_format(floor($points), 0, '', ',');
			break;		
		case "0":
		default:
			$fpoints = $points; 
	}		
	return $fpoints;
	
 }
function isIEAdm () {
	$document = JFactory::getDocument();
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/MSIE/i', $user_agent)) {
		$juribase = str_replace('/administrator', '', JURI::base());
		$document->addScript($juribase.'components/com_altauserpoints/assets/js/html5.js');
	}
}
function createURLQRcodePNG( $url='', $width='200', $path='' ) {
	
	if (!$url) return;
	
	require_once JPATH_SITE.'/components/com_altauserpoints/assets/barcode/BarcodeQR.php';
	
	$qrcode = new BarcodeQR();	
	$qrcode->url( $url );
	$qrcode->draw( $width, $path );
	
}
function aup_CopySite ($align='center')
{
	// Get Copyright for Backend
	$copyStart = 2016; 
	$copyNow = date('Y');  
	if($copyStart == $copyNow)
		$copySite = $copyStart;
	else
		$copySite = $copyStart." - ".$copyNow ;

	$f = 'manifest.xml';
	$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/'.$f);
	
	$_copyright =  '<br />
	<div align="'.$align.'">
	<span><b>AltaUserPoints <a href="https://www.nordmograph.com/extensions/updateserver/changelog/com_altauserpoints.xml" target="_blank">Version '.$xml->version.'</a> </b> &copy; '.$copySite.' - Bernard Gilly - Adrien Roussel <a href="https://www.nordmograph.com/extensions/index.php?option=com_virtuemart&view=category&virtuemart_category_id=8&Itemid=58" target="_blank">www.nordmograph.com</a><br /><a href="https://www.nordmograph.com/extensions/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=120&virtuemart_category_id=8&Itemid=58" target="_blank" >Download</a> 
	| <a href="https://www.nordmograph.com/extensions/index.php?option=com_kunena&view=category&catid=151&Itemid=645" target="_blank" >Support Forum</a> | <a href="https://www.nordmograph.com/extensions/index.php?option=com_content&view=category&id=74&Itemid=288" target="_blank">API Documentation</a><br />AltaUserPoints is Free Software released under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL License</a></span></div>';
	echo $_copyright;
}
function aup_createIconPanel( $link, $class, $text, $javascript='' ) {
	if ( $link ) 
	{
	?>
	<div class="pull-left">
		<ul style="list-style:none;">
			<li>
				<a class="btn btn-large thumbnail height-90 width-100 " href="<?php echo $link; ?>" <?php echo $javascript; ?>>
					<h1><i class="<?php echo $class; ?> hasTooltip" title="<?php echo $text; ?>"></i></h1>
					<div class="clearfix"></div>
					<span class="small"><?php echo $text; ?></span>
				</a>
			</li>
		</ul>
	</div>
	<?php
	} else {
	?>
	<div class="pull-left">
		<ul style="list-style:none;">
			<li>
				<a class="btn thumbnail height-90 width-100 center" href="" <?php echo $javascript; ?>>
					<img src="<?php echo $image; ?>" alt="<?php echo $text; ?>" align="top" style="margin-top: 10px;" border="0" />
					<div class="clearfix"></div>
					<span class="small" style="color:#BCBCBC;"><?php echo $text; ?></span>
				</a>
			</li>
		</ul>
	</div>
	<?php
	}
	
}
function nicetimeAdm($date, $offset=1)
{
	$config = JFactory::getConfig();
	$tzoffset = $config->get('config.offset');
	
	if(empty($date)) {
		return "No date provided";
	}
	
	$datetimestamp = strtotime($date);
	if ( $offset ) {
		$date = date('Y-m-d H:i:s', $datetimestamp + ($tzoffset * 60 * 60));
	} else {
		$date = date('Y-m-d H:i:s', $datetimestamp);
	}
   
	$period          = array(JText::_( 'AUP_SECOND' ), JText::_( 'AUP_MINUTE' ), JText::_( 'AUP_HOUR' ), JText::_( 'AUP_DAY' ), JText::_( 'AUP_WEEK' ), JText::_( 'AUP_MONTH' ), JText::_( 'AUP_YEAR' ), JText::_( 'AUP_DECADE' ));
	$periods         = array(JText::_( 'AUP_SECONDS' ), JText::_( 'AUP_MINUTES' ), JText::_( 'AUP_HOURS' ), JText::_( 'AUP_DAYS' ), JText::_( 'AUP_WEEKS' ), JText::_( 'AUP_MONTHS' ), JText::_( 'AUP_YEARS' ), JText::_( 'AUP_DECADES' ));
	
	$lengths         = array("60","60","24","7","4.35","12","10");
   
	//$now             = time();
	$now = strtotime(gmdate('Y-m-d H:i:s')) + ($tzoffset * 60 * 60);
	$unix_date       = strtotime($date);
   
	   // check validity of date
	if(empty($unix_date)) {   
		return "Bad date";
	}
	// is it future date or past date
	if($now > $unix_date) {  
		$difference     = $now - $unix_date;
		$tense         = JText::_( 'AUP_AGO' );
	   
	} else {
		$difference     = $unix_date - $now;
		$tense         = JText::_( 'AUP_FROM_NOW' );
	}
   
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
   
	if($difference != 1) {
		//return "$difference $periods[$j] {$tense}";
		$nicetime = $difference . " " . $periods[$j];
		return sprintf($tense, $nicetime);			
	} else {
		//return "$difference $period[$j] {$tense}";
		$nicetime = $difference . " " . $period[$j];
		return sprintf($tense, $nicetime);		
	}
}

function getIdPluginFunction( $plugin_function )
{
	$db	   = JFactory::getDBO();
	$query = "SELECT `id` FROM #__alpha_userpoints_rules 
	WHERE `plugin_function`='$plugin_function'";
	$db->setQuery( $query );
	$plugin_id = $db->loadResult();
	return	$plugin_id;	
}
function getCpanelToolbar()
{
	JToolBarHelper::custom( 'cpanel', 'dashboard.png', 'dashboard.png', JText::_('AUP_CPANEL'), false );
	JToolBarHelper::divider();
}
function getPrefHelpToolbar()
{
	$language 	= JFactory::getLanguage();
	$tag 		= $language->getTag();
	
	JToolBarHelper::divider();
	JToolBarHelper::custom( 'rules', 'move.png', 'move.png', JText::_('AUP_RULES'), false );	
	// Options button.
	if (JFactory::getUser()->authorise('core.admin', 'com_altauserpoints')) {
		JToolBarHelper::preferences('com_altauserpoints');
	}
	JToolBarHelper::divider();
	JToolBarHelper::help( 'screen.altauserpoints', true );
}

function getReferreidByID( $id )
{	
	if ( !$id ) return;	
	// get referre id
	$db	   = JFactory::getDBO();
	$q = "SELECT referreid FROM #__alpha_userpoints WHERE `id`=".$db->quote($id)." ";
	$db->setQuery( $q );
	$referreid = $db->loadResult();
	return $referreid;
}
?>