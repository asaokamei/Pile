<?php
use Tuum\View\Viewer\View;

/** @var View $view */

$name = $view['name'];

?>
<!Doctype html>
<html>
<head>
    <title>Welcome to <?= $name; ?></title>
</head>
<body>
<h1>Welcome to <?= $name; ?></h1>
<p>This is from SampleController::onWelcome(),</p>
<p>and a sample/welcome view file.</p>

<form name="jump" method="get" action="/sample/">
    <input type="text" name="name" value="<?= $name; ?>" />
    <input type="submit" value="say hello" />
</form>
<?php
var_dump($view);
?>

</body>
</html>