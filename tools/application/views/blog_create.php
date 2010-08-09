<html>
<head>
	<title>Create New Blog</title>
</head>
<body>
<h2>Create New Blog</h2>
<form action="<?php echo CONF_BASE_URL; ?>/blog/create" method="post">
	<div id="label">Title</div>
	<div id="input"><input type='text' name='data[title]' size='40'></div>

	<div id="label">Body</div>
	<div id="input"><input type='text' name='data[body]' size='40'></div>

	<input type="submit" value="Submit">
</form>
</body>
</html>