<?php
namespace astrojournal;

/* class_lib.php */

/* This is the file for all the general classes that do the things */


/**********************************************************
* This class modifies the "All Observations" screen to    *
* include custom columns and make them sortable by        *
* observation date.                                       *
***********************************************************/
class pm_customAdminColumns
{
	public function __construct()
	{
		add_action('manage_astrojournal_posts_columns',array($this, 'aj_addAdminColumn'));
		add_action('manage_astrojournal_posts_custom_column',array($this, 'aj_populateColumn'),10,2);
		add_filter( 'manage_edit-astrojournal_sortable_columns', array($this, 'aj_sortable_columns'));
		add_action('pre_get_posts', array($this, 'aj_columns_orderby'));
	}
	
	public function aj_addAdminColumn($column)
 	{
		unset($column['date']);				// remove default date column
		unset($column['comments']);			// remove default comment count column
		$column['obDateTime'] = 'Observed';	// add Observation Date/Time column
		return $column;
	}

	public function aj_populateColumn($column, $post_id)
	{
		/* Build Date/Time Column */
		if ($column == 'obDateTime')
		{
			$dateTime = get_post_meta( $post_id, 'obDateTime', true );
			echo date("M d, Y", $dateTime) . '<br>';
			echo date('g:i a', $dateTime);
			//echo '<br>' . $dateTime;
		}
	}
	
	public function aj_sortable_columns($sortable_columns)
	{
		$sortable_columns['obDateTime'] = 'obDateTime_column';
		return $sortable_columns;
	}
	
	public function aj_columns_orderby($query)
	{
		if (!is_admin())
			return;
		
		$orderby = $query->get( 'orderby');
		if( 'obDateTime_column' == $orderby )
		{
			$query->set('meta_key','obDateTime');
			$query->set('orderby','meta_value_num');
		}
	}
}


/****************************************************************
* This creates the AstroJournal shortcode - [aj_observations]   *
* Also adds a helper metabox to the edit screen showing post id *
*****************************************************************/
class aj_shortcode
{
	public function __construct()
	{
		add_shortcode('aj_observations', array($this, 'build_aj_shortcode'));
		add_action('add_meta_boxes', array($this, 'add_aj_shortcode_metabox'));
	}
	
	public function build_aj_shortcode($atts)
	{
		//global $post;
		$new_atts = shortcode_atts(array(
			'post_type'      => 'astrojournal',
			'p'              => '',			// post ID
			'posts_per_page' => '-1',
			'show'           => 'list',		// just titles or with excerpts, or full content
			'orderby'        => 'post_date',
			'order'          => 'DESC'
		), $atts);
		
		$postID = $new_atts['p'];
		
		// global namespace \WP_Query
		$query = new \WP_Query($new_atts);
		$string = '';
		
		if ($query -> have_posts())
		{
			while ($query -> have_posts())
			{
				$query -> the_post();
				$post_class = join(' ', get_post_class());
				
				// twenty seventeen breaks "entry-header" on large screen pages, commenting out for now
				$string .= '<article id="post-'.get_the_ID().'" class="'.$post_class.'">';
				//$string .= '<header class="entry-header">';
				$string .= '<h2 class="entry-title"><a href="'.get_the_permalink().'">'.get_the_title().'</a></h2>';
				$string .= '<div class="entry-meta">meta</div>';
				//$string .= '</header>';
				
				// show the excerpt if show => 'excerpt', default is 'list' of titles
				if ($new_atts['show'] == 'excerpt')
				{
					$string .= '<div class="entry-content">'.get_the_excerpt().'</div>';
				}
				
				// show the full content if show => 'content', default is 'list' of titles
				if ($new_atts['show'] == 'content')
				{
					$string .= '<div class="entry-content">'.get_the_content().'</div>';
				}
				
				$string .= '</article>';
			}
		}
		
		// reset to be nice, cuz we made a secondary loop
		wp_reset_postdata();
		
		return $string;
	}
	
	public function add_aj_shortcode_metabox()
	{
		/* If not in admin, then do nothing */
		if (!is_admin())
			return;
		
		/* Add the meta box */
		add_meta_box(
			'aj_shortcode_id',
			'Shortcode',
			array($this, 'build_shortcode_metabox'),
			'astrojournal',
			'side',
			'default'
		);
	}
	
	public function build_shortcode_metabox($post)
	{
		// grab the post ID
		$postID = $post->ID;
		
		// make it useful
		echo '<p>To display just this post use:</p>';
		echo '<p><em>[astrojournal p=' . $postID . ']</em></p>';
	}
	
}