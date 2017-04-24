<?php

/**
 * Configuration loader class.
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
