<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

$storage = new MockArraySessionStorage();
$GLOBALS[ 'session.storage' ] = $storage;

return $storage;