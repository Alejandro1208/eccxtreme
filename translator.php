<?php

class Language {

    /**
     * List of translations
     *
     * @var array
     */
    var $language   = array();
    /**
     * List of loaded language files
     *
     * @var array
     */
    var $is_loaded  = array();
    /**
     * default language folder
     *
     * @var string
     */
    private $language_folder;
    /**
     * default prefix on language array key
     *
     * @var string
     */
    private $language_prefix;

    private $language_list = array(
            'en' => 'english',
            'es' => 'spanish',
            'por' => 'portuguese'
        );

    private $langu;

    public function __construct()
    {

    }

    public function load($langfile = '', $idiom = '')
    {
        $this->_set_language();
        //echo $this->language_folder;

        $langfile = str_replace('.php', '', $langfile);
        // add prefix on language key
        $this->language_prefix = $langfile;

//        $langfile .= '.php';

        if (in_array($langfile, $this->is_loaded, TRUE))
        {
            return;
        }

        if ($idiom == '')
        {
            $deft_lang = $this->language_folder;
            $idiom = ($deft_lang == '') ? 'spanish' : $deft_lang;
        }

        $langfilepath = 'language/'.$langfile.'.lang.'.$idiom.'.php';
        // Determine where the language file is and load it
        if (file_exists($langfilepath))
        {
            include($langfilepath);
        }

        if ( ! isset($lang))
        {
            return;
        }

        $this->is_loaded[] = $langfile;
        // add prefix value of array key
        $lang = $this->_set_prefix($lang);
        $this->language = array_merge($this->language, $lang);
        unset($lang);
        return TRUE;
    }

    public function line($line = '')
    { 
        $value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
        return $value;
    }

    private function _set_prefix($lang = array())
    {
        $output = array();
        foreach ($lang as $key => $val)
        {
            $key = $this->language_prefix . "." . $key;
            $output[$key] = $val;
        }

        return $output;
    }

    private function _set_language()
    {
        $langu = (isset($_GET['lang'])) ? strtolower($_GET['lang']) : $this->_default_lang();

        if (! key_exists($langu, $this->language_list)) $langu = $this->_default_lang ();
        
        $this->language_folder = $this->language_list[$langu];
        $this->langu = $langu;
        return $this;
    }

    private function _default_lang()
    {
        return 'es';
    }

    public function getLoadedLanguage(){
        return $this->langu;
    }
}