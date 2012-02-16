<?php
/*
Plugin Name: Investor Relations Button
Description: Vorschaltung eines Disclaimers vor Seitenbetrachtung
Author: blogsport.de
Author URI: http://blogsport.de/
Version: 0.9
*/

/**
 *  Einstellungsseite für Plugin hinzufügen
 */
add_action('admin_menu', 'optionPage');
function optionPage() {
   add_options_page('Investor Relations Button', 'IR Button', 'administrator', 'irbutton/irb-options.php');   
}

/**
 * Setzte Cookie wenn Disclaimer bestätigt wurde
 */
if ( isset($_POST['irb_submit']) )
	setcookie('irb_' . COOKIEHASH, 'read', 0, COOKIEPATH, COOKIE_DOMAIN);

/**
 * Content durch Disclaimer ersetzen
 */
function irb_replace_content($content) {
	global $post;
	$buttonText = get_option('irb_buttonText');
	$disclaimer = get_option('irb_disclaimer');
	if ( is_page() && get_post_meta($post->ID,'_irb',true) && !$_COOKIE['irb_'.COOKIEHASH] && !isset($_POST['irb_submit']) ) {
		$content = $disclaimer;
		$content .= '<form name="irbutton" method="post" action="'. get_permalink() .'"><input class="irbutton" type="submit" name="irb_submit" value="'. $buttonText .'" /></form>';
   	}
	return $content;
}

/**
 * Auswahl im Edit Screen ob Seite mit oder ohne Disclaimer
 */
function irb_meta_box() {
	add_meta_box( 'irb', 'Investor Relations Button', 'irb_meta', 'page', 'side' );
}

function irb_meta() {
	global $post;
	$irb = false;
	if (get_post_meta($post->ID,'_irb',true)) $irb = true;
	?>
	<input type="checkbox" id="irb" name="irb" <?php checked($irb); ?>/> <label for="irb">Seite mit Disclaimer?</label>
	<?php
}


/**
 * Auswahl speichern
 */
function irb_insert_page($pID) {
	if (isset($_POST['irb'])) {
		if (!get_post_meta($pID,'_irb', true))
			add_post_meta($pID, '_irb', true, true);
	} else {
		if (get_post_meta($pID,'_irb',true))
			delete_post_meta($pID, '_irb');
	}
}

add_action('admin_menu',		'irb_meta_box');
add_action('wp_insert_post',	'irb_insert_page');
add_action('the_content',		'irb_replace_content');
?>