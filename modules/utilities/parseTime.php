<?php
class parseTime {
   static function getTime() {
    $week = date('W', 1448869500);
    $day = date('l', 1448869500);
    echo $week . ' : ' . $day;
  } 
}
?>
