<?php

namespace App\Commands;

use App\Exceptions\CannotOpenFileException;
use App\Exceptions\ProductExistsException;
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

final class AddCommand extends Command
{

    protected function configure(): void
    {
        $this->setName('add')
            ->setDescription('Add product to list')
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
        $price = $validatorService->askValidatedInteger('Enter product price: ');
        $filePath = $input->getArgument('filepath');

        try {
            $repository = $this->getItemRepository(filePath: $filePath);
            $repository->add(name: $product, price: $price);
        } catch (CannotOpenFileException|ProductExistsException $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }

        $output->writeln('<success>Product successfully added</>');
        return self::SUCCESS;
    }

    /**
     * @throws CannotOpenFileException
     */
    private function getItemRepository(string $filePath): ItemRepositoryInterface
    {
        return new FileItemRepository(storage: new FileStorage($filePath));
    }

    private function setOutputStyles(OutputFormatterInterface $outputFormatter): void
    {
        $outputFormatter->setStyle('success', new OutputFormatterStyle('green', options: ['bold']));
        $outputFormatter->setStyle('error', new OutputFormatterStyle('red', options: ['bold']));
    }

}