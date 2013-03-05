<?php
namespace perceptlink;

class ApiDataPacketBuilder {

  public function buildDataPacket($recs) {

    $dataMap = array();

    foreach ($recs as $recKey => $rec) {

      $newRec = array();
      $chrono = array();
      $engagement = array();

      $chrono['occurred'] = $rec->getDate();
      $engagement['type'] = $rec->getEngagementType(); 
      $engagement['weight'] = $rec->getEngagementWeight();

      $newRec['chrono'] = $chrono;
      $newRec['engagement'] = $engagement;
      $newRec['identity'] = $rec->getIdentity();
      $newRec['context'] = $rec->getContext();
      $newRec['features'] = $rec->getFeatures();
      $newRec['itemset'] = array();

      $itemset = array();

      foreach ($rec->getItemset() as $itemKey => $item) {

        $itemInfo = array();
        $itemInfo['item_id'] = $item->getItemId();
        $itemInfo['features'] = $item->output();
        
        $itemset[] = $itemInfo;

      }

      $newRec['itemset'] = $itemset;

      $dataMap[] = $newRec;
      
    }

    return $dataMap;

  }

}

?>
