<h1>Carrinho de compras</h1>

<table class="table table-dafault table-hover table-bordered table-cart">
    <tr>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Qtd</th>
        <th>Preço</th>
    </tr>
    <tbody>
        <?php $tal = 0?>
        <?php foreach ($list as $p):?>
        <tr>
            <td style="width: 100px"><img src="<?=BASE_URL.'media/products/'. $p['image']?>" alt=""></td>
            <td><?=$p['name']?></td>
            <td><?=$p['qt']?></td>
            <td>R$ <?=number_format($p['price'], 2, ',', '.')?></td>
            <?php $tal += $p['qt'] * $p['price']?>
        </tr>
        <?php endforeach?>
        <tr class="active tal">
            <td colspan="3"><b>Sub Total</b></td>
            <td>R$ <?=number_format($tal, 2, ',', '.')?></td>
        </tr>
    </tbody>
</table>