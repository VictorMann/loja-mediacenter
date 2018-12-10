<?php foreach ($subs as $sub):?>
    <li>
        <a href="<?=BASE_URL. 'categories/enter/'. $sub['id']?>">
            <?=str_repeat('--', $level). ' '?>
            <?=$sub['name']?>
        </a>
    </li>
    <?php
        if (count($sub['subs']) > 0)
        {
            $this->loadView('menu_subcategory', [
                'subs' => $sub['subs'],
                'level' => $level+1
            ]);
        }
    ?>
<?php endforeach?>