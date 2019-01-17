window.addEventListener('load', event => {
    
    $('.botao-efetuarCompra').click(function(e) {
        e.preventDefault();

        let id = PagSeguroDirectPayment.getSenderHash();

        let name = $('[name=name]').val().trim();
        let phone = $('[name=phone]').val().trim();
        let cpf = $('[name=cpf]').val().trim();
        let email = $('[name=email]').val().trim();
        let password = $('[name=password]').val().trim();
        let cep = $('[name=cep]').val().trim();
        let endereco = $('[name=endereco]').val().trim();
        let numero = $('[name=numero]').val().trim();
        let complemento = $('[name=complemento]').val().trim();
        let bairro = $('[name=bairro]').val().trim();
        let cidade = $('[name=cidade]').val().trim();
        let estado = $('[name=estado]').val().trim();

        let cartao_titular = $('[name=cartao_titular]').val().trim();
        let cartao_cpf = $('[name=cartao_cpf]').val().trim();
        let cartao_num = $('[name=cartao_num]').val().trim();
        let cvv = $('[name=cartao_cvv]').val().trim();
        let v_mes = $('[name=cartao_mes]').val().trim();
        let v_ano = $('[name=cartao_ano]').val().trim();
        let parc = $('[name=parc]').val();

        if (cartao_num && cvv && v_mes && v_ano) {

            // criando token para o cartao
            // forma de segurança do pagsegura para as transaçoes
            PagSeguroDirectPayment.createCardToken({
                cardNumber: cartao_num,
                brand: window.cardBrand,
                cvv: cvv,
                expirationMonth: +v_mes,
                expirationYear: +v_ano,
                success: r => {
                    console.log(r);
                    window.cardToken = r.card.token;

                    $.ajax({
                        url: BASE_URL + 'psckttransparente/checkout',
                        method: 'POST',
                        data: {
                            id,
                            name,
                            phone,
                            cpf,
                            email,
                            password,
                            cep,
                            endereco,
                            numero,
                            complemento,
                            bairro,
                            cidade,
                            estado,
                            cartao_titular,
                            cartao_cpf,
                            cartao_num,
                            cvv,
                            v_mes,
                            v_ano,
                            parc,
                            cartao_token: window.cardToken
                        },
                        dataType: 'json',
                        success: function(r) {
                            if (r.error) alert(r.msg);
                            console.log(r);

                        },
                        error: r => console.log(r) 
                    })


                },
                error: r => console.log(r),
                complete: r => console.log('complete token card')
            });
        }
    });


    $('input[name=cartao_num]').on('keyup', function(event) {
        let valor = this.value.trim();
        if (valor.length == 6) {

            PagSeguroDirectPayment.getBrand({
                cardBin: valor,
                success: r => {
                    console.log(r);
                    // obtendo bandeira do cartao
                    window.cardBrand = r.brand.name;
                    // limitando o numero do codigo de segurança correspondente a bandeira
                    $('input[name=cartao_cvv]').attr('maxlength', r.brand.cvvSize);
                    
                    // parcelas
                    PagSeguroDirectPayment.getInstallments({
                        amount: localStorage.getItem('total_com_frete'), // total da compra
                        brand: window.cardBrand,                         // bandeira card
                        // maxInstallmentNoInterest: 10,                 // 10x sem juros SEM A PROP FICA COM JUROS EM TODAS PARC
                        success: r => {
                            // parcelas
                            let parc = r.installments[window.cardBrand],
                                // documents Fragment para add options
                                optionsValues = document.createDocumentFragment();
                            parc.forEach(p => {
                                let op = document.createElement('option');
                                op.value = `${p.quantity};${p.installmentAmount};${p.interestFree}`;
                                op.innerText = `${p.quantity}x de R$ ${p.installmentAmount}`;
                                optionsValues.appendChild(op);
                            });

                            $('select.parcelas').html(optionsValues);
                        },
                        error: r => console.log(r),
                        complete: r => r
                    });
                },
                error: r => console.error(r),
                complete: r => console.log('complete')
            });
        }
    });
});