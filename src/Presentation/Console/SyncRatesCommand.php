<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\UseCase\SyncRates\SyncRatesHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rates:sync', description: 'Sync exchange rates for all active pairs')]
final class SyncRatesCommand extends Command
{
    public function __construct(private readonly SyncRatesHandler $handler)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->handle();

        $output->writeln('<info>Rates synced.</info>');

        return Command::SUCCESS;
    }
}
