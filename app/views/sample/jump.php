<?php
use Tuum\View\Viewer\View;

/** @var View $view */


?>
<?= $this->render('layout/header', [
    'title' => 'Let\'s Jump!',
]); ?>
<h1>Let's Jump!</h1>
<p>This is from SampleController::onJump().</p>
<p>and a sample/welcome view file.</p>

<?= $view->message; ?>

<form name="jump" method="get" action="jumper">
    <input type="text" name="message" value="message" />
    <input type="submit" value="jump" />
</form>
<?php
var_dump($view);
?>

<?= $this->render('layout/footer'); ?>