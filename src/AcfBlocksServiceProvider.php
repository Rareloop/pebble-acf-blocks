<?php

namespace Rareloop\Lumberjack\AcfBlocks;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class AcfBlocksProvider extends ServiceProvider
{
    public function boot(Config $config)
    {
        collect($config->get('acfblocks.blocks'))->each(function ($block) {
            $block::register();
        });
    }
}
