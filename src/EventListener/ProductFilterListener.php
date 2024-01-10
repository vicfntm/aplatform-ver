<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Helper\UriParser;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Ulid;

class ProductFilterListener
{
    private const PATH   = '/api/products';
    private const METHOD = 'POST';

    public function onKernelRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        if ($req->getPathInfo() === static::PATH) {
            $id            = UriParser::uidParser($req->get('category_id'));
            $convertedUlid = Ulid::fromString($id);
            $toBaseForm    = $convertedUlid->toRfc4122();
            $w = [];
            parse_str($event->getRequest()->getQueryString(), $w);
            $w['category_id'] = $toBaseForm;
            $event->getRequest()->server->set('QUERY_STRING', http_build_query($w));
        }
    }
}
