<html>
<head></head>	
<body>

<h1>Running the Stocks Feed (hopefully)</h1>
<p>This page needs to be open for the feed to work.</p>
<p><a href="<?php echo base_url(); ?>stocks_feed/info" target="_blank">View Info About Stock Prices (opens new tab)</a></p>

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