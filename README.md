# LoLItemUsage
work in progresss

#### Requirements
- php
  - curl
  - SQLite 3

#### file description

| File                  | descriptions |
| --------------------- | ------------ |
| getdataset.php        | simple interface for getMatchData.php |
| lib/getMatchData.php  | parse and add dataset into queue |
| lib/.worker.php       | background task to get match data |
| lib/SQLiteQueue.php   | handling queue using SQLite 3 |
| lib/config.php        | configuartion file. Your API key goes here. |


#### Attribution

LoLItemUsage isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
