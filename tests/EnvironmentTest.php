<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use DevRoc\WebsiteCore\Environment;

/** Test optionele env-inlezing zonder bestaande gebruikersinstellingen te wijzigen. */
final class EnvironmentTest extends TemporaryDirectoryTestCase
{
    /** Afwezigheid van een .env is toegestaan. */
    public function testMissingFileIsOptional(): void
    {
        Environment::load($this->directory);
        self::assertSame('standaard', Environment::get('CORE_TEST_' . bin2hex(random_bytes(8)), 'standaard'));
    }

    /** Bestaande omgevingswaarden gaan voor waarden uit een bestand. */
    public function testLoadsWithoutOverwritingExistingValue(): void
    {
        $key = 'CORE_TEST_' . bin2hex(random_bytes(8));
        file_put_contents($this->directory . '/.env', "$key=uit-bestand\n");
        try {
            Environment::load($this->directory);
            self::assertSame('uit-bestand', Environment::get($key));
            $_ENV[$key] = 'bestaand';
            Environment::load($this->directory);
            self::assertSame('bestaand', Environment::get($key));
        } finally {
            unset($_ENV[$key], $_SERVER[$key]);
        }
    }
}
