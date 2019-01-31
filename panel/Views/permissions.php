<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Permissões</h1>
</section>

<!-- Main content -->
<section class="content container-fluid">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Grupos de Permissões</h3>
            <div class="box-tools">
                <a href="<?=BASE_URL?>permissions/add" class="btn btn-success">
                    Adicionar
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome da permissão</th>
                        <th style="width: 110px">Qtd. de ativos</th>
                        <th style="width: 105px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item):?>
                        <tr>
                            <td><?=$item['name']?></td>
                            <td><?=$item['total_users']?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?=BASE_URL?>permissions/edit/<?=$item['id']?>" class="btn btn-xs btn-primary">Editar</a>
                                    <a href="<?=BASE_URL?>permissions/del/<?=$item['id']?>" class="btn btn-xs btn-danger <?=($item['total_users']>0)?'disabled':''?>">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (!empty($_SESSION['mensagem'])):?>
        <div class="mensagem">
            <div class="alert <?=$_SESSION['mensagem']['class']?>" style="opacity: .8">
                <?=$_SESSION['mensagem']['text']?>
            </div>
        </div>
    <?php unset($_SESSION['mensagem']); endif;?>

</section>