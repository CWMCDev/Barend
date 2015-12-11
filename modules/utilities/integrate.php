<?php
class integrate {
   
  static function addPresention($schedule, $presention, $week) {
    
    $newSchedule = array();
    $timeParser = new parseTime();
    
    $dagenNamen = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag');
    
    foreach($schedule as $lesson) {
      $lesson = get_object_vars($lesson);
       
      $day = $timeParser::getTime($lesson['start']);
      $status = "";
      
      foreach($presention as $presentie) {
        foreach ($presentie as $weekPresention) {
         
          if ($weekPresention["week"] == $week) {
            
            $dayPresention = $weekPresention["dagen"][$day];
            $status = $dayPresention[(string)($lesson['startTimeSlot'] - 1)]["status"];
            
          }
        }
      }
      $lesson["dayOfWeek"] = $day;
      $lesson["status"] = $status;
      
      $newSchedule[] = $lesson;
    }
    
    return $newSchedule;
  }
   
}
?>
