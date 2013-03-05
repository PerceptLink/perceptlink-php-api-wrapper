<?php
namespace perceptlink;

class ApiBuilder {

  private $elements = array();
  private $charLimit = 128;

  public function builder($name, $value) {
    $this->elements[$name] = $this->__preprocessObject($value);
  }

  public function output() {
    return $this->elements;
  }

  private function __preprocessObject($obj) {
    if (is_string($obj)) {
      $obj = substr($obj, 0, $this->charLimit);
    }
    return $obj;
  }

}

?>
