# LoL Item Usage Analysis
work in progresss

#### Requirements
- php
  - curl
  - SQLite 3
This is a set of PHP CLI program to get data ready.

#### file description

| File                  | descriptions |
| --------------------- | ------------ |
| lib/getMatchData.php  | parse and add dataset into queue |
| lib/worker.php        | background task to get match data |
| lib/analyzer.php      | extract data from raw json and store into database |
| lib/ItemAnalyzer.php  | handling SQLite 3 database for main data |
| lib/preparejson.php   | prepare data for website |
| lib/SQLiteQueue.php   | handling queue using SQLite 3 |
| lib/config.php        | configuartion file. Your API key goes here. |


#### Attribution

LoL Item Usage Analysis isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
