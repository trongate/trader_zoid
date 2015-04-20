<html>
<head></head>	
<body>

<p>Click on a stock symbol below to view a chart.</p>

<?php
$this->load->module('chart_analyser');
$total_stocks = count($stocks);
$half_amount = $total_stocks/2;
$count = 0;

echo "<table border='0' cellpadding='7'>";
echo "<tr><td valign='top'>";

foreach($stocks as $symbol) {
	$count++;

/*
	//some code to make sure the symbols are being added to the database
	$this->load->module('stocks_feed');
	$this_symbol = str_replace('NYSE:', '', $symbol);
	$mysql_query = "select * from stocks_feed where stock_symbol = '$this_symbol'";
	$query = $this->stocks_feed->_custom_query($mysql_query);
	$num_rows = $query->num_rows();

	if ($num_rows<1) {
		$the_colour = "red";
	} else {
		$the_colour = "black";
	}

	echo "<h2 style='color: ".$the_colour."'>$mysql_query returns $num_rows rows</h2>";
*/

	$url = base_url()."stocks_feed/view_chart/".$symbol;
	$percent_move = $this->chart_analyser->get_percent_move_today($symbol);

	if (($percent_move>90) || ($percent_move<-90)) {
		$percent_move = 0;
	}

	if ($percent_move>0) {
		$info = "higher";
		$bg_colour = "lime";
	}

	if ($percent_move<0) {
		$info = "lower";
		$bg_colour = "#FF3300";
	}

	if ($percent_move==0) {
		$info = " (unchanged)";
		$bg_colour = "white";
	}

	$percent_move = number_format($percent_move, 2);
	$percent_change_info = "<span style='color: black;'>".$percent_move."% ".$info."</span>";
	echo "<li style='font-weight: bold; border: 1px black solid; padding: 7px; width: 270px; background-color: ".$bg_colour."; margin-bottom: 7px;'>$count <a href='".$url."'>".$symbol."</a> ".$percent_change_info."</li>";
	if ($count>=$half_amount) {
		echo "</td><td valign='top'>";
		$count = 0;
	}

}
?>

</td></tr></table>

<div id="container"></div>
<script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
    var currentUserId = 1;
    var userHasMessage = false;
    // create a new websocket
    var socket = io.connect('http://localhost:8000');
</script>
</body>
</html>