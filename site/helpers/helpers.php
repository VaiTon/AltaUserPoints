<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
function getCopyrightNotice ()
{
	$cparams 	= JComponentHelper::getParams('com_altauserpoints');
	$hide_credits = $cparams->get('hide_credits',0);
	
	$copyStart = 2015; 
	$copyNow = date('Y');  
	if ($copyStart == $copyNow) 
	{ 
			$copySite = $copyStart;
	} 
	else 
	{
			$copySite = $copyStart."-".$copyNow ;
	}
	if(!$hide_credits)
	{
		/** 
		*  IMPORTANT !
		*  Provide copyright on frontend
		*  If you remove or hide this lines below,
		*  please make a donation if you find AltaUserPoints useful
		*  and want to support its continued development.
		*  Your donations help by hardware, hosting services and other expenses that come up as we develop,
		*  protect and promote AltaUserPoints and other free components.
		*  You can donate on https://www.nordmograph.com/extensions
		*
		*/	
	
		echo "<p>&nbsp;</p><div style=\"clear:both;text-align:center;\">
		<span>Powered by <a href=\"https://www.nordmograph.com/extensions\" 
		target=\"_blank\">AltaUserPoints</a> &copy; $copySite</span>
		</div><p>&nbsp;</p>";
	}
}
	
function getLinkToInvite( $referrerid, $systemRegistration ) 
{
	$uri        = JURI::getInstance();
	$base    	= $uri->toString( array('scheme', 'host', 'port'));
			
	$referrer_link = '';
	
			// prepare link for register
			$referrer_exist  = ( $referrerid ) ? "&referrer=$referrerid" : "";
			
			switch ( $systemRegistration ) {
			
				case 'cb' :
					$referrer_link    	= "index.php?option=com_comprofiler&task=registers$referrer_exist";
					break;
					
				case 'cbe' :
					$referrer_link    	= "index.php?option=com_cbe&task=registers$referrer_exist";
					break;
					
				case 'VM' : 
					$referrer_link 		= "index.php?option=com_virtuemart&page=shop.registration$referrer_exist"; 
					break;	
					
				case 'hikas' : 
					$referrer_link 		= "index.php?option=com_hikashop&view=user&layout=form$referrer_exist"; 
					break;		
					
				case 'js':
					$referrer_link 		= "index.php?option=com_community&view=register$referrer_exist"; 
					break;

				case 'ose':
					$referrer_link 		= "index.php?option=com_osemsc&view=register&layout=onestep$referrer_exist"; 
					break;
					
				case 'ext': // extendedreg
					$referrer_link    	= "index.php?option=com_extendedreg&task=register$referrer_exist";
					break;
				case 'es':
				  $referrer_link		= "index.php?option=com_easysocial&view=registration$referrer_exist";
				  break;
				case 'ep':
				  $referrer_link		= "index.php?option=com_users&view=registration$referrer_exist";
				  break;	
				case 'J!' :
				case 'joomunity' :			
				default   :			
					$referrer_link    	= "index.php?option=com_users&view=registration$referrer_exist";
					
			}			
			return $base.JRoute::_( $referrer_link , false );
	}
	
	function getAvatar( $useAvatarFrom, $userinfo, $height='', $width='', $class='' ) 
	{		
		$db	    = JFactory::getDBO();
		
		$avatar = '';
		
		$setheight = ( $height!='' )? 'height="'.$height.'"' : '';
		$setwidth  = ( $width!=''  )? 'width="'.$width.'"'   : 'width="'.$height.'"';
		
		if ( $width=='' ) $width = $height;		
		
		$defaultAvatarAUP = JURI::root() . 'components/com_altauserpoints/assets/images/avatars/generic_gravatar_grey.png';
		
		switch ( $useAvatarFrom ) 
		{	
			case 'gravatar':
				$email   = $userinfo->email ;
				$gravatar_url = 'https://www.gravatar.com/avatar/';
				$gravatar_url .= md5( strtolower(trim($email)) );
				$gravatar_url .= '?d=' . urlencode($defaultAvatarAUP);	
				if ( $height ) {
					$gravatar_url .= '&amp;s=$height';		
				} else $gravatar_url .= '&amp;s=80';		
				$avatar = '<img src="'.$gravatar_url.'" alt=""/>';
				break;			
			case 'kunena':
				if(!defined("_AUP_KUNENA_PATH")) {
					define('_AUP_KUNENA_PATH', JPATH_ROOT . '/media/kunena');
				}
				if(!defined("_AUP_KUNENA_LIVE_PATH")) {
					define('_AUP_KUNENA_LIVE_PATH', JURI::base(true) . '/media/kunena');		
				}				
				$Avatarname = $userinfo->avatar;
				$q = "SELECT a.*, b.* FROM #__kunena_users as a 
				LEFT JOIN #__users as b on b.id=a.userid 
				where a.userid=".$db->quote($userinfo->id);			
				$db->setQuery( $q );
				$userProfilKunena = $db->loadObject();
				$fb_avatar = @$userProfilKunena->avatar;		
				if ($fb_avatar != '')
				{
					if(!file_exists( _AUP_KUNENA_PATH . '/avatars/' . $fb_avatar))
					{
						$avatar = _AUP_KUNENA_LIVE_PATH . '/avatars/' . $fb_avatar;
					}
					else
					{
						$avatar = _AUP_KUNENA_LIVE_PATH . '/avatars/' . $fb_avatar;
					}
				}
        		else $avatar = _AUP_KUNENA_LIVE_PATH . '/avatars/nophoto.jpg';
				break;
			case 'cb':
				$q = "SELECT avatar , avatarapproved FROM #__comprofiler 
				WHERE user_id = ".$db->quote($userinfo->id)." ";
				$db->setQuery($q);
				$result = $db->loadObject();
				if(!empty($result->avatar) && $result->avatarapproved=='1')
				{
					$avatar = JURI::base(true)."/images/comprofiler/".$result->avatar;
				} 
				else
				{
					$avatar = JURI::base(true)."/components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png";
				}						
				break;
			case 'cbe':
				global $mosConfig_lang;
				$q = "SELECT avatar, avatarapproved FROM #__cbe WHERE user_id =".$db->quote($userinfo->id)." ";
				$db->setQuery($q);
				$result = $db->loadObject();
				$avatar = $result->avatar;
				if(file_exists(JPATH_ROOT . "/components/com_cbe/images/".$mosConfig_lang)) {
					$uimagepath = JURI::base(true)."/components/com_cbe/images/".$mosConfig_lang."/";
				} else $uimagepath = JURI::base(true)."/components/com_cbe/images/english/";
				if($result->avatarapproved==0) {
					$avatar = $uimagepath . "pendphoto.jpg";
				} elseif($result->avatar=='' || $result->avatar==null) {
					$avatar = $uimagepath . "nophoto.jpg";
				} else $avatar = JURI::base(true)."/images/cbe/".$avatar;						
				break;
			
			case 'jomsocial':
				$q = "SELECT avatar FROM #__community_users WHERE userid =".$db->quote($userinfo->id)." ";	
				$db->setQuery($q);	
				$result = $db->loadResult();	
				if(!empty($result)) {		
					$avatar = JURI::base(false). $result;
				} else {
					$avatar = JURI::base(true)."/components/com_community/assets/default_thumb.jpg"; 	
				}
				break;
			case 'easysocial':
				require_once JPATH_ADMINISTRATOR.'/components/com_easysocial/includes/easysocial.php';
				$esf = ES::user($userinfo->id);
				$avatar = $esf->getAvatar('square');
			break;
			case 'easyblog':
				require_once JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
				//$config = EB::config();
				$eb = EB::user($userinfo->id);
				$avatar = $eb->getAvatar('square');				
			break;
			case 'easyprofile':
				$q = "SELECT avatar FROM #__jsn_users WHERE `id`=".$db->quote($userinfo->id);
				$db->setQuery($q);
				$result = $db->loadResult();
				if(!empty($result)) {		
					$avatar = $result;
				} else {
					$avatar=$defaultAvatarAUP;	
				}				
			break;
			case 'clexus':
				$q = "SELECT picture FROM #__mypms_profiles WHERE `name`=".$db->quote($userinfo->username);
				$db->setQuery($q);
				$result = $db->loadResult();
				if(!empty($result)) {		
					$avatar = $result;
				} else {
					$avatar = $defaultAvatarAUP;	
				}				
				break;
			case 'K2':
				$q = "SELECT image FROM #__k2_users WHERE userID=".$db->quote($userinfo->id)." ";
				$db->setQuery($q);
				$result = $db->loadResult();
				if(!empty($result)) {
					$avatar = JURI::base(true)."/media/k2/users/".$result;
				} else {
					$avatar = $defaultAvatarAUP;	
				}
				break;
			case 'altauserpoints':
				
				if(!defined("_AUP_AVATAR_LIVE_PATH")) {
					define('_AUP_AVATAR_LIVE_PATH', JURI::base(true) . '/components/com_altauserpoints/assets/images/avatars/');
				}
				
				//$usr_avatar = ( $userinfo->avatar!='' ) ? JPATH_COMPONENT . '/assets/images/avatars/' . $userinfo->avatar : JPATH_COMPONENT . '/assets/images/avatars/generic_gravatar_grey.gif' ;
				$usr_avatar = ( $userinfo->avatar!='' ) ? JPATH_ROOT . '/components/com_altauserpoints/assets/images/avatars/' . $userinfo->avatar : JPATH_ROOT . '/components/com_altauserpoints/assets/images/avatars'. '/generic_gravatar_grey.gif' ;
				if(file_exists($usr_avatar)){
					$image = new JImage( $usr_avatar );
					$avatar = $image->createThumbs( array( $width .'x'.$height ), JImage::CROP_RESIZE, JPATH_ROOT . '/components/com_altauserpoints' .'/assets/images/avatars/thumbs' );
					$avatar = myImage::getLivePathImage($avatar); 		
				}else {				
					$avatar = $defaultAvatarAUP;
				}
				
				break;
				
			case 'jomWALL': 
				// for version 2.5
				$config 					= JComponentHelper::getParams('com_awdwall');
				$template 					= $config->get('temp', 'blue');
				$avatarintergration 		= $config->get('avatarintergration', '0');
				
				$q 	= "SELECT facebook_id FROM #__jconnector_ids WHERE user_id = "  . $db->quote($userId);
				$db->setQuery($q);
				$facebook_id = $db->loadResult();
				if($facebook_id)
				{
					$avatar='https://graph.facebook.com/'.$facebook_id.'/picture?type=large';
				}
				else
				{
					
					$q 	= 'SELECT avatar FROM #__awd_wall_users WHERE user_id = ' . (int)$userId;
					$db 	=  JFactory::getDBO();
					$db->setQuery($q);
					$img = $db->loadResult();		
					
					if($img == NULL){
						$avatar = JURI::root() . "components/com_awdwall/images/".$template."/".$template."51.png";
					}else{
						$avatar = JURI::root() . "images/wallavatar/" . $userId . "/thumb/tn51" . $img;
					}
				}
			
				if($avatarintergration==1) // k2
				{
						if(file_exists(JPATH_SITE . '/components/com_k2/k2.php'))
						{
							require_once (JPATH_SITE . '/components/com_k2/helpers/utilities.php');
						
						$avatar=K2HelperUtilities::getAvatar($userId);
						}
					
				}
				else if($avatarintergration==2) // easyblog
				{
						if(file_exists(JPATH_SITE . '/components/com_easyblog/easyblog.php'))
						{
							require_once (JPATH_SITE . '/components/com_easyblog/helpers/helper.php');
						
						$blogger	= EasyBlogHelper::getTable( 'Profile', 'Table');
						$blogger->load( $userId );
						$avatar=$blogger->getAvatar();
						}
				}
				else if($avatarintergration==3) // altauserpoint
				{
						if(file_exists(JPATH_SITE . '/components/com_altauserpoints/altauserpoints.php'))
						{
							require_once (JPATH_SITE . '/components/com_altauserpoints/helper.php');
							require_once (JPATH_SITE . '/components/com_altauserpoints/helpers/helpers.php');
						
							$_user_info = AltaUserPointsHelper::getUserInfo ( $referrerid='', $userId  );
							$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
							$useAvatarFrom = $com_params->get('useAvatarFrom');
							$height = 50;
							$width=50;
							$avatar = getAvatar( $useAvatarFrom, $_user_info, $height,$width);	
							$doc = new DOMDocument();
							$doc->loadHTML($avatar);
							$imageTags = $doc->getElementsByTagName('img');
							
							foreach($imageTags as $tag) {
								$avatar=$tag->getAttribute('src');
							}
						}
				}
				break;
			default:
				$avatar = '';
		}		
		
		if ( $avatar && $useAvatarFrom!='gravatar' && $useAvatarFrom!='jomsocial') {
			
			$avatar = '<img src="' . $avatar . '" border="0" alt="" ' . $setheight . $setwidth . ' ' . $class . ' />';
			
		} elseif ( $useAvatarFrom=='jomsocial' ){ 
			$avatar = '<img src="' . $avatar . '" border="0" alt="" ' . $setheight . $setwidth . ' />';
		}
		
		return $avatar;	
	}
	
	function getProfileLink( $profilechoice, $userinfo, $xhtml=true )
	{		
		
		switch ( $profilechoice )
		{
			case 'ku' :			
				$profilLink = JRoute::_('index.php?option=com_kunena&func=fbprofile&userid='.$userinfo->userid, $xhtml);
				$menu = 'com_kunena';
				break;		
			case 'cb' :
				$profilLink = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$userinfo->userid,$xhtml);
				$menu = 'index.php?option=com_comprofiler&view=userprofile';
				break;				
			case 'cbe' :
				$profilLink = JRoute::_( 'index.php?option=com_cbe&task=userProfile&user=' . $userinfo->userid, $xhtml);
				$menu = 'index.php?option=com_cbe&task=userProfile';
				break;
			case 'js' :
				$profilLink = JRoute::_( 'index.php?option=com_community&view=profile&userid=' . $userinfo->userid, $xhtml);
				$menu = 'index.php?option=com_community&view=profile';
				break; 
			case 'es' :
	    		$profilLink = JRoute::_('index.php?option=com_easysocial&view=profile&id='.$userinfo->userid, $xhtml);
				$menu = 'index.php?option=com_easysocial&view=profile';
				break; 
			case 'eb' :
	    		$profilLink = JRoute::_('index.php?option=com_easyblog&view=blogger&layout=listings&id='.$userinfo->userid, $xhtml);
				$menu = 'index.php?option=com_easyblog&view=blogger';
				break; 	
			case 'ep' :
	    		$profilLink = JRoute::_('index.php?option=com_jsn&view=profile&id='.$userinfo->userid, $xhtml);
				$menu = 'index.php?option=com_jsn&view=profile';
				break; 	
			case 'j!' :
				$profilLink = JRoute::_( 'index.php?option=com_users&view=profile', $xhtml);
				$menu = 'index.php?option=com_users&view=profile';
				break;
				
			case 'jw' :
				if (is_file ( JPATH_ROOT . '/components/com_awdwall/helpers/user.php' ))
				{
					include_once JPATH_ROOT . '/components/com_awdwall/helpers/user.php';
					$itemId = AwdwallHelperUser::getComItemId();
					$profilLink = JRoute::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid=' .  $userinfo->userid . '&Itemid=' . $itemId, false);
				} else $profilLink = '';	
				$menu = '';			
				break;					
				
			default :
				// AUP Link Profile	
				$profilLink = JRoute::_( 'index.php?option=com_altauserpoints&view=account&userid=' . $userinfo->referreid , $xhtml);
				$menu = 'index.php?option=com_altauserpoints&view=account';
				break;	
		}
		if($menu !=''){	
			$db	   	= JFactory::getDBO();
			$lang 	= JFactory::getLanguage();
			$q = "SELECT id FROM #__menu 
			WHERE `link`=".$db->quote($menu)." 
			AND `type`='component' AND `published`='1'  AND access='1' AND ( language =".$db->quote($lang->getTag())." OR language='*')";
			$db->setQuery( $q );
			$profilLink .= '&Itemid=' . $db->loadResult();
		}
		
		return $profilLink;
	
	}
	
	function nicetime($date, $showTense=1)
	{		
		$config = JFactory::getConfig();
		$tzoffset = $config->get('config.offset');
		
		if(empty($date)) {
			return "No date provided";
		}
		
		$datetimestamp = strtotime($date);
		$date = date('Y-m-d H:i:s', $datetimestamp + ($tzoffset * 60 * 60));
	   
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
			$nicetime = $difference . " " . $periods[$j];
			if ( $showTense ) {
				return sprintf($tense, $nicetime);
			} else return $nicetime;
		} else {		
			$nicetime = $difference . " " . $period[$j];
			if ( $showTense ) {
				return sprintf($tense, $nicetime);
			} else return $nicetime;
		}

	}

	function _getIconCategoryRule( $category )
	{
		$icon = ( $category!='' ) ? '<img src="'.JURI::root() . 'components/com_altauserpoints/assets/images/categories/'.$category.'.gif" alt="" align="absmiddle" />' : '';
		return $icon;
	}

	function _updateProfileViews ( $referreid ) 
	{		
		$db	   = JFactory::getDBO();
		$q = "UPDATE #__alpha_userpoints SET profileviews = profileviews + 1 
		WHERE `referreid`=" . $db->quote($referreid). "";
		$db->setQuery( $q );
		$db->execute();
	}
	
	
	function isIE () {
		$document = JFactory::getDocument();
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/MSIE/i', $user_agent)) {
			$juribase = str_replace('/administrator', '', JURI::base());
			$document->addScript($juribase.'components/com_altauserpoints/assets/js/html5.js');
		}
	}
	
	 
	 function getFormattedPoints( $points ){
	 
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
	 
	 
class myImage extends JImage
{
    function __construct() { }

    public static function getPathImage($avatar)
    {
        return $avatar[0]->path;
    }
	
	public static function getLivePathImage($avatar)
	{
		$path = $avatar[0]->path;
		$livePathAvatar = str_replace('\\', '/',$path);
		$pos = strpos($livePathAvatar, 'components');	
		$livePathAvatar = substr_replace($livePathAvatar, JURI::base(), 0, $pos);
		
		return $livePathAvatar;
	}
}

?>