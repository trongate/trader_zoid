



<html>
<head>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load('visualization', '1.1', {packages: ['line']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('number', 'Day');
      data.addColumn('number', 'Previous Chart (date)');
      data.addColumn('number', 'Today Chart');

      data.addRows([
        <?php echo $data_block; ?>
      ]);

      var options = {
        chart: {
          title: 'Box Office Earnings in First Two Weeks of Opening',
          subtitle: 'in millions of dollars (USD)'
        },
        width: 900,
        height: 500
      };

      var chart = new google.charts.Line(document.getElementById('linechart_material'));

      chart.draw(data, options);
    }
  </script>
</head>
<body>
  <div id="linechart_material"></div>
  <h1>Chart Diff Score: <?php echo $chart_diff_score; ?></h1>
  <?php
  echo anchor('chartcalc/test', "<p>Run the thing again</p>");
  ?>
</body>
</html>