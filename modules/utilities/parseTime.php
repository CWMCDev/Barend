<?php
class parseTime {
   static function getTime() {
    $day = date('l', 1448869500);
    switch($day){
      case "Monday": 
        echo 'Maandag';
        break;
    }
  } 
}
?>
