<?php
/*
Plugin Name: Contact Widget
Description: Kontakt-Widget mit Weiterleitung zu einer Kontaktseite. Addon f&uuml;r Contact Form 7 (getestet mit Version: 3.1.1).
Author: blogsport.de
Author URI: http://blogsport.de 
Version: 0.9
*/

class WP_Widget_Contact_CF7 extends WP_Widget {

	function WP_Widget_Contact_CF7() {
		$widget_ops = array( 'description' => 'Kontaktformular mit Weiterleitung zu einer Kontaktseite' );
		$this->WP_Widget('contact_cf7', 'Kontakt-Widget', $widget_ops);
	}

	/**
	 * Widget Output in der Sidebar
	 */
	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Contact Us') : $instance['title']);

		?>
		<style>
			.widget_<?php echo $this->id_base ?> textarea{
				height:80px;
				width:100%;
			}
			.widget_<?php echo $this->id_base ?> input[type="text"]{width:100%;}
		</style>

		<?php
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;

			if ( $instance['subheading'] )	
			    echo '<p class="cf7_widget_subheading">'. $instance['subheading'] .'</p>';

			$widget_text = '<form action="' .get_page_link($instance['page']). '" method="post">';
			$contact_form = wpcf7_contact_form( $instance['form'] );
			$widget_text .= apply_filters( 'wpcf7_form_elements', $contact_form->form_do_shortcode() );
			$widget_text .= '</form>';
			echo $widget_text;
			?>
    	<div class="clear"></div>
		<?php
			echo $after_widget;
	}

	/**
	* Settings speichern
	*/
	function update( $new_instance, $old_instance ) {
		update_option('wp_widget_cf7_form_id', $new_instance['form']);
		update_option('wp_widget_cf7_page_id', $new_instance['page']);
		return $new_instance;
	}

	/**
	 * Settings-Formular für Kontakt-Widget
	 */
 	function form( $instance ) { 
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => false, 'subheading' => false, 'form' => 0, 'page' => 0) );
		$contact_forms = get_posts( array('numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'post_type' => 'wpcf7_contact_form' ) );
		$pages = get_pages();
		$contact_pages = array();
		foreach ( $pages as $page )
			if (preg_match('/\[contact-form/', $page->post_content) == 1) $contact_pages[] = $page;

		if ( empty($contact_forms) )
			echo '<p>Bitte zuerst unter "Kontakte" ein Kurzformular für das Widget und ein Langformular für die mit dem Widget zu verkn&uuml;pfende Kontaktseite anlegen!</p>';

		else { ?>

			<p><label for="<?php echo $this->get_field_id('title'); ?>">Titel</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
	
			<p><label for="<?php echo $this->get_field_id('subheading'); ?>">Beschreibung</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('subheading'); ?>" name="<?php echo $this->get_field_name('subheading'); ?>" value="<?php echo esc_attr( $instance['subheading'] ); ?>" /></p>
	
			<p><label for="<?php echo $this->get_field_id('form'); ?>">Contact Form 7 Formular-ID</label>
			<select name="<?php echo $this->get_field_name('form'); ?>" size="1" id="<?php echo $this->get_field_id('form'); ?>"><?php foreach ( $contact_forms as $contact_form ) echo '<option'.selected($instance['form'],$contact_form->ID,false).'>'.$contact_form->ID.'</option>'; ?></select>
	
			<?php if ( empty($contact_pages) )
				echo '<p>Bitte Kontaktseite anlegen!</p>';
			else { ?>
				<p><label for="<?php echo $this->get_field_id('page'); ?>">Name der zugeh&ouml;rigen Kontaktseite</label><br/>
				<select class="widefat" name="<?php echo $this->get_field_name('page'); ?>" size="1" id="<?php echo $this->get_field_id('page'); ?>"><?php foreach ( $contact_pages as $contact_page ) echo '<option value="'.$contact_page->ID.'"'.selected($instance['page'],$contact_page->ID,false).'>'.$contact_page->post_title.'</option>'; ?></select><?php
			}

		}

	}

	/**
	 * Seiten-Kontaktformular mit Feldern aus dem Kontakt-Widget befüllen
	 */
 	function fill_form($content) {
		if ( is_page() and $_SERVER['REQUEST_METHOD'] == 'POST' and get_the_ID() == get_option('wp_widget_cf7_page_id') ) {
			$wpcf7_contact_form = wpcf7_contact_form( get_option('wp_widget_cf7_form_id') );
			$labels = $wpcf7_contact_form->form_scan_shortcode();
			foreach( $labels as $label ) {
				if ( isset($_POST[$label['name']]) ) {
					if ( strpos($label['type'], 'textarea') !== false )
						// textarea
						$content = preg_replace('/(?<=\<textarea name=\"'.$label['name'].'\")(.*?\>)(?=\<)/', '$1'.stripslashes($_POST[$label['name']]), $content);
					else
						// input text
						$content = preg_replace('/(?<=type=\"text\" name=\"'.$label['name'].'\" value=\")(?=\")/', $_POST[$label['name']], $content);
				}
			}
		}
		return $content;
	}

	/**
	 * Prüfe, ob "Contact Form 7" aktiviert ist, dann erst Registrierung!
	 */
	function register_widget() {
		if ( !defined('WPCF7_VERSION') ) {
			function cf7_required() {
				echo '<div class="error"><p><strong>Contact Widget</strong> ben&ouml;tigt das <strong>Contact Form 7</strong>-Plugin!</p></div>';
			}
			add_action('admin_notices', 'cf7_required');
		}		
		else
			register_widget('WP_Widget_Contact_CF7');
	}
}
add_action("widgets_init", array('WP_Widget_Contact_CF7', 'register_widget'));

// Niedrige Hook-Priorität, damit CF7-Shortcode bereits ersetzt ist
add_action('the_content', array('WP_Widget_Contact_CF7', 'fill_form'), 200);
?>