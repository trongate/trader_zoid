<h1>Chart for <?php echo $stock_symbol; ?></h1>

<h2>
<a href="<?php echo base_url(); ?>stocks_feed/view_chart/<?php echo $stock_symbol; ?>/daily">Daily Chart<a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="<?php echo base_url(); ?>stocks_feed/view_chart/<?php echo $stock_symbol; ?>/weekly">Weekly Chart<a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="<?php echo base_url(); ?>stocks_feed/view_chart/<?php echo $stock_symbol; ?>/monthly">Monthly Chart<a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</h2>


<?php
$this->load->module('timedate');
$num_rows = $query->num_rows();
$count = 0;
    foreach($query->result() as $row) {
	$count++;
	$datetime = $this->timedate->get_nice_date($row->date_added, 'hourmin');
	$price = $row->price;
	echo $price;
	if ($count<$num_rows) {
		echo ", ";
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
      ['Mon', -10, 30, 45, 50],
      ['Tue', 31, 38, 55, 66],
      ['Wed', 50, 55, 77, 80],
      ['Thu', 77, 77, 66, 50],
      ['Fri', 68, 66, 22, 15]
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
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>