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
			add_action('init', array($this, 'registerPostType'));
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
				'add_new'            => __('New ' . $name),
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
		register_post_type($this->postTypeName, $args);
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

class pm_createTaxonomy
{
	protected $textdomain;
	protected $taxonomies;
	
	public function __construct($textdomain)
	{
		$this -> textdomain = $textdomain;
		$this -> taxonomies = array();
		
		add_action('init', array($this, 'registerTaxonomy'));
	}
	
	public function makeTaxonomy($taxName, $singularName, $pluralName, $postTypes, $settings=array())
	{
		/* Cleanup slugs just in case */
		$this -> taxName = strtolower(str_replace(' ', '_', $taxName));
		$this -> postTypes = $postTypes;
		
		/* Default labels */
		$default_labels = array(
			'name'                       => __($pluralName, $this->textdomain),
			'singular_name'              => __($singularName, $this->textdomain),
			'search_items'               => __('Search ' . strtolower($pluralName), $this->textdomain),
			'poular_items'               => __('Popular ' . strtolower($pluralName), $this->textdomain),
			'choose_from_most_used'      => __('Choose from most used ' . strtolower($pluralName), $this->textdomain),
			'all_items'                  => __('All ' . strtolower($pluralName), $this->textdomain),
			'parent_item'                => __('Parent ' . $singularName, $this->textdomain),
			'parent_item_colon'          => __('Parent ' . $singularName, $this->textdomain),
			'edit_item'                  => __('Edit ' . $singularName, $this->textdomain),
			'update_item'                => __('Update ' . $singularName, $this->textdomain),
			'add_new_item'               => __('Add New ' . $singularName, $this->textdomain),
			'new_item_name'              => __('New ' . $singularName, $this->textdomain),
			'add_or_remove_items'        => __('Add or remove ' . strtolower($pluralName), $this->textdomain),
			'menu_name'                  => __($pluralName, $this->textdomain),
			'separate_items_with_commas' => __('Separate ' . strtolower($pluralName) . ' with commas', $this->textdomain)
		);
		
		$default_args = array(
			'hierarchical'      => true,
			'label'             => $pluralName,
			'labels'            => $default_labels,
			'public'            => true,
			'show_admin_column' => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array('slug' => sanitize_title_with_dashes($pluralName)),
		);
		
		$this -> args = array_merge($default_args, $settings);
	}
		
	public function registerTaxonomy()
	{
		register_taxonomy($this->taxName, $this->postTypes, $this->args);
	}
		
}

/* CREATE CUSTOM POST TYPE */
$astroJournal = new pm_createCustomPostType('AstroJournal', 'astrojournal');

/* CREATE TAXONOMIES */
$equipment = new pm_createTaxonomy('astrojournal');
$equipment -> makeTaxonomy('equipment', 'Equipment', 'Equipment', array('astrojournal', 'post'));

$conditions = new pm_createTaxonomy('astrojournal');
$conditions -> makeTaxonomy('condition', 'Condition', 'Conditions', array('astrojournal', 'post'));

$locations = new pm_createTaxonomy('astrojournal');
$locations -> makeTaxonomy('locations', 'Location', 'Locations', array('astrojournal', 'post'));

$objectType = new pm_createTaxonomy('astrojournal');
$objectType -> makeTaxonomy('objecttype', 'Object Type', 'Object Types', array('astrojournal', 'post'));

/* TAXONOMIES THAT NEED SPECIAL HANDLING */
//$constellation;
//$observationDateTime;

?>