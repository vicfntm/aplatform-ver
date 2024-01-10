<?php

declare(strict_types=1);


namespace App\Helper;


use App\Entity\Order;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionHelper
{
    private \SplObjectStorage $orderStorage;

    public function __construct(private readonly Session $userSession)
    {
        $this->orderStorage = $this->userSession->get('orders', new \SplObjectStorage());
    }

    public function pushOrder(Order $order): void
    {
        $this->orderStorage->attach($order);
        $this->userSession->set('orders', $this->orderStorage);
    }
    public function pullOrders()
    {
        return $this->userSession->get('orders');
    }


}
