<?php

namespace App\Services;

use App\Exceptions\ValidateException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class ValidateInputService
{
    public function __construct(
        private readonly QuestionHelper $questionHelper,
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
    ) {
    }

    public static function make(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output): self
    {
        return new self($questionHelper, $input, $output);
    }

    public function askValidatedInteger(string $question): int
    {
        $questionObject = new Question($question);
        $questionObject->setValidator(function (string $answer) {
            if (!preg_match('/^-?\d+$/', $answer)) {
                throw new ValidateException('Please enter numeric');
            }
            return (int)$answer;
        });
        return (int)$this->ask($questionObject);
    }

    public function askValidatedString(string $question)
    {
        $questionObject = new Question($question);
        return $this->ask($questionObject);
    }

    public function ask(Question $question): mixed
    {
        return $this->questionHelper->ask($this->input, $this->output, $question);
    }


}