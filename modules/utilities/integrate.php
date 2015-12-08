<?php
class integrate {
   
  static function addPresention($schedule, $presention, $week) {
    $newSchedule = array();
    $timeParser = new parseTime();
    
    $dagenNamen = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag');
    
    foreach($schedule as $lesson) {
      $day = $timeParser::getTime($lesson->start);
      
      $resetLesson = array();
      
      foreach($lesson as $item => $value) {
        $resetLesson[] = $item=> $value;
      }
      $resetLesson[] = 'dayOfWeek'=>$day;
      
      foreach($presention as $weekPresention) {
        if ($weekPresention->week == $week) {
          $dayPresention = $weekPresention->dagen[$dagenNamen[$day]];
          
          $Status = $dayPresention[$lesson->startTimeSlot]->status;
          $resetLesson[] = 'status'=>$Status;
        }
      }
      
      $newSchedule[] = $lesson;
    }
    
    return $newSchedule;
  }
   
}
?>
