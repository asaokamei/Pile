<?php
use Demo\Tasks\TaskDao;
use Tuum\Web\Psr7\Request;

/** @var $_request Request */

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Task Demo</title>
</head>
<body>

<h1>Task Demo</h1>
<ul>
    <li><form name="init" action="/demoTasks/initialize" method="post" ><input type="submit" value="initialize"/></form></li>
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
</style>

<?php if(isset($tasks)) : ?>

<table>
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
        <td><span class="<?= $class; ?>" ><?= $task[2] ?></span></td>
        <td>
            <form name="toggle" method="post" action="<?= $basePath.'/'.$task[0].'/toggle' ?>" >
                <input type="submit" value="toggle" />
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

<h3>debug info</h3>
<?php
unset($__data['_request']);
var_dump($__data);
?>

</body>
</html>