<html>
<head>
	<title>{controller_class} Application</title>
</head>
<body>
<h2>List of {controller_class}</h2>
{addlink}
<table border=1>
{tablehead}
<?php foreach(${tablename} as $row): ?>
{tabledata}
<?php endforeach; ?>
</table>
</body>
</html>