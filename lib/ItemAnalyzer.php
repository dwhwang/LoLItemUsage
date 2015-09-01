<?php
class ItemAnalyzer{

  private static $_create_log =
'CREATE TABLE IF NOT EXISTS log (
  region TEXT,
  match INTEGER,
  version TEXT,
  queueType TEXT,
  PRIMARY KEY (region, match)
)';

  private static $_create_itemdata =
'CREATE TABLE IF NOT EXISTS itemdata (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  item INTEGER,
  champion INTEGER,
  buytime INTEGER,
  map INTEGER,
  win INTEGER,
  version TEXT,
  queueType TEXT
)';

  private static $_newlog = 'INSERT OR IGNORE INTO log (region, match, version, queueType) VALUES (?, ?, ?, ?)';
  private static $_checklog = 'SELECT count(*) AS count FROM log WHERE region = ? AND match = ?';
  private static $_countlog = 'SELECT version, queueType, count(*) AS count FROM log GROUP BY version, queueType';

  private static $_newdata = 'INSERT OR IGNORE INTO itemdata (item, champion, buytime, map, win, version, queueType) VALUES (?, ?, ?, ?, ?, ?, ?)';

  //TODO: select query
  private static $_get0data  = 'SELECT item, win, version, queueType, count(*) as count FROM itemdata WHERE buytime < 600000 GROUP BY version, queueType, item, win';
  private static $_get10data = 'SELECT item, win, version, queueType, count(*) as count FROM itemdata WHERE buytime >= 600000 AND buytime < 1200000 GROUP BY version, queueType, item, win';
  private static $_get20data = 'SELECT item, win, version, queueType, count(*) as count FROM itemdata WHERE buytime >= 1200000 AND buytime < 1800000 GROUP BY version, queueType, item, win';
  private static $_get30data = 'SELECT item, win, version, queueType, count(*) as count FROM itemdata WHERE buytime >= 1800000 GROUP BY version, queueType, item, win';
  //TODO: champion data query

  private static $_lock = 'BEGIN IMMEDIATE';
  private static $_unlock = 'COMMIT';

  private $dbo = null;
  private $stmt_log = null;
  private $stmt_check = null;
  private $stmt_newdata = null;
  private $stmt_getdata = null;

  public function __construct($db){
    if(!$db){
      $db_file = './data.db';
    }else{
      $db_file = $db;
    }
    $this->dbo = new SQLite3($db_file);
    $this->dbo->exec($this::$_create_log);
    $this->dbo->exec($this::$_create_itemdata);
  }

  public function __destruct(){
    $this->dbo->close();
  }

  public function log($region, $matchid, $version, $queueType){
    if($this->stmt_log === null) {
      $this->stmt_log = $this->dbo->prepare($this::$_newlog);
    }else{
      $this->stmt_log->clear();
    }
    $this->stmt_log->bindValue(1, $region);
    $this->stmt_log->bindValue(2, $matchid);
    $this->stmt_log->bindValue(3, $version);
    $this->stmt_log->bindValue(4, $queueType);
    $this->stmt_log->execute();
  }

  public function check($region, $matchid){
    if($this->stmt_check === null) {
      $this->stmt_check = $this->dbo->prepare($this::$_checklog);
    }else{
      $this->stmt_check->clear();
    }
    $this->stmt_check->bindValue(1, $region);
    $this->stmt_check->bindValue(2, $matchid);
    $result = $this->stmt_check->execute();
    $row = $result->fetchArray();
    if ($row['count'] > 0){
      return true;
    }else{
      return false;
    }
  }

  public function insertdata($item, $champion, $buytime, $map, $win, $version, $queueType){
    if($this->stmt_newdata === null) {
      $this->stmt_newdata = $this->dbo->prepare($this::$_newdata);
    }else{
      $this->stmt_newdata->clear();
    }
    $this->stmt_newdata->bindValue(1, $item);
    $this->stmt_newdata->bindValue(2, $champion);
    $this->stmt_newdata->bindValue(3, $buytime);
    $this->stmt_newdata->bindValue(4, $map);
    $this->stmt_newdata->bindValue(5, $win);
    $this->stmt_newdata->bindValue(6, $version);
    $this->stmt_newdata->bindValue(7, $queueType);
    $this->stmt_newdata->execute();
  }

  public function begin(){
    $this->dbo->exec($this::$_lock);
  }

  public function commit(){
    $this->dbo->exec($this::$_unlock);
  }

  public function getdata(){
    $out = [];
    $out['total'] = [];
    $out['data'] = [];
    $out['total']['5.11'] = [];
    $out['total']['5.14'] = [];
    $result = $this->dbo->query($this::$_countlog);
    while($row = $result->fetchArray()){
      if(!isset($out['total'][$row['version']][$row['queueType']])) $out['total'][$row['version']][$row['queueType']] = [];
      $out['total'][$row['version']][$row['queueType']]['games'] = $row['count'];
      $out['total'][$row['version']][$row['queueType']]['zeroToTen'] = 0;
      $out['total'][$row['version']][$row['queueType']]['tenToTwenty'] = 0;
      $out['total'][$row['version']][$row['queueType']]['twentyToThirty'] = 0;
      $out['total'][$row['version']][$row['queueType']]['thirtyToEnd'] = 0;
    }

    $result = $this->dbo->query($this::$_get0data);
    while($row = $result->fetchArray()){
      if(!isset($out['data'][$row['item']])) $out['data'][$row['item']] = [];
      if(!isset($out['data'][$row['item']][$row['version']])) $out['data'][$row['item']][$row['version']] = [];
      if(!isset($out['data'][$row['item']][$row['version']][$row['queueType']])) {
        $out['data'][$row['item']][$row['version']][$row['queueType']] = [];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['zeroToTen'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['tenToTwenty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['twentyToThirty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['thirtyToEnd'] = [0, 0];
      }
      $out['data'][$row['item']][$row['version']][$row['queueType']]['zeroToTen'][$row['win']] = $row['count'];
      $out['data']['total'][$row['version']][$row['queueType']]['zeroToTen'] += $row['count'];
    }

    $result = $this->dbo->query($this::$_get10data);
    while($row = $result->fetchArray()){
      if(!isset($out['data'][$row['item']])) $out['data'][$row['item']] = [];
      if(!isset($out['data'][$row['item']][$row['version']])) $out['data'][$row['item']][$row['version']] = [];
      if(!isset($out['data'][$row['item']][$row['version']][$row['queueType']])) {
        $out['data'][$row['item']][$row['version']][$row['queueType']] = [];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['zeroToTen'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['tenToTwenty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['twentyToThirty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['thirtyToEnd'] = [0, 0];
      }
      $out['data'][$row['item']][$row['version']][$row['queueType']]['tenToTwenty'][$row['win']] = $row['count'];
      $out['data']['total'][$row['version']][$row['queueType']]['tenToTwenty'] += $row['count'];
    }

    $result = $this->dbo->query($this::$_get20data);
    while($row = $result->fetchArray()){
      if(!isset($out['data'][$row['item']])) $out['data'][$row['item']] = [];
      if(!isset($out['data'][$row['item']][$row['version']])) $out['data'][$row['item']][$row['version']] = [];
      if(!isset($out['data'][$row['item']][$row['version']][$row['queueType']])) {
        $out['data'][$row['item']][$row['version']][$row['queueType']] = [];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['zeroToTen'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['tenToTwenty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['twentyToThirty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['thirtyToEnd'] = [0, 0];
      }
      $out['data'][$row['item']][$row['version']][$row['queueType']]['twentyToThirty'][$row['win']] = $row['count'];
      $out['data']['total'][$row['version']][$row['queueType']]['twentyToThirty'] += $row['count'];
    }

    $result = $this->dbo->query($this::$_get30data);
    while($row = $result->fetchArray()){
      if(!isset($out['data'][$row['item']])) $out['data'][$row['item']] = [];
      if(!isset($out['data'][$row['item']][$row['version']])) $out['data'][$row['item']][$row['version']] = [];
      if(!isset($out['data'][$row['item']][$row['version']][$row['queueType']])) {
        $out['data'][$row['item']][$row['version']][$row['queueType']] = [];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['zeroToTen'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['tenToTwenty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['twentyToThirty'] = [0, 0];
        $out['data'][$row['item']][$row['version']][$row['queueType']]['thirtyToEnd'] = [0, 0];
      }
      $out['data'][$row['item']][$row['version']][$row['queueType']]['thirtyToEnd'][$row['win']] = $row['count'];
      $out['data']['total'][$row['version']][$row['queueType']]['thirtyToEnd'] += $row['count'];
    }

    return $out;
  }

}
?>
