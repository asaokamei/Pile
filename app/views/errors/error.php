<!DOCTYPE html>
<html>
<head>
    <title>It's an Error!</title>
    <meta charset="utf-8"/>
</head>
<body>

<h1>A Generic Error Happened</h1>

<p>Don't know what exactly have happened, but some error has happened and that is why you are seeing this screen...</p>
<p>sorry.</p>

<?php
if( isset($trace) && is_array($trace) ) {
    var_dump($trace);
}
?>
</body>
</html>