<?php
namespace perceptlink;

class ApiHttpURLFetcher {

  private $userAgent;
  private $timeout;
  private $method;
  private $responseCode = 600;
  private $content = null;

  public function __construct($userAgent, $timeout, $method) {
    $this->userAgent = $userAgent;
    $this->timeout = $timeout;
    $this->method = $method;
  }

  public function getContent() {
    return $this->content;
  }

  public function getResponseCode() {
    return $this->responseCode;
  }

  public function getSuccessState() {
    if ($this->responseCode < 400) {
      return true;
    }
    return false;
  }

  public function initialize() {
    $this->content = null;
    $this->responseCode = 600;
  }

  public function postData($url, $contentType, $request) {
    $this->initialize();

    $headers = array();
    $headers[] = 'Content-Type: ' . $contentType;

    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    # curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    if ($this->timeout) {
      curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $result = curl_exec($ch);
    curl_close($ch);

    $this->content = $result;

  }

}

?>
