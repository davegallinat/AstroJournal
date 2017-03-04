<?php
namespace astrojournal;

/* class_createObservationDateTimeMeta.php */

/*
* Class for creating the observation Date/Time metadata
* and generating the calendar selection box
*/

class createObservationDateTimeMeta
{
	/* __constructor */
	public function __construct($metaName, $label, $postTypes)
	{
		/* Setup some variable for later */
		$this -> metaName = strtolower(str_replace(' ', '_', $metaName));
		$this -> label = $label;
		$this -> postTypes = $postTypes;
		
		
		add_action('admin_enqueue_scripts', array($this, 'enqueueDatetimePicker'));
		add_action('add_meta_boxes', array($this, 'addDateTimeMetaBox'));
		add_action('save_post', array($this, 'saveDateTime'));
		
	}
	
	/* register metabox */
	public function addDateTimeMetaBox()
	{
		/* If not in admin, then do nothing */
		if (!is_admin())
			return;
		
		/* Add the meta box */
		add_meta_box(
			$this->metaName.'_metaboxdiv',
			__($this->label),
			array($this, 'buildDateTimeMetaBox'),
			$this->postTypes,
			'side',
			'default'
		);
	}
	
	/* build metabox */
	public function buildDateTimeMetaBox($post)
	{
		/* Get saved date/time if it exists */
		$obDateTime = get_post_meta($post->ID, 'obDateTime', true);
		
		/* Create nonce */
		wp_nonce_field($this -> metaName . '_nonce', $this -> metaName . '_nonce_field');
		
		/* Create input field */
		if (!empty($obDateTime))
		{
			echo '<p><input id="obDateTime" name="obDateTime" type="text" value="' . date("M d, Y g:i a", $obDateTime) . '" placeholder="mm/dd/yyyy 00:00 am" /></p>';
		echo '<p>Observed on: ' . date("M d, Y", $obDateTime) . ' @ '.date('g:i a', $obDateTime) . '</p>';
		}
		else
		{
			echo '<p><input id="obDateTime" name="obDateTime" type="text" value="" placeholder="mm/dd/yyyy 00:00 am" /></p>';
		}
		
		
	}
	
	/* save data */
	public function saveDateTime($post_id)
	{
		/* nonce check */
		if (!isset($_POST[$this -> metaName . '_nonce_field']))
		{
			return $post_id;
		}
		
		/* Verify nonce */
		if (!wp_verify_nonce($_POST[$this -> metaName . '_nonce_field'], $this -> metaName . '_nonce'))
		{
			return $post_id;
		}
		
		/* Check if autosave, if it is then do nothing*/
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return $post_id;
		}
		
		/* Can user edit? */
		if (!current_user_can('edit_posts'))
		{
			return $post_id;
		}
		
		/* If the field is filled we save */
		if (!empty($_POST['obDateTime']))
		{
			$this -> obDateTime = $_POST['obDateTime'];
			$this -> obDateTime = strtotime($this -> obDateTime);
			update_post_meta($post_id, 'obDateTime', $this -> obDateTime);
		}
	
	}
	
	/* enqueue scripts */
	public function enqueueDatetimePicker()
	{
		wp_enqueue_script(
			'timepicker',
			plugins_url('/helpers/jquery-ui-timepicker-addon.js', __FILE__),
			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
			false,
			true
		);
		
		wp_enqueue_script(
			'aj_datetime_picker',
			plugins_url('/js/aj_timepicker.js', __FILE__),
			array('jquery', 'timepicker'),
			false,
			true
		);
		
		wp_enqueue_style(
			'jquery-ui-css',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css'
		);
		
		wp_enqueue_style(
			'timepicker-css',
			plugins_url('/helpers/jquery-ui-timepicker-addon.css', __FILE__)
		);
			
			
	}
	
}