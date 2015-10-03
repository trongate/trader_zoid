<h1>Active Candidates</h1>
<?php
echo anchor('dashboard', "Dashboard");
?>
<ul>
<?php
foreach($query->result() as $row) {
	$stock_symbol = $row->stock_symbol;
	echo "<li>".$stock_symbol."</li>";
}
?>
</ul>

<h1>Non Active Candidates</h1>
<ul>
<?php
foreach($query2->result() as $row) {
	$stock_symbol = $row->stock_symbol;
	echo "<li>".$stock_symbol."</li>";
}
?>
</ul>