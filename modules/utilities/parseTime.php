<?php
class parseTime {
   static function getTime() {
    $day = date('l', 1448869500);
    switch($day){
      case "Monday": 
        return 'Maandag';
        break;
      case "Tuesday": 
        return 'Dinsdag';
        break;
      case "Wednesday": 
        return 'Woensdag';
        break;
      case "Thursday": 
        return 'Donderdag';
        break;
      case "Friday": 
        return 'Vrijdag';
        break;
    }
  } 
}
?>
