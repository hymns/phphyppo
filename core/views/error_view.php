<html>
<head>
<title>View Missing</title>

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

<h1>View Missing</h1>

<p>This error page generate dynamically by this framework.</p>

<p>To solve this problem, please create filename:</p>
<code><?php echo $error_msg; ?>.php</code>

<p>Then save at:</p>
<code class='code'><?php echo APPDIR . 'views' . DS; ?></code>

<p>Example source code inside this view file will look like:</p>
<pre>
&lt;html&gt;
    &lt;head&gt;
        &lt;title&gt;&lt;?=<span style="color: red;">$title</span>;?&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;?=<span style="color: red;">$content</span>;&gt;
    &lt;/body&gt;
&lt;/html&gt;
</pre>
<p>Note: Usually this view file contain html code blend with php code</p>
</body>
</html>
<?php
exit;
?>