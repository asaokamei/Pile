<?php
use Tuum\View\Viewer\View;

/** @var View $view */

$name = $view['name'];

?>
<?= $this->render('layout/header', [
    'title' => 'Welcome to ' . $name,
]); ?>
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

<?= $this->render('layout/footer'); ?>