<?php
namespace perceptlink;

require_once 'ApiBuilder.php';

class ApiItemRecord {

  private $itemId = null;
  private $itemFeatures;

  /**
   *
   */
  public function __construct($itemId) {
    $this->itemId = $itemId;
    $this->itemFeatures = new ApiBuilder();
  }

  /**
   *
   */
  public function builder($name, $value) {
    $this->itemFeatures->builder($name, $value);
  }

  /**
   *
   */
  public function getItemId() {
    return $this->itemId;
  }

  /**
   *
   */
  public function output() {
    return $this->itemFeatures->output();
  }

}

?>
