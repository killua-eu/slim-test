<?php

namespace Glued\Models;
//use Glued\Models\Mapper;
class BookMapper extends Mapper
{

   public function getBooks() {
     $result = $this->mysqli->query(sprintf("SELECT * FROM `book` WHERE JSON_CONTAINS(tags,'%s')",
                                        $this->mysqli->real_escape_string('["JavaScript"]')));
     printf("%d rows matching.\n", $this->mysqli->affected_rows);
     while ($myrow = $result->fetch_assoc()) {
         $results[] = $myrow;
     };

     $results[] = "<br /><br />Using the mysqlidb class:";
     $this->db->where("JSON_CONTAINS(tags, ?)", array('["JavaScript"]'));
     $results[] = $this->db->get("book");
     $results[] = "<br />Last executed query was ". $this->db->getLastQuery();

     
     return $results;



   }
}