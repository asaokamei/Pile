<!Doctype html>
<html>
<head>
    <title>Welcome to Tuum</title>
</head>
<body>
<h1>Let's Jump!</h1>
<p>This is from SampleController::onJump().</p>
<?php
unset($__data['_request']);
var_dump($__data);
?>
<p>and a sample/welcome view file.</p>
<form name="jump" method="get" action="jumper">
    <input type="text" name="message" value="message" />
    <input type="submit" value="jump" />
</form>
</body>
</html>