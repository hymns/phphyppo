<html>
<head>
	<title>Authorize Area</title>
	<style type="text/css">
	label{
		float: left;
		width: 160px;
		font-weight: bold;
	}

	input{
		width: 180px;
		margin-bottom: 5px;
	}

	#submit_button {
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
<h2>Authorize Area</h2>
<form action="<?php echo CONF_BASE_PATH; ?>/users/login" method="post">
<?php if (!empty($error)) echo '<span style="color: red;">' . $error . '</span>'; ?>
	<div>
	<label for="data[username]">Username</label>
	<input type="text" name="data[username]" maxlength="30" /><br />
	
	<label for="data[password]">Password</label>
	<input type="password" name="data[password]" maxlength="30" /><br />
	<input type="submit" id="submit_button" value="Submit">
	</div>
</form>

