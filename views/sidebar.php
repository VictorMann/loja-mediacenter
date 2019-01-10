<aside>
    <h1><?=$this->lang->get('FILTER')?></h1>
    <div class="filterarea">
        <form method="GET">
            
            <input type="hidden" name="s" value="<?=isset($_GET['s'])?$_GET['s']:''?>">
            <input type="hidden" name="category" value="<?=isset($_GET['category'])?$_GET['category']:''?>">
            
            <div class="filterbox">
                <div class="filtertitle"><?=$this->lang->get('BRANDS')?></div>
                <div class="filtercontent">
                    <?php if (!empty($viewData['filters']['brands'])):?>
                        <?php foreach ($viewData['filters']['brands'] as $brand):?>
                            <div class="filteritem">
                                <label><input type="checkbox" name="filter[brand][]" value="<?=$brand['id']?>" <?=isset($viewData['filters_selected']['brand']) && in_array($brand['id'], $viewData['filters_selected']['brand'])?'checked':''?>> <?=$brand['name']?></label>
                                <span class="pull-right">(<?=$brand['qtd']?>)</span>
                            </div>
                        <?php endforeach?>
                    <?php endif?>
                </div>
            </div>
            <div class="filterbox">
                <div class="filtertitle"><?=$this->lang->get('PRICE')?></div>
                <div class="filtercontent">
                    <input type="hidden" name="filter[slider][min]" value="<?=$viewData['filters']['slider']['min']?>">
                    <input type="hidden" name="filter[slider][max]" value="<?=$viewData['filters']['slider']['max']?>">
                    <input type="text" id="amount" readonly>
                    <div id="slider-range"></div>
                </div>
            </div>
            <div class="filterbox">
                <div class="filtertitle"><?=$this->lang->get('RATING')?></div>
                <?php if (!empty($viewData['filters']['stars'])):?>
                    <?php foreach ($viewData['filters']['stars'] as $star => $qtd):?>
                        <div class="filtercontent">
                            <label>
                                <input type="checkbox" name="filter[star][]" value="<?=$star?>" <?=isset($viewData['filters_selected']['star']) && in_array($star, $viewData['filters_selected']['star'])?'checked':''?>> 
                                <?=$star ?: $this->lang->get('NOSTAR')?>
                            </label>
                            <span class="pull-right">(<?=$qtd?>)</span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </div>
            <div class="filterbox">
                <div class="filtertitle"><?=$this->lang->get('SALE')?></div>
                <div class="filtercontent">
                    <div class="filteritem">
                        <label><input type="checkbox" name="filter[sale]" <?=!empty($viewData['filters_selected']['sale'])?'checked':''?>> Em promoção</label>
                        <?php if (!empty($viewData['filters']['sale'])):?>
                            <span class="pull-right">(<?=$viewData['filters']['sale']?>)</span>
                        <?php endif?>
                    </div>
                </div>
            </div>
            <div class="filterbox">
                <div class="filtertitle"><?=$this->lang->get('OPTIONS')?></div>
                <div class="filtercontent">
                <?php if (!empty($viewData['filters']['options'])):?>
                    <?php foreach ($viewData['filters']['options'] as $option):?>
                        <strong><?=$option['name']?></strong>
                        <?php foreach ($option['options'] as $op):?>
                            <div class="filteritem">
                                <label><input type="checkbox" name="filter[options][]" value="<?=$op['value']?>" <?=isset($viewData['filters_selected']['options']) && in_array($op['value'], $viewData['filters_selected']['options'])?'checked':''?>> <?=$op['value']?></label>
                                <span class="pull-right">(<?=$op['count']?>)</span>
                            </div>
                        <?php endforeach?>
                    <?php endforeach?>
                <?php endif?>
                </div>
            </div>
        </form>
    </div>

    <div class="widget">
        <h1><?=$this->lang->get('FEATUREDPRODUCTS')?></h1>
        <div class="widget_body">
            <?php $this->loadView('widget_item', ['list' => $viewData['widget_featured1']])?>
        </div>
    </div>
</aside>