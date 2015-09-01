<?php
/**
 * This is a background task and should not run in browser.
 **/
require('./config.php');
require('./SQLiteQueue.php');
require('./ItemAnalyzer.php');

$db = new ItemAnalyzer('../data.db');
$items = json_decode(file_get_contents('./apitem.json'), true);
// get json list
// parse json

$time_start = microtime(true);

while(true){
  //check for json to process
  $json = array_diff(scandir('../json'), array('..', '.', 'done'));
  if (!empty($json)) {
    echo "processing...\n";
    while(!empty($json)){
      $job = current($json);
      $jobkey = key($json);
      process($db, $job, $items);
      next($json);
      unset($job, $json[$jobkey]);
    }
  } else {
    // nothing left, sleep for some time
    break;
  }
}
$time_end = microtime(true);
$time = $time_end - $time_start;
printf("Done. Time used: %.3f seconds.\n", $time);

function process($db, $jsonfile, $items){
  $content = file_get_contents('../json/'.$jsonfile);
  $data = json_decode($content, true);
  if($data){
    $db->begin();

    $region = $data['region'];
    $match = $data['matchId'];
    $version = substr($data['matchVersion'], 0, 4);
    $map = $data['mapId'];
    $queueType = substr($data['queueType'], 0, 6);

    //mark match processed
    $db->log($region, $match, $version, $queueType);

    // prepare participantid -> championid array
    $player = [];
    foreach($data['participants'] as $p){
      $win = $p['stats']['winner'] ? 1 : 0;
      $player[$p['participantId']] = array(
        'championId'=>$p['championId'],
        'winner'=>$win
      );
    }

    // go through timeline for event
    foreach($data['timeline']['frames'] as $frame){
      if(isset($frame['events']))
      foreach($frame['events'] as $event){
        if($event['eventType'] == 'ITEM_PURCHASED'){
          //check if it is ap item
          if(in_array($event['itemId'], $items)){
            $db->insertdata(
              $event['itemId'],
              $player[$event['participantId']]['championId'],
              $event['timestamp'],
              $map,
              $player[$event['participantId']]['winner'],
              $version,
              $queueType
            );
          }
        }
      }
    }
    $db->commit();
  }else{ // bad file
    $message = "Decode error: $json_file \n";
    $log = './jsonlog.txt';
    echo $message;
    file_put_contents($log, $message, FILE_APPEND);
  }
  //TODO: remove file ?
  rename('../json/'.$jsonfile, '../json/done/'.$jsonfile);
}

?>
