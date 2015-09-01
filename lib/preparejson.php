<?php

require('./ItemAnalyzer.php');
$db = new ItemAnalyzer('../data.db');

$result = $db->getdata();

file_put_contents('./itemdata.json', json_encode($result));

?>
