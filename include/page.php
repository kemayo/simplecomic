<?php

class Page {
    public $title;
    
    private $css = array();
    private $js = array();
    private $breadcrumbs = array();
    
    private $debug = array();
    private $start_time = 0;
    
    public function set_start_time($start_time) {
        $this->start_time = $start_time;
    }
    
    public function add_css($url) {
        $this->css[$url] = $url;
    }
    public function add_js($url) {
        $this->js[$url] = $url;
    }
    public function add_breadcrumb($label, $url) {
        $this->breadcrumbs[] = array($label, $url);
    }
    public function get_breadcrumbs() {
        return $this->breadcrumbs;
    }
    
    public function debug($key, $details) {
        if(DEBUG) {
            if(!isset($this->debug[$key])) {
                $this->debug[$key] = array();
            }
            $this->debug[$key][] = $details;
        }
    }
    public function get_debug() {
        return $this->debug;
    }
    
    public function output_css() {
        foreach($this->css as $url) {
            echo '<link rel="stylesheet" href="', url($url, false, true), '" type="text/css" />', "\n";
        }
    }
    
    public function output_js() {
        foreach($this->js as $js) {
            if(preg_match('/^<script/', $js)) {
                echo $js;
            } else {
                echo '<script src="', url($js, false, true), '" type="text/javascript"></script>';
            }
        }
    }
    
    public function elapsed() {
        return microtime(true) - $this->start_time;
    }
}

?>