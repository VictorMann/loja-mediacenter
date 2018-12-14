let slider = $(':hidden[name*=slider]', 'form');

const sr = $('#slider-range').slider({
	range: true,
	min: 0,
	max: maxslider, // var vinda do php
	values: slidervalues,
	slide: (event, ui ) => $('#amount').val(`R$ ${ui.values[0]} - R$ ${ui.values[1]}`),
	change: (event, ui) => {
		ui.values.forEach((v,i) => slider.eq(i).val(v));
		slider.get(0).form.submit();
	}
});

$('#amount')
.val(`R$ ${sr.slider('values', 0)} - R$ ${sr.slider('values', 1)}`);


// atualização do filtro por options
$('.filterarea :input').on('change', function(e) {
	this.form.submit();
});
