# LoL Item Usage Analysis

You can see the demo [here](http://dwhwang.github.io/LoLItemUsageAnalysis/demo.html).

This project is intended for Riot's API Challenge 2.0. 
I couln't finished my whole idea on time, but at least I can show some part of my ideas in the demo.

A few things not done yet:

1. worker.php should seperate file writing action to another thread. It's holding the speed down.
2. Planning the query about item vs champion vs buying time vs patch. The data is there, but the problem is how to present it.
3. That front end in the demo page still needs a lot of work...
4. Collect more data. The demo only shows data from NA.
5. Comment & documentation. 
6. Not going to use php as back end if it ends up as a CLI program next time... I was trying to make it a web service but run into a lot of issues.

#### Requirements
- php
  - curl extention
  - SQLite 3 extention

This is a set of PHP CLI program to get data ready, so it should only be run in command line.

#### file description

Front end

| File                  | descriptions |
| --------------------- | ------------ |
| site/demo.html             | demo page to show off collected data |
| site/app.js                | demo page javascript |
| site/json/itemdata.json    | prepared data from preparejson.php, this is the main data |
| site/json/item511.json     | cached 5.11.1 item static json for convience |
| site/json/item514.json     | cached 5.14.1 item static json for convience |

Back end

| File                  | descriptions |
| --------------------- | ------------ |
| lib/getMatchData.php  | parse and add dataset into queue |
| lib/worker.php        | background task to get match data |
| lib/analyzer.php      | extract data from raw json and store into database |
| lib/ItemAnalyzer.php  | handling SQLite 3 database for main data |
| lib/preparejson.php   | prepare data for front end website |
| lib/SQLiteQueue.php   | handling queue using SQLite 3 |
| lib/config.php        | configuartion file. Your API key goes here. |

The back end process flow should be like this:

1. getMatchData.php - get dataset into queue
2. worker.php - call api to get raw json
3. analyzer.php - extract data from raw json into database
4. preparejson.php - get data from database and put into json for front end use

#### Attribution

LoL Item Usage Analysis isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.
