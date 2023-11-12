<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        if ($input->getArgument('action') === 'add') {
            if ($actions->add('sdsd', 10)) {
                $output->writeln('success');
            }
        }

        return Command::SUCCESS;
    }
}