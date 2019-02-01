<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Permissões</h1>
</section>

<!-- Main content -->
<section class="content container-fluid">

    <form method="POST" action="<?=BASE_URL?>permissions/add_action">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Novo Grupo de Permissão</h3>
                <div class="box-tools">
                    <input type="submit" class="btn btn-success" value="Salvar">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group <?=array_key_exists('name', $errors)?'has-error':''?>">
                    <label for="">Nome do grupo</label>
                    <input type="text" class="form-control" name="name">
                    <?php if(array_key_exists('name', $errors)):?>
                        <span class="help-block"><?=$errors['name']?></span>
                    <?php endif?>
                </div>
                <hr>
                <?php foreach ($permission_items as $item):?>
                    <div class="form-group">
                        <input type="checkbox" name="items[]" value="<?=$item['id']?>" id="item-<?=$item['id']?>">
                        <label for="item-<?=$item['id']?>"><?=utf8_encode($item['name'])?></label>
                    </div>
                <?php endforeach?>
            </div>
        </div>
    </form>
    
</section>