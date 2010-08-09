<html>
<head>
	<title>Update Blog</title>
</head>
<body>
<h2>Update Blog</h2>
<form action="<?php echo CONF_BASE_URL; ?>/blog/update" method="post">
<input type='hidden' name='data[id]' value='<?php echo $id; ?>'>
	<div id="label">Title</div>
	<div id="input"><input type='text' name='data[title]' size='40' value='<?php echo $title; ?>'></div>

	<div id="label">Body</div>
	<div id="input"><input type='text' name='data[body]' size='40' value='<?php echo $body; ?>'></div>

	<input type="submit" value="Update">
</form>
</body>
</html>