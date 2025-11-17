<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\UseCase\AddCurrencyPair\AddCurrencyPairHandler;
use App\Application\UseCase\AddCurrencyPair\AddCurrencyPairRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:pair:add', description: 'Add currency pair for tracking')]
final class AddCurrencyPairCommand extends Command
{
    public function __construct(
        private readonly AddCurrencyPairHandler $handler
    ) {
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
        $base  = (string) $input->getArgument('base');
        $target = (string) $input->getArgument('target');

        $directPair = $this->handler->handle(new AddCurrencyPairRequest($base, $target));
        $output->writeln(sprintf(
            '<info>Now tracking %s/%s (id: %d)</info>',
            $directPair->getBaseCurrency(),
            $directPair->getTargetCurrency(),
            $directPair->getId(),
        ));

        $reversePair = $this->handler->handle(new AddCurrencyPairRequest($target, $base));
        $output->writeln(sprintf(
            '<info>Now tracking %s/%s (id: %d)</info>',
            $reversePair->getBaseCurrency(),
            $reversePair->getTargetCurrency(),
            $reversePair->getId(),
        ));

        return Command::SUCCESS;
    }
}
