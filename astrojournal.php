<?php
/*
 * @package AstroJournal
 * @version 0.9
 */
/*
Plugin Name: AstroJournal
Plugin URI: https://github.com/plaidmelon/AstroJournal
Description: Plugin for keeping an astronomy observation journal.
Version: 0.9
Author: David Gallinat
Author URI: https://github.com/plaidmelon
*/

/* CREATE ASTROJOURNAL POST TYPE */
function astrojournal_setup_post_type() {
	$astrojournal_labels = apply_filters('astrojournal_labels', array(
		'name'                => 'AstroJournal',
		'singular_name'       => 'AstroJournal',
		'add_new'             => __('New Observation', 'astrojournal'),
		'add_new_item'        => __('Add New Observation', 'astrojournal'),
		'edit_item'           => __('Edit Observation', 'astrojournal'),
		'new_item'            => __('New Observation', 'astrojournal'),
		'all_items'           => __('All Observations', 'astrojournal'),
		'view_item'           => __('View Observation', 'astrojournal'),
		'view_items'          => __('View Observations', 'astrojournal'),
		'search_items'        => __('Search Observations', 'astrojournal'),
		'not_found'           => __('No Observations found', 'astrojournal'),
		'not_found_in_trash'  => __('No Observations found in Trash', 'astrojournal'),
		'parent_item_colon'   => '',
		'menu_name'           => __('AstroJournal', 'astrojournal'),
	));
	
	$astrojournal_args = array(
		'labels'              => $astrojournal_labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'query_var'           => true,
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'supports'            => apply_filters('astrojournal_supports', array( 'title', 'editor', 'thumbnail', 'author', 'excerpt', 'comments', 'revisions')),
		'menu_icon'           => 'dashicons-star-filled',
	);
	register_post_type('astrojournal', apply_filters('astrojournal_post_type_args', $astrojournal_args));
}

add_action('init', 'astrojournal_setup_post_type');

/* CREATE CUSTOM TAXONOMIES */
function build_astrojournal_taxonomies() {
	/* Equipment */
	if (!taxonomy_exists('equipment')) {
	register_taxonomy(
		'equipment',
		array(
			'astrojournal'
			),
		array(
			'hierarchical' => true,
			'labels' => array(
						'name'          => _x('Equipment', 'taxonomy general name'),
						'singular_name' => _x('Equipment', 'taxonomy singular name'),
						'search_items'  => __('Search Equipment'),
						'all_items'     => __('All Equipment'),
						'edit_item'     => __( 'Edit Equipment' ),
						'update_item'   => __( 'Update Equipment' ),
						'add_new_item'  => __( 'Add New Equipment' ),
						'new_item_name' => __( 'New Equipment' ),
						'menu_name'     => __('Equipment'),
						),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'equipment'),
			)
		);
		
		/* Should I insert some basic equipment headings here? */
	}

	/* Object Type */
	if (!taxonomy_exists('object_type')) {
		register_taxonomy(
		'object_type',
		array(
			'astrojournal'
			),
		array(
			'hierarchical' => true,
			'labels' => array(
							'name' => _x('Object type', 'taxonomy general name'),
							'singular_name' => _x('Object type', 'taxonomy singular name'),
							'search_items' => __('Search Object types'),
							'all_items' => __('All Object types'),
							'edit_item' => __( 'Edit Object type' ),
							'update_item' => __( 'Update Object type' ),
							'add_new_item' => __( 'Add New Object type' ),
							'new_item_name' => __( 'New Object type' ),
							'menu_name' => __('Object types'),
							),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'object-type'),
			)
		);
	}

	/* Conditions */
	if (!taxonomy_exists('conditions')) {
		register_taxonomy(
		'conditions',
		array(
			'astrojournal'
			),
		array(
			'hierarchical' => true,
			'labels' => array(
							'name' => _x('Conditions', 'taxonomy general name'),
							'singular_name' => _x('Condition', 'taxonomy singular name'),
							'search_items' => __('Search Conditions'),
							'all_items' => __('All Conditions'),
							'edit_item' => __( 'Edit Condition' ),
							'update_item' => __( 'Update Condition' ),
							'add_new_item' => __( 'Add New Condition' ),
							'new_item_name' => __( 'New Condition' ),
							'menu_name' => __('Conditions'),
							),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'conditions'),
			)
		);
	}
	
	/* Locations */
	if (!taxonomy_exists('locations')) {
		register_taxonomy(
		'locations',
		array(
			'astrojournal'
			),
		array(
			'hierarchical' => true,
			'labels' => array(
							'name' => _x('Location', 'taxonomy general name'),
							'singular_name' => _x('Location', 'taxonomy singular name'),
							'search_items' => __('Search Locations'),
							'all_items' => __('All Locations'),
							'edit_item' => __( 'Edit Location' ),
							'update_item' => __( 'Update Location' ),
							'add_new_item' => __( 'Add New Location' ),
							'new_item_name' => __( 'New Location' ),
							'menu_name' => __('Locations'),
							),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'locations'),
			)
		);
	}
}

add_action('init', 'build_astrojournal_taxonomies', 0);


/************************************ 
* INSERT CONSTELLATIONS INTO TAXONOMY
*
* Future versions might handle this differently.
* 
* This takes a little more to create
* the list of constellations,
* I should probably move this to a seperate file.
*
*************************************/

/* Call the creation function */
add_action('init', 'create_constellation_taxonomy');

/* Function to create constellation taxonomy */
function create_constellation_taxonomy() {
	if (!taxonomy_exists('constellation')) {
		register_taxonomy(
			'constellation',
			'astrojournal',
			array(
				'hierarchical'=> true,
				'label'=> __('Constellations'),
				'show_ui'=> false,
				'show_admin_column' => true,
				'query_var'=>'constellation',
				'rewrite'=>array('slug'=>'constellation')
			
			)
		);
		
		/* Get the file with all of our constellation info */
		include 'constellation_list.php';
		
		/* Register all the constellations */
		for ($row = 0; $row < 88; $row++) {
			if (!term_exists($constellation_list[$row]["name"], 'constellation')) {
				wp_insert_term($constellation_list[$row]["name"], 'constellation', array(
				'description'=>$constellation_list[$row]["description"],
				'slug'=>$constellation_list[$row]["abbr"]));
			}
		}
	}
}

/* Remove default meta_box */
/* Wordpress div id = constellationdiv */
function remove_default_constellation_meta_box() {
	remove_meta_box('constellationdiv', 'astrojournal', 'side');
}

add_action( 'admin_menu' , 'remove_default_constellation_meta_box' );

/* Create custom dropdown meta_box */
function add_constellation_meta_box() {
	
	/* If we're not in admin then stop */
	if (! is_admin())
		return;
	
	/* Otherwise, go ahead and make the box */
	add_meta_box(
		'constellation_meta_box_ID',
		__('Constellation'),
		'constellation_meta_box_build',
		'astrojournal',
		'side',
		'default'
	);
}

function constellation_meta_box_build($post) {
	/* Create nonce */
	echo '<input type="hidden" name="constellation_nonce" id="constellation_nonce" value="' . 
		wp_create_nonce('astrojournal_constellation_nonce') . '" />';
	
	/* Get all terms, even those without observations attached */
	$constellations = get_terms('constellation', 'hide_empty=0');
	
	/* Start the dropdown box */
	?>
	<select name="post_constellation" id="post_constellation">
		<!-- Display constellations as options -->
		<?php
		/* Get constellation attached to observation */
		$names = wp_get_object_terms($post->ID, 'constellation');
		?>
		<option class='constellation-option' value=''>None</option>
		<?php
		foreach ($constellations as $constellation) {
			if (!is_wp_error($names) && !empty($names) && !strcmp($constellation->slug, $names[0]->slug))
				echo '<option class="constellation-option" value="' . $constellation->slug . '" selected>' . $constellation->name . '</option>';
			else echo '<option class="constellation-option" value="' . $constellation->slug . '">' . $constellation->name . '</option>';
		}
		?>
	</select>
	<?php
}

add_action('admin_menu', 'add_constellation_meta_box');


/* Save meta_box data */
add_action('save_post', 'save_constellation_data');

function save_constellation_data($post_id) {
	/* Verify nonce */
	if (!wp_verify_nonce($_POST['constellation_nonce'],'astrojournal_constellation_nonce')) {
		return $post_id;
	}
	
	/* Check if autosave, if it is then do nothing*/
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return $post_id;
	}
	
	/* Check permissions first */
	if ('page' == $_POST['astrojournal']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
	} else {
		if (!current_user_can('edit_post', $post_id))
			return $post_id;
	}
	
	/* OK, now we can save */
	$post = get_post($post_id);
	if (($post->post_type == 'astrojournal') || ($post->post_type == 'page')) {
		$constellation = $_POST['post_constellation'];
		wp_set_object_terms($post_id, $constellation, 'constellation');
	}
	return $constellation;
}


/*****************************************************
* Append our AstroJournal post type to the main query.
*
* Later on I might give the user the option of where to
* include the AstroJournal posts.
*******************************************************/

/* Priority 99 so all the other stuff (posts) gets included first */
add_filter('pre_get_posts', 'astrojournal_include_posts_in_main', 99);

function astrojournal_include_posts_in_main($query) {
	if ($query->is_home() && $query->is_main_query()){
		$post_types = $query->get('post_type');
		
		if (!is_array($post_types) && !empty($post_types)) {
			$post_types = explode(',', $post_types);
		}
		
		/* Check if empty, include post just in case */
		/* Should I do this, what if they excluded on purpose? */
		if (empty($post_types)) {
			$post_types[] = 'post';
		}
		
		/* Include the AstroJournal in the post_types array */
		$post_types[] = 'astrojournal';
		
		/* trim and remove empty stuff */
		$post_types = array_map('trim', $post_types);
		$post_types = array_filter($post_types);
		
		/* update query list of post_types */
		$query->set('post_type', $post_types);
	}
	
	return $query;
}

/************************************ 
* CREATE OBSERVATION DATE META
*
*************************************/

/* Register meta_box */
function register_observation_date_meta_box($post) {
	add_meta_box(
		'observation_date_meta_box',         // ID of the box
		__( 'Observation Date' ),            // Box title
		'create_observation_date_meta_box',  // html to build the box
		'astrojournal',                      // what page(s) to show
		'side',                              // where on the page
		'high'                               // how important is the meta
	);
}
add_action('add_meta_boxes_astrojournal', 'register_observation_date_meta_box');

/* HTML for the meta_box */
function create_observation_date_meta_box($post) {
	// Get already saved date, if exists
	$observation_date = get_post_meta($post->ID, 'observation_date', true);
	
	// Create nonce
	wp_nonce_field(plugin_basename(__FILE__), 'aj_observation_date_nonce');
	?>
	
	<p>Date (mm/dd/yyyy): <input id="aj_observation_date" name="aj_observation_date" type="text" placeholder="mm/dd/yyyy" value="<?php echo date('m/d/Y', $observation_date); ?>" /></p>
	<?php
}

/* Save the date */
function save_observation_date($post_id) {
	// Check nonce
	if (!wp_verify_nonce($_POST['aj_observation_date_nonce'], plugin_basename(__FILE__)))
		return;
	
	// Check user
	if (!current_user_can('edit_posts'))
		return;
	
	// Has the field been filled
	if (!empty($_POST['aj_observation_date'])) {
	$observation_date = $_POST['aj_observation_date'];
	}
	
	$observation_date = strtotime($observation_date);
	update_post_meta($post_id, 'observation_date', $observation_date);

}
add_action('save_post', 'save_observation_date');

/* datepicker code */
add_action( 'admin_enqueue_scripts', 'enqueue_date_picker' );
function enqueue_date_picker(){
            wp_enqueue_script(
			'aj_datepicker', 
			plugins_url('admin.js', __FILE__ ), 
			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
			time(),
			true
		);	
		
		wp_enqueue_style(
			'jquery-ui-css',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css'
		);
}

/**********************************************************************
*
* Trying to get meta
*
************************************************************************/





/***********************************************************************/

/**********************
*
* Settings Page
*
***********************/

/* Add settings menu item */
add_action('admin_menu', 'add_astrojournal_settings_menu_item');

function add_astrojournal_settings_menu_item() {
	add_submenu_page('edit.php?post_type=astrojournal', 'AstroJournal Settings', 'Settings', 'manage_options', 'astrojournal-settings', 'astrojournal_settings_page_build');
}

/* Create actual settings page */
function astrojournal_settings_page_build() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	/* Build the form */
	?>
	<div class="wrap">
	<h1>AstroJournal Settings<h1>
		
	<p>Nothing here yet, still working on it.</p>
	
	<form method="post" action="options.php">
		<input type="date" name="the_date" />
		<?php
		settings_fields('section');
		do_settings_sections('astrojournal-settings');
		submit_button();
		?>
	</form>
	</div> <!-- end wrap -->
	<?php
}
