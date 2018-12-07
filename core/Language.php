<?php

class Language
{
    private $l;
    private $ini;

    public function __construct()
    {
        global $config;
        $this->l = $config['default_lang'];

        if (!empty($_SESSION['lang']) && file_exists('lang/'. $_SESSION['lang'] .'.ini'))
        {
            $this->l = $_SESSION['lang'];
        }

        $this->ini = parse_ini_file('lang/'. $this->l .'.ini');
    }

    public function get($word, $return = false)
    {
        $text = isset($this->ini[$word])
        ? $this->ini[$word]
        : $word;

        if ($return) return $text;
        else echo $text;
    }
}