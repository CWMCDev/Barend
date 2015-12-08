<?php
class parseTime {
   static function getTime() {
    $day = date('l', 1448869500);
    switch($day){
      case "Monday": 
        echo 'Maandag';
        break;
      case "Tuesday": 
        echo 'Dinsdag';
        break;
      case "Wednesday": 
        echo 'Woensdag';
        break;
      case "Thursday": 
        echo 'Donderdag';
        break;
      case "Friday": 
        echo 'Vrijdag';
        break;
    }
  } 
}
?>
