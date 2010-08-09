<html>
<head>
	<title>phpHyppo Tools</title>
</head>
<body>
<h2 style='margin: 0px;'>Application Builder (DB: <?php echo strtoupper($db_type); ?>)</h2>
<i>"Give us the configs, and we'll finish the job" - hymns</i><br /><br />

<h3 style='margin: 0px;'>Directory Permissions</h3>
<i>"Directory permissions need to be set to mode 0777 (writable)"</i>
<p>
<img src="<?php echo CONF_BASE_URL; ?>/media/img/<?php echo $permission_controller; ?>.gif"> <?php echo APPDIR; ?>controllers<br>
<img src="<?php echo CONF_BASE_URL; ?>/media/img/<?php echo $permission_model; ?>.gif"> <?php echo APPDIR; ?>models<br>
<img src="<?php echo CONF_BASE_URL; ?>/media/img/<?php echo $permission_view;?>.gif"> <?php echo APPDIR; ?>views
</p><br />

<?php if (sizeof($tables) > 0) : ?>
<h3 style='margin: 0px;'>Existing Table(s)</h3>
<i>"Please select one of tables below will use for your application"</i>
<!--- table lists --->
<ul>
<?php foreach($tables as $table): ?>
 <li><a href="<?php echo CONF_BASE_URL; ?>/apps/model/<?php echo str_replace($db_prefix, '', $table[$column_name]); ?>"><?php echo $table[$column_name]; ?></a></li>
<?php endforeach; ?>
</ul>
<!--- /table lists --->
<?php else : ?>
<h3>Table not found</h3>
Please create at least one table before you can use this application builder.
<?php endif; ?>
</body>
</html>