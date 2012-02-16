<?php
/*
Plugin Name: More Link Modifier
Plugin URI: http://psychopyko.com/downloads/more-link-modifier/
Description: Allows you to easily modify the text for more link. Also lets you remove the default anchor link.
Author: Peggy Kuo
Author URI: http://psychopyko.com
Version: 1.0.2
*/

// Add the "More Link" option page into admin menu
add_action('admin_menu', 'optionPage');
function optionPage() {
   add_options_page(__('More Link Modifier','more_link_modifier'), __('More Link','more_link_modifier'), 'administrator', 'more-link-modifier/more-link-options.php');   
}

// Modify the more link
function modifyMoreLink($content) {
   $isJumpDisabled = get_option('moreLink_isJumpDisabled');
   $linkText = get_option('moreLink_linkText');
	
   // Get the formatted title - ie. limited to the options selected in admin panel 
   $formattedTitle = getFormattedTitled();   
   
   // Replace the [TITLE] in the linkText with the formatted title
   // Note: if [TITLE] is not in linkText, will just return unchanged
   // -- modified by gigi --
   $linkText = empty( $linkText ) ? '(mehr&nbsp;&hellip;)' : preg_replace("/\[TITLE\]/", $formattedTitle, $linkText);
   
   // Rename the link
   $content = renameMoreLink($content, $linkText);
   
   // If jump is disabled, remove the jump
   if ($isJumpDisabled == "on") {
         $content = disableJump($content);
   }
   
   return $content;
}

// The magical wordpress hook :)
if (function_exists('add_action')) {
   add_action('the_content', 'modifyMoreLink');
}


// Format the original post title according to the options specified in admin page
function getFormattedTitled() {
   $limitType = get_option('moreLink_limitType');
   $maxLimit = get_option('moreLink_maxLimit');
   $showEllipsis = get_option('moreLink_showEllipsis');

   $title = the_title('','',false);
   
   if ($limitType == "char") {
      // Only get the first 'n' characters of the title
      $titleLen = strlen($title);
      $title = substr($title,0,$maxLimit);
      
      // If shown ellipsis option is checked, add '...' to title if the length of the title
      // is more than the maximum characters shown
      if ($showEllipsis == "on") {
         if ($titleLen > $maxLimit) {
            $title .= "...";
         }
      }
      
   } else if ($limitType == "word") {
      // Create two strings ($pattern/$replacement) depending on the maximum number of words to be shown
      // eg. if maximum number of words was 3 (ie. $maxLimit is 3)
      // $pattern = "/(\S+) (\S+) (\S+) (.*)/"
      // $replacement = "\$1 \$2 \$3"
      $pattern = "/";
      for ($i=1; $i<=$maxLimit; $i++) {
         $pattern .= "(\S+) ";
         $replacement .= "\$$i ";
      }
      $replacement = rtrim($replacement);
      $pattern .= "(.*)/";
      
      // If show ellipsis option is checked, will add '...' to end of replacement
      // $replacement = "\$1 \$2 \$3..."
      if ($showEllipsis == "on") {
         $replacement .= "...";
      }
      
      // Use regex with pattern/replacement strings defined above to match the title
      // Note: If the number of words in title is less than the maximum number of words, the regex
      //       will not match, and will return the title unchanged.
      $title = preg_replace($pattern, $replacement, $title);      
      
   } else {
      // Do nothing
   }   
   return $title;
}

// Match for the existing text between the anchor tags and replace it
function renameMoreLink($content, $newLinkName) {
   $pattern = "/class=\"more-link\">([^<]+)</";
   $replacement = "class=\"more-link\">$newLinkName<";
   $content = preg_replace($pattern, $replacement, $content);

   return "$content";
}

// Match for the anchor in link and remove it
function disableJump($content) { 
   $pattern = "/\#more-\d+\" class=\"more-link\"/";
   $replacement = "\" class=\"more-link\"";
   $content = preg_replace($pattern, $replacement, $content);
   return "$content";
}

function more_link_modifier_textdomain()
{
	load_plugin_textdomain('more_link_modifier',false, trailingslashit( plugin_basename( dirname (__FILE__) ) ) . 'languages');
}
add_action('init', 'more_link_modifier_textdomain');

?>