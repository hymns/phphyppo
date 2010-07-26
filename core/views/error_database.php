<html>
<head>
<title>Database Error</title>

<style type="text/css">

body {
	background-color: #fff;
	margin: 40px;
	font-family: 'Lucida Grande', Verdana, Sans-serif;
	font-size: 12px;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 16px;
	font-weight: bold;
	margin: 24px 0 2px 0;
	padding: 5px 0 6px 0;
}

code {
	font-family: Monaco, Verdana, Sans-serif;
	font-size: 12px;
	background-color: #FFF6BF;
	border: 1px solid #FFD324;
	color: #444444;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

.code {
	font-family: Monaco, Verdana, Sans-serif;
	font-size: 12px;
	background-color: #DEFFAF;
	border: 1px solid #ACD919;
	color: #444444;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

pre {
	font-family: Monaco, Verdana, Sans-serif;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

</style>
</head>
<body>

<h1>Database Error</h1>

<p>This error page generate dynamically by this framework.</p>

<p>We found database error:</p>
<code><?php echo $error_msg; ?></code>

<p>To solve this problem, please check database setting file:</p>
<code class='code'><?php echo $filename; ?>.php</code>

<p>Locate the file under:</p>
<code class='code'><?php echo APPDIR . 'configs' . DS; ?></code>

<p>Example database setting will look like:</p>
<pre>
<span style="color: orange">/* database engine plugin */</span>
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_plugin'</span>] = <span style="color: green">'ActiveRecord'</span>;

<span style="color: orange">/* connection engine type */</span>
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_type'</span>] = <span style="color: green">'mysql'</span>;

<span style="color: orange">/* database information */</span>
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_host'</span>] = <span style="color: green">'localhost'</span>;
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_name'</span>] = <span style="color: green">'dbname'</span>;
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_user'</span>] = <span style="color: green">'dbuser'</span>;
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_pass'</span>] = <span style="color: green">'dbpass'</span>;

<span style="color: orange">/* database connection persistence */</span>
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_persistent'</span>] = false;

<span style="color: orange">/* database character set */</span>
<span style="color: #ff0000">$config</span>[<span style="color: green">'db_charset'</span>] = <span style="color: green">'UTF-8'</span>;
</pre>

</body>
</html>
<?php
exit;
?>