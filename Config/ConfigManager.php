<?php

/**
 * Configuration loader class for Gloversure_Mamut
 *
 * @category  Mamut
 * @package   Gloversure_Mamut
 * @author    Charles Palmer <chp@gloversure.co.uk>
 * @copyright 2016 Gloversure Ltd
 *
 */
class Gloversure_Mamut_Config_ConfigManager {

    protected $data = array();

    public function __construct($file) {
        
        $this->load($file);
    }
    
    /**
     * Loads the data from a given file
     *
     * @param string $file
     *
     * @access private
     *
     */
    private function load($file) {
        if(!empty($file)) {
            $this->data = include $file;
        }
    }

    /**
     * Attempts to get a value by a given key
     *
     * @param string $key
     *
     * @access public
     *
     */
    public function get($key) {
        if(isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

}
