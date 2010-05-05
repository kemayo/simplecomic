<?php

class DataBase {
    private $conn;
    private $debug;
    public $debug_output = array();
    
    function __construct($dbhost, $dbname, $dbuser, $dbpass, $debug = false) {
        $this->conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if(!$this->conn) {
            die("Database unavailable.");
        }
        mysqli_set_charset($this->conn, 'utf8');
        $this->debug = $debug;
    }

    function debug($sql) {
        if($this->debug) {
            $this->debug_output[] = $sql;
        }
    }

    function query($sql, $values = false) {
        if($values) {
            if(!is_array($values)) {
                $values = array($values);
            }
            $values = array_map("db_quoter", $values);
            $sql = vsprintf($sql, $values);
        }
        $this->debug($sql);
        return mysqli_query($this->conn, $sql);
    }

    function fetch($sql, $values = false) {
        $q = $this->query($sql, $values);
        if(!$q) {
            return array();
        }
        $results = array();
        while($r = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
            $results[] = $r;
        }
        mysqli_free_result($q);
        return $results;
    }
    
    function fetch_first($sql, $values = false) {
        $q = $this->query($sql, $values);
        if(!$q) {
            return false;
        }
        $r = mysqli_fetch_array($q, MYSQLI_ASSOC);
        mysqli_free_result($q);
        return $r;
    }
    
    function quick($sql, $values = false) {
        $q = $this->query($sql, $values);
        if(!$q) {
            return false;
        }
        $r = mysqli_fetch_array($q, MYSQLI_NUM);
        mysqli_free_result($q);
        if($r) {
            return current($r);
        }
    }
    
    function insert_id($sql, $values = false) {
        $q = $this->query($sql, $values);
        if(!$q) {
            return false;
        }
        $id = mysqli_insert_id($this->conn);
        mysqli_free_result($q);
        return $id;
    }
}

function db_quoter($s) {
    if(!is_numeric($s)) {
        $s = "'".addslashes($s)."'";
    }
    return $s;
}

/*
function query($sql, $values = null, $howmany = QUERY_ALL, $result_type = MYSQLI_ASSOC) {
    if($q) {
        $results = false;
        } elseif($howmany == QUERY_SINGLEVALUE) {
            if($r = mysqli_fetch_array($q, $result_type)) {
                $results = current($r);
            }
        } elseif($howmany == QUERY_SINGLEVALUE_ARRAY) {
            $results = array();
            while($r = mysqli_fetch_array($q, $result_type)) {
                $results[] = current($r);
            }
        }
    }
}
*/
?>
