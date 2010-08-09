<html>
<head>
	<title>Blog Application</title>
</head>
<body>
<h2>Latest Blog</h2>
<b><a href="<?php echo CONF_BASE_URL; ?>/blog/create">Add New</a></b>
<table border=1>
<tr>
	<th>Action</th>
	<th>id</th>
	<th>title</th>
	<th>body</th>
</tr>

<?php foreach($pages as $page): ?>
<tr>
	<td>&nbsp;<a href="<?php echo CONF_BASE_URL; ?>/blog/view/<?php echo $page['id']; ?>">View</a> <a href="<?php echo CONF_BASE_URL; ?>/blog/update/<?php echo $page['id']; ?>">Update</a> <a href="<?php echo CONF_BASE_URL; ?>/blog/delete/<?php echo $page['id']; ?>">Delete</a></td>	<td><?php echo $page['id']; ?></td>
	<td><?php echo $page['title']; ?></td>
	<td><?php echo $page['body']; ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>