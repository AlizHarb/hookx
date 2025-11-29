<?php

declare(strict_types=1);

namespace AlizHarb\Hookx\Integrations\Symfony;

use AlizHarb\Hookx\HookManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HookXBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $definition = new Definition(HookManager::class);
        $definition->setFactory([HookManager::class, 'getInstance']);
        $definition->setPublic(true);
        
        $container->setDefinition(HookManager::class, $definition);
        $container->setAlias('hookx', HookManager::class);
    }
}
