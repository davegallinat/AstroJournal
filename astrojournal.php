<?php
namespace astrojournal;
/*
* @package AstroJournal
* @version 1.0
*/
/*
Plugin Name: AstroJournal
Plugin URI: https://plaidmelon.github.io/AstroJournal/
Description: Wordpress plugin for keeping an astronomy observation journal.
Version: 1.0
Author: David Gallinat
Author URI: https://github.com/plaidmelon
*/

/****************
* USER SETTINGS *
*****************/



// Other options - NOT FINISHED
//show by shortcode?
//




/********************************************************************************/
/*							DO NOT EDIT BELOW THIS LINE							*/
/*						(unless you know what you're doing)						*/
/********************************************************************************/


/***********
* INCLUDES *
************/
require 'class_pm_createCustomPostType.php';
include 'class_pm_createTaxonomy.php';
include 'class_createConstellationTaxonomy.php';
include 'class_createObservationDateTimeMeta.php';
include 'class_astroJournalSettings.php';
include 'class_lib.php';

/********************************
* CREATE ASTROJOURNAL POST TYPE *
*********************************/
$astrojournal = new pm_createCustomPostType('astrojournal');
$astrojournal -> buildCustomPostType('astrojournal', 'Observation', 'Observations', array('menu_name'=>'AstroJournal'), array('menu_icon'=> 'dashicons-star-filled'));


/********************
* CREATE TAXONOMIES *
*********************/
/* buildTaxonomy arguments are: taxName, singularName, pluralName, postTypes, labels, settings */

$equipment = new pm_createTaxonomy('astrojournal');
$equipment -> buildTaxonomy('equipment', 'Equipment', 'Equipment', array('astrojournal'), array(), array());

$conditions = new pm_createTaxonomy('astrojournal');
$conditions -> buildTaxonomy('condition', 'Conditions', 'Conditions', array('astrojournal'), array(), array());

$locations = new pm_createTaxonomy('astrojournal');
$locations -> buildTaxonomy('location', 'Location', 'Locations', array('astrojournal'), array(), array());

$objectType = new pm_createTaxonomy('astrojournal');
$objectType -> buildTaxonomy('objecttype', 'Object Type', 'Object Types', array('astrojournal'), array(), array());


/****************************************
* TAXONOMIES THAT NEED SPECIAL HANDLING *
*****************************************/

/* Constellations */
// Don't mess with this or you'll break it.
// Seriously, don't mess with it.
$constellations = new createConstellationTaxonomy();
$constellations -> buildConstellationTaxonomy('constellation', 'Constellation', 'Constellations', array('astrojournal'));

/* Observation Date-Time */
// Utilizes:
// jQueryUI (https://jQueryUI.com)
// jQuery Timepicker add-on (https://github.com/trentrichardson/jQuery-Timepicker-Addon)
$dateTime = new createObservationDateTimeMeta('obDateTime', 'Observation Date-Time', array('astrojournal'));


/******************
* PERMALINK FLUSH *
*******************/
/* The aj_flush_permalinks class will set a flag/option upon plugin activation, register the CPT,
* then hopefully flush permalinks and delete the flag. It is a workaround
* for the fact that the register_activation_hook fires before the init action
* which registers the CPT, but the permalinks need to be flushed after the CPT is registered.
*
* Thanks to Andrés Villarreal (http://andrezrv.com/2014/08/12/efficiently-flush-rewrite-rules-plugin-activation/)
* for the workaround.
*/

class aj_flush_permalinks
{
	public function __construct()
	{
		register_activation_hook(__FILE__, array($this, 'set_activate_flag'));
		register_deactivation_hook(__FILE__, array($this, 'flush_rewrite'));
		
		/* priority set to 20, hopefully fires after register_post_type init */
		add_action('init', array($this, 'check_activate_flag'), 20);
	}
	
	public function set_activate_flag()
	{
		if (!get_option('aj_rewrite_flag'))
		{
			add_option('aj_rewrite_flag', true);
		}
	}

	public function check_activate_flag()
	{
		if (get_option('aj_rewrite_flag'))
		{
			flush_rewrite_rules();
			delete_option('aj_rewrite_flag');
		}
	}
	
	public function flush_rewrite()
	{
		flush_rewrite_rules();
	}
	
}

/* Call the permalink flush */
$aj_flush_permalinks = new aj_flush_permalinks();


/***************************
* GENERAL SETTINGS & CALLS *
****************************/
// Show on frontpage
$astrojournal_on_frontpage = new astrojournal_on_frontpage();

// Include in Recent Posts widget
$astrojournal_in_recent = new astrojournal_in_recent();

// Include in archives
$astrojournal_include_in_archives = new astrojournal_include_in_archives();

// Settings admin
$astroJournalSettings = new astroJournalSettings();

// Create custom admin columns
$customAdminColumns = new pm_customAdminColumns();

// Create shortcode
$aj_shortcode = new aj_shortcode();


/************************
* Hey, it's the bottom! *
*************************/