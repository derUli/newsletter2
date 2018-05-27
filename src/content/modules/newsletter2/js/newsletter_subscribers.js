$("#check-all").click(function() {
	$('input.subscriber-checkbox').not(this).prop('checked', this.checked);
});

$('input.subscriber-checkbox')
		.click(
				function(event) {
					$("#check-all")
							.prop(
									"checked",
									$('input.subscriber-checkbox').not(
											':checked').length <= 0);

				});

$("#confirmed").change(function(event) {
	$(this).closest("form").submit();
});