<?php
class integrate {
   
  static function addPresention($schedule, $presention, $week) {
    
    $newSchedule = array();
    $timeParser = new parseTime();
    
    $dagenNamen = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag');
    
    foreach($schedule as $lesson) {
      print_r(get_object_vars($lesson));
      return false;
       
      $day = $timeParser::getTime($lesson->start);
      $Status = "";
      
      foreach($presention as $presentie) {
        foreach ($presentie as $weekPresention) {
         
          if ($weekPresention["week"] == $week) {
            
            $dayPresention = $weekPresention["dagen"][$day];
            $Status = $dayPresention[(string)($lesson->startTimeSlot- 1)]["status"];
            
          }
        }
      }
      $lesson["dayOfWeek"] = $day;
      $lesson["status"] = $Status;
      
      $newSchedule[] = $lesson;
    }
    
    return $newSchedule;
  }
   
}
?>
