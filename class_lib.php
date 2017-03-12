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


/**************************************************************
* This creates the AstroJournal shortcode - [aj_observations] *
***************************************************************/
class aj_shortcode
{
	public function __construct()
	{
		add_shortcode('aj_observations', array($this, 'build_aj_shortcode'));
	}
	
	public function build_aj_shortcode($atts)
	{
		
		$new_atts = shortcode_atts(array(
			'post_type'   => 'astrojournal',
			'post_status' => 'publish',
			'display'     => 'list'
			
		), $atts);
		
		
		// global namespace \WP_Query
		$query = new \WP_Query($new_atts);
		$string = '';
		
		if ($query -> have_posts())
		{
			$string .= '<ul>';
			while ($query -> have_posts())
			{
				$query -> the_post();
				$string .= '<li><a href="'.get_the_permalink().'">' . get_the_title() . '</a></li>';
			}
			$string .= '</ul>';
		}
		
		// reset to be nice, cuz we used a second loop
		wp_reset_postdata();
		
		return $string;
	}
	
}