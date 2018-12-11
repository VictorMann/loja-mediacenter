const sr = $('#slider-range').slider({
	range: true,
	min: 0,
	max: maxslider, // var vinda do php
	values: [0, maxslider],
	slide: ( event, ui ) =>
		$('#amount').val(`R$ ${ui.values[0]} - R$ ${ui.values[1]}`)
});

$('#amount')
.val(`R$ ${sr.slider('values', 0)} - R$ ${sr.slider('values', 1)}`);
