<?php
namespace perceptlink;

class ApiResponseReader {

  static public function getResultCode($json) {
    $res = self::parseResultMessage($json, 'result', 'code');
    if (empty($res)) {
      return 600;
    }
    return intval($res);
  }

  static public function getResultMessage($json) {
    return self::parseResultMessage($json, 'result', 'message');
  }

  static public function getDataElements($json) {
    $data = self::parseResultMessage($json, 'data', 'list');
    if (empty($data)) {
      return array();
    }
    return $data;
  }

  static public function parseResultMessage($json, $top, $name) {
    $vals = json_decode($json, true);;
    if (is_array($vals)) {
      if (array_key_exists($top, $vals)) {
        if (array_key_exists($name, $vals[$top])) {
          return $vals[$top][$name];
        }
      }
    }
    return null;
  }

}

?>
