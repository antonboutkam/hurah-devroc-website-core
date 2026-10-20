<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore;

use League\CommonMark\CommonMarkConverter;
use RuntimeException;

/** Leest alleen expliciet gekozen contentbestanden binnen de website-map. */
final class ContentReader
{
    /** @param string $directory Absolute website-map buiten de documentroot. */
    public function __construct(private readonly string $directory)
    {
    }

    /**
     * @param string $relative Vast relatief pad uit een controller, nooit een vrije URL.
     * @return string Gecontroleerd absoluut bestandspad.
     * @throws RuntimeException Als het bestand ontbreekt of buiten de contentmap valt.
     */
    public function file(string $relative): string
    {
        $root = realpath($this->directory);
        $file = realpath($this->directory . '/' . $relative);
        if ($root === false || $file === false || !is_file($file)
            || !str_starts_with($file, $root . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Contentbestand ontbreekt of staat buiten de website-map.');
        }
        return $file;
    }

    /**
     * @param string $relative Relatief pad naar een Markdown-bestand.
     * @return string HTML zonder ruwe HTML-invoer en onveilige linkprotocollen.
     * @throws RuntimeException Als het bestand niet leesbaar is.
     */
    public function markdown(string $relative): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
        ]);
        return (string) $converter->convert($this->read($relative));
    }

    /**
     * @param string $relative Relatief pad naar JSON; de controller controleert de velden.
     * @return array<mixed> De gegevens voor de eigen module.
     * @throws \JsonException Bij ongeldige JSON.
     * @throws RuntimeException Als het bestand geen object of lijst bevat.
     */
    public function json(string $relative): array
    {
        $data = json_decode($this->read($relative), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new RuntimeException('Gebruik een JSON-object of lijst.');
        }
        return $data;
    }

    /**
     * @param string $relative Relatief contentpad.
     * @return string Onbewerkte bestandsinhoud.
     * @throws RuntimeException Als lezen niet lukt.
     */
    private function read(string $relative): string
    {
        $text = file_get_contents($this->file($relative));
        if ($text === false) {
            throw new RuntimeException('Het contentbestand kon niet worden gelezen.');
        }
        return $text;
    }
}
