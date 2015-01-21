<?php

use Tuum\View\Tuum\Renderer;
use Tuum\Web\App;

return Renderer::forge($app->get(App::TEMPLATE_DIR));