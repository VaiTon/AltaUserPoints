<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$user =  JFactory::getUser();
$colspan = 4;
if ( $this->params->get( 'usrname' )=='' ) $colspan = 6;
if ( $this->useAvatarFrom ) $colspan++;
if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="adminForm">
<div class="table-responsive">
<table class="category table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<?php if ( $this->useAvatarFrom ) { ?>
				<th id="avatar_header_title">&nbsp;
				</th>
				<?php } ?>				
				<th id="medal_header_title">&nbsp;																						
				</th>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='name') { ?>			
				<th id="name_header_title">
					<?php echo JText::_('AUP_NAME'); ?>
				</th>
				<?php } ?>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='username') { ?>	
				<th id="username_header_title">
					<?php echo JText::_( 'AUP_USERNAME' ); ?>
				</th>
				<?php } ?>
				<th id="date_header_title">
					<?php echo JText::_('AUP_DATE'); ?>
				</th>
				<th id="reason_header_title">
					<?php echo JText::_( 'AUP_REASON_FOR_AWARD' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php	
			require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';
			
			for ($i=0, $n=count( $this->detailrank ); $i < $n; $i++)			
			{
				$row 	=& $this->detailrank[$i];
				
				$_user_info = AltaUserPointsHelper::getUserInfo ( $row->referreid );				
				$linktoprofil = getProfileLink( $this->linkToProfile, $_user_info );
				
				if ($row->icon ) {
					$pathicon = JURI::root() . 'components/com_altauserpoints/assets/images/awards/icons/';
					$icone = '<img src="'.$pathicon . $row->icon.'" width="16" height="16" border="0" alt="" />';
				} else $icone = '';	
				
			?>
			<tr class="cat-list-row<?php echo $i%2; ?>">	
			<?php
					// load avatar if need
					if ( $this->useAvatarFrom ) {
						$avatar = getAvatar( $this->useAvatarFrom, $_user_info, $this->params->get( 'heightAvatar', '48' ) );							
						// add link to profil if need
						$startprofil = '<a href="#" >';
						$endprofil  = '</a>';
						if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
							$startprofil =  '<a href="' . $linktoprofil . '">';
							$endprofil   = '</a>';
						}					
						echo '<td headers="categorylist_header_title" class="list-title">'
						.$startprofil
						.$avatar
						.$endprofil
						.'</td>';
					}			
			?>
				<td headers="categorylist_header_title" class="list-title">
					<?php echo $icone; ?>
				</td>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='name') { ?>			
				<td headers="categorylist_header_title" class="list-title">	
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  '<a href="' . $linktoprofil . '">' . $row->name . '</a>';
					} else $profil = $row->name ;
						echo $profil;			 
					 ?>					
				</td>
				<?php } ?>
				<?php if ( $this->params->get( 'usrname' )=='' || $this->params->get( 'usrname' )=='username') { ?>	
				<td headers="categorylist_header_title" class="list-title">
					<?php
					if ( $this->params->get( 'show_links_to_users', 1) && $user->id || $this->params->get( 'show_links_to_users', 1) && !$user->id && $this->allowGuestUserViewProfil ){
						$profil =  '<a href="' . $linktoprofil . '">' . $row->username . '</a>';
					} else $profil = $row->username ;
						echo $profil;			 
					 ?>					
				</td>
				<?php } ?>
				<td headers="categorylist_header_title" class="list-title">
					<?php 
					echo JHTML::_('date',  $row->dateawarded,  JText::_('DATE_FORMAT_LC') );
					?>
				</td>
				<td headers="categorylist_header_title" class="list-title">			
					<?php 
					echo JText::_( $row->rank );
					if ( $row->reason ) echo ' - ' . JText::_( $row->reason ); 
					?>
				</td>
			</tr>
			<?php				
				}
			?>
		</tbody>
	</table>
</div>
	<div class="pagination">
	<?php 
	echo $this->pagination->getPagesCounter() . "<br />" ;
	echo $this->pagination->getPagesLinks(); 
	?>
	</div>
	<input type="hidden" name="option" value="com_altauserpoints" />
	<input type="hidden" name="controller" value="medals" />
	<input type="hidden" name="task" value="detailsmedal" />
	<input type="hidden" name="cid" value="<?php echo $row->cid ; ?>" />
</form>