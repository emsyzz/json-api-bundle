<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\JsonApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ObjectMapperCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processLinkRepositories($container);
        $this->processDataTypeHandlers($container);
    }

    /**
     * Process links repositories
     *
     * @param ContainerBuilder $container
     */
    protected function processLinkRepositories(ContainerBuilder $container)
    {
        $definition   = $container->findDefinition('mrtn_json_api.object_mapper.link_repository_provider');
        $repositories = $container->findTaggedServiceIds('mrtn_json_api.object_mapper.link_repository');

        foreach ($repositories as $id => $tags) {
            foreach ($tags as $tag)
            {
                if (! isset($tag['alias'])) {
                    throw new \LogicException('Alias must be defined for a "link-repository" tag');
                }

                $definition->addMethodCall('registerRepository', [
                    trim($tag['alias']),
                    new Reference($id)
                ]);
            }
        }
    }

    protected function processDataTypeHandlers(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('mrtn_json_api.object_mapper.datatype_manager');
        $extensions = $container->findTaggedServiceIds('mrtn_json_api.object_mapper.datatype_handler');

        foreach ($extensions as $id => $tags) {
            $definition->addMethodCall('registerDataTypeHandler', [
                new Reference($id)
            ]);
        }
    }
}