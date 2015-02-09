<?php
use Demo\Tasks\TaskDao;
use Tuum\View\Viewer\View;

/** @var View $view */

$basePath = $view['basePath'];
$tasks    = $view->asList('tasks');

?>
<?= $this->render('layout/header', [
    'title' => 'Demo Task Application',
]); ?>

<h1>Task Demo</h1>

<?= $view->message; ?>

<ul>
    <li><form name="init" action="/demoTasks/initialize" method="post" >
            <?= $view->hiddenTag('_token'); ?>
            <input type="submit" value="initialize"/>
        </form></li>
</ul>

<h2>task list</h2>

<style>
    span.done {
        color: gray;
        font-weight: normal;
    }
    span.active {
        color: blue;
        font-weight: bold;
    }
    form {
        display: inline;
    }
</style>

<?php if(isset($tasks)) : ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>task</th>
        <th>done</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach($tasks as $task) :
        $class = ($task[1] === TaskDao::ACTIVE) ? 'active' : 'done';
    ?>
    <tr>
        <td><?= $task[0] ?></td>
        <td>
            <span class="<?= $class; ?>" ><?= $task[2] ?></span>
            <?php
            if($task[1]===TaskDao::DONE) {
                echo "
                <form name=\"delete\" method=\"post\" action=\"{$basePath}/{$task[0]}/delete\" >
                    {$view->hiddenTag('_token')}
                    <input type='submit' value='del' />
                </form>
                ";
            }
            ?>
        </td>
        <td>
            <form name="toggle" method="post" action="<?= $basePath.'/'.$task[0].'/toggle' ?>" >
                <?= $view->hiddenTag('_token'); ?>
                <input type="submit" value="toggle" />
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

    <h3>adding a new task</h3>

    <form name="add" method="post" action="<?= $basePath; ?>/">
        <?= $view->hiddenTag('_token'); ?>
        <table class="table">
            <tbody>
            <tr>
                <td width="15%">add a new task:</td>
                <td>
                    <input type="text" name="task" placeholder="add a new task..." style="width: 40em;"/>
                </td>
                <td>
                    <input type="submit" value="add task"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <p>This form does not have CsRf token. should return forbidden error.</p>
    <form name="add" method="post" action="<?= $basePath; ?>/">
        <table class="table">
            <tbody>
            <tr>
                <td width="15%"> cannot add:</td>
                <td>
                    <input type="text" name="task" placeholder="add a new task..." style="width: 40em;"/>
                </td>
                <td>
                    <input type="submit" value="add task"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

<?php endif; ?>


<h3 id="debug-title" >debug info</h3>
<div style="display: none" id="debug-info">
<?php
var_dump($view);
?>
</div>

<?= $this->render('layout/footer'); ?>