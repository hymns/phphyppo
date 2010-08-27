<html>
<head>
	<title>{controller_class} Application</title>
</head>
<body>
<h2>Latest {controller_class}</h2>
{addlink}
<table border=1>
{tablehead}
<?php foreach(${tablename}s as ${tablename}): ?>
{tabledata}
<?php endforeach; ?>
</table>
</body>
</html>