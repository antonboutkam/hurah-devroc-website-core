<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use PHPUnit\Framework\TestCase;

/** Geeft iedere test eigen tijdelijke bestanden; volgt geen symlinks bij opruimen. */
abstract class TemporaryDirectoryTestCase extends TestCase
{
    protected string $directory;

    /** Maakt een unieke fixturemap buiten de repository. */
    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/website-core-test-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    /** Ruimt alleen de eigen fixtures op, ook wanneer een assertion faalt. */
    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);
    }

    /** @param string $directory Eigen fixturemap die verwijderd mag worden. */
    private function removeDirectory(string $directory): void
    {
        foreach (new \FilesystemIterator($directory) as $entry) {
            if ($entry->isDir() && !$entry->isLink()) {
                $this->removeDirectory($entry->getPathname());
            } else {
                unlink($entry->getPathname());
            }
        }
        rmdir($directory);
    }
}
