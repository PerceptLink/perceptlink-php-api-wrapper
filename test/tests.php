<?php
require_once 'PHPUnit/Framework.php';
 
class WrapperTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
  }

  protected function tearDown() {
  }
  
  public function testBuilderCharLimitation() {
    require_once 'src/ApiBuilder.php';
    $limit = 128;
    $testString = '';
    for ($i = 0; $i < 300; $i++) {
      $testString .= 'a';
    }
    $ab = new perceptlink\ApiBuilder();
    $ab->builder('name', $testString);
    $output = $ab->output();
    $length = strlen($output['name']);
    $this->assertEquals($limit, $length);
  }

  public function testApiDataPacketBuilderAccuracy() {

    require_once 'src/ApiDataPacketBuilder.php';
    $adp = new perceptlink\ApiDataPacketBuilder();
    $this->assertEquals(array(), $adp->buildDataPacket(array())); 

    require_once 'src/ApiEngagementRecord.php';
    $aer_list = array();

    $aer = new perceptlink\ApiEngagementRecord('12-31-2012 12:59:59');
    $aer->contextBuilder('group', 'test1');
    $aer->featureBuilder('feat1', 'tfeat1');
    $aer->identityBuilder('ident1', 'tident1');
    $aer_list[] = $aer;

    $aer = new perceptlink\ApiEngagementRecord('12-31-20123 12:59:59');
    $aer->contextBuilder('group', 'test2');
    $aer->featureBuilder('feat1', 'tfeat2');
    $aer->identityBuilder('ident1', 'tident2');
    # $aer_list[] = $aer;

    require_once 'src/ApiItemRecord.php';

    $item = new perceptlink\ApiItemRecord('item100');
    $item->builder('if1', 'if1_value');

    $aer->itemsetBuilder($item);
    $aer_list[] = $aer;

    $recs = $adp->buildDataPacket($aer_list);

    $this->assertEquals(count($aer_list), count($recs));
    $this->assertEquals('test1', $recs[0]['context']['group']);
    $this->assertEquals('test2', $recs[1]['context']['group']);

    $this->assertEquals('tfeat2', $recs[1]['features']['feat1']);
    $this->assertEquals('tident1', $recs[0]['identity']['ident1']);

    $this->assertEquals(0, count($recs[0]['itemset']));
    $this->assertEquals(1, count($recs[1]['itemset']));

    $this->assertEquals('if1_value', $recs[1]['itemset'][0]['features']['if1']);
    $this->assertEquals('item100', $recs[1]['itemset'][0]['item_id']);

  }

  public function testApiEngagementRecordAccuracy() {

    require_once 'src/ApiEngagementRecord.php';

    $aer = new perceptlink\ApiEngagementRecord('12-31-2012 12:59:59');
    $aer->contextBuilder('group', 'test1');
    $aer->featureBuilder('feat1', 'tfeat1');
    $aer->identityBuilder('ident1', 'tident1');
  
    $aer->setEngagement('buy', 5.0);
    $this->assertEquals(5.0, $aer->getEngagementWeight());
    $this->assertEquals('buy', $aer->getEngagementType());

    $context = $aer->getContext();
    $features = $aer->getFeatures();
    $identity = $aer->getIdentity();
    $this->assertEquals('test1', $context['group']);
    $this->assertEquals('tfeat1', $features['feat1']);
    $this->assertEquals('tident1', $identity['ident1']);

    require_once 'src/ApiItemRecord.php';

    $item = new perceptlink\ApiItemRecord('item100');
    $item->builder('if1', 'if1_value');
    $aer->itemsetBuilder($item);

    $item = new perceptlink\ApiItemRecord('item200');
    $item->builder('if2', 'if2_value');
    $aer->itemsetBuilder($item);

    $this->assertEquals(2, count($aer->getItemset()));

    $items = $aer->getItemset();
    
    $this->assertEquals('item100', $items[0]->getItemId());
    $this->assertEquals('item200', $items[1]->getItemId());
  }
  
  public function testApiItemRecordAccuracy() {

    require_once 'src/ApiItemRecord.php';

    $item = new perceptlink\ApiItemRecord('item100');
    $item->builder('if1', 'if1_value');
    $item->builder('if2', 'if2_value');

    $this->assertEquals('item100', $item->getItemId());
    $output = $item->output();
    $this->assertEquals('if1_value', $output['if1']);
    $this->assertEquals('if2_value', $output['if2']);

  }

  public function testJSONAccuracy() {

    require_once 'src/ApiResponseReader.php';

    $json_dict = array();
    $json_dict['result'] = array(
      'code' => 200,
      'message' => 'test_message',
    );

    $json_string = json_encode($json_dict, false);

    $rc = perceptlink\ApiResponseReader::getResultCode($json_string);
    $this->assertEquals(200, $rc);

    $rm = perceptlink\ApiResponseReader::getResultMessage($json_string);
    $this->assertEquals('test_message', $rm);

    $data = array();
    $data['data'] = array();
    $lst = array();

    $element1['item_id'] = 'item100';
    $element1['n1'] = '1';
    $element1['n2'] = '2';
    $element1['n3'] = '3';
    
    $element2['item_id'] = 'item200';
    $element2['n1'] = '1';
    $element2['n2'] = '2';
    $element2['n3'] = '3';

    $lst[] = $element1;
    $lst[] = $element2;

    $data['data']['list'] = $lst;

    $json_data = json_encode($data);

    $recs = perceptlink\ApiResponseReader::getDataElements($json_data);
    $this->assertEquals('1', $recs[0]['n1']);
  }

  public function testFetchRecommendations() {

    require_once 'src/ApiSession.php';
    $apiKey = 'aaaaa';
    $url = 'https://api.perceptlink.com/api/1/test/ok_fetch_recommendations';

    $aso = new perceptlink\ApiSession($apiKey, $url);
    $aso->getItemRecommendations();
    $recs = $aso->extractData();
    $this->assertEquals(3, count($recs));
    $this->assertEquals(200, $recs[1]['item_id']);
    $this->assertEquals('Z', $recs[2]['recommendations'][1]);
    $this->assertEquals('A', $recs[0]['recommendations'][0]);

  }

  public function testFetchRecommendation() {

    require_once 'src/ApiSession.php';
    require_once 'src/ApiSingletonRequest.php';

    $apiKey = 'aaaaa';
    $url = 'https://api.perceptlink.com/api/1/test/ok_fetch_recommendation';

    $singleton = new perceptlink\ApiSingletonRequest();
    $singleton->builder('item_id', 150);

    $aso = new perceptlink\ApiSession($apiKey, $url);
    $aso->getItemRecommendation($singleton);
    $data = $aso->extractData();

    $this->assertEquals(150, $data[0]['item_id']);

  }

  public function testFetchAllocations() {

    require_once 'src/ApiSession.php';
    $apiKey = 'apiKey';
    $url = 'https://api.perceptlink.com/api/1/test/ok_fetch_allocations';

    $aso = new perceptlink\ApiSession($apiKey, $url);
    $aso->getContentAllocations();
    $data = $aso->extractData();
    $this->assertEquals('sg', $data[0]['group']);
    $this->assertEquals(0.46, $data[2]['allocation']);
  }

  public function testFetchAllocation() {

    require_once 'src/ApiSession.php';
    require_once 'src/ApiSingletonRequest.php';

    $apiKey = 'aaaaa';
    $url = 'https://api.perceptlink.com/api/1/test/ok_fetch_allocation';

    $singleton = new perceptlink\ApiSingletonRequest();
    $singleton->builder('group', 'sg');

    $aso = new perceptlink\ApiSession($apiKey, $url);
    $aso->getContentAllocation($singleton);
    $data = $aso->extractData();

    $this->assertEquals('sg', $data[0]['group']);
  }

  public function testFetchLastRecordSubmitted() {

    require_once 'src/ApiSession.php';

    $apiKey = 'aaaaa';
    $url = 'https://api.perceptlink.com/api/1/test/ok_last_record_submitted';

    $aso = new perceptlink\ApiSession($apiKey, $url);
    $lRecord = $aso->fetchLastEngagementRecordSubmitted();
    $data = $aso->extractData();
    $context = $data[0]['context'];

    $this->assertEquals($context['group'], 'sg');

  }

}
?>
