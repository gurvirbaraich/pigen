<?php

namespace Pigen\Foundation\CommandLine\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'insult')]
class InsultCommand extends Command
{
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    // Variable to indicate whether the system is connected to internet.
    $internetConnected = @fsockopen("www.google.com", 80);

    if (!$internetConnected) {
      // Printing the error message
      $output->writeln("The system is not connected.");

      // Exiting the script with status code of 1.
      return Command::FAILURE;
    }

    // Get the json content as plain text.
    $contents = file_get_contents("https://evilinsult.com/generate_insult.php?lang=en&type=json");

    // Converting plain text to json.
    $json = json_decode($contents);

    // Insulting the developer.
    $output->writeln($json->insult);

    // Exiting the script with status code of 0.
    return Command::SUCCESS;
  }
}
