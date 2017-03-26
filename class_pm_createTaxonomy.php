<?php
namespace astrojournal;

/* class_pm_createTaxonomy.php */

/* CLASS FOR CREATING THE TAXONOMIES */

class pm_createTaxonomy
{
	protected $textdomain;
	
	/* Constructor */
	public function __construct($textdomain)
	{
		/* Initialize textdomain for use later */
		$this -> textdomain = $textdomain;
		
		/* Add WP hook */
		add_action('init', array($this, 'registerTaxonomy'));
	}
	
	/* Build taxonomy settings */
	public function buildTaxonomy($taxName, $singularName, $pluralName, $postTypes, $labels=array(), $settings=array())
	{
		/* Cleanup slugs just in case */
		$this -> taxName = strtolower(str_replace(' ', '_', $taxName));
		$this -> postTypes = $postTypes;
		
		/* Default labels */
		$default_labels = array(
			'name'                       => __($singularName, $this->textdomain),
			'singular_name'              => __($singularName, $this->textdomain),
			'search_items'               => __('Search ' . strtolower($pluralName), $this->textdomain),
			'popular_items'              => __('Popular ' . strtolower($pluralName), $this->textdomain),
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
		
		/* Merge default labels with those passed in */
		$this -> labels = array_merge($default_labels, $labels);
		
		/* Default args */
		$default_args = array(
			'hierarchical'      => true,
			'label'             => $singularName,
			'labels'            => $this -> labels,
			'public'            => true,
			'show_admin_column' => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array('slug' => sanitize_title_with_dashes(strtolower($pluralName))),
		);
		
		/* Merge default settings with those passed in */
		$this -> args = array_merge($default_args, $settings);
	}
		
	/* Actually register the taxonomy */
	public function registerTaxonomy()
	{
		if (!taxonomy_exists($this->taxName))
		{
			register_taxonomy($this->taxName, $this->postTypes, $this->args);
		}
	}
		
}