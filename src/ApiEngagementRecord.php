<?php
namespace perceptlink;

require_once 'ApiBuilder.php';

class ApiEngagementRecord {

  private $transactionDate;

  private $context, $identity, $features;

  private $itemset = array();

  private $engagementType = null;
  private $engagementWeight = null;

  private $charLimit = 128;

  /**
   *
   */
  public function __construct($date) {

    $this->transactionDate = $date;

    $this->context  = new ApiBuilder();
    $this->identity = new ApiBuilder();
    $this->features = new ApiBuilder();

  }

  /**
   *
   */
  public function identityBuilder($name, $value) {
    $this->identity->builder($name, $value);
  }

  /**
   *
   */
  public function contextBuilder($name, $value) {
    $this->context->builder($name, $value);
  }

  /**
   *
   */
  public function featureBuilder($name, $value) {
    $this->features->builder($name, $value);
  }

  /**
   *
   */
  public function itemsetBuilder(ApiItemRecord $item) {
    $this->itemset[] = $item;
  }

  /**
   *
   */
  public function setEngagement($engagementType, $engagementWeight) {
    if ($engagementWeight < 0) {
      $engagementWeight = 0.0;
    }
    if (strlen($engagementType) > $this->charLimit) {
      $engagmentType = substr($engagementType, 0, $this->charLimit);
    }
    $this->engagementType = $engagementType;
    $this->engagementWeight = $engagementWeight;
  }

  /**
   *
   */
  public function getContext() {
    return $this->context->output();
  }

  /**
   *
   */
  public function getIdentity() {
    return $this->identity->output();
  }

  /**
   *
   */
  public function getFeatures() {
    return $this->features->output();
  }

  /**
   *
   */
  public function getItemset() {
    return $this->itemset;
  }

  /**
   *
   */
  public function getDate() {
    return $this->transactionDate;
  }

  /**
   *
   */
  public function getEngagementType() {
    return $this->engagementType;
  }

  /**
   *
   */
  public function getEngagementWeight() {
    return $this->engagementWeight;
  }

}

?>
