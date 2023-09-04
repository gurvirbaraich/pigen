<?php

namespace Pigen\Foundation\CommandLine\Commands;

use Carbon\Carbon;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Terminal;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
  private bool $isRunning = false;

  protected $colors = [
    'ANY' => 'red',
    'GET' => 'blue',
    'HEAD' => '#6C7280',
    'OPTIONS' => '#6C7280',
    'POST' => 'yellow',
    'PUT' => 'yellow',
    'PATCH' => 'yellow',
    'DELETE' => 'red',
  ];

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $process = new Process($this->serveCommand(), ABSPATH . '/public');

    $process->start(
      fn ($_, $buffer) => $this->handleOutput($buffer, $output)
    );

    while ($process->isRunning()) {
      sleep(1);
    }

    return $process->getExitCode();
  }

  private function handleOutput($buffer, OutputInterface $output)
  {
    if (!$this->isRunning) {
      $this->isRunning = true;
      $serverStartRegex = '/\[(.*)\].*Development Server \((.*)\) started/m';

      $details = explode(
        ",",
        preg_replace($serverStartRegex, "$1,$2", $buffer)
      );

      if (count($details) == 2) {
        $output->writeln("\n<bg=bright-blue> INFO </> Server running on [" . trim($details[1]) . "]\n");
      }

      return;
    }

    $buffer = explode("\n", $buffer);

    foreach ($buffer as $line) {
      if (str_contains($line, "GET") || str_contains($line, "POST")) {
        $terminalWidth = $this->getTerminalWidth();
        $requestRegex = '/\[.*\].*\[(.*)\]: (\w+) (.*)/m';

        $request = explode(
          ",",
          preg_replace($requestRegex, "$1,$2,$3", $line)
        );

        $timestamp = Carbon::now();
        $out = sprintf("%s %s %s %s", $timestamp, $request[1], $request[2], $request[0]);
        
        $dots = $terminalWidth - mb_strlen($out) - 1;
        
        $timestamp = explode(" ", $timestamp);
        $timestamp = "<fg=gray>" .  $timestamp[0] . "</> " . $timestamp[1];
        $out = sprintf("%s <fg=". $this->colors[$request[1]] .">%s</> %s %s", $timestamp, $request[1], $request[2], $request[0]);

        $output->writeln(
          sprintf("%s <fg=gray>%s</>", $out, str_repeat(".", $dots < 0 ? 0 : $dots))
        );
      }
    }
  }

  private function serveCommand()
  {
    $serve = [
      (new PhpExecutableFinder)->find(false),
      '-S',
      '0.0.0.0:8087'
    ];

    return $serve;
  }

  private function getTerminalWidth()
  {
    return (new Terminal())->getWidth();
  }
}
