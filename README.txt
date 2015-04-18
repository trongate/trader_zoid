Congratulations Mad Skull.  You've just downloaded Trader Zoid :
 the super cool day trading app for and by members of the Insider Club
 - www.insiderclub.org.

 The thing is NOWHERE NEAR finished yet, but here's some info to get you 
 started and up to speed with where we are:

 STEP 1:  create a mysql database called 'trader_zoid'

 STEP 2:  run the following SQL code:

CREATE TABLE IF NOT EXISTS `stocks_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_symbol` varchar(12) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `last_trade` varchar(75) NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

(sample data for this table can be found in the _junk folder)


STEP 3:  (assuming you're running XAMPP, WAMP or some similar thing)
Go to http://localhost/trader_zoid/stock_reader/get_data

STEP 4:  Swoon with delight over how the thing has just read from the
thing and chucked it into the database.  Yeeeehaaa!

NOTE #1:  You can view a rubbish candlestick daily chart of the stock at 
http://localhost/trader_zoid/stocks_feed/view_chart/MCD (where the 4th 
segment is a stock symbol).   THIS TOTALLY NEEDS WORK!!!

NOTE #2:  You can get a list of all the stocks we're checking in stock_reader.php

NOTE #3:  There's a YouTube video showing where we are here: https://www.youtube.com/watch?v=spxOoerPqA4


