<h1>Active Candidates</h1>

<table border="1">
	<tr><td valign="top">
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
</ul></td>

<td valign="top"><?php
echo Modules::run('auto_comments/view');
?></td>


</tr></table>

<?php
echo anchor('candidates/activate_one/', 'Activate Just One Stock Symbol (for testing)');
echo "<br><br>";
echo anchor('auto_comments/clear/', 'Clear Comments');