jQuery(document).ready(function () {
	jQuery('#dp_holliday').attr('readonly', true);
	jQuery('#dp_holliday').multiDatesPicker({
		dateFormat: "[mm,dd,yy]",
		minDate: 0,
});

});