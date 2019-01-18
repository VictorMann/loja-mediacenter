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

// ** Pagina Produto
//
// alterando imagem principal com miniaturas ao clicar
let img_principal = document.querySelector('.main-photo img');
document.querySelectorAll('.photo_item img').forEach(img => 
	img.addEventListener('click', function(event) {
		img_principal.src = this.src;
	})
);
/*
$('.photo_item img').click(function(event) {
	$('.main-photo img').attr('src', this.src);
});
*/
$('.cart-control-qt')
.each(function(){
	this.dataset.lastValue = this.value;	
})
.blur(function(event) {
	
	if (this.dataset.lastValue == this.value) return;
	this.dataset.lastValue = this.value;
	
	let id = $(this).closest('tr').attr('data-id');

	let form = new FormData();
	form.append('id', id);
	form.append('qt', this.value);

	fetch(BASE_URL + 'cart/update', {
		method: 'POST',
		body: form,
	})
	.then(res => res.ok ? res.json() : Promise.reject(res.statusText))
	.then(dados => {
		let valItem = $(this).closest('tr').find('.cart-price-item').text();
		let talCart = $('.cart-total:first').text();
		
		valItem = textoMoedaParaNumero(valItem);
		talCart = textoMoedaParaNumero(talCart);
		
		talCart -= valItem * dados.qt_last;
		talCart += valItem * dados.qt_now;

		window.localStorage.setItem('total_sem_frete', talCart.toFixed(2));

		let form = new FormData();
		form.append('valor', window.localStorage.getItem('total_sem_frete'));

		fetch('cart/ajustartotal', {
			method: 'POST', 
			body: form
		})
		.then(res => res.ok ? res.text() : Promise.reject(res.statusText))
		.then(valor => {
			console.log(valor);
			$('.total-com-frete').text( numeroParaTextoMoeda(valor) );
		});
		
		talCart = numeroParaTextoMoeda(talCart);
		$('.cart-total').text(talCart);
	})
	.catch(console.log);
	
});
function toArray(texto) {
	return Array.prototype.slice.call(texto, 0);
}
function textoMoedaParaNumero(texto) {
	return +texto.replace(/[r$ .]/ig, '').replace(',', '.');
}
function numeroParaTextoMoeda(n) {
	n = Number(n);
	n = toArray(n.toFixed(2).replace('.',',')).reverse();
	let pos = 3 + n.indexOf(',');
	for (;pos < n.length; pos += 3) {
		if (!(pos+1 in n)) break;
		n[pos] = '.' + n[pos];
	}
	return 'R$ ' + n.reverse().join('');
}


