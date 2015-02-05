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
    ?>
    <tr>
        <td><?= $task[0] ?></td>
        <td><?= $task[2] ?></td>
        <td></td>
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