<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use DevRoc\WebsiteCore\Command\ServerStartCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Tester\CommandTester;

/** Valideert CLI-invoer voordat een serverproces kan worden gestart. */
final class ServerStartCommandTest extends TemporaryDirectoryTestCase
{
    /** @return iterable<array{string}> Ongeldige poortwaarden. */
    public static function invalidPorts(): iterable
    {
        foreach (['0', '1023', '65536', 'abc', '8080;echo fout'] as $value) {
            yield [$value];
        }
    }

    /** @param string $port Ongeldige invoer die een foutcode moet geven. */
    #[DataProvider('invalidPorts')]
    public function testRejectsInvalidPort(string $port): void
    {
        $tester = new CommandTester(new ServerStartCommand($this->directory));
        self::assertSame(2, $tester->execute(['--port' => $port]));
    }

    /** Zonder front controller mag de server niet starten. */
    public function testRejectsMissingEntrypoint(): void
    {
        $tester = new CommandTester(new ServerStartCommand($this->directory));
        self::assertSame(1, $tester->execute(['--port' => '8080']));
    }
}
