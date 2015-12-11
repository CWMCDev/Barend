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

function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}
?>
