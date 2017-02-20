<?php
/*
Plugin Name: AstroJournal
Plugin URL: https://github.com/plaidmelon/AstroJournal
Description: Plugin for keeping astronomy observation journal.
Version: 1.0
Author: David Gallinat
Author URI: https://github.com/plaidmelon
*/


/* CREATE ASTROJOURNAL POST TYPE */
function astrojournal_setup_post_type() {
	$astrojournal_labels = apply_filters('astrojournal_labels', array(
		'name'                => 'AstroJournal',
		'singular_name'       => 'AstroJournal',
		'add_new'             => __('Add New', 'astrojournal'),
		'add_new_item'        => __('Add New Observation', 'astrojournal'),
		'edit_item'           => __('Edit Observation', 'astrojournal'),
		'new_item'            => __('New Observation', 'astrojournal'),
		'all_items'           => __('All Observations', 'astrojournal'),
		'view_item'           => __('View Observation', 'astrojournal'),
		'search_items'        => __('Search Observations', 'astrojournal'),
		'not_found'           => __('No Observations found', 'astrojournal'),
		'not_found_in_trash'  => __('No Observations found in Trash', 'astrojournal'),
		'parent_item_colon'   => '',
		'menu_name'           => __('AstroJournal', 'astrojournal'),
		'exclude_from_search' => true
	));
	
	$astrojournal_args = array(
		'labels'             => $astrojournal_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => apply_filters('astrojournal_supports', array( 'title', 'editor', 'thumbnail', 'revisions')),
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
				'label'=> __('Constellation'),
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
		<option class='constellation-option' value='
		<?php if (!count($names)) echo "selected";?>'>None</option>
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

