<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore;

use Dotenv\Dotenv;

/**
 * Laadt de optionele .env uit de applicatie, ook wanneer dit package een symlink is.
 */
final class Environment
{
    /**
     * Bestaande serverinstellingen hebben voorrang op waarden uit het lokale bestand.
     *
     * @param string $projectRoot Absolute map met de website-map en eventuele .env.
     * @throws \Dotenv\Exception\InvalidFileException Bij een ongeldig env-bestand.
     */
    public static function load(string $projectRoot): void
    {
        Dotenv::createImmutable($projectRoot)->safeLoad();
    }

    /**
     * Leest een tekstinstelling zonder een database of domeinconfiguratie te vereisen.
     *
     * @param string $name Naam, bijvoorbeeld APP_NAME.
     * @param string $default Waarde wanneer de instelling ontbreekt.
     * @return string De ingestelde tekst of de standaardwaarde.
     */
    public static function get(string $name, string $default = ''): string
    {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
