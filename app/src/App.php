<?php

namespace Zaibatsu;

use Silex\Application;

/**
 * Class App
 * @package Zaibatsu
 */
class App extends Application
{
    use Application\TwigTrait;
    use Application\SwiftmailerTrait;
    use Application\SecurityTrait;
}