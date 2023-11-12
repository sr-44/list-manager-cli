<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Cli extends Command
{
    protected function configure(): void
    {
        $this->setName('cli')
            ->addArgument('filename', InputArgument::REQUIRED)
            ->addArgument('action', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $actions = new Actions($input->getArgument('filename'));
        $product = $this->ask($input, $output, 'Введите продукт: ');
        $price = (int)$this->ask($input, $output, 'Введите цену: </>');

        if ($input->getArgument('action') === 'add') {
            if ($actions->add($product, $price)) {
                $output->writeln('success');
            }
        }

        return Command::SUCCESS;
    }
    private function ask(InputInterface $input, OutputInterface $output, $question)
    {
        $helper = $this->getHelper('question');
        $question = new Question($question);
        return $helper->ask($input, $output, $question);
    }

}