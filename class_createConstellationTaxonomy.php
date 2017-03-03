<?php
namespace astrojournal;

/* class_createConstellationTaxonomy.php */
/* This class create the custom constellation taxonomy and populates constellation data */

class createConstellationTaxonomy
{
	/* __constructor */
	public function __construct()
	{
		add_action('init', array($this, 'registerConstellationTaxonomy'));
		add_action( 'admin_menu' , array($this, 'removeDefaultMetaBox'));
		add_action('admin_menu', array($this, 'addCustomMetaBox'));
		add_action('save_post', array($this, 'saveMetaData'));
	}
		
	/* Build constellation taxonomy */
	public function buildConstellationTaxonomy($taxName, $singularName, $pluralName, $postTypes, $settings=array())
	{
		/* Cleanup slugs just in case */
		$this -> taxName = strtolower(str_replace(' ', '_', $taxName));
		$this -> singularName = $singularName;
		$this -> pluralName = $pluralName;
		$this -> postTypes = $postTypes;
		
		/* Default args */
		$default_args = array(
			'hierarchical'      => true,
			'label'             => __($this -> pluralName),
			'show_admin_column' => true,
			'show_ui'           => true,
			'query_var'         => $taxName,
			'rewrite'           => array('slug' => sanitize_title_with_dashes($taxName)),
		);
		
		/* Merge default settings with those passed in */
		$this -> args = array_merge($default_args, $settings);
	}
	
	/* registerConstellationTaxonomy */
	public function registerConstellationTaxonomy()
	{
		if (!taxonomy_exists($this->taxName))
		{
			register_taxonomy($this->taxName, $this->postTypes, $this->args);
			
			/* Get the file with all of our constellation info */
			include 'inc/constellation_list.php';
		
			/* Register all the constellations */
			for ($row = 0; $row < 88; $row++) {
				if (!term_exists($constellation_list[$row]["name"], $this->taxName)) {
					wp_insert_term($constellation_list[$row]["name"], $this->taxName, array(
					'description'=>$constellation_list[$row]["description"],
					'slug'=>$constellation_list[$row]["abbr"]));
				}
			}
		}
	}
	
	/* Remove default meta box */
	public function removeDefaultMetaBox()
	{
		remove_meta_box($this->taxName.'div', $this->postTypes, 'side');
	}
	
	/* Add custom meta box - dropdown */
	public function addCustomMetaBox()
	{
		/* If we're not in admin then stop */
		if (! is_admin())
			return;
	
		/* Otherwise, go ahead and make the box */
		add_meta_box(
			$this->taxName.'metaboxdiv',
			__($this->singularName),
			array($this, 'buildConstellationMetaBox'),
			$this->postTypes,
			'side',
			'default'
		);
	}
	
	/* buildConstellationMetaBox */
	public function buildConstellationMetaBox($post)
	{
		/* Create nonce */
		wp_nonce_field($this -> taxName . '_nonce', $this -> taxName . '_nonce_field');
		
		/* Get all terms, even those without observations attached */
		$constellations = get_terms('constellation', 'hide_empty=0');
		
		/* Get constellation attached to observation */
		$names = wp_get_object_terms($post->ID, 'constellation');
		
		/* Start the dropdown box */
		echo '<select name="post_constellation" id="post_constellation">';
			/* Display constellations as options */
			echo '<option class="constellation-option" value="">None</option>';
			
			foreach ($constellations as $constellation) {
				if (!is_wp_error($names) && !empty($names) && !strcmp($constellation->slug, $names[0]->slug))
				{
					echo '<option class="constellation-option" value="' . $constellation->slug . '" selected>' . $constellation->name . '</option>';
				}

				else
				{
					echo '<option class="constellation-option" value="' . $constellation->slug . '">' . $constellation->name . '</option>';
				}
			}
			echo '</select>';
			
	}
	
	
	/* saveConstellationData */
	public function saveMetaData($post_id)
	{
		/* nonce check */
		if (!isset($_POST[$this -> taxName . '_nonce_field']))
		{
			return $post_id;
		}
		
		/* Verify nonce */
		if (!wp_verify_nonce($_POST[$this -> taxName . '_nonce_field'], $this -> taxName . '_nonce'))
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
		
		/* OK, now we save */
		$post = get_post($post_id);
		$constellation = $_POST['post_constellation'];
		wp_set_object_terms($post_id, $constellation, $this->taxName);
		
		return $constellation;
	}
	
	
}