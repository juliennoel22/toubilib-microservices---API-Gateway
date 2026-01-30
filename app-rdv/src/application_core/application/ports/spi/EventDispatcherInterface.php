<?php

namespace toubilib\core\application\ports\spi;

interface EventDispatcherInterface
{
    public function dispatch(string $eventName, array $data): void;
}
