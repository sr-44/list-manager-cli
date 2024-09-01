<?php

namespace App\Commands;

use App\Exceptions\CannotOpenFileException;
use App\Exceptions\ProductNotExistsException;
use App\Interfaces\ItemRepositoryInterface;
use App\Repositories\FileItemRepository;
use App\Services\ValidateInputService;
use App\Storage\FileStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeleteCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('delete')
            ->setDescription('Remove product from list')
            ->addArgument('filepath', mode: InputArgument::REQUIRED, description: 'Path to file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setOutputStyles($output->getFormatter());

        $validatorService = new ValidateInputService(
            questionHelper: $this->getHelper('question'),
            input: $input,
            output: $output
        );

        $product = $validatorService->askValidatedString('Enter product name: ');
        $filePath = $input->getArgument('filepath');

        try {
            $repository = $this->getItemRepository($filePath);
            $repository->delete($product);
        } catch (CannotOpenFileException|ProductNotExistsException $e) {
            $output->writeln(sprintf("<error>%s</>", $e->getMessage()));
            return self::FAILURE;
        }

        $output->writeln('<success>Product successfully removed</>');
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
    private function getItemRepository(string $file): ItemRepositoryInterface
    {
        return new FileItemRepository(storage: new FileStorage($file));
    }

}