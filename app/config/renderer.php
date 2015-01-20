<?php

use Tuum\Locator\Renderer;
use Tuum\Web\App;

return Renderer::forge($app->get(App::TEMPLATE_DIR));