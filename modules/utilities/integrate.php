<?php
class integrate {
   
  static function addPresention($schedule, $presention, $week) {
    $newSchedule = array();
    $timeParser = new parseTime();
    
    $dagenNamen = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag');
    
    foreach($schedule as $lesson) {
      $day = $timeParser::getTime($lesson->start);
      

      
      foreach($presention as $weekPresention) {
        if ($weekPresention->week == $week) {
          $dayPresention = $weekPresention->dagen[$dagenNamen[$day]];
          
          $Status = $dayPresention[$lesson->startTimeSlot-1]->status;
        }
      }
      
      $resetLesson = array();
      
      $newSchedule[] = $resetLesson;
    }
    
    return $newSchedule;
  }
   
}
?>
