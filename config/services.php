<?php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use App\Convertors\MediaNormalizer;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services(); // Automatically registers your services as commands, event subscribers, etc.
    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below
    $services->alias('m.norm', MediaNormalizer::class);
    $services->set(MediaNormalizer::class)->public();
//    $services->alias('commodity.relevant', RelevantCommodityService::class);
//    $services->set(OrderAction::class)->bind(CommodityDefiner::class, 'commodity.relevant');
//    $services->set(UpdateOrderCommodityService::class)->public();
//    $services->set(UpdateOrderAction::class)->public()->bind(
//        CommodityDefiner::class,
//        UpdateOrderCommodityService::class
//    );
};
