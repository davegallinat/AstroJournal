<?php
namespace astrojournal;

/* class_pm_createCustomPostType.php */

/* CLASS FOR CREATING THE CUSTOM POST TYPE */

class pm_createCustomPostType
{
	protected $textdomain;
	
	/* Constructor */
	public function __construct($textdomain)
	{
		/* Initialize textdomain for use later */
		$this -> textdomain = $textdomain;
		
		/* Add the WP hook */
		add_action('init', array($this, 'registerCustomPostType'));
	}
	
	/* Build post type settings */
	public function buildCustomPostType($cptName, $singularName, $pluralName, $labels = array(), $settings = array())
	{
		/* Cleanup cptName just in case */
		$this -> cptName = strtolower(str_replace(' ', '_', $cptName));
		
		/* Default labels */
		$default_labels = array(
			'name'                  => _x($pluralName, $this->textdomain),
			'singular_name'         => _x($singularName, $this->textdomain),
			'add_new'               => _x('Add New ' . $singularName, $this->textdomain),
			'add_new_item'          => __('Add New ' . $singularName, $this->textdomain),
			'edit_item'             => __('Edit ' . $singularName, $this->textdomain),
			'new_item'              => __('New ' . $singularName, $this->textdomain),
			'new_item_name'         => __('New ' . $singularName, $this->textdomain),
			'view_item'             => __('View ' . $singularName, $this->textdomain),
			'view_items'            => __('View ' . $pluralName, $this->textdomain),
			'search_items'          => __('Search ' . strtolower($pluralName), $this->textdomain),
			'not_found'             => __('No '. strtolower($pluralName) . ' found.', $this->textdomain),
			'not_found_in_trash'    => __('No ' . strtolower($pluralName) . ' found in trash.', $this->textdomain),
			'parent_item'           => __('Parent ' . $singularName, $this->textdomain),
			'parent_item_colon'     => __('Parent ' . $singularName, $this->textdomain),
			'all_items'             => __('All ' . $pluralName, $this->textdomain),
			'archives'              => __($singularName . ' Archives', $this->textdomain),
			'attributes'            => __($singularName . ' Attributes', $this->textdomain),
			'insert_into_item'      => __('Insert into ' . strtolower($singularName), $this->textdomain),
			'uploaded_to_this_item' => __('Uploaded to this ' . strtolower($singularName), $this->textdomain),
			'update_item'           => __('Update ' . $singularName, $this->textdomain),
			'menu_name'             => __($pluralName, $this->textdomain),
		);
		
		/* Merge default labels with those passed in */
		$this -> labels = array_merge($default_labels, $labels);
		
		
		/* Default arguments for register_post_type */
		$default_args = array(
			'label'               => $pluralName,
			'labels'              => $this -> labels,
			'description'         => '',
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-post',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions'),
			'has_archive'         => true,
			'rewrite'             => array('slug' => sanitize_title_with_dashes(strtolower($pluralName))),
			'query_var'           => true,
			'can_export'          => true,
		);
		
		/* Merge default settings with those passed in */
		$this -> args = array_merge($default_args, $settings);
	}
	
	/* Register the post type */
	public function registerCustomPostType()
	{
		register_post_type($this->cptName, $this->args);
	}
	
}