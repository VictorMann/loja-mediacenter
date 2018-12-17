<?php foreach ($subs as $sub):?>
    <option 
    value="<?=$sub['id']?>"
    <?=!empty($_GET['category']) && $_GET['category'] == $sub['id']?'selected':''?>>
        <?=str_repeat('--', $level) . ' '?>
        <?=$sub['name']?>
    </option>
    <?php
        if (count($sub['subs']) > 0)
        {
            $this->loadView('search_subcategory', [
                'subs' => $sub['subs'],
                'level' => $level+1
            ]);
        }
    ?>
<?php endforeach?>