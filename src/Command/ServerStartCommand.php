<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Start de bestaande PHP-servertechniek met een expliciete applicatiedocumentroot.
 */
final class ServerStartCommand extends Command
{
    /**
     * @param string $publicDirectory Absolute map met index.php en publieke assets.
     */
    public function __construct(private readonly string $publicDirectory)
    {
        parent::__construct('server:start');
    }

    /** Beschrijft het commando en de instelbare lokale poort voor console --help. */
    protected function configure(): void
    {
        $this
            ->setDescription('Start de lokale website; stop met Ctrl+C.')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'Lokale poort (1024 t/m 65535).', '8080');
    }

    /**
     * Start een apart PHP-proces en geeft het resultaat door aan de terminal.
     *
     * @param InputInterface $input Opties uit het consolecommando.
     * @param OutputInterface $output Terminal waarin ook servermeldingen verschijnen.
     * @return int Nul bij succes, anders een foutcode.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = filter_var($input->getOption('port'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1024, 'max_range' => 65535],
        ]);
        if ($port === false) {
            $output->writeln('<error>Kies een gehele poort tussen 1024 en 65535.</error>');

            return Command::INVALID;
        }

        $public = realpath($this->publicDirectory);
        if ($public === false || !is_file($public . '/index.php')) {
            $output->writeln('<error>De documentroot bevat geen index.php.</error>');

            return Command::FAILURE;
        }

        $output->writeln(sprintf('Open http://127.0.0.1:%d/ (stoppen: Ctrl+C)', $port));
        $process = new Process([
            PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', $public, $public . '/index.php',
        ], $public);
        $process->setTimeout(null);

        return $process->run(static function (string $type, string $buffer) use ($output): void {
            $output->write($buffer, false, OutputInterface::OUTPUT_RAW);
        });
    }
}
