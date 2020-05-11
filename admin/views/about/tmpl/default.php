<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

defined( '_JEXEC' ) or die( 'Restricted access' );	
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'cpanel' || document.formvalidator.isValid(document.id('rules-form'))) {
			Joomla.submitform(task, document.getElementById('about-form'));
		//}
		//else {
			//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<table class="noshow">
	<tr>
		<td width="100%" valign="top"><img src="<?php echo JURI::base(); ?>components/com_altauserpoints/assets/images/aup_logo.png" alt="" /><br />
		<p><br />
		  AltaUserPoints is the first component for Joomla 3.x created to  provide a method for users to accumulate points for
performing certain actions on your website such as posting articles,
invite new users, invite a friend to read an article, etc... It also provides an API that allows
developers to easily add other actions.<br />
  AltaUserPoints is useful in providing an incentive for users to participate in the website, and be more active.<br />
  <br />
  ALTAUSERPOINTS IS DISTRIBUTED &quot;AS IS&quot;. NO WARRANTY OF ANY KIND IS EXPRESSED OR IMPLIED. YOU USE IT AT YOUR OWN RISK.<br />
  THE AUTHOR WILL NOT BE LIABLE FOR ANY DAMAGES, INCLUDING BUT NOT LIMITED TO DATA LOSS, LOSS OF PROFITS OR ANY OTHER KIND OF LOSS WHILE USING OR MISUSING THIS SCRIPT.  <br />
  <br />
  <b>Author</b> : Bernard Gilly - Adrien Roussel<br />
  <br />
  <b>Official website</b> : <a href="https://www.nordmograph.com/extensions" target="_blank">www.nordmograph.com</a>        
		<p><b>Contact :</b> <a href="mailto:contact@nordmograph.com">contact@nordmograph.com</a> <br />
          <br />
          <b>Credits</b>: 
          <span class="smallgrey">Special thank's for testing, good suggestions for features &amp; translations to</span> Sami Haaranen (<span lang="EN-GB" xml:lang="EN-GB"><a target="_blank" href="http://www.joomla.fi/">www.joomla.fi</a></span>),  Mike Gusev (<a href="http://www.migusbox.com" target="_blank">www.migusbox.com</a>), Adrien Roussel (<a href="https://www.nordmograph.com">www.nordmograph.com</a>). <br />
          <br />
		    <b><?php echo JText::_( 'AUP_VERSION' ) . " " . _ALTAUSERPOINTS_NUM_VERSION ; ?></b><br />
		    <br />
		    AltaUserPoints is Free Software released under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL License</a>.
		    <b><br />
	      Ever thought about giving something back?</b><br>
	Please make a donation if you find AltaUserPoints useful and want to support its continued development.
	Your donations help by  hardware, hosting services and other expenses that come up as we develop, protect and promote AltaUserPoints and other free components.
<form action="index.php" method="post" name="adminForm" id="about-form" class="form-validate">			
<input type="hidden" name="option" value="com_altauserpoints" />
<input type="hidden" name="task" value="cpanel" />
</form>
        </p>
	  </td>
	</tr>
</table>