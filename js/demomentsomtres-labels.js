jQuery(document).ready(function() {
	jQuery(".tots").click(function(event){
		event.preventDefault();
		jQuery(this).parent().parent().find(":checkbox").prop( "checked", true );
	});
	jQuery(".cap").click(function(event){
		event.preventDefault();
		jQuery(this).parent().parent().find(":checkbox").prop( "checked", false );
	});
});
