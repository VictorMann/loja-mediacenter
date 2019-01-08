<div class="product_item">
    <a href="<?=BASE_URL?>product/open/<?=$id?>">
        <div class="product_tags">
            <?php if ($sale):?>
                <div class="product_tag tag_red"><?=$this->lang->get('SALE')?></div>
            <?php endif?>
            <?php if ($bestseller):?>
                <div class="product_tag tag_green"><?=$this->lang->get('BESTSELLER')?></div>
            <?php endif?>
            <?php if ($new_product):?>
                <div class="product_tag tag_blue"><?=$this->lang->get('NEWPRODUCT')?></div>
            <?php endif?>
        </div>

        
        <div class="product_image">
            <img src="<?=BASE_URL?>media/products/<?=$images[0]['url']?>">
        </div>
        <div class="product_name"><?=$name?></div>
        <div class="product_brand"><?=$brand_name?></div>
        <div class="product_price_from"><?=$price_from ? 'R$ '. number_format($price_from, 2, ',', '.'): ''?></div>
        <div class="product_price">R$ <?=number_format($price, 2, ',', '.')?></div>
        <div style="clear:both"></div>
    </a>
</div>