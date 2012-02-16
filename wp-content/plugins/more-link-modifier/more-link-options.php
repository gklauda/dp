<?php
// If the form was submitted - update the values in the database
if ( isset($_POST['Submit']) ) {
   update_option('moreLink_isJumpDisabled',$_POST['moreLink_isJumpDisabled']);
   update_option('moreLink_limitType',$_POST['moreLink_limitType']);
   update_option('moreLink_maxLimit',$_POST['moreLink_maxLimit']);
   update_option('moreLink_showEllipsis',$_POST['moreLink_showEllipsis']);
   update_option('moreLink_linkText',formatLinkText($_POST['moreLink_linkText']));
}

// If the reset to default option is chosen, just update to the WordPress default
if ( isset($_POST['Reset']) ) {
   update_option('moreLink_isJumpDisabled','');
   update_option('moreLink_limitType','none');
   update_option('moreLink_maxLimit','');
   update_option('moreLink_showEllipsis','');
   update_option('moreLink_linkText','Continue reading \'[TITLE]\'');
}

// Get the values from the database and store them in the variables
$isJumpDisabled = get_option('moreLink_isJumpDisabled');
$limitType = get_option('moreLink_limitType');
$maxLimit = get_option('moreLink_maxLimit');
$showEllipsis = get_option('moreLink_showEllipsis');
$linkText = get_option('moreLink_linkText');

// Helper function to format the link text - so it won't kill the plugin
function formatLinkText($text) {
   // Strip all HTML tags except for: b, u, i, strong, em
   $allowed_html_tags = "<b><u><i><strong><em>";
   $text = strip_tags($text,$allowed_html_tags);
      
   // Strip the slashes
   $text = stripslashes($text);
   
   return $text;
}


?>

<?php
   /* Some simple style and HTML to display the option page in admin panel*/
?>

<style type="text/css">
   .moreLink_indent {
      margin-left: 20px;
      line-height: 1.5em;
   }
</style>

<div class="wrap">


<h2><?php _e('More Link Modifier','more_link_modifier') ?></h2>
<form name="moreLink_form" method="post" action="options-general.php?page=more-link-modifier/more-link-options.php">
   <p><?php _e('The More Link Modifier lets you easily change what is displayed when you use <b>&lt;!--more--&gt</b> in your posts. It also allows you to remove the anchor link (ie. disable the default behaviour of jumping to the middle of the post when the link is clicked). For some examples/help, have a look at the <a href="http://psychopyko.com/downloads/more-link-modifier/">plugin help page</a>. If you have any suggestions or found any bugs, feel free to send me an email: <a href="mailto:psychopykoATgmail">psychopyko -at- gmail</a>','more_link_modifier') ?></p>
   
   <h3><?php _e('Link Display Options','more_link_modifier') ?></h3>
   <div class="moreLink_indent">
      <p><?php _e('Enter some text below to replace the default <i>more</i> link text. You can either enter plaintext - eg. <i>Continue Reading...</i> or combine plaintext and the post\'s title. To include the title, use <b>[TITLE]</b> - eg. <i>Continue Reading [TITLE]</i>. Below are some options that allow you to limit the number of characters or words to be displayed from the title. If you want, you can also use very basic HTML - bold, italic underline, special characters (eg. &amp;raquo;)','more_link_modifier') ?></p>
      <p><?php _e('Custom Link Text','more_link_modifier') ?>: <input type="text" name="moreLink_linkText" value="<?php echo empty( $linkText ) ? htmlentities('(mehr&nbsp;&hellip;)') : htmlentities($linkText); /* modified by gigi */ ?>" size="50"/></p>   
      <b><?php _e('Limits for Title','more_link_modifier') ?></b><br />
      <input type="radio" name="moreLink_limitType" value="none" <?php checked("none",$limitType) ?> /> <?php _e('Display the entire title','more_link_modifier') ?><br />
      <input type="radio" name="moreLink_limitType" value="char" <?php checked("char",$limitType) ?> /> <?php _e('Limit title by number of characters','more_link_modifier') ?><br />
      <input type="radio" name="moreLink_limitType" value="word" <?php checked("word",$limitType) ?> /> <?php _e('Limit title by number of words (max: 99)','more_link_modifier') ?><br /> 
      <?php _e('Limit title to ','more_link_modifier');?><input type="text" size="3" name="moreLink_maxLimit" value="<?php echo "$maxLimit" ?>" /><?php _e(' characters/words.', 'more_link_modifier') ?><br />
      <input type="checkbox" name="moreLink_showEllipsis" <?php checked("on",$showEllipsis) ?>/> <?php _e('Display ellipsis (...) at the end of the title if it is has been cut off. eg. The answer to life...','more_link_modifier'); ?><br />
      <i><?php _e('Note: These limits/options only apply to the post title, and not the plaintext you have entered.','more_link_modifier') ?></i>
   </div>
   
   <h3><?php _e('Remove Anchor Link','more_link_modifier') ?></h3>
   <div class="moreLink_indent">
      <p><?php _e('By default, WordPress includes an anchor to the more link so it will jump to the section of the post corresponding to where you placed your more link. You can disable this here','more_link_modifier'); ?></p>
      <input type="checkbox" name="moreLink_isJumpDisabled" <?php checked("on",$isJumpDisabled) ?>/> <?php _e('Remove the anchor link','more_link_modifier'); ?><br />
   </div>
   
   <p class="submit">
      <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options &raquo;','more_link_modifier') ?>" />
      <input type="submit" name="Reset" value="<?php _e('Reset to WordPress default values','more_link_modifier') ?>" />
   </p>
</form>
   
   
</div>