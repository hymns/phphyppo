<html>
<head>
	<title>phpHyppo Tools</title>
</head>
<body>
<h2 style='margin: 0px;'>Application Builder (DB: <?php echo strtoupper($db_type); ?>)</h2>
<i>"Give us the configs, and we'll finish the job" - hymns</i><br /><br />

<form action='<?php echo CONF_BASE_URL; ?>/apps/deploy' method='post'>
	<input type='hidden' name='tablename' value='<?php echo $tablename; ?>' />
	
	<h3 style='margin: 0px;'>Application Controller</h3>
	<i>"Please enter your controller name for your application"</i><br />	
	<p>
	<input type='text' name='controller_name' /> <i>(text only, no space &amp; lowercase)</i>
	</p><br />
	
	<h3 style='margin: 0px;'>Application Functions</h3>
	<i>"Please select controller functions for your application"</i><br />	
	<p>
	<input type='checkbox' name='action[]' value='list' checked> Lists<br />
	<input type='checkbox' name='action[]' value='create' checked> Create <br />
	<input type='checkbox' name='action[]' value='view' checked> Read <br />
	<input type='checkbox' name='action[]' value='update'> Update <br />
	<input type='checkbox' name='action[]' value='delete'> Delete
	</p><br />

	<h3 style='margin: 0px;'>Table '<?php echo $tablename; ?>' Structure</h3>
	<i>"Please change the primary key for your '<?php echo $tablename; ?>' table if necessary"</i><br /><br />	
	<table border=1>
		<tr>
			<th>Key</th>
			<th>Field</th>
			<th>Type</th>
			<th>Extra</th>
		<tr>
		<?php 
		// loop over fields lists
		foreach($fields as $field): 
			// mysql
			if ($db_type == 'mysql'):
		?>
		<tr>
			<td><input type='radio' name='primary' value='<?php echo $field['Field']; ?>' <?php if ($field['Key'] == 'PRI') echo 'checked' ?>></td>
			<td><?php echo $field['Field']; ?></td>
			<td><?php echo $field['Type']; ?></td>
			<td><?php echo $field['Extra']; ?>&nbsp;</td>				
		</tr>
		<input type='hidden' name='fieldname[]' value='<?php echo $field['Field']; ?>'>
		<input type='hidden' name='type[<?php echo $field['Field']; ?>]' value='<?php echo $field['Type']; ?>'>
		<?php 
			// postgres
			else :
				// check if any auto increment field
				if (preg_match("/nextval/i", $field['extra']))
					echo '<input type="hidden" name="autoincrement" value="'.$field['field'].'">';
		?>
		<tr>
			<td><input type='radio' name='primary' value='<?php echo $field['field']; ?>' <?php if ($field['primary'] == 'true') echo 'checked' ?>></td>
			<td><?php echo $field['field']; ?></td>
			<td><?php echo $field['type']; ?></td>
			<td><?php echo $field['extra']; ?>&nbsp;</td>				
		</tr>
		<input type='hidden' name='fieldname[]' value='<?php echo $field['field']; ?>'>	
		<input type='hidden' name='type[<?php echo $field['field']; ?>]' value='<?php echo $field['type']; ?>'>		
		<?php
			// end db type
			endif;
		// end loop
		endforeach; 
		?>
	</table>
	<br />
	<input type='submit' value='Build Application'> <input type='reset' value='Reset'>
</form>
</body>
</html>