<?php
require("../utils/check_session.php");
require("../utils/show_popup.php");
require("../utils/functions.php");
require("../utils/menu.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta content="IE=Edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta
		content="width=device-width,height=device-height,user-scalable=no,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0"
		name="viewport">
	<title>Login Attempt Statistics</title>
	<link rel="shortcut icon" href="../assets/favicons/favicon_no-bg.ico" type="image/x-icon">
	<link rel="stylesheet" media="all" href="../css/styles.css">
	<link rel="manifest" href="../manifest.json">
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
		}

		th,
		td {
			padding: 8px;
			text-align: left;
			border-bottom: 1px solid #ddd;
		}

		th {
			background-color: #f2f2f2;
			cursor: pointer;
		}
	</style>
	<script src="../js/script.js"></script>
	<script src="../js/js"></script>
	<script src="../js/main.js"></script>
</head>

<body>
	<?php require("../utils/header.php"); ?>
	<div class="container-item scale-team-container">
		<div class="row mt-4">
			<h3 class="font-weight-bold text-uppercase text-center mt-4 pt-4">Login Attempt Statistics</h3>
			<div class="p-0 scale-introduction dancer from-down" id="introduction">
				<p>Here are some statistics about login attempts on the website.</p>
				<p>Click on a column header to sort the table by that column.</p>
			</div>
			<?php
			define($_SERVER['REDIRECT_SECURE_KEY'], true);
			require_once "../utils/db_config.php";

			$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			$queries = [
				'users' => "SELECT username, 
                    SUM(CASE WHEN successful_attempts = 1 THEN 1 ELSE 0 END) AS successful,
                    SUM(CASE WHEN successful_attempts = 0 THEN 1 ELSE 0 END) AS failed,
                    SUM(1) AS total_attempts
                FROM login_attempts
                GROUP BY username",
				'ip' => "SELECT IFNULL(NULLIF(ip_address, ''), 'Data not collected') AS ip_address, 
                SUM(CASE WHEN successful_attempts = 1 THEN 1 ELSE 0 END) AS successful,
                SUM(CASE WHEN successful_attempts = 0 THEN 1 ELSE 0 END) AS failed,
                SUM(1) AS total_attempts
            FROM login_attempts
            GROUP BY ip_address",
				'os' => "SELECT IFNULL(NULLIF(os, ''), 'Data not collected') AS os, 
                SUM(CASE WHEN successful_attempts = 1 THEN 1 ELSE 0 END) AS successful,
                SUM(CASE WHEN successful_attempts = 0 THEN 1 ELSE 0 END) AS failed,
                SUM(1) AS total_attempts
            FROM login_attempts
            GROUP BY os",
				'browser' => "SELECT IFNULL(NULLIF(browser, ''), 'Data not collected') AS browser, 
                SUM(CASE WHEN successful_attempts = 1 THEN 1 ELSE 0 END) AS successful,
                SUM(CASE WHEN successful_attempts = 0 THEN 1 ELSE 0 END) AS failed,
                SUM(1) AS total_attempts
            FROM login_attempts
            GROUP BY browser",
				'totals' => "SELECT SUM(CASE WHEN successful_attempts = 1 THEN 1 ELSE 0 END) AS successful,
					SUM(CASE WHEN successful_attempts = 0 THEN 1 ELSE 0 END) AS failed,
					SUM(1) AS total_attempts
			FROM login_attempts"
			];

			foreach ($queries as $key => $sql) {
				$results[$key] = $conn->query($sql);
			}

			echo "<h4 class=\"font-weight-bold text-uppercase text-center mt-4 pt-4\">Table of total Login Attempts</h2>";
			displayTotalTable($results['totals'], ['Number of Attempts', 'Failures', 'Successes'], 'TableTotals');

			echo "<h4 class=\"font-weight-bold text-uppercase text-center mt-4 pt-4\">Table of Login Attempts by User</h2>";
			displayTable($results['users'], ['Username', 'Number of Attempts', 'Failures', 'Successes'], 'tableUsers');

			echo "<h4 class=\"font-weight-bold text-uppercase text-center mt-4 pt-4\">Table of Login Attempts by IP Address</h2>";
			displayTable($results['ip'], ['IP Address', 'Number of Attempts', 'Failures', 'Successes'], 'tableIP');

			echo "<h4 class=\"font-weight-bold text-uppercase text-center mt-4 pt-4\">Table of Login Attempts by OS</h2>";
			displayTable($results['os'], ['OS', 'Number of Attempts', 'Failures', 'Successes'], 'tableOS');

			echo "<h4 class=\"font-weight-bold text-uppercase text-center mt-4 pt-4\">Table of Login Attempts by Browser</h2>";
			displayTable($results['browser'], ['Browser', 'Number of Attempts', 'Failures', 'Successes'], 'tableBrowser');

			$conn->close();
			?>
		</div>
	</div>

	<script>
		function sortTable(n, tableId) {
			var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			table = document.getElementById(tableId);
			switching = true;
			dir = "asc";
			while (switching) {
				switching = false;
				rows = table.rows;
				for (i = 1; i < (rows.length - 1); i++) {
					if (rows[i].id === 'totalsRow') {
						continue;
					}
					shouldSwitch = false;
					x = rows[i].getElementsByTagName("TD")[n];
					y = rows[i + 1].getElementsByTagName("TD")[n];
					var xContent = (isNaN(parseInt(x.innerHTML))) ? x.innerHTML.toLowerCase() : parseInt(x.innerHTML);
					var yContent = (isNaN(parseInt(y.innerHTML))) ? y.innerHTML.toLowerCase() : parseInt(y.innerHTML);

					if (dir == "asc") {
						if (xContent > yContent) {
							shouldSwitch = true;
							break;
						}
					} else if (dir == "desc") {
						if (xContent < yContent) {
							shouldSwitch = true;
							break;
						}
					}
				}
				if (shouldSwitch) {
					rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					switching = true;
					switchcount++;
				} else {
					if (switchcount == 0 && dir == "asc") {
						dir = "desc";
						switching = true;
					}
				}
			}
		}
	</script>
</body>

</html>