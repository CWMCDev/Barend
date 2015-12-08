<?php
class parseTime {
   static function getTime($unixTime) {
    $day = date('l', $unixTime);
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
