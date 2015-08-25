<?php
require('./config.php');
require('./SQLiteQueue.php');

$region = isset($_REQUEST['region']) ? $_REQUEST['region'] : null;
$dataset = isset($_REQUEST['dataset']) ? $_REQUEST['dataset'] : null;

if ($region !== null && $dataset !== null) {
  //check dataset exsist
  if(file_exists('../dataset/'.$dataset)){
    header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
    // read dataset into array
    $content = file_get_contents('../dataset/'.$dataset);
    $matches = json_decode($content);
    // add data set to worker queue
    $db = new SQLiteQueue('../queue.db');
    $db->push($region, $matches);
    echo 'Adding '.count($matches).' matches into queue.';
  }else{
    header($_SERVER["SERVER_PROTOCOL"]." 404 DATA SET NOT FOUND");
  }
}else{
  header($_SERVER["SERVER_PROTOCOL"]." 400 MISSING PARAMETER");
}
?>
