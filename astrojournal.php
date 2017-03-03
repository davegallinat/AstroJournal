<?php
namespace astrojournal;
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


require 'class_pm_createCustomPostType.php';
include 'class_pm_createTaxonomy.php';
include 'class_createConstellationTaxonomy.php';



/****************************
* Start Observation DateTime
*****************************/

class createObservationDateTimeMeta
{
	/* __constructor */
	
	/* register metabox */
	
	/* build metabox */
	
	/* save data */
	
	/* enqueue scripts */
	
}

/**************************
* End observation DateTime
**************************/


/* CREATE CUSTOM POST TYPE */
$astrojournal = new pm_createCustomPostType('astrojournal');
$astrojournal -> buildCustomPostType('astrojournal', 'Observation', 'Observations', array('menu_name'=>'AstroJournal'), array('menu_icon'=> 'dashicons-star-filled'));


/* CREATE TAXONOMIES */
$equipment = new pm_createTaxonomy('astrojournal');
$equipment -> buildTaxonomy('equipment', 'Equipment', 'Equipment', array('astrojournal'), array(), array());

$conditions = new pm_createTaxonomy('astrojournal');
$conditions -> buildTaxonomy('condition', 'Condition', 'Conditions', array('astrojournal'), array(), array());

$locations = new pm_createTaxonomy('astrojournal');
$locations -> buildTaxonomy('location', 'Location', 'Locations', array('astrojournal'), array(), array());

$objectType = new pm_createTaxonomy('astrojournal');
$objectType -> buildTaxonomy('objecttype', 'Object Type', 'Object Types', array('astrojournal'), array(), array());

/* TAXONOMIES THAT NEED SPECIAL HANDLING */
// Constellations
$constellations = new createConstellationTaxonomy();
$constellations -> buildConstellationTaxonomy('constellation', 'Constellation', 'Constellations', array('astrojournal'));

//$observationDateTime;

?>