<?php

namespace App;

use RuntimeException;
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
        $inputAction = $input->getArgument('action');
        switch ($inputAction) {
            case ActionsType::CALCULATE->value:
                $output->writeln($actions->calculate());
                break;
            case ActionsType::REMOVE->value:
                $product = $this->ask($input, $output, "Enter product's name: ");
                if ($actions->remove($product)) {
                    $output->writeln('Successful removed');
                }
                break;
            case ActionsType::ADD->value:
            case ActionsType::CHANGE->value:
                $product = $this->ask($input, $output, "Enter product's name:");
                $price = (int)$this->ask($input, $output, "Enter product's price:");
                if ($inputAction === ActionsType::ADD->value && $actions->add($product, $price)) {
                    $output->writeln('Successful added');
                } elseif ($inputAction === ActionsType::CHANGE->value && $actions->change($product, $price)) {
                    $output->writeln('Successful changed');
                }
                break;
            default:
                throw new RuntimeException('Wrong action, use: add, remove, change, calculate');
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