<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use DevRoc\WebsiteCore\ContentReader;

/** Controleert contentgrenzen en filtering, zonder echte studentgegevens. */
final class ContentReaderTest extends TemporaryDirectoryTestCase
{
    /** Markdown mag opmaak bevatten maar geen script of javascript-link. */
    public function testMarkdownFiltersHtmlAndUnsafeLinks(): void
    {
        file_put_contents($this->directory . '/text.md', "## Titel\n\n**vet**\n\n<script>alert(1)</script>\n\n[klik](javascript:alert(1))\n");
        $html = (new ContentReader($this->directory))->markdown('text.md');
        self::assertStringContainsString('<h2>Titel</h2>', $html);
        self::assertStringContainsString('<strong>vet</strong>', $html);
        self::assertStringNotContainsString('<script', $html);
        self::assertStringNotContainsString('href="javascript:', $html);
    }

    /** Geldige JSON blijft data en wordt niet uitgevoerd. */
    public function testReadsJson(): void
    {
        file_put_contents($this->directory . '/data.json', '{"published":false}');
        self::assertSame(['published' => false], (new ContentReader($this->directory))->json('data.json'));
    }

    /** Ongeldige JSON mag niet stilzwijgend worden geaccepteerd. */
    public function testRejectsMalformedJson(): void
    {
        file_put_contents($this->directory . '/data.json', '{');
        $this->expectException(\JsonException::class);
        (new ContentReader($this->directory))->json('data.json');
    }

    /** Een scalar past niet in de afgesproken gegevensstructuur. */
    public function testRejectsScalarJson(): void
    {
        file_put_contents($this->directory . '/data.json', 'true');
        $this->expectException(\RuntimeException::class);
        (new ContentReader($this->directory))->json('data.json');
    }

    /** Een bestaand bestand buiten de root blijft via traversal onbereikbaar. */
    public function testRejectsTraversal(): void
    {
        mkdir($this->directory . '/content');
        file_put_contents($this->directory . '/outside.md', 'fictief');
        $this->expectException(\RuntimeException::class);
        (new ContentReader($this->directory . '/content'))->file('../outside.md');
    }

    /** Symlinks mogen de rootgrens niet omzeilen. */
    public function testRejectsOutsideSymlink(): void
    {
        mkdir($this->directory . '/content');
        file_put_contents($this->directory . '/outside.md', 'fictief');
        if (!@symlink($this->directory . '/outside.md', $this->directory . '/content/alias.md')) {
            self::markTestSkipped('Symlinks niet beschikbaar.');
        }
        $this->expectException(\RuntimeException::class);
        (new ContentReader($this->directory . '/content'))->file('alias.md');
    }
}
