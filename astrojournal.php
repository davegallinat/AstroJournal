<?php
/*
* @package AstroJournal
* @version 1.0
*/
/*
Plugin Name: AstroJournal
Plugin URI: https://github.com/plaidmelon/AstroJournal
Description: Wordpress plugin for keeping an astronomy observation journal.
Version: 1.0
Author: David Gallinat
Author URI: https://github.com/plaidmelon
*/


class pm_createCustomPostType
{
	/* CONSTRUCTOR */
	public function __construct($pluginName, $name, $args = array(), $labels = array())
	{
		/* Setup CPT variables */
		$this -> pluginName     = $pluginName;
		$this -> postTypeName   = strtolower(str_replace(' ', '', $name));
		$this -> postTypeArgs   = $args;
		$this -> postTypeLabels = $labels;
		
		/* Check if exists and add action*/
		if (!post_type_exists($this->postTypeName))
		{
			add_action('init', array(&$this, 'registerPostType'));
		}
		
		/* Listen for save */
		$this -> saveMeta();
	}
	
	/* REGISTER POST TYPE */
	public function registerPostType()
	{
		/* Create correct capitalization and form */
		$name = ucwords($this -> postTypeName); // Capitalize words
		$pluralName = $name . 's'; // Make plural label name
		
		/* Create labels */
		$labels = array_merge(
		
			/* Setup default labels */
			array(
				'name'               => _x($pluralName, 'post type general name'),
				'singular_name'      => _x($name, 'post type singular name'),
				'add_new'            => _x('New ' . $name),
				'add_new_item'       => __('Add New ' . $name),
				'edit_item'          => __('Edit ' . $name),
				'new_item'           => __('New ' . $name),
				'all_items'          => __('All ' . $pluralName),
				'view_item'          => __('View ' . $name),
				'view_items'         => __('View ' . $pluralName),
				'search_items'       => __('Search ' . $pluralName),
				'not_found'          => __('No ' . $pluralName . ' found'),
				'not_found_in_trash' => __('No ' . $pluralName . ' found in trash'),
				'parent_item_colon'  => '',
				'menu_name'          => __($this -> pluginName),
			),
			
			/* Merge new labels */
			$this -> postTypeLabels
		);
		
		/* Create args */
		$args = array_merge(
		
			/* Setup default args */
			array(
				'label'               => $pluralName,
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'query_var'           => true,
				'capability_type'     => 'post',
				'has_archive'         => true,
				'heirarchical'        => false,
				'exclude_from_search' => true,
				'supports'            => array('title', 'editor', 'thumbnail', 'author', 'excerpt', 'comments', 'revisions'),
				'menu_icon'           => 'dashicons-star-filled',
			),
			
			/* Merge new args */
			$this -> postTypeArgs
		);
		
		/* Add finally register the post type */
		register_post_type('$this->postTypeName', $args);
	}
	
	/* UPDATE POST META WHEN POST IS SAVED */
	public function saveMeta()
	{
		
	}
	
	/* ADD POST TYPE TO MAIN QUERY */
	public function addToQuery()
	{
		
	}
}

class pm_createTaxonomy extends pm_createCustomPostType
{	
	/* CONSTRUCTOR */
	public function __construct($taxName, $taxArgs = array(), $taxLabels = array())
	{

		/* Get Post type name so we can attach the taxonomy */
		$postTypeName = $this -> postTypeName;
	
		/* Setup taxonomy variables */
		$this -> taxName   = strtolower(str_replace(' ', '_', $taxName));
		$this -> taxArgs   = $taxArgs;
		$this -> taxLabels = $taxLabels;

		add_action('init', array($this,'addTaxonomy'), 0);
	}
		
	/* REGISTER TAXONOMY */
	public function addTaxonomy()
	{
		$name   = ucwords(str_replace('_', ' ', $this -> taxName)); // Capitalize words & replace spaces
		$pluralName = $name . 's'; //make a plural form
		
		/* Setup default labels */
		$labels = array_merge(
			array(
				'name'              => _x($pluralName, 'taxonomy general name'),
				'singular_name'     => _x($name, 'taxonomy singular name'),
				'search_items'      => __('Search ' . $pluralName),
				'all_items'         => __('All ' . $pluralName),
				'parent_item'       => __('Parent ' . $name),
				'parent_item_colon' => __('Parent ' . $name),
				'edit_item'         => __('Edit ' . $name),
				'update_item'       => __('Update ' . $name),
				'add_new_item'      => __('Add New ' . $name),
				'new_item_name'     => __('New ' . $name),
				'menu_name'         => __($pluralName),
			),
			/* Merge new labels */
			$this -> taxLabels
		);
		
		/* Setup default args */
		$args   = array_merge(
			array(
				'hierarchical'      => true,
				'label'             => $pluralName,
				'labels'            => $labels,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'query_var'         => $pluralName,
				'rewrite'           => array('slug' => $pluralName),
			),
			/* Merge new args */
			$this -> taxArgs
		);
		
		/* Aaaannd, register the taxonomy */
		register_taxonomy('$this -> taxName', '$this -> postTypeName', $args);
	}
		
	/* CREATE CUSTOM META BOX - PASS BOOLEAN */
	public function addMetaBox()
	{
		
	}

}

/* CREATE CUSTOM POST TYPE */
$astroJournal = new pm_createCustomPostType('AstroJournal', 'Observation');

/* CREATE TAXONOMIES */
$equipment = new pm_createTaxonomy('Equipment');	
//$objectType = new pm_createTaxonomy();
//$conditions = new pm_createTaxonomy();
//$locations = new pm_createTaxonomy();

/* TAXONOMIES THAT NEED SPECIAL HANDLING */
//$constellation;
//$observationDateTime;





?>