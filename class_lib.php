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