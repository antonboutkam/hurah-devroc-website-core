<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore\Tests;

use DevRoc\WebsiteCore\TemplateRenderer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;

/** Legt de veilige standaardinstellingen van Twig vast. */
final class TemplateRendererTest extends TemporaryDirectoryTestCase
{
    /** Gewone templategegevens worden als tekst ontsnapt. */
    public function testEscapesHtml(): void
    {
        file_put_contents($this->directory . '/view.twig', '{{ value }}');
        self::assertSame('&lt;script&gt;', (new TemplateRenderer($this->directory))->render('view.twig', ['value' => '<script>']));
    }

    /** Onbekende variabelen geven een fout in plaats van onzichtbaar leeg resultaat. */
    public function testRequiresKnownVariables(): void
    {
        file_put_contents($this->directory . '/view.twig', '{{ missing }}');
        $this->expectException(RuntimeError::class);
        (new TemplateRenderer($this->directory))->render('view.twig');
    }

    /** De renderer mag niet buiten de ingestelde templatemap lezen. */
    public function testRejectsOutsideTemplate(): void
    {
        mkdir($this->directory . '/views');
        file_put_contents($this->directory . '/outside.twig', 'fictief');
        $this->expectException(LoaderError::class);
        (new TemplateRenderer($this->directory . '/views'))->render('../outside.twig');
    }
}
