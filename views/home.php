<?
/**
 *  Lista de produtos com grid bootstrap 
 *  a cada trêz col-sm-4 gera uma nova linha
 *  para receber trêz novamente....
 */
?>

<?php $count = 0?>

<div class="row">
    <?php foreach($list as $p): ?>
        
        <div class="col-sm-4">
            <?=$this->loadView('product_item', $p)?>
        </div>

        <?php $count ++?>

        <?php if ($count > 2): ?>
            </div>
            <div class="row">
            <?php $count = 0?>
        <?php endif ?>

    <?php endforeach ?>
</div>