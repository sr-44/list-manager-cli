<?php

namespace App\Commands;

use App\Exceptions\CannotOpenFileException;
use App\Interfaces\ItemRepositoryInterface;
use App\Repositories\FileItemRepository;
use App\Storage\FileStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CalculateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('calculate')
            ->setDescription('Calculate product prices')
            ->addArgument('filepath', mode: InputArgument::REQUIRED, description: 'Path to file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setOutputStyles($output->getFormatter());

        $filePath = $input->getArgument('filepath');

        try {
            $repository = $this->getItemRepository($filePath);
        } catch (CannotOpenFileException $e) {
            $output->writeln(sprintf("<error>%s</>", $e->getMessage()));
            return self::INVALID;
        }

        $calculated = $repository->totalSum();

        $output->writeln(sprintf("<success>%s</>", $calculated));
        return self::SUCCESS;
    }

    private function setOutputStyles(OutputFormatterInterface $outputFormatter): void
    {
        $outputFormatter->setStyle('success', new OutputFormatterStyle('green', options: ['bold']));
        $outputFormatter->setStyle('error', new OutputFormatterStyle('red', options: ['bold']));
    }

    /**
     * @throws CannotOpenFileException
     */
    private function getItemRepository(string $filePath): ItemRepositoryInterface
    {
        return new FileItemRepository(new FileStorage($filePath));
    }

}