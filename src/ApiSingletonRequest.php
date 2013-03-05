<?php
namespace perceptlink;

class ApiSingletonRequest {

  private $data = array();

  public function builder($name, $value) {
    $this->data[$name] = $value;
  }

  public function output() {
    return $this->data;
  }

}

?>
