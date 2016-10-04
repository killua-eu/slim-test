<?php
namespace Glued\Models;
abstract class Mapper {
    protected $db;
    public function __construct($db) {
        $this->mysqli = $db;
        $this->db = new \MysqliDb ($this->mysqli);
    }
}