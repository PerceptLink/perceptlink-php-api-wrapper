<?php
namespace perceptlink;

require 'ApiHttpURLFetcher.php';

class ApiRequestMaker {

  private $aso;
  private $apiUrl;
  private $apiKey;
  private $defaultUserAgent = 'PerceptLink PHP Wrapper 1.0';
  private $defaultTimeout = 5000;
  private $contentType = 'application/json';

  public function __construct($aso, $apiUrl, $apiKey) {
    $this->aso = $aso;
    $this->apiUrl = $apiUrl;
    $this->apiKey = $apiKey;
  }

  public function fetchRecommendation($req) {
    $request = $this->buildSingletonRequest('fetch_recommendation', $req);
    return $this->doFetch($request);
  }

  public function fetchRecommendations() {
    $request = $this->buildRequest('fetch_recommendations');
    return $this->doFetch($request);
  }

  public function fetchAllocation($req) {
    $request = $this->buildSingletonRequest('fetch_allocation', $req);
    return $this->doFetch($request);
  }

  public function fetchAllocations() {
    $request = $this->buildRequest('fetch_allocations');
    return $this->doFetch($request);
  }

  public function fetchLastEngagementRecordSubmitted() {
    $request = $this->buildRequest('last_engagement_record_submitted');
    return $this->doFetch($request);
  }

  public function postData(array $data) {
    $request = $this->buildPostDataRequest('post_event_data', $data);
    return $this->doFetch($request);
  }

  public function doFetch($request) {
    $this->aso->setRawHTTPRequest($request);
    $fetcher = new ApiHttpURLFetcher($this->defaultUserAgent, $this->defaultTimeout, 'POST');
    $fetcher->postData($this->apiUrl, $this->contentType, $request);
    $res = $fetcher->getContent();
    $this->aso->setRawHTTPResponse($res);
    return $res;
  }

  public function buildRequest($request_type) {
    $header = array();
    $header['api_key'] = $this->apiKey;
    $header['type'] = $request_type;
    return json_encode($header);
  }

  public function buildSingletonRequest($request_type, $criteria) {
    $header = array();
    $header['api_key'] = $this->apiKey;
    $header['type'] = $request_type;
    $header['criteria'] = $criteria;
    return json_encode($header);
  }

  public function buildPostDataRequest($request_type, $data) {
    $header = array();
    $header['api_key'] = $this->apiKey;
    $header['type'] = $request_type;
    $header['data'] = $data;
    return json_encode($header);
  }

}

?>
