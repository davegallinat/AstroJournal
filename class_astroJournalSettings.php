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
		
		add_settings_field(
			'aj_show_on_frontpage_ID',                  // option ID
			'Show on Frontpage',                        // option title
			array($this, 'build_aj_show_on_frontpage'), // print option field function
			'astrojournal-settings-admin',              // page slug
			'aj_general_settings_section'               // section to put option in
		);
		
		add_settings_field(
			'aj_include_in_archives_ID',                  // option ID
			'Include in Archives',                        // option title
			array($this, 'build_aj_include_in_archives'), // print option field function
			'astrojournal-settings-admin',                // page slug
			'aj_general_settings_section'                 // section to put option in
		);
		
		// add the default options and values
		$add_options = array(
			'aj_show_on_frontpage_ID' => 1,
			'aj_include_in_archives_ID' => 1
		);
		add_option('aj_general_settings', $add_options);
	}
	
	public function aj_sanitize($input)
	{
		$input['aj_show_on_frontpage_ID'] = ($input['aj_show_on_frontpage_ID'] == 1 ? 1 : 0);
		$input['aj_include_in_archives_ID'] = ($input['aj_include_in_archives_ID'] == 1 ? 1 : 0);
		
		return $input;
	}
	
	public function print_aj_general_settings_section()
	{
		echo '<p>General settings for how AstroJournal behaves.</p>';
	}
	
	public function build_aj_show_on_frontpage()
	{
		$show_on_front = get_option('aj_general_settings');
		$show_on_front = $show_on_front['aj_show_on_frontpage_ID'];
		echo '<input type="checkbox" id="aj_show_on_frontpage_ID" name="aj_general_settings[aj_show_on_frontpage_ID]"  value="1"' . checked( 1, $show_on_front, false) . '/>';
	}
	
	public function build_aj_include_in_archives()
	{
		$include_in_archives = get_option('aj_general_settings');
		$include_in_archives = $include_in_archives['aj_include_in_archives_ID'];
		
		echo '<input type="checkbox" id="aj_include_in_archives_ID" name="aj_general_settings[aj_include_in_archives_ID]"  value="1"' . checked( 1, $include_in_archives, false) . '/>';
	}
}


/**********************************************************
* Should AstroJournal Observation posts show on frontpage *
***********************************************************/
class astrojournal_on_frontpage
{
	public function __construct($show_astrojournal_on_frontpage)
	{
		$show_on_front_page = get_option('aj_general_settings');
		$show_on_front_page = $show_on_front_page['aj_show_on_frontpage_ID'];
		
		if ($show_on_front_page == 1)
		{
			add_filter('pre_get_posts', array($this, 'modifyQuery'), 99);
		}
		else {
			return;
		}
	}
	
	public function modifyQuery($query)
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