<?php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use App\Convertors\MediaNormalizer;
use App\Service\MessageGenerator;

return function(ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services(); // Automatically registers your services as commands, event subscribers, etc.
    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below
    $services->alias('m.norm', MediaNormalizer::class);
    $services->set(MediaNormalizer::class)->public();
};
