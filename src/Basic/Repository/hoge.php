<?php

namespace App\Basic\DomainService;

class PhysicalDistributionBase
{
    public function ship(Baggage $baggage): Baggage
    {
        // 略
        return $baggage;
    }

    public function receive(Baggage $baggage): void
    {
        // 略
    }
}

class TransportService
{

    public function transport(PhysicalDistributionBase $from, PhysicalDistributionBase $to, Baggage $baggage): void
    {
        $shippedBaggage = $from->ship($baggage);
        $to->receive($shippedBaggage);

        // 略
    }
}

class Baggage
{

}

