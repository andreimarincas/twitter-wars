Twitter Wars
============

Please refer to <https://gist.github.com/991055> Task 1.
<br /><br />

##Installation

<br />
**Environment:** Mac OS X (v10.7.5) 
<br />
**Development:** NetBeans IDE 7.2 
<br />
**Server:** MAMP (Mac Apache 
MySQL PHP) Version 2.1.1
<br />
####PHP
Version: 5.4.4 <br />
Apache Port: 8888

####MySQL
**Socket:** /Applications/MAMP/tmp/mysql/mysql.sock <br />
Edit /etc/php.ini to set this socket to mysql.default_socket <br />
**MySQL Port:** 8889

**Apache document root:** /Applications/MAMP/htdocs <br /><br />


##Configuration

<br />
Download this as *zip* and extract *TwitterWars* project folder into Apache’s document root folder. <br />
Then open the Terminal to run some scripts (see *Running* section), and finaly open a browser (I used Mozilla Firefox 15.0.1) to view some results.
<br />

###Database
<br />
Open *phpMyAdmin* in your browser (MAMP *Open start page* or type *http://localhost:8888/MAMP/?language=English*, then select *phpMyAdmin* tab) and import *twitter_wars.sql*. Then run this to create a database with the tables *london_tweets* and *exeter_tweets*. They contain Twitter data for the last week.<br /><br />
Create a user and grant him full priviledges to twitter_wars database. In my case the SQL connection is established through the following defines in config.php:<br /><br />

*define('DB_HOST', 'localhost');<br />
define('DB_USER', 'andrei');<br />
define('DB_PASS', 'wdtTxjd8wczPewfS');<br />
define('DB_NAME', 'twitter_wars');<br />*

<br />

##Running
<br />
First of all, start Apache Server and MySQL server in MAMP.

###Fetching Twitter Data
<br />
To connect to and consume the Twitter stream via the Streaming API: <br /><br />
*$ cd /Applications/MAMP/htdocs/TwitterWars <br />
$ php twitter_fetcher.php <br />*
<br />
This will add real-time statuses from London and Exeter areas into the database. <br />
To disable printing the statuses to the console pass “h” as the 1st argument:<br /><br />
*$ php twitter_fetcher.php h*

<br />
###Retrieving older statuses

<br />
To get twitter statuses for the last week:<br /><br />
*$ php twitter_search.php [place]* <br /><br />
where [place] is (optionally) either “London” or “Exeter”. If not specified then the statuses from both places are retrieved. Example:<br /><br />
*$ php twitter_search.php London*<br /><br />
This will retrieve 1000 statuses per day (that are not already in database) for the last 7 days. You can change this by altering DAYS_COUNT and TWEETS_PER_DAY constant members of the TwitterSearch class. <br /><br />

###Spellchecking
<br />
This work is done by *dbspell.php* for statuses already fetched and added to the database. From Terminal execute:<br /><br />
*$ php dbspell.php [since] [until] [place]*<br /><br />
**[since]** Analyze statuses after this “Y-m-d” date.<br />
**[until]** Analyze statuses no older than this “Y-m-d” date.<br />
**[place]** London or Exeter. or all statuses in the given interval, from Exeter.<br /><br />
Example:<br /><br />
*$ php dbspell.php 2012-09-16 2012-09-20 Exeter*<br /><br />
This will check the spelling for all statuses in the given interval, from Exeter.<br />
Note: Only statuses that have not been analyzed yet will be considered.
<br /><br />
###Compare Spelling

<br />
In order to compare the spelling quality of statuses from London and Exeter, I generated some statistics in a graph representation, over several days. To obtain this, enter the following URL in your browser:<br /><br />
*http://localhost:8888/TwitterWars/index.php*<br /><br />
This will output the spelling correctness for the tweets over the last week, for those that are spellchecked.<br />
You can also specify the number of days for which to generate the report, before a given date. Example:<br /><br />
*http://localhost:8888/TwitterWars/index.php?days=5&until=2012-09-19*<br /><br />
will output this: <br />

![twitter_spelling.png](https://github.com/andreimarincas/twitter-wars/blob/master/TwitterWars/images/twitter_spelling.png)
<br />
![twitter_count.png](https://github.com/andreimarincas/twitter-wars/blob/master/TwitterWars/images/twitter_count.png)
