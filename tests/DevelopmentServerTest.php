<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use DevRoc\WebsiteCore\DevelopmentServer;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Process\Process;

/** Beschermt de publieke root, inclusief de gevonden symlink-regressie. */
final class DevelopmentServerTest extends TemporaryDirectoryTestCase
{
    /** @return iterable<string, array{string, bool}> URL's en hun verwachte toelating. */
    public static function paths(): iterable
    {
        yield 'css' => ['/style.css', true];
        yield 'querystring' => ['/style.css?v=1', true];
        yield 'verborgen bestand' => ['/.env', false];
        yield 'gecodeerde punt' => ['/%2eenv', false];
        yield 'verborgen map' => ['/.private/config.txt', false];
        yield 'traversal' => ['/../outside.txt', false];
        yield 'gecodeerde traversal' => ['/%2e%2e/outside.txt', false];
        yield 'PHP' => ['/index.php', false];
        yield 'hoofdletters PHP' => ['/index.PHP', false];
        yield 'nulbyte' => ['/style.css%00', false];
        yield 'backslash' => ['/a%5cstyle.css', false];
        yield 'ontbreekt' => ['/missing.css', false];
        yield 'map' => ['/', false];
    }

    /** @param string $uri Test-URL. @param bool $allowed Verwachte toelating. */
    #[DataProvider('paths')]
    public function testPublicPaths(string $uri, bool $allowed): void
    {
        $this->fixtures();
        self::assertSame($allowed, DevelopmentServer::isStaticFile($this->directory . '/public', $uri));
    }

    /** Controleert zowel verborgen symlinkdoelen als gewone toegestane assets. */
    public function testSymlinkTargetsAreChecked(): void
    {
        $this->fixtures();
        $public = $this->directory . '/public';
        $this->link($public . '/.env', $public . '/env.txt');
        $this->link($public . '/.private', $public . '/alias');
        $this->link($this->directory . '/outside.txt', $public . '/outside.txt');
        $this->link($public . '/index.php', $public . '/source.txt');
        $this->link($public . '/style.css', $public . '/theme.css');
        foreach (['/env.txt', '/alias/config.txt', '/outside.txt', '/source.txt'] as $uri) {
            self::assertFalse(DevelopmentServer::isStaticFile($public, $uri), $uri);
        }
        self::assertTrue(DevelopmentServer::isStaticFile($public, '/theme.css'));
    }

    /** Bewijst via echte HTTP dat verborgen symlinkdoelen niet worden uitgeleverd. */
    public function testHttpCannotReadHiddenSymlinkTargets(): void
    {
        $this->fixtures();
        $public = $this->directory . '/public';
        $this->link($public . '/.env', $public . '/env.txt');
        $this->link($public . '/.private', $public . '/alias');
        file_put_contents($public . '/index.php', '<?php require ' . var_export(dirname(__DIR__) . '/vendor/autoload.php', true)
            . '; if (DevRoc\\WebsiteCore\\DevelopmentServer::isStaticFile(__DIR__, $_SERVER["REQUEST_URI"])) { return false; }'
            . ' http_response_code(404); echo "Niet gevonden";');
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertIsResource($socket);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        $process = new Process([PHP_BINARY, '-S', $address, '-t', $public, $public . '/index.php']);
        $process->start();
        try {
            $ready = false;
            for ($attempt = 0; $attempt < 100; ++$attempt) {
                $connection = @stream_socket_client('tcp://' . $address, $errno, $error, 0.1);
                if ($connection !== false) {
                    fclose($connection);
                    $ready = true;
                    break;
                }
                usleep(20000);
            }
            self::assertTrue($ready, 'Lokale PHP-server start niet: ' . $process->getErrorOutput());
            foreach (['/.env' => 404, '/env.txt' => 404, '/alias/config.txt' => 404, '/style.css' => 200] as $uri => $status) {
                $connection = stream_socket_client('tcp://' . $address, $errno, $error, 2);
                self::assertIsResource($connection);
                stream_set_timeout($connection, 2);
                fwrite($connection, "GET $uri HTTP/1.0\r\nHost: localhost\r\nConnection: close\r\n\r\n");
                $response = stream_get_contents($connection);
                fclose($connection);
                self::assertStringContainsString(' ' . $status . ' ', strtok($response, "\r\n"), $uri);
                self::assertStringNotContainsString('FICTIEF_GEHEIM', $response);
            }
        } finally {
            $process->stop();
        }
    }

    /** Maakt uitsluitend fictieve gevoelige bestanden aan. */
    private function fixtures(): void
    {
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/public/.private');
        foreach (['public/.env', 'public/.private/config.txt', 'outside.txt'] as $path) {
            file_put_contents($this->directory . '/' . $path, 'FICTIEF_GEHEIM');
        }
        file_put_contents($this->directory . '/public/style.css', 'body { color: blue; }');
        file_put_contents($this->directory . '/public/index.php', '<?php echo "test";');
        file_put_contents($this->directory . '/public/index.PHP', '<?php echo "test";');
    }

    /** @param string $target Fixturedoel. @param string $link Fixturealias. */
    private function link(string $target, string $link): void
    {
        if (!@symlink($target, $link)) {
            self::markTestSkipped('Deze omgeving staat geen symlinks toe; gebruik Linux CI voor deze regressietest.');
        }
    }
}
