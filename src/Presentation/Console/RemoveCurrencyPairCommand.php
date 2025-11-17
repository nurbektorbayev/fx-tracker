<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\UseCase\RemoveCurrencyPair\RemoveCurrencyPairHandler;
use App\Application\UseCase\RemoveCurrencyPair\RemoveCurrencyPairRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:pair:remove', description: 'Deactivate currency pair')]
final class RemoveCurrencyPairCommand extends Command
{
    public function __construct(
        private readonly RemoveCurrencyPairHandler $handler
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('base', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = (string)$input->getArgument('base');
        $target = (string)$input->getArgument('target');

        $this->handler->handle(
            new RemoveCurrencyPairRequest($base, $target)
        );

        $output->writeln(sprintf(
            '<info>Pair %s/%s deactivated (if existed).</info>',
            strtoupper($base),
            strtoupper($target),
        ));

        return Command::SUCCESS;
    }
}
