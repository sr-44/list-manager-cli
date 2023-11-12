<?php

namespace App;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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
        // Set style for output text
        $output->getFormatter()->setStyle('success', new OutputFormatterStyle('green', options: ['bold', 'blink']));
        $output->getFormatter()->setStyle('error', new OutputFormatterStyle('red', options: ['bold', 'blink']));
        $inputAction = $input->getArgument('action');


        try {
            $actions = new Actions($input->getArgument('filename'));

            switch ($inputAction) {
                case ActionsType::CALCULATE->value:
                    $output->writeln($actions->calculate());
                    break;

                case ActionsType::REMOVE->value:
                    $product = $this->ask($input, $output, "<fg=yellow>Enter product's name: </>", productExists: true);
                    if ($actions->remove($product)) {
                        $output->writeln('<success>Successful removed</>');
                    }
                    break;

                case ActionsType::ADD->value:
                case ActionsType::CHANGE->value:

                    $productExists = $inputAction === ActionsType::CHANGE->value;
                    $product = $this->ask($input, $output, "<fg=yellow>Enter product's name: </>", productExists: $productExists);
                    $price = (int)$this->ask($input, $output, "<fg=yellow>Enter product's price: </>", intValidate: true);

                    if ($inputAction === ActionsType::ADD->value && $actions->add($product, $price)) {
                        $output->writeln('<success>Successful added</>');
                    } elseif ($inputAction === ActionsType::CHANGE->value && $actions->change($product, $price)) {
                        $output->writeln('<success>Successful changed</>');
                    }
                    break;

                default:
                    throw new RuntimeException('Wrong action, use: add, remove, change, calculate');
            }

        } catch (RuntimeException $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function ask(InputInterface $input, OutputInterface $output, $question, bool $intValidate = false, bool $productExists = null)
    {
        $helper = $this->getHelper('question');
        $question = new Question($question);

        if ($intValidate) {
            $validator = static function (string $answer) {
                if (!preg_match('/^-?\d+$/', $answer)) {
                    throw new RuntimeException('Please enter numeric');
                }
                return (int)$answer;
            };
        }

        if ($productExists === true) {
            $validator = function (string $answer) use ($input) {
                if (!$this->checkProduct($input, $answer)) {
                    throw new RuntimeException('This product does not exists');
                }
                return $answer;
            };
        } elseif ($productExists === false) {
            $validator = function (string $answer) use ($input) {
                if ($this->checkProduct($input, $answer)) {
                    throw new RuntimeException('This product is already exists');
                }
                return $answer;
            };
        }

        if (isset($validator)) {
            $question->setValidator($validator);
        }

        return $helper->ask($input, $output, $question);
    }


    private function checkProduct(InputInterface $input, $answer): bool
    {
        return str_contains(file_get_contents($input->getArgument('filename')), $answer);
    }

}