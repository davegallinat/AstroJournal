<?php
namespace astrojournal;

/* class_lib.php */

/* This is the file for all the general classes that do various things */

/**********************************************************
* Should AstroJournal Observations post show on frontpage *
* There is a boolean variable in astrojournal.php to set  *
* $show_on_front variable                                 *
***********************************************************/
class astrojournal_on_frontpage
{
	public function __construct($show_on_front)
	{
		if ($show_on_front)
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