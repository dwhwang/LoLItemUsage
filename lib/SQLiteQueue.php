<?php

class SQLiteQueue {
  // sql statement
  private static $_create =
'CREATE TABLE IF NOT EXISTS queue (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  region TEXT,
  match INTEGER
)';
  private static $_unique = 'CREATE UNIQUE INDEX IF NOT EXISTS uq_locmatches ON queue (region, match)';
  private static $_count = 'SELECT COUNT(*) FROM queue';
  private static $_push = 'INSERT OR IGNORE INTO queue (region, match) VALUES (?, ?)';
  private static $_pop_get = 'SELECT id, region, match FROM queue ORDER BY id LIMIT 1';
  private static $_pop_del = 'DELETE FROM queue WHERE id = ?';
  private static $_lock = 'BEGIN';
  private static $_unlock = 'COMMIT';

  // class variable
  private $db_file = null;
  private $dbo = null;
  private $stmt_push = null;
  private $stmt_del = null;

  public function __construct($db){
    if(!$db){
      $this->db_file = './queue.db';
    }else{
      $this->db_file = $db;
    }
    $this->dbo = new SQLite3($this->db_file);
    $this->dbo->exec($this::$_create);
    $this->dbo->exec($this::$_unique);
  }

  public function __destruct(){
    $this->dbo->close();
  }

  public function push($region, $match){
    if($this->stmt_push === null) {
      $this->stmt_push = $this->dbo->prepare($this::$_push);
    }else{
      $this->stmt_push->clear();
    }
    $this->stmt_push->bindValue(1, $region);

    if(is_array($match)){
      foreach($match as $m){
        $this->stmt_push->bindValue(2, $m);
        $this->stmt_push->execute();
      }
    }else{
      $this->stmt_push->bindValue(2, $match);
      $this->stmt_push->execute();
    }
  }

  public function pop(){
    $this->dbo->exec($this::$_lock);
    $result = $this->dbo->querySingle($this::$_pop_get, true);
    if(empty($result)) {
      $result = null;
    }else{
      if($this->stmt_del === null){
        $this->stmt_del = $this->dbo->prepare($this::$_pop_del);
      }else{
        $this->stmt_del->clear();
      }
      $this->stmt_del->bindValue(1, $result['id']);
      $this->stmt_del->execute();
    }
    $this->dbo->exec($this::$_unlock);
    return $result;
  }

  public function peek(){
    $result = $this->dbo->querySingle($this::$_pop_get, true);
    return $result;
  }

  public function count(){
    $result = $this->dbo->querySingle($this::$_count);
    return $result;
  }
}

?>
