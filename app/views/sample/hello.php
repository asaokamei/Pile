<?php
use Tuum\View\Viewer\View;
/** @var $view View */

$name = $view['name'];

?>
<?= $this->render('layout/header', [
    'title' => 'Hello ' . $name,
]); ?>

<h1>Hello <?= $name; ?></h1>
<p>This is from SampleController::onHello( $name ),</p>
<p>and a sample/hello view file.</p>

<?= $this->render('layout/footer'); ?>