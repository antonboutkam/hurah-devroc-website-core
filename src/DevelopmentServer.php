<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore;

/**
 * Herkent publieke assets die de ingebouwde PHP-server zelf mag uitleveren.
 */
final class DevelopmentServer
{
    /**
     * Staat alleen bestaande, niet-verborgen assets binnen de documentroot toe.
     *
     * PHP-bestanden lopen altijd via de front controller. Ook symlinks naar
     * bestanden buiten public en paden met een verborgen segment worden geweigerd.
     *
     * @param string $publicDirectory Absolute documentroot van deze applicatie.
     * @param string $requestUri De URL inclusief een eventuele querystring.
     * @return bool Of het routerscript false mag teruggeven aan de PHP-server.
     */
    public static function isStaticFile(string $publicDirectory, string $requestUri): bool
    {
        $path = rawurldecode(explode('?', $requestUri, 2)[0]);
        if (str_contains($path, "\0") || str_contains($path, '\\')) {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if (str_starts_with($segment, '.')) {
                return false;
            }
        }

        $root = realpath($publicDirectory);
        $file = realpath($publicDirectory . '/' . ltrim($path, '/'));
        if ($root === false || $file === false || !is_file($file)
            || !str_starts_with($file, $root . DIRECTORY_SEPARATOR)) {
            return false;
        }

        // Een openbare alias kan naar een verborgen bestand of verborgen map wijzen.
        // Controleer daarom ook het opgeloste pad, zonder de documentroot zelf.
        $resolvedPath = substr($file, strlen($root) + 1);
        foreach (explode(DIRECTORY_SEPARATOR, $resolvedPath) as $segment) {
            if (str_starts_with($segment, '.')) {
                return false;
            }
        }

        return !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['php', 'phtml', 'phar'], true);
    }
}
