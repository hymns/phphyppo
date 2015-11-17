<h2>List of {controller_class}</h2>
<p>{addlink}</p>
<table class="table">
    <thead>
      	{tablehead}
    </thead>
    <tbody>
		<?php foreach(${tablename} as $row): ?>
		{tabledata}
		<?php endforeach; ?>
    </tbody>
  </table>