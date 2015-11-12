<html>
<head>
<title>Model Missing</title>

<style type="text/css">
body { background-color: #fff; margin: 40px; font-family: 'Lucida Grande', Verdana, Sans-serif; font-size: 12px; color: #4F5155; }
a { color: #003399; background-color: transparent; font-weight: normal; }
h1 { color: #444; background-color: transparent; border-bottom: 1px solid #D0D0D0; font-size: 16px; font-weight: bold; margin: 24px 0 2px 0; padding: 5px 0 6px 0; }
.msg { font-family: Monaco, Verdana, Sans-serif; font-size: 12px; background-color: #FFF6BF; border: 1px solid #FFD324; color: #444444; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px; }
.path { font-family: Monaco, Verdana, Sans-serif; font-size: 12px; background-color: #DEFFAF; border: 1px solid #ACD919; color: #444444; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px; }
.code { font-family: Monaco, Verdana, Sans-serif; font-size: 12px; background-color: #f9f9f9; border: 1px solid #D0D0D0; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px; }
</style>
</head>
<body>

<h1>Model Missing</h1>

<p>This error page generate dynamically by this framework.</p>

<p>We found missing model for:</p>
<code class="msg"><?php echo ucfirst($model) . '()'; ?></code>

<p>To solve this problem, please create filename:</p>
<code class='path'><?php echo strtolower($model); ?>.php</code>

<p>Then save at:</p>
<code class='path'><?php echo APPDIR . 'models' . DS; ?></code>

<p>Example source code inside this model file will look like:</p>
<pre class="code">
&lt;?php
class <span style="color: blue;"><?php echo ucfirst($model);?></span> extends <span style="color: blue;">AppModel</span>
{
	public function <span style="color: blue;">your_model_function</span>()
	{
		<span style="color: orange;">... your code here ...</span>
	}
}
?&gt;
</pre>

</body>
</html>