const sr = $('#slider-range').slider({
	range: true,
	min: 0,
	max: maxslider, // var vinda do php
	values: slidervalues,
	slide: ( event, ui ) =>
		$('#amount').val(`R$ ${ui.values[0]} - R$ ${ui.values[1]}`)
});

$('#amount')
.val(`R$ ${sr.slider('values', 0)} - R$ ${sr.slider('values', 1)}`);


// atualização do filtro por options
$('.filterarea :input').on('change', function(e) {
	this.form.submit();
});
