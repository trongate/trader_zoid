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

<h1><?php echo $headline; ?></h1>
<?php echo $percent_change; ?>

    <div id="chart_div" style="width: 100%; height: 500px;"></div>
  </body>
</html>