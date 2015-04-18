<html>
<head></head>	
<body>



<p>Click on a stock symbol below to view a chart.</p>

<ul>
<?php
foreach($stocks as $symbol) {
	$url = base_url()."stocks_feed/view_chart/".$symbol;
	echo "<li style='margin-bottom: 7px;'><a href='".$url."'>".$symbol."</a></li>";
}
?>
</ul>


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