<?php

class Filters extends model
{
  public function getFilters()
  {
    $dados = [
      'brands' => [],
      'maxslider' => 999,
      'stars' => [],
      'sale' => false,
      'options' => [],
    ];

    $brands = new Brands;
    $dados['brands'] = $brands->getListTotalItems();

    return $dados;
  }
}