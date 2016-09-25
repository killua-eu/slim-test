<?php

namespace Glued\Models;
//use Glued\Models\Mapper;
class BookMapper extends Mapper
{

   public function getBooks() {
     $result = $this->db->query(sprintf("SELECT * FROM `book` WHERE JSON_CONTAINS(tags,'%s')",
                                        $this->db->real_escape_string('["JavaScript"]')));
     printf("%d rows matching.\n", $this->db->affected_rows);
     while ($myrow = $result->fetch_assoc()) {
         $results[]=$myrow;
     };
     $result->close();
     return $results;
   }
}