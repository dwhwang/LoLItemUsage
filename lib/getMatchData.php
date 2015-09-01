<?php
require('./config.php');
require('./SQLiteQueue.php');

$region = isset($argv[1]) ? $argv[1] : null;
$dataset = isset($argv[2]) ? $argv[2] : null;

if ($region !== null && $dataset !== null) {
  //check dataset exsist
  if(file_exists('./'.$dataset)){
    // read dataset into array
    $content = file_get_contents('./'.$dataset);
    $matches = json_decode($content);
    // add data set to worker queue
    $db = new SQLiteQueue('../queue.db');
    $db->push($region, $matches);
    echo 'Adding '.count($matches)." matches into queue.\n";
  }else{
    echo "DATA SET NOT FOUND\n";
  }
}else{
  echo "MISSING PARAMETER\n";
}
?>
