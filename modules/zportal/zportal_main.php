<?php
class Zportal {

	public $base_url = 'https://candea.zportal.nl/api/v2';
	public $type = 'student';
	public $week = 0;
	public $key = '';
	public $token;

	public function __construct($type = 'student') {
		$this->type = $type;
	}

	public function setAppKey($key = '') {
		if($key)
			$this->key = $key;

		return true;
	}
	public function setWeek($week = 0, $year = 0) {
		if($this->week == 0)
			$week = date('W');

		$this->week = $week;

		if($year == 0)
			$year = date('Y');

		$this->year = $year;

		return true;
	}

	/**
	 * Get the start and end date of a week
	 * @param  integer  The year
	 * @param  integer  The week number
	 * @return array    array(start, end)
	 */
	public function getStartEnd($year = 0, $week = 0) {
    $start = 0;
    if (strlen($week) < 2) {
      $start = strtotime($year."W0".$week);
    } else {
      $start = strtotime($year."W".$week);
    }
    $end = $start + 604800;
    
		return array($start, $end);
	}

	/**
	 * Get the access token using user code
	 * @param  integer User code
	 * @return string  Access token
	 */
	public function getToken() {
		$url = $this->base_url.'/oauth/token';
		$data = array(
			'grant_type'=>'authorization_code',
			'code'=>str_replace(' ', '',$this->key),
			'expires_in'=>4356789246579
		);
		$curl = curl::post($url, $data);
		$json = json_decode($curl);


		if(!$json) {
			return false;
		}
		else {
			$this->token = $json->access_token;
			return true;
		}
	}
	public function setToken($token) {
		$this->token = $token;
	}

	public function logout() {
		$url = $this->base_url.'/oauth/logout';
		$data = [
			'access_token' => $this->token
		];
		curl::post($url, $data);
	}

	/**
	 * Get the schedule
	 * @param  string   The token
	 * @param  integer  Start timestamp
	 * @param  integer  End timestamp
	 * @return array    The schedule data
	 */
	public function getSchedule($week, $type, $id) {
		$startend = '';
		if($week != 0) {
			if($week < date('W'))
				$year = date('Y') + 1;
			else
				$year = date('Y');

			$startend = $this->getStartEnd($year, $week);
			$startend = '&start='.$startend[0].'&end='.$startend[1];
		}
    
    if ($type == 'student') {
      $type = 'user';
    } else if ($type == 'room') {
      $type = 'locations';
    } else if ($type == 'teacher') {
      $type = 'teachers';
    } else if ($type == 'class') {
      $type = 'groups';
    } else if ($type == 'subject') {
      $type = 'subjects';
    }
    
    if ($id == 'self') {
      $id = '~me';
    }
    
		$url = $this->base_url.'/appointments?'.$type.'='.$id.'&access_token='.$this->token.$startend;
		$curl = curl::get($url);
		$json = json_decode($curl);
    
    echo $curl;
		return $json;
	}

}
?>