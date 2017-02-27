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
	public function __construct($name, $args = array(), $labels = array())
	{
		/* Setup CPT variables */
		$this -> postTypeName   = strtolower(str_replace(' ', '', $name));
		$this -> postTypeDisplayName = $name;
		$this -> postTypeArgs   = $args;
		$this -> postTypeLabels = $labels;
		
		/* Check if exists and add action*/
		if (!post_type_exists($this->postTypeName))
		{
			add_action('init', array(&$this, 'register_post_type'));
		}
		
		/* Listen for save */
		$this -> saveMeta();
	}
	
	/* REGISTER POST TYPE */
	public function registerPostType()
	{
		
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

class pm_createTaxonomy extends pm_createCustomPostType
{	
	/* CONSTRUCTOR */
	public function __construct($taxName, $taxArgs = array(), $taxLabels = array())
	{
		/* Setup taxonomy variables */
		$this -> taxName   = strtolower(str_replace(' ', '', $name));
		$this -> taxArgs   = $taxArgs;
		$this -> taxLabels = $taxLabels;
		
	}
		
	/* REGISTER TAXONOMY */
	public function addTaxonomy()
	{
		
	}
		
	/* CREATE CUSTOM META BOX - PASS BOOLEAN */
	public function addMetaBox()
	{
		
	}

}

/* CREATE CUSTOM POST TYPE */
$astroJournal = new pm_createCustomPostType();

/* CREATE TAXONOMIES */
$equipment = new pm_createTaxonomy();	
$objectType = new pm_createTaxonomy();
$conditions = new pm_createTaxonomy();
$locations = new pm_createTaxonomy();

/* TAXONOMIES THAT NEED SPECIAL HANDLING */
$constellation;
$observationDateTime;





?>