<?php
/*
Plugin Name: AstroJournal
Plugin URL: 
Description: 
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
		'supports'           => apply_filters('astrojournal_supports', array( 'title', 'editor')),
	);
	register_post_type('astrojournal', apply_filters('astrojournal_post_type_args', $astrojournal_args));
}

add_action('init', 'astrojournal_setup_post_type');

/* CREATE CUSTOM TAXONOMIES */
function build_astrojournal_taxonomies() {
	/* Equipment */
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
			'query_var' => true,
			'rewrite' => array('slug' => 'equipment'),
			)
		);
	
	/* Object Type */
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
			'query_var' => true,
			'rewrite' => array('slug' => 'object_type'),
			)
		);
		
	/* Constellations */
	/* I'm going to handle this section differently, see code at bottom of file */
	/*
	register_taxonomy(
		'constellations',
		array(
			'astrojournal'
			),
		array(
			'hierarchical' => true,
			'labels' => array(
							'name' => _x('Constellation', 'taxonomy general name'),
							'singular_name' => _x('Constellation', 'taxonomy singular name'),
							'search_items' => __('Search Constellations'),
							'all_items' => __('All Constellations'),
							'edit_item' => __( 'Edit Constellation' ),
							'update_item' => __( 'Update Constellation' ),
							'add_new_item' => __( 'Add New Constellation' ),
							'new_item_name' => __( 'New Constellation' ),
							'menu_name' => __('Constellations'),
							),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'constellation'),
			)
		);
	*/
		
	/* Conditions */
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
		
	/* Locations */
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
			'query_var' => true,
			'rewrite' => array('slug' => 'locations'),
			)
		);
	
}

add_action('init', 'build_astrojournal_taxonomies', 0);


/************************************ 
* INSERT CONSTELLATIONS INTO TAXONOMY
*
* Future versions will likely do this differently, but
* I'm being lazy here and just creating a custom meta box.
* I should create a custom category of constellations.
* 
* This takes a little more to create
* the list of constellations.
* I should probably move this to a seperate file.
*
*************************************/



/* Constellation list */
/*
$constellation_list = array (
	'Andromeda',
	'Antlia',
	'Apus',
	'Aquarius',
	'Aquila',
	'Ara',
	'Aries',
	'Auriga',
	'Boötes',
	'Caelum',
	'Camelopardalis',
	'Cancer',
	'Canes Venatici',
	'Canis Major',
	'Canis Minor',
	'Capricornus',
	'Carina',
	'Cassiopeia',
	'Centaurus',
	'Cepheus',
	'Cetus',
	'Chamaeleon',
	'Circinus',
	'Columba',
	'Coma Berenices',
	'Corona Austrina',
	'Corona Borealis',
	'Corvus',
	'Crater',
	'Crux',
	'Cygnus',
	'Delphinus',
	'Dorado',
	'Draco',
	'Equuleus',
	'Eridanus',
	'Fornax',
	'Gemini',
	'Grus',
	'Hercules',
	'Horologium',
	'Hydra',
	'Hydrus',
	'Indus',
	'Lacerta',
	'Leo',
	'Leo Minor',
	'Lepus',
	'Libra',
	'Lupus',
	'Lynx',
	'Lyra',
	'Mensa',
	'Microscopium',
	'Monoceros',
	'Musca',
	'Norma',
	'Octans',
	'Ophiuchus',
	'Orion',
	'Pavo',
	'Pegasus',
	'Perseus',
	'Phoenix',
	'Pictor',
	'Pisces',
	'Piscis Austrinus',
	'Puppis',
	'Pyxis',
	'Reticulum',
	'Sagitta',
	'Sagittarius',
	'Scorpius',
	'Sculptor',
	'Scutum',
	'Serpens',
	'Sextans',
	'Taurus',
	'Telescopium',
	'Triangulum',
	'Triangulum Australe',
	'Tucana',
	'Ursa Major',
	'Ursa Minor',
	'Vela',
	'Virgo',
	'Volans',
	'Vulpecula'
);
*/
