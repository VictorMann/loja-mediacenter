<h1>Checkout transparent - Pagseguro</h1>

<form action="">
    <div class="row">
        <div class="col-xs-4">
           
            <h3>Dados Pessoais</h3>

            <div class="form-group">
                <label for="">Nome</label>
                <input type="text" name="name" class="form-control" value="Victor Carlos Mann">
            </div>

            <div class="form-group">
                <label for="">Telefone</label>
                <input type="text" name="phone" class="form-control" value="11987654321">
            </div>

            <div class="form-group">
                <label for="">CPF</label>
                <input type="text" name="cpf" class="form-control" value="45061400879">
            </div>

            <div class="form-group">
                <label for="">E-mail</label>
                <input type="text" name="email" class="form-control" value="c08477041629417892174@sandbox.pagseguro.com.br">
            </div>

            <div class="form-group">
                <label for="">Senha</label>
                <input type="password" name="password" class="form-control" value="14e8UT6D62M5HM0c">
            </div>
        </div>
        <div class="col-xs-4">

            <h3>Informações de endereço</h3>

            <div class="form-group">
                <label for="">CEP</label>
                <input type="text" name="cep" class="form-control" value="07145000">
            </div>

            <div class="form-group">
                <label for="">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="Est Zirconio">
            </div>

            <div class="form-group">
                <label for="">Número</label>
                <input type="text" name="numero" class="form-control" value="155">
            </div>

            <div class="form-group">
                <label for="">Complemento</label>
                <input type="text" name="complemento" class="form-control" value="Perto do mercado">
            </div>

            <div class="form-group">
                <label for="">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="Pq. Primavera">
            </div>

            <div class="form-group">
                <label for="">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="Guarulhos">
            </div>

            <div class="form-group">
                <label for="">Estado</label>
                <input type="text" name="estado" class="form-control" value="SP">
            </div>
        </div>
    
        <div class="col-xs-4">

            <h3>Informações de Pagamento</h3>

            <input type="hidden" name="total_amount" value="<?=$_SESSION['total_amount']?>">
            
            <div class="form-group">
                <label for="">Titular do cartão</label>
                <input type="text" name="cartao_titular" class="form-control" pattern="\d+" value="Fulano Ciclano">
            </div>

            <div class="form-group">
                <label for="">CPF do titular</label>
                <input type="text" name="cartao_cpf" class="form-control" pattern="\d+" value="45061400879">
            </div>

            <div class="form-group">
                <label for="">N. cartão</label>
                <input type="text" name="cartao_num" class="form-control" pattern="\d+">
            </div>

            <div class="form-group">
                <label for="">Cod. cartão</label>
                <input type="text" name="cartao_cvv" class="form-control" pattern="\d+" value="123">
            </div>

            <div class="clearfix">
                <div class="form-group pull-left" style="width: 60px">
                    <label for="">Validade</label>
                    <input type="number" name="cartao_mes" max="12" min="1" step="1" class="form-control" pattern="\d{1,2}" value="12">
                </div>

                <div class="form-group pull-left" style="width: 100px;margin-top: 1.8em;margin-left: .5em;">
                    <select name="cartao_ano" class="form-control">
                        <?php for ($i=date('Y'); $i < date('Y')+20; $i ++):?>
                            <option value="<?=$i?>" <?=$i==2030?'selected':''?>><?=$i?></option>
                        <?php endfor?>
                    </select>
                </div>
            </div>

            <h3>Parcelas</h3>
            <div class="form-group">
                <select name="parc" class="form-control parcelas">
                    <option value="">empty</option>
                </select>
            </div>

            <button class="btn btn-lg btn-success botao-efetuarCompra" type="button">Finalizar Comprar</button>
            
        </div>
    </div>
</form>

<script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
<script src="<?=BASE_URL?>assets/js/psckttransparente.js"></script>
<script>
PagSeguroDirectPayment.setSessionId('<?=$sessionCode?>');
</script>