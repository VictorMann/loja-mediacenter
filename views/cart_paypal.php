<h1>Paypal</h1>

<?php if ($error):?>
    <div class="alert alert-danger">
        <?=$error?>
    </div>
<?php endif?>

<form method="POST">
    <div class="row">
        <div class="col-xs-4">
           
            <h3>Dados Pessoais</h3>

            <div class="form-group">
                <label for="">Nome</label>
                <input type="text" name="name" class="form-control" value="Ciclano Pagante">
            </div>

            <div class="form-group">
                <label for="">Telefone</label>
                <input type="text" name="phone" class="form-control" value="11987654321">
            </div>

            <div class="form-group">
                <label for="">CPF</label>
                <input type="text" name="cpf" class="form-control" value="<?=PAGSEGURO_USER_CPF?>">
            </div>

            <div class="form-group">
                <label for="">E-mail</label>
                <input type="text" name="email" class="form-control" value="<?='teste@teste.com'?>">
            </div>

            <div class="form-group">
                <label for="">Senha</label>
                <input type="password" name="password" class="form-control" value="<?='123'?>">
            </div>
        </div>
        <div class="col-xs-4">

            <h3>Informações de endereço</h3>

            <div class="form-group">
                <label for="">CEP</label>
                <input type="text" name="cep" class="form-control" value="07145020">
            </div>

            <div class="form-group">
                <label for="">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="rua Comodor">
            </div>

            <div class="form-group">
                <label for="">Número</label>
                <input type="text" name="numero" class="form-control" value="300">
            </div>

            <div class="form-group">
                <label for="">Complemento</label>
                <input type="text" name="complemento" class="form-control" value="Perto do mercado">
            </div>

            <div class="form-group">
                <label for="">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="Pq. Lisboa">
            </div>

            <div class="form-group">
                <label for="">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="Terezinha">
            </div>

            <div class="form-group">
                <label for="">Estado</label>
                <input type="text" name="estado" class="form-control" value="SP">
            </div>
        </div>
    
        <div class="col-xs-4">


            <button class="btn btn-lg btn-success botao-efetuarCompra" type="submit">Finalizar Comprar</button>
            
        </div>
    </div>
</form>