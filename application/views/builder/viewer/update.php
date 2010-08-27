<html>
<head>
	<title>Update {controller_class}</title>
	<style type="text/css">
	label{
		float: left;
		width: 160px;
		font-weight: bold;
	}

	input, textarea{
		width: 180px;
		margin-bottom: 5px;
	}

	textarea{
		width: 250px;
		height: 150px;
	}

	.boxes{
		width: 1em;
	}

	#submit_button{
		margin-left: 160px;
		margin-top: 5px;
		width: 90px;
	}

	br{
		clear: left;
	}
	</style>	
</head>
<body>
<h2>Update {controller_class}</h2>
<form action="<?php echo CONF_BASE_URL; ?>/{controller}/update" method="post">
{content}
	<input type="submit" id="submit_button" value="Update">
</form>
</body>
</html>