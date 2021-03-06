<?php
/**
 * domareg.hu API calls object
 *
 * @author Péter Képes
 * @version V1.0
 * @copyright Tárhelypark.hu, 06 February, 2013
 **/

define('SERVER_URL', 'https://ugyfel.domareg.hu/api/');

class DomareghuApi {
  private $ch;
  private $api_url;

  function __construct($api_url = SERVER_URL) {
    if (defined('DEV_SERVER_URL')) {
      $this->api_url = DEV_SERVER_URL;
    } else {
      $this->api_url = $api_url;
    }
  }
  /**
   * opens a HTTP connection
   * All commands send with one conection for performance reason
   *
   * @return void
   * @author Péter Képes
   **/
  function openHTTPConnection() {
    // Init connection
    $this->ch = curl_init();
    curl_setopt($this->ch,CURLOPT_POST,true);
    curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Accept: application/json'));
  }

  /**
   * closes oened HTTP connection
   *
   * @return void
   * @author Péter Képes
   **/
  function closeHTTPConnection() {
    //close connection
    curl_close($this->ch);
  }

  /**
   * send
   * All commands send with one conection for performance reason
   *
   * @return true if sucesed false if something bad happend
   * @author Péter Képes
   **/
  function sendCommand($command, $data) {
    curl_setopt($this->ch,CURLOPT_URL,$this->api_url . $command);
    curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($data));
    curl_setopt($this->ch,CURLOPT_TIMEOUT,20);
    $result = curl_exec($this->ch);
    $info = curl_getinfo($this->ch);

    if($result === false || is_null($result) || $result === "" || $info['http_code'] < 200 || $info['http_code'] > 202) {
      $eresult=array();
      $eresult["error"] = true;
      $eresult["error_code"] = $info['http_code'];
      $eresult["error_message"] = "Domain regisztrációs hiba!</br>";
      if ($info['http_code'] != 0)
        $eresult["error_message"] .= 'HTTP response code: ' . $info['http_code'] . "<br/>";
      if (curl_error($this->ch) != '')
        $eresult["error_message"] .= 'Curl error: ' . curl_error($this->ch) . "<br/>";
      if ($result != '')
        $eresult["error_message"] .= $result;

      return $eresult;
    }

    return json_decode($result, true);
  }
}