<h1>Carrinho de compras</h1>

<table class="table table-dafault table-hover table-bordered table-cart">
    <thead>
        <tr>
            <th style="width: 100px" class="cell-empty img"><i class="empty"></i></th>
            <th>Nome</th>
            <th>Qtd</th>
            <th>Preço</th>
        </tr>
    </thead>
    <tbody>
        <?php $tal = 0?>
        <?php foreach ($list as $p):?>
        <tr data-id="<?=$p['id']?>">
            <td><img src="<?=BASE_URL.'media/products/'. $p['image']?>" alt=""></td>
            <td><?=$p['name']?></td>
            <td><input class="cart-control-qt" type="number" min=1 step=1 value="<?=$p['qt']?>"></td>
            <td class="cart-price-item">R$ <?=number_format($p['price'], 2, ',', '.')?></td>
            <td style="width: 20px">
                <a href="<?=BASE_URL. 'cart/del/'. $p['id']?>"><i class="glyphicon glyphicon-remove text-danger"></i></a>
            </td>
            <?php $tal += $p['qt'] * $p['price']?>
        </tr>
        <?php endforeach?>
        <tr>
            <td class="cell-empty"><i class="empty"></i></td>
        </tr>
        <tr class="active tal">
            <td colspan="3">Sub Total</td>
            <td class="cart-total">R$ <?=number_format($tal, 2, ',', '.')?></td>
        </tr>
    </tbody>
</table>
<hr>
<div class="ctn-shipping flx flx-between">
    <div class="calc-frete">
        <h4>Qual é seu CEP?</h4>
        <form method="POST">
            <input type="text" name="cep" pattern="\d{5}-?\d{3}">
            <input type="submit" value="Calcular">
        </form>
    </div>
    <div class="amount">
        <?php if (!empty($shipping)):?>
            <h4><b>FRETE: R$ <?=$shipping['price'] . " - {$shipping['date']} dia(s)"?></b></h4>
            <h4 class="bg-success"><b>TOTAL: R$ <?=number_format($tal + str_replace(',', '.', $shipping['price']), 2, ',', '.')?></b></h4>
        <?php endif?>
    </div>
</div>
    

