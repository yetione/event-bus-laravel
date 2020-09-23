<?php

namespace Yetione\EventBus\Console;

use Yetione\EventBus\Consumers\ConsumerFactory;
use Yetione\EventBus\Services\EventBusService;
use Illuminate\Console\Command;
use Yetione\RabbitMQ\Service\RabbitMQService;

class RunConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbus:consumer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for running consumer';

    /**
     * Execute the console command.
     *
     * @param RabbitMQService $rabbitMQService
     * @param ConsumerFactory $consumerFactory
     * @return int
     */
    public function handle(RabbitMQService $rabbitMQService, ConsumerFactory $consumerFactory)
    {
        $consumer = $consumerFactory->make($this->argument('name'));
        $result = $rabbitMQService->runConsumer($consumer);

        $this->info('Success!');
        return $result;
    }
}
