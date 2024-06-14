<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\TransactionParser;
use App\Service\TransactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:process-file',
    description: 'Process incoming file and display results',
)]
class ProcessFileCommand extends Command
{
    public function __construct(
        readonly TransactionParser $transactionParser,
        readonly TransactionService $transactionService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED,'File path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('path');

        if (!$filePath) {
            $io->note('Please pass an argument');
        }

        if (!file_exists($filePath)) {
            $io->error(sprintf('The file "%s" does not exist.', $filePath));

            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            $io->error(sprintf('The file "%s" could not be read.', $filePath));

            return Command::FAILURE;
        }

        $rates = $this->transactionService->processTransactions($filePath);

        $io->text(implode(PHP_EOL, $rates));

        return Command::SUCCESS;
    }
}
