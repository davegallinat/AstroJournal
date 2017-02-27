jQuery(document).ready(function(){
	jQuery('#aj_observation_date').datepicker({
            dateFormat : "mm/dd/yy"
        });	
});

jQuery(document).ready(function(){
	jQuery('#aj_observation_datetime').datetimepicker({
	timeFormat: "h:mm tt", dateFormat : "mm/dd/yy"
});	
});
