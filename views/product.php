<div class="row">
    <div class="col-sm-5">
        <div class="main-photo">
            <img src="<?=BASE_URL . 'media/products/' . $p_imgs[0]['url']?>" alt="">
        </div>
        <?php if (count($p_imgs) > 1):?>
            <ul class="gallery list-unstyled">
                <?php foreach ($p_imgs as $img):?>
                    <li class="photo_item">
                        <img src="<?=BASE_URL . 'media/products/' . $img['url']?>" alt="">
                    </li>
                <?php endforeach?>
            </ul>
        <?php endif?>
    </div>
    <div class="col-sm-7">
        <h2><?=$p['name']?></h2>
        <small><?=$p['brand']?></small><br>
        <?php if ($p['rating'] > 0) echo str_repeat('* ', $p['rating'])?>
        <hr>
        <p><?=utf8_encode($p['description'])?></p>
        <hr>
        De: <span class="p_price_from">R$ <?=$p['price_from']?></span><br>
        Por: <span class="p_price_origin">R$ <?=$p['price']?></span>

        <form method="POST" class="f-add-cart" action="<?=BASE_URL?>cart/add">
            <input type="hidden" name="id_product" value="<?=$p['id']?>">
            <input type="number" name="qt" min=1 max=99 value=1 step=1>
            <input type="submit" value="<?=$this->lang->get('ADD_TO_CART')?>">    
        </form>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-6">
        <?php if ($p_opts):?>
            <h3><?=$this->lang->get('PRODUCT_SPECIFICATIONS')?></h3>
            <?php foreach ($p_opts as $opt):?>
                <strong><?=$opt['name']?></strong>: <?=$opt['value']?><br>
            <?php endforeach?>
        <?php endif?>
    </div>
    <div class="col-sm-6">
        <h3><?=$this->lang->get('PRODUCT_REVIEWS')?></h3>
        <hr>
        <?php if ($p_rates):?>
            <?php foreach ($p_rates as $r):?>
                <strong><?=$r['user_name']?></strong> - <?=str_repeat('* ', $r['points'])?><br>
                "<?=$r['comment']?>"
                <hr>
            <?php endforeach?>
        <?php endif?>
    </div>
</div>