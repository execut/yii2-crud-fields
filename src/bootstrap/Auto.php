<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\bootstrap;

use yii\base\BootstrapInterface;
use yii\console\Application;

/**
 * Auto bootstrap
 * @package execut\books
 */
class Auto implements BootstrapInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap($app)
    {
        $bootstraps = [];
        if ($app instanceof Application) {
            $bootstraps[] = new Backend();
        }

        foreach ($bootstraps as $bootstrap) {
            $bootstrap->bootstrap($app);
        }
    }
}
