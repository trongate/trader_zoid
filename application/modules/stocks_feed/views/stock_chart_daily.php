<h1><?php echo $headline; ?></h1>

<?php
$this->load->module('stocks_feed');
$this->load->module('timedate');
$total_rows = $query->num_rows();
$count = 0;
$data_string = ""; //start building a string containing all the data we need
foreach($query->result() as $row) {
	$count++;
	$candle_prices_data = $this->stocks_feed->get_prices_for_candle($row->id, 'daily');
	$opening_price = $candle_prices_data['opening_price'];
	$closing_price = $candle_prices_data['closing_price'];
	$lowest_price = $candle_prices_data['lowest_price'];
	$highest_price = $candle_prices_data['highest_price'];

	if ($lowest_price=="") {
		$lowest_price = $closing_price;
	}

	if ($highest_price=="") {
		$highest_price = $closing_price;
	}

	$this_time = $this->timedate->get_nice_date($row->date_added, 'hourmin');

	if ($count<$total_rows) {
		//add a comma
		$additional_code = ",";
	} else {
		$additional_code = "";
	}

	if ($count==1) {
		$data_string.= "['".$this_time."', $lowest_price, $opening_price, $closing_price, $highest_price]".$additional_code."
		";
	}

	if ($count==10) {
		$count = 0;
	}
}
?>


<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      <?php echo $data_string; ?>

      // Treat first row as data as well.
    ], true);

    var options = {
      legend:'none',
      color: 'black',
      backgroundColor: 'white',
      candlestick: {
            fallingColor: { strokeWidth: 0, fill: '#a52714' }, // red
            risingColor: { strokeWidth: 0, fill: '#0f9d58' }   // green
          }
    };

    var chart = new google.visualization.CandlestickChart(document.getElementById('chart_div'));

    chart.draw(data, options);
  }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 100%; height: 500px;"></div>
  </body>
</html>