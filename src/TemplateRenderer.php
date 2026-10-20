<?php

declare(strict_types=1);

namespace DevRoc\WebsiteCore;

use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;

/**
 * Richt Twig hetzelfde in voor beide sites; de views blijven in de applicatie.
 */
final class TemplateRenderer
{
    private Twig $twig;

    /**
     * @param string $modulesDirectory Absolute applicatiemap met moduleviews.
     */
    public function __construct(string $modulesDirectory)
    {
        $this->twig = new Twig(new FilesystemLoader($modulesDirectory), [
            'autoescape' => 'html',
            'strict_variables' => true,
            'debug' => false,
            'cache' => false,
        ]);
    }

    /**
     * Rendert een view zoals Home/view.twig met gegevens van de controller.
     *
     * @param string $view Pad vanaf de modules-map.
     * @param array<string, mixed> $data Waarden voor de Twig-variabelen.
     * @return string HTML voor de response.
     * @throws \Twig\Error\Error Wanneer de view ontbreekt of niet geldig is.
     */
    public function render(string $view, array $data = []): string
    {
        return $this->twig->render($view, $data);
    }
}
