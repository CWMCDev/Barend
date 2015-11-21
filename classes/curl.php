<?php
class curl {

  /**
   * Generate a field string
   * @param  array  The content
   * @return string The string
   */
  public static function fieldstring($content=array()) {
    $fields_string = '';
    foreach($content as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    return $fields_string;
  }

    /**
     * @param string $request
     * @param array  $options
     * @param array  $headers
     *
     * @return mixed
     * @throws Exception
     */
  public static function get($request='', $options=array(), $headers=array()) {
      //Initializing...
      $ch = curl_init();
      //Set the URL
      curl_setopt($ch, CURLOPT_URL, $request);
      //Some defaults
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, '3');
      //Extra options
      foreach($options as $opt => $val)
          curl_setopt($ch, $opt, $val);
      //Headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //Set the content as a variable
      $content = curl_exec($ch);
      if (FALSE === $content)
          throw new Exception(curl_error($ch), curl_errno($ch));
      //Close the connection
      curl_close($ch);
      //Return it
      return $content;
  }

    /**
     * @param string $request
     * @param array  $content
     * @param array  $options
     * @param array  $headers
     *
     * @return array|mixed
     * @throws Exception
     */
  public static function post($request='', $content=array(), $options=array(), $headers=array()) {
      //Set the post fields
      if(is_array($content)) {
        $fields_string = '';
        foreach($content as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
      } else {
        $fields_string = $content;
      }
      
      //Initializing...
      $ch = curl_init();
      
      //Set the URL
      curl_setopt($ch, CURLOPT_URL, $request);
      //Default options
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, '3');
      //Extra options
      foreach($options as $opt => $val)
          curl_setopt($ch, $opt, $val);
      //Add POST content
      curl_setopt($ch,CURLOPT_POST, count($content));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
      //Add headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //Run!
      $content = curl_exec($ch);
      if (FALSE === $content)
          throw new Exception(curl_error($ch), curl_errno($ch));
      //Close the connection
      curl_close($ch);
      //Return the page.
      return $content;
  }

    /**
     * @param string $request
     * @param string $content
     * @param array  $options
     * @param array  $headers
     *
     * @return string
     * @throws Exception
     */
  public static function put($request='', $content='', $options=array(), $headers=array()) {
      //Init
      $ch = curl_init();
      
      // Set the URL
      curl_setopt($ch, CURLOPT_URL, $request);
      //Add the headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //Default options
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_TIMEOUT, '3');
      //Extra options
      foreach($options as $opt => $val)
          curl_setopt($ch, $opt, $val);
      //Add content
      curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
      //Run!
      $content = curl_exec($ch);
      if (FALSE === $content)
          throw new Exception(curl_error($ch), curl_errno($ch));
      //Close the connection
      curl_close($ch);
      //Return the page.
      return $content;
  }

    /**
     * @param string $request
     * @param array  $content
     * @param array  $options
     * @param array  $headers
     *
     * @return array|string
     * @throws Exception
     */
  public static function delete($request='', $content=array(), $options=array(), $headers=array()) {
      //Set the content
      $fields_string = '';
      foreach($content as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
      rtrim($fields_string, '&');
      //Initializing....
      $ch = curl_init();
      
      //Set the URL
      curl_setopt($ch, CURLOPT_URL, $request);
      //Default options
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, '3');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      //Extra options
      foreach($options as $opt => $val)
          curl_setopt($ch, $opt, $val);
      //Set the data
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      //Extra headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //Run!
      $content = trim(curl_exec($ch));
      if (FALSE === $content)
          throw new Exception(curl_error($ch), curl_errno($ch));
      //Close the connection
      curl_close($ch);
      //Return the page
      return $content;
  }
}