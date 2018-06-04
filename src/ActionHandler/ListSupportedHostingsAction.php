<?php declare(strict_types = 1);

namespace App\ActionHandler;

use App\ResourceHandler\ChainedHandler;

class ListSupportedHostingsAction
{
    /**
     * @var ChainedHandler $handler
     */
    private $handler;

    public function __construct(ChainedHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(): array
    {
        return [
            'type' => 'collection',
            'data' => $this->handler->getSupportedHosts()
        ];
    }
}
