<?php

class Page {
    public $title;
    
    private $css = array();
    private $js = array();
    private $breadcrumbs = array();
    
    private $debug = array();
    
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
        if(!DEBUG) {
            // no point in accumulating
            return;
        }
        $this->debug[$key] = $details;
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
}

?>