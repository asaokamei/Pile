<?= $this->render('layout/header'); ?>

<h1>TuumPHP Framework</h1>
<p>TuumPHP is yet-another micro framework for PHP.</p>

<h3>URL Mappers</h3>
<ul>
    <li><a href="tuum.html" >html file</a></li>
    <li><a href="tuum.txt" >text file</a></li>
    <li><a href="tuum.md" >markdown file (not found)</a></li>
    <li><a href="errors.php" >php exception thrown</a></li>
</ul>

<h3>Routes (closure style)</h3>
<ul>
    <li><a href="closure">from closure</a></li>
    <li><a href="closure-view">from closure-view</a></li>
</ul>

<h3>Sample Controller</h3>
<ul>
    <li><a href="sample/?name=TuumPHP" >sample welcome</a></li>
    <li><a href="sample/tuum" >sample hello world</a></li>
    <li><a href="sample/jump" >redirect with message</a></li>
</ul>

<h3><a href="demoTasks" >Demo Task Application</a></h3>

<?= $this->render('layout/footer'); ?>