<?php
use WScore\Pile\Http\UrlGenerator;

/** @var UrlGenerator $url */
$url = $this->url();
?>
<h1>Pile Micro Framework</h1>
<p>Pile is yet-another micro framework for PHP.</p>

<h3>Example Links</h3>
<ul>
    <li><a href="<?= $url('text')?>" >text only</a></li>
    <li><a href="<?= $url('closure')?>" >closure</a></li>
</ul>