<?php foreach ($list as $w): ?>
<div class="widget_item">
    <figure class="widget_image">
        <a href="#">
            <img src="<?=BASE_URL?>media/products/<?=$w['images'][0]['url']?>">
        </a>
    </figure>
    <div class="widget_desc">
        <a href="#">
            <h3><?=$w['name']?></h3>
            <p><span>R$ <?=number_format($w['price_from'], 2, ',', '.')?></span> R$ <?=number_format($w['price'], 2, ',', '.')?></p>
        </a>
    </div>
</div>
<?php endforeach ?>