<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

 // no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined("_AUP_IMAGE_LIVE_PATH")) {
	define('_AUP_IMAGE_LIVE_PATH', JURI::base(true) . '/components/com_altauserpoints/assets/images/');
}

// current user
$user =  JFactory::getUser();
$document = JFactory::getDocument();

$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
$_profilelink	= $com_params->get('linkToProfile');

//$line=1; // use for alternate color
$db = JFactory::getDBO();
$nullDate = $db->getNullDate();
$nullDate2 = "0000-00-00";

// update profile views counter
if ( $this->referreid!=@$_SESSION['referrerid'] ) {	
	_updateProfileViews ( $this->referreid );
	$profilviews = $this->userinfo->profileviews+1;
} else $profilviews = $this->userinfo->profileviews;

?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; 
echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'page-profile')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'page-profile', JText::_('AUP_PROFILE', true)); ?>
	
<?php
if ( $this->avatar ) { 
	?>
		<div style="float:right;padding: 0 10px 0 10px;">
		<?php echo $this->avatar ; ?>
		</div>
	<?php
} 

echo '<h1>' . $this->myname . '</h1>';
echo "<b>" . JText::_('AUP_USERNAME') . " : " . $this->myusername . "</b><br />";
echo "<b>ID</b> : " . $this->referreid . "<br />";
$points_color = '';
if ($this->currenttotalpoints >0)
$points_color = 'badge badge-info';
if ($this->currenttotalpoints<0)
$points_color = 'badge badge-warning';
if ($this->currenttotalpoints==0)
$points_color = 'badge';

echo "<b>" . JText::_('AUP_MYPOINTS') . "</b> : ";
?>
<span class="<?php echo $points_color ; ?>"><?php echo getFormattedPoints( $this->currenttotalpoints ); ?></span>
<?php
if ( @$this->userrankinfo )
{ 
	if ( $this->userrankinfo->image )
	{
		$pathimage = JPATH_COMPONENT . '/assets/images/awards/large/'.$this->userrankinfo->image;
		$image = new JImage( $pathimage );
		$userrankimg = $image->createThumbs( 
			array( '16x16' ), 
			JImage::CROP_RESIZE, 
			JPATH_COMPONENT .'/assets/images/awards/large/thumbs' 
		);
		$userrankimg = myImage::getLivePathImage($userrankimg);
		echo '<img src="'.$userrankimg.'" alt="" />';
	}
 	echo " (". $this->userrankinfo->rank . ")";
}
echo "<br />";
echo "<b>" . JText::_('AUP_LASTUPDATE') . "</b> : " . JHTML::_('date', $this->lastupdate, JText::_('DATE_FORMAT_LC2') ) . "<br />";
echo "<b>" . JText::_('AUP_MEMBER_SINCE') . "</b> : " . JHTML::_('date', $this->userinfo->registerDate, JText::_('DATE_FORMAT_LC3') ) . "<br />";
echo "<b>" . JText::_('AUP_LAST_ONLINE') . "</b> : " . nicetime( $this->userinfo->lastvisitDate ) . "<br />";
if ( $this->referraluser!='' )
{
	if ( $this->params->get( 'show_links_to_users', 1) )
	{
		$_user_info = AltaUserPointsHelper::getUserInfo ( $this->referraluser );
		$linktoprofilreferral = getProfileLink( $_profilelink, $_user_info );
		$linktoprofilreferral =  "<a href=\"" . JRoute::_($linktoprofilreferral) . "\">" . $this->referralname . "</a>";
	} 
	else
		$linktoprofilreferral = $this->referralname;
	echo "<b>" . JText::_('AUP_MYREFRERRALUSER') . "</b> : " ;
	echo $this->referraluser . " (" . $linktoprofilreferral . ")";
	echo "<br />";
}
/*if ( $this->params->get( 'show_links_to_users', 1) )
{
	echo "<b>" . JText::_('AUP_PROFILE_VIEWS') . "</b> : <span class=\"badge badge-info\">" . $profilviews . "</span><br />";
}*/

 $rule_enabled = AltaUserPointsHelper::checkRuleEnabled( 'sysplgaup_invitewithsuccess', 0, '' );
if($rule_enabled)
{
	$referrer_link = getLinkToInvite( $this->referreid, $this->cparams->get('systemregistration')  );
	?><br />
	
	<br />
	<div class="alert alert-info"><span aria-hidden="true" class="icon-info-circle"></span>&nbsp;&nbsp;<?php echo JText::_( 'AUP_INVITATION_LINK' ); ?><br /><input type="text"  name="referrer_link" id="referrer_link" onfocus="select();" readonly="readonly" class="inputbox" value="<?php echo $referrer_link; ?>" /></div>		 
	
	<?php
}

// Integration Uddeim
// ******************
if ( $this->referreid==@$_SESSION['referrerid'] && $this->enabledUDDEIM && $user->id==$this->userinfo->userid  ) {
	require_once JPATH_SITE . '/components/com_uddeim/uddeim.api.php';
	require_once JPATH_COMPONENT.'/helpers/uddeim_alert.php';
	require_once JPATH_COMPONENT.'/helpers/uddeim_mailbox.php';
}
// end integration Uddeim


// start About Me information
if ( $_profilelink=='' )
{
	echo JHtml::_('bootstrap.startAccordion', 'mySlide', array());	
	if( $this->params->get( 'showQRCode', 1) )
	{		
		echo JHtml::_('bootstrap.addSlide', 'mySlide', JText::_('AUP_QRCODE'), 'slide-qrcode');		
		echo '<div id="QRcodeInvite">'; 
		$gcode_url ='https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.urlencode($referrer_link).'&choe=UTF-8';
		echo '<img src="'.$gcode_url.'.png" alt="QRCode" />';
		echo '</div>';			
		echo JHtml::_('bootstrap.endSlide'); 
	}
	if ( $this->rowsreferrees && $this->params->get( 'show_tab_referrees', 1 ))
	{
		echo JHtml::_('bootstrap.addSlide', 'mySlide', JText::_('AUP_MYREFERREES'), 'slide-referrees');
		?>
		
		<table class="category table table-striped table-bordered table-hover">
		    <thead>
				<tr>
				  <th id="categorylist_header_id">ID</th>
				  <th id="categorylist_header_username"><?php echo JText::_( 'AUP_USERNAME' ); ?></th>
				  <?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
					<th id="categorylist_header_name"><?php echo JText::_( 'AUP_NAME' ); ?></th>
				  <?php } ?>
				</tr>
			</thead>
			<tbody>
		<?php
		foreach ( $this->rowsreferrees as $referrees ) {		
			echo "<tr><td headers=\"categorylist_header_title\" class=\"list-title\">" . $referrees->referreid . "</td>";		
			if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AltaUserPointsHelper::getUserInfo ( $referrees->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $referrees->username . "</a>";
			} else $linktoprofil = $referrees->username;
			echo "<td headers=\"categorylist_header_title\" class=\"list-title\">" . $linktoprofil . "</td>";			
			if ( $this->params->get( 'show_name_cols' )) {
				echo "<td headers=\"categorylist_header_title\" class=\"list-title\">" . $referrees->name . "</td>";
			}			
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
		echo JHtml::_('bootstrap.endSlide');
	}
	
	if ( $this->params->get( 'show_tab_statistics', 1 ) ) {
		echo JHtml::_('bootstrap.addSlide', 'mySlide', JText::_('AUP_STATISTICS'), 'slide-statistics');
		?>

<table class="category table table-striped table-bordered table-hover">
	<?php if ( $this->params->get( 'show_community_points', 1 ) ) { ?>
	<tr>
	  <td><?php echo JText::_( 'AUP_TOTAL_COMMUNITY_POINTS' ); ?></td>
	  <td><span class="badge badge-info"><?php echo getFormattedPoints( $this->totalpoints ); ?></span></td>
	</tr>
	<?php } ?>
	
	<tr>
	  <td><?php echo JText::_( 'AUP_MYPOINTS' ); ?></td>
	  <td><span class="<?php echo $points_color; ?>"><?php echo getFormattedPoints( $this->currenttotalpoints ); ?></span></td>
	</tr>	
	<?php if ( $this->params->get( 'show_percentage', 1 ) ) { ?>
	<tr>
	  <td><?php echo JText::_( 'AUP_MY_PERCENTAGE_OF_POINTS' ); ?></td>
	  <td><span class="badge badge-info">
	  <?php
	  	if ( $this->totalpoints > 0 ) {
	  		$percent = (($this->currenttotalpoints)/($this->totalpoints))*100;
		} else $percent = 0;
		if ( $percent >= 1 ) {
				echo number_format($percent,1,'.','')."%";
		} elseif ( $percent >= 0 && $percent < 1 ) {
				echo number_format($percent,3,'.','')."%";
		}
	  ?>
	  </span>
	  </td>
	</tr>
	<?php } ?>
</table>
<table class="category table table-striped table-bordered table-hover">
<thead>
	<tr>
	  <th id="categorylist_header_mystatistics"><?php echo JText::_( 'AUP_MY_STATISTICS' ); ?></th>
	  <th id="categorylist_header_pointsearned"><?php echo JText::_( 'AUP_POINTS_EARNED' ); ?></th>
	  <th id="categorylist_header_pointsspent"><?php echo JText::_( 'AUP_POINTS_SPENT' ); ?></th>
	</tr>
</thead>
<tbody>
	<tr>
	  <td><?php echo JText::_( 'AUP_SINCE_THE_BEGENNING' ); ?></td>
	  <td><span class="badge badge-info"><?php echo getFormattedPoints( $this->mypointsearned ); ?></span></td>
	  <td><span class="badge badge-warning"><?php echo getFormattedPoints( $this->mypointsspent ); ?></span></td>
	</tr>
	<tr>
	  <td><?php echo JText::_( 'AUP_THIS_MONTH' ); ?></td>
	  <td><span class="badge badge-info"><?php echo getFormattedPoints( $this->mypointsearnedthismonth ); ?></span></td>
	  <td><span class="badge badge-warning"><?php echo getFormattedPoints( $this->mypointsspentthismonth ); ?></span></td>
	</tr>
	<tr>
	  <td><?php echo JText::_( 'AUP_THIS_DAY' ); ?></td>
	  <td><span class="badge badge-info"><?php echo getFormattedPoints( $this->mypointsearnedthisday ); ?></span></td>
	  <td><span class="badge badge-warning"><?php echo getFormattedPoints( $this->mypointsspentthisday ); ?></span></td>
	</tr>
</tbody>
</table>

<?php  if ( $this->params->get( 'show_top10', 1 )) { ?>	
<table class="category table table-striped table-bordered table-hover">
<thead>
	<tr>
	<?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
	  <th id="categorylist_header_name2" width="25%"><?php echo JText::_( 'AUP_NAME' ); ?></th>
	 <?php } ?> 
	 <?php  if ( $this->params->get( 'show_username_cols', 1 ) || (!$this->params->get( 'show_name_cols' ) && !$this->params->get( 'show_username_cols' ) ) ) { ?>
	  <th id="categorylist_header_username2"><?php echo JText::_( 'AUP_USERNAME' ); ?></th>
	  <?php } ?> 	  
	 <th id="categorylist_header_blank">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
	  <th id="categorylist_header_points2" width="15%"><?php echo JText::_( 'AUP_POINTS' ); ?></th>
	</tr>
<thead>
<tbody>
	<?php		
		for ($i=0, $n=count( $this->pointsearned ); $i < $n; $i++)
		{
			$row 	= $this->pointsearned[$i];
			
			if ($i==0) {
				$maxpoints = $row->sumpoints;
				$barwidth = 100;
			}
			else {
				if ( $row->sumpoints > 0 )
				{
					$barwidth = @round(($row->sumpoints * 100) / $maxpoints);
				} 
				else
				{
					$barwidth = 0;
				}
			}
	?>
			<tr class="cat-list-row<?php echo $i % 2; ?>">
			<?php  if ( $this->params->get( 'show_name_cols' )) { ?>	
			  <td headers="categorylist_header_title" class="list-title">
			  <?php 
			  if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AltaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $row->name . "</a>";
			  } else $linktoprofil = $row->name;
				
			  	echo $linktoprofil;
			  ?> 
			  </td>
			<?php } ?>
			<?php  if ( $this->params->get( 'show_username_cols', 1 ) || (!$this->params->get( 'show_name_cols' ) && !$this->params->get( 'show_username_cols' ) ) ) { ?>	
			  <td headers="categorylist_header_title" class="list-title">
			  <?php 
			  if ( $this->params->get( 'show_links_to_users', 1) ){
				$_user_info = AltaUserPointsHelper::getUserInfo ( $row->referreid );
				$linktoprofil = getProfileLink( $_profilelink, $_user_info );

				$linktoprofil =  "<a href=\"" . JRoute::_($linktoprofil) . "\">" . $row->username . "</a>";
			  } else $linktoprofil = $row->username;
			  
			  echo $linktoprofil;			  
			  ?> 
			  </td>
			  <?php } ?>
			  <td headers="categorylist_header_title" class="list-title">
				<div class="progress progress-striped active">
					<div class="bar" style="width: <?php echo $barwidth;?>%;"></div>
				</div>
			  </td>
			  <td headers="categorylist_header_title" class="list-title"><span class="badge badge-info"><?php echo getFormattedPoints( $row->sumpoints );?></span></td>
			</tr>
			<?php
		}
	?>
	</tbody>
</table>
		<?php
		}	
		echo JHtml::_('bootstrap.endSlide');
	}
	echo JHtml::_('bootstrap.endAccordion'); 
}
?>
	<?php echo JHtml::_('bootstrap.endTab');

	
	if ( $this->medalslistuser && $this->params->get( 'show_tab_medals' ) ) {

		echo JHtml::_('bootstrap.addTab', 'myTab', 'page-medals', JText::_('AUP_MEDALS', true));
		?>
		<table class="category table table-striped table-bordered table-hover">		
		<thead>
		  <tr>
			<th id="icon_header_blank">&nbsp;			
			</th>
			<th id="icon_header_date">
				<?php echo JText::_('AUP_DATE'); ?>
			</th>
			<th id="icon_header_medals">
				<?php echo JText::_('AUP_MEDALS'); ?>
			</th>
			<th id="icon_header_reason">
				<?php echo JText::_('AUP_REASON_FOR_AWARD'); ?>
			</th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		$i=0;
		foreach ( $this->medalslistuser as $medaluser ) { 
		?>
		<tr class="cat-list-row<?php echo $i % 2; ?>" >
			<td headers="categorylist_header_title" class="list-title">
			<?php			
				if ( $medaluser->image ) {
				$pathimage = JURI::root() . 'components/com_altauserpoints/assets/images/awards/large/';
				?>
				<img src="<?php echo $pathimage.$medaluser->image ; ?>" alt="" />	
			<?php }  ?>
			</td>
			<td headers="categorylist_header_title" class="list-title">
				<?php 
					echo JHTML::_('date',  $medaluser->medaldate,  JText::_('DATE_FORMAT_LC') );
				?>
			</td>
			<td headers="categorylist_header_title" class="list-title">
				<?php 
					echo JText::_( $medaluser->rank );
				?>
			</td>
			<td headers="categorylist_header_title" class="list-title">
				<?php 
					echo JText::_( $medaluser->reason );
				?>
			</td>
		</tr>
		<?php
		$i++; 
		} 
		?>
		</tbody>
		</table>		
		
		<?php echo JHtml::_('bootstrap.endTab'); 
		}
		
	// coupons code
	if ( $this->mycouponscode && $this->referreid==@$_SESSION['referrerid'] && $user->id==$this->userinfo->userid ) {
		echo JHtml::_('bootstrap.addTab', 'myTab', 'page-mycoupons', JText::_('AUP_MYCOUPONS', true));
		?>
		<table class="category table table-striped table-bordered table-hover">
		<thead>
			<tr>
			  <th id="coupon_header_date"><?php echo JText::_( 'AUP_DATE' ); ?></th>
			  <th id="coupon_header_couponcode"><?php echo JText::_( 'AUP_COUPONSCODE' ); ?></th>
			  <th id="coupon_header_detail"><?php echo JText::_( 'AUP_DETAIL' ); ?></th>
			  <th id="coupon_header_points"><?php echo JText::_( 'AUP_POINTS' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $this->mycouponscode as $coupon ) {
			$pos = strpos($coupon->keyreference, '##');
			if ( $pos===false ) {
				$realcoupontitle = $coupon->keyreference; 
			} else {
				$realcoupontitle = substr( $coupon->keyreference, 0, $pos );
			}
			
			?>
			<tr class="cat-list-row<?php echo $i % 2; ?>" >
				<td headers="categorylist_header_title" class="list-title"><?php echo JHTML::_('date',  $coupon->insert_date,  JText::_('DATE_FORMAT_LC2') ); ?></td>
				<td headers="categorylist_header_title" class="list-title"><?php echo $realcoupontitle; ?></td>
				<td headers="categorylist_header_title" class="list-title"><?php echo $coupon->datareference; ?></td>
				<td headers="categorylist_header_title" class="list-title"><?php echo getFormattedPoints( $coupon->points ); ?></td>
			</tr>
		<?php
		}
		?>		
		</tbody>
	</table>
		<?php
		echo JHtml::_('bootstrap.endTab');
		
	}
	
	
// LATEST ACTIVITY
if ( $this->params->get( 'num_item_activities' )!='0' && $this->rowslastpoints ) {

	echo JHtml::_('bootstrap.addTab', 'myTab', 'page-activities', JText::_('AUP_LASTACTIVITY', true));
	
	if ( $this->params->get( 'num_item_activities' )=='all' ) {
		echo '<p>'.$this->pagination->getResultsCounter().'</p>';
	}
	
	?>
	<table class="category table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th id="activity_date_header_title">
					<?php echo JText::_('AUP_DATE'); ?>
				</th>
				<th id="activity_action_header_title">
				<?php echo JText::_('AUP_ACTION'); ?>
				</th>
				<th id="activity_points_header_title">
					<?php echo JText::_('AUP_POINTS'); ?>
				</th>
				<th id="activity_expire_header_title">
					<?php echo JText::_('AUP_EXPIRE'); ?>
				</th>
				<th id="activity_detail_header_title">
					<?php echo JText::_('AUP_DETAIL'); ?>
				</th>
				<th id="activity_approved_header_title">
					<?php echo JText::_('AUP_APPROVED'); ?>
				</th>
			</tr>
		</thead>
	<tbody>
	<?php
	if ( $this->rowslastpoints ) {
		$k = 0;
		foreach ($this->rowslastpoints as $lastpoints)
		{ 	
			$points_color = '';
			if ($lastpoints->points >0)
				$points_color = 'badge badge-info';
			if ($lastpoints->points<0)
				$points_color = 'badge badge-warning';
			if ($lastpoints->points==0)
				$points_color = 'badge';
		?>
			<tr class="cat-list-row<?php echo $k % 2; ?>" >
				<td headers="categorylist_header_title" class="list-title">
					<?php echo JHTML::_('date',  $lastpoints->insert_date,  JText::_('DATE_FORMAT_LC2') ); ?>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo JText::_($lastpoints->rule_name); ?>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<span class="<?php echo $points_color ; ?>"><?php echo getFormattedPoints( $lastpoints->points ); ?></span>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<?php			
						if ( $lastpoints->expire_date == $nullDate )
						{
							echo '-';
						} else {
							echo JHTML::_('date',  $lastpoints->expire_date,  JText::_('DATE_FORMAT_LC') );
						}						 
					 ?>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<?php				
					switch ( $lastpoints->plugin_function ) {
						case 'sysplgaup_dailylogin':
							echo JHTML::_('date', $lastpoints->datareference, JText::_('DATE_FORMAT_LC1') );
							break;
						case 'plgaup_getcouponcode_vm':
						case 'plgaup_alphagetcouponcode_vm':
						case 'sysplgaup_buypointswithpaypal':
							if ( $this->referreid!=@$_SESSION['referrerid'] ) {
								echo '';
							} else echo $lastpoints->datareference;
							break;
						default:
							echo $lastpoints->datareference;
					}				
					?>
				</td>
				<td headers="categorylist_header_title" class="list-title">
					<?php
					$img = ( $lastpoints->approved )? 'tick.png' : 'publish_x.png' ;	
					$alt = ( $lastpoints->approved )? 'AUP_APPROVED' : 'AUP_PENDINGAPPROVAL' ;	 
					 ?>
					 <img src="components/com_altauserpoints/assets/images/<?php echo $img; ?>" border="0" title="<?php echo JText::_( $alt ); ?>" alt="<?php echo JText::_( $alt ); ?>" />
				</td>
			</tr>
			
		<?php 
			$k++;
		} 
	}
	?>
	  </tbody>
	</table>
	<?php 
	if ( $this->params->get( 'num_item_activities' )=='all' )
	{
		echo '<div class="pagination">';
		echo $this->pagination->getPagesLinks();
		echo '<br />' . $this->pagination->getPagesCounter();
		echo '</div>';
	}
	?>
	<?php
		// if activities -> allow to download CSV format for owner
		if ( $this->rowslastpoints && $this->referreid==@$_SESSION['referrerid'] && $user->id==$this->userinfo->userid )
		{	
			$linktodownloadactivity = "index.php?option=com_altauserpoints&amp;view=account&amp;task=downloadactivity&amp;userid=".$user->id;			
			$linktodownloadactivity =  "<img src=\"components/com_altauserpoints/assets/images/icon_csv.gif\" alt=\"\" />&nbsp;&nbsp;<a href=\"" . JRoute::_($linktodownloadactivity) . "\">" . JText::_('AUP_DOWNLOAD_MY_FULL_ACTIVITY') . "</a>";
			echo  "<br /> " . $linktodownloadactivity;
		}
		
		echo JHtml::_('bootstrap.endTab');
	
} // end if show activities
		
	// -------------------------------------------------------------------	
	// add new Tab module position
	$renderer = $document->loadRenderer( 'modules' );
	$options = array( 'style' => 'none' );
	echo $renderer->render( 'AltaUserPoints Profile Tab', $options, null);
	// -------------------------------------------------------------------	

echo JHtml::_('bootstrap.endTabSet'); 

	/** 
	*
	*  Provide copyright on frontend
	*  If you remove or hide this line below,
	*  please make a donation if you find AltaUserPoints useful
	*  and want to support its continued development.
	*  Your donations help by hardware, hosting services and other expenses that come up as we develop,
	*  protect and promote AltaUserPoints and other free components.
	*  You can donate on https://www.nordmograph.com/extensions
	*
	*/	
	getCopyrightNotice ();
?>