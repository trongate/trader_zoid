<html>
<head></head>	
<body>The feed is running.
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