<?php
namespace perceptlink;

require_once 'ApiDataPacketBuilder.php';
require_once 'ApiRequestMaker.php';
require_once 'ApiResponseReader.php';

class ApiSessionException extends \Exception {}

class ApiSession {

  private $batchSize = 1000;
  private $apiKey;
  private $apiPostURL;
  private $mostRecentApiSessionRecord = null;
  private $asrList = array();

  private $rawHTTPRequest = null;
  private $rawHTTPResponse = null;

  /**
   *
   */
  public function __construct($apiKey, $apiPostURL) {

    $this->apiKey = $apiKey;
    $this->apiPostURL = $apiPostURL;

    if (empty($apiKey)) {
      throw new ApiSessionException('Did not supply an API key'); 
    }
    if (empty($apiPostURL)) {
      throw new ApiSessionException('Did not supply an API URL'); 
    }

  }

  /**
   *
   */
  public function addEngagmentEvent(ApiSessionRecord $asr) {
    $this->asrList[] = $asr;
  }

  /**
   *
   */
  public function dispatchEngagementEvents() {

    if (count($this->asrList) < 1) {
      throw new ApiSessionException('No records to dispatch');
    }

    $pb = new ApiDataPacketBuilder();
    $dat = $pb->buildDataPacket($this->asrList);

    $listSize = count($this->asrList);
    $counter = 0;

    while ($counter < $listSize) {
      $endMarker = $counter + $this->batchSize;
      if ($endMarker > $listSize) {
        $endMarker = $listSize;
      }
      $batch = array_slice($dat, $counter, $endMarker);
      $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
      $result = $areq->postData($batch);
      if (ApiResponseReader::getResultCode($result) < 400) {
        $counter = $endMarker;
        $this->mostRecentApiSessionRecord = $batch[(count($batch) - 1)];
      } else {
        $rm = ApiResponseReader::getResultMessage($result);
        throw new ApiSessionException('Post failure with message: ' . rm . '; you can get most recent successfully sent record and try again');
      }
    }


  }

  /**
   *
   */
  public function fetchLastEngagementRecordSubmitted() {
    $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
    $res = $areq->fetchLastEngagementRecordSubmitted();
    $this->setRawHTTPResponse($res);
    $this->verifyFetchState($res);
    return json_decode($res);
  }

  /**
   *
   */
  public function getItemRecommendation($request) {
    $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
    $res = $areq->fetchRecommendation($request->output());
    $this->setRawHTTPResponse($res);
  }

  /**
   *
   */
  public function getItemRecommendations() {
    $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
    $res = $areq->fetchRecommendations();
    $this->setRawHTTPResponse($res);
    return ApiResponseReader::getDataElements($res);
  }

  /**
   *
   */
  public function getContentAllocation($request) {
    $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
    $res = $areq->fetchAllocation($request->output());
    $this->setRawHTTPResponse($res);
  }

  /**
   *
   */
  public function getContentAllocations() {
    $areq = new ApiRequestMaker($this, $this->apiPostURL, $this->apiKey);
    $res = $areq->fetchAllocations();
    $this->setRawHTTPResponse($res);
    return ApiResponseReader::getDataElements($res);
  }

  /**
   *
   */
  public function setRawHTTPRequest($value) {
    $this->rawHTTPRequest = $value;
  }

  /**
   *
   */
  public function getRawHTTPRequest() {
    return $this->rawHTTPRequest;
  }

  /**
   *
   */
  public function setRawHTTPResponse($value) {
    $this->rawHTTPResponse = $value;
  }

  /**
   *
   */
  public function getRawHTTPResponse() {
    return $this->rawHTTPResponse;
  }

  /**
   *
   */
  public function extractData() {
    $this->verifyFetchState($this->rawHTTPResponse);
    return ApiResponseReader::getDataElements($this->rawHTTPResponse);
  }

  /**
   *
   */
  public function verifyFetchState($response) {
    if (ApiResponseReader::getResultCode($response) > 200) {
      throw new ApiSessionException('Error on fetch:' . ApiResponseReader::getResultMessage($response));
    }
  }

}

?>
