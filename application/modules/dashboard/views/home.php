<h1>Dashboard</h1>

<style type="text/css">
li {
	margin-bottom: 24px;
}
</style>

<ul>
<?php
echo anchor('candidates/restart', '<li>Repopulate Candidates Table</li>');

echo anchor('candidates/view_current_candidates', '<li>View Current Candidates</li>');

echo anchor('historical_dates_to_be_checked/populate_table', '<li>Populate Historical Dates To Be Checked Table</li>');

echo anchor('analysis_phase_one/go', '<li>Start Phase One Analysis</li>');