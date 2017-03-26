<?php
namespace astrojournal;

/* class_astroJournalSettings.php */
/* This is the library that handles the AstroJournal Settings */

/********************************************************
* This class handles the creation of the settings admin *
*********************************************************/
class astroJournalSettings
{
	private $options;
	
	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_ajSettingsPage'));
		add_action('admin_init', array($this, 'register_ajSettings'));
	}
	
	public function add_ajSettingsPage()
	{
		add_submenu_page(
			'edit.php?post_type=astrojournal',   // parent slug
			'AstroJournal Settings',             // page title
			'Settings',                          // menu title
			'manage_options',                    // capability
			'astrojournal-settings-admin',       // page slug
			array($this, 'build_ajSettingsPage') // build page function
		);
	}
	
	public function build_ajSettingsPage()
	{
		// get options
		$this -> options = get_option('aj_general_settings'); // uses settings name
		
		// add options if they don't exist
		
		/* Begin the page */
		?>
		<div class="wrap">
			<h1>AstroJournal Settings</h1>
		
		<!-- Begin Settings form -->
			<form method="post" action="options.php">
				<?php
				// WP hidden fields function
				settings_fields('aj_general_settings_group');        // uses group name
				do_settings_sections('astrojournal-settings-admin'); // uses page slug
				
				submit_button();
				?>
			</form>
		</div> <!-- end wrap -->
		<?php
	}
	
	public function register_ajSettings()
	{
		register_setting(
			'aj_general_settings_group', // group name
			'aj_general_settings',       // settings name
			array($this, 'aj_sanitize')  // sanitize function
		);
		
		add_settings_section(
			'aj_general_settings_section',                     // section ID
			'General Settings',                                // section title
			array($this, 'print_aj_general_settings_section'), // print group function
			'astrojournal-settings-admin'                      // page slug
		);
		
		/* Option to show AstroJournal posts on the frontpage */
		add_settings_field(
			'aj_show_on_frontpage_id',                  // option ID
			'Show on Frontpage',                        // option title
			array($this, 'build_aj_show_on_frontpage'), // print option field function
			'astrojournal-settings-admin',              // page slug
			'aj_general_settings_section'               // section to put option in
		);
		
		/* Option to show AstroJournal in the "Recent Posts" widget */
		add_settings_field(
			'aj_include_recent_posts_id',                  // option ID
			'Show in Recent Posts Widget',                 // option title
			array($this, 'build_aj_include_recent_posts'), // print option field function
			'astrojournal-settings-admin',                 // page slug
			'aj_general_settings_section'                  // section to put option in
		);
		
		/* Option to include AstroJournal in the archive */
		add_settings_field(
			'aj_include_in_archives_id',                  // option ID
			'Include in archives',                        // option title
			array($this, 'build_aj_include_in_archives'), // print option field function
			'astrojournal-settings-admin',                // page slug
			'aj_general_settings_section'                 // section to put option in
		);
		
		// add the default options and values
		$add_options = array(
			'aj_show_on_frontpage_id'    => 1,
			'aj_include_recent_posts_id' => 1,
			'aj_include_in_archives_id'  => 1
		);
		
		/* not sure which to use below, they both might give an error */
		add_option('aj_general_settings', $add_options);
		//update_option('aj_general_settings', $add_options);
	}
	
	/* Not sure why, but the sanitizing is only working if done long hand */
	public function aj_sanitize($input)
	{
		// set a new variable to work with
		$new_input = array();
		
		// check if aj_show_on_frontpage_id is checked, set to 1 to use in checked() later
		if (isset($input['aj_show_on_frontpage_id']))
		{
			$new_input['aj_show_on_frontpage_id'] = 1;
		}
		else {
			$new_input['aj_show_on_frontpage_id'] = 0;
		}
		
		// check if aj_include_recent_posts_id is checked, set to 1 to use in checked() later
		if (isset($input['aj_include_recent_posts_id']))
		{
			$new_input['aj_include_recent_posts_id'] = 1;
		}
		else {
			$new_input['aj_include_recent_posts_id'] = 0;
		}
		
		// check if aj_include_in_archives_id is checked, set to 1 to use in checked() later
		if (isset($input['aj_include_in_archives_id']))
		{
			$new_input['aj_include_in_archives_id'] = 1;
		}
		else {
			$new_input['aj_include_in_archives_id'] = 0;
		}
		
		
		return $new_input;
	}
	
	public function print_aj_general_settings_section()
	{
		echo '<p>General settings for how AstroJournal behaves.</p>';
	}
	
	public function build_aj_show_on_frontpage()
	{
		$show_on_front = get_option('aj_general_settings');
		$show_on_front = $show_on_front['aj_show_on_frontpage_id'];
		echo '<input type="checkbox" id="aj_show_on_frontpage_id" name="aj_general_settings[aj_show_on_frontpage_id]"  value="1"' . checked( 1, $show_on_front, false) . '/>';
	}
	
	public function build_aj_include_recent_posts()
	{
		$show_in_recent = get_option('aj_general_settings');
		$show_in_recent = $show_in_recent['aj_include_recent_posts_id'];
		echo '<input type="checkbox" id="aj_include_recent_posts_id" name="aj_general_settings[aj_include_recent_posts_id]"  value="1"' . checked( 1, $show_in_recent, false) . '/>';
	}
	
	public function build_aj_include_in_archives()
	{
		$include_in_archives = get_option('aj_general_settings');
		$include_in_archives = $include_in_archives['aj_include_in_archives_id'];
		echo '<input type="checkbox" id="aj_include_in_archives_id" name="aj_general_settings[aj_include_in_archives_id]"  value="1"' . checked( 1, $include_in_archives, false) . '/>';
	}

}

/**********************************************************
* Should AstroJournal Observation posts show on frontpage *
***********************************************************/
class astrojournal_on_frontpage
{
	public function __construct()
	{
		$show_on_front_page = get_option('aj_general_settings');
		$show_on_front_page = $show_on_front_page['aj_show_on_frontpage_id'];
		
		if ($show_on_front_page == 1)
		{
			add_filter('pre_get_posts', array($this, 'modifyQueryFrontPage'), 97);
		}
		else {
			return;
		}
	}
	
	public function modifyQueryFrontPage($query)
	{
		if ($query->is_home() && $query->is_main_query()){
			$post_types = $query->get('post_type');
		
			if (!is_array($post_types) && !empty($post_types)) {
				$post_types = explode(',', $post_types);
			}
		
			/* Check if empty, include post just in case */
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
}

/*********************************************************
* Should AstroJournal be included in recent posts widget *
**********************************************************/
class astrojournal_in_recent
{
	public function __construct()
	{
		$include_in_recent = get_option('aj_general_settings');
		$include_in_recent = $include_in_recent['aj_include_recent_posts_id'];
		
		if ($include_in_recent == 1)
		{
			add_filter('widget_posts_args', array($this, 'modifyRecent'));
		}
		else {
			return;
		};
	}
	
	public function modifyRecent($args)
	{
		if (!empty($args['post_type']))
		{
			// add AstroJournal to the post_type array
			$args['post_type'][] = 'astrojournal';
		}
		else {
			// If for some reason $args['post_type'] is empty (it shouldn't be),
			// include 'post' just to be safe
			$args['post_type'] = array('post', 'astrojournal');
		}
		
		return $args;
	}
	
}

/***************************************************
* Should AstroJournal be included in the archives  *
*
* This is a two part process - add to the widget   *
* and then to archive.php. modifyArchive is almost *
* identical to modifyQueryFrontPage from above     *
****************************************************/
class astrojournal_include_in_archives
{
	public function __construct()
	{
		$include_in_archives = get_option('aj_general_settings');
		$include_in_archives = $include_in_archives['aj_include_in_archives_id'];
		
		if ($include_in_archives == 1)
		{
			add_filter('pre_get_posts', array($this, 'modifyArchives'), 99);
			add_filter('getarchives_where', array($this, 'addToWidget'));
		}
	}
	
	public function addToWidget($where)
	{
		$where = str_replace("post_type = 'post'", "post_type IN ('post', 'astrojournal')", $where);
		return $where;
	}
	
	public function modifyArchives($query)
	{
		if (!is_admin() && $query->is_main_query() && $query->is_archive())
		{
			$post_types = $query->get('post_type');
			
			if (!is_array($post_types) && !empty($post_types))
			{
				$post_types = explode(',', $post_types);
			}
			
			if (empty($post_types))
			{
				$post_types[] = 'post';
			}
			
			$post_types[] = 'astrojournal';
			
			$post_types = array_map('trim', $post_types);
			$post_types = array_filter($post_types);
			
			/* update the $query list */
			$query->set('post_type', $post_types);
		}
		
	}
	
}