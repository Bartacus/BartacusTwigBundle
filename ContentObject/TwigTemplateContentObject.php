<?php

declare(strict_types=1);

/*
 * This file is part of the Bartacus Twig bundle.
 *
 * Copyright (c) Emily Karisch
 *
 * This bundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This bundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this bundle. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Bartacus\Bundle\TwigBundle\ContentObject;

use Bartacus\Bundle\BartacusBundle\Bootstrap\SymfonyBootstrap;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Template;
use Twig\TemplateWrapper;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class TwigTemplateContentObject
{
    /**
     * @var TypoScriptService
     */
    private $typoScriptService;

    /**
     * @var PageRenderer
     */
    private $pageRenderer;

    public function __construct(TypoScriptService $typoScriptService, PageRenderer $pageRenderer)
    {
        $this->typoScriptService = $typoScriptService;
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param string $name  The content object name, eg. "TWIGTEMPLATE"
     * @param array  $conf  The array with TypoScript properties for the content object
     * @param string $TSkey a string label used for the internal debugging tracking
     */
    public function cObjGetSingleExt(string $name, array $conf, $TSkey, ContentObjectRenderer $cObj): string
    {
        if ('TWIGTEMPLATE' === $name) {
            return $this->render($conf, $cObj);
        }

        return '';
    }

    /**
     * Rendering the cObject, TWIGTEMPLATE.
     *
     * Configuration properties:
     * - template string+stdWrap The Twig template file
     * - variable array of cObjects, the keys are the variable names in twig
     *
     * Example:
     * 10 = TWIGTEMPLATE
     * 10.template = layouts/default.html.twig
     * 10.variables {
     *   mylabel = TEXT
     *   mylabel.value = Label from TypoScript coming
     * }
     *
     * @param array $conf Array of TypoScript properties
     *
     * @throws LoaderError  When the template cannot be found
     * @throws RuntimeError When a previously generated cache is corrupted
     * @throws SyntaxError  When an error occurred during compilation
     *
     * @return string The rendered output
     */
    public function render(array $conf, ContentObjectRenderer $cObj): string
    {
        if (!\is_array($conf)) {
            $conf = [];
        }

        $name = $this->getTemplate($conf, $cObj);

        $variables = $this->getContentObjectVariables($conf, $cObj);
        $variables['settings'] = $this->transformSettings($conf);
        $twig = $this->getTwigEnvironment();
        /** @var TemplateWrapper $template */
        $template = $twig->load($name);
        $context = $twig->mergeGlobals($variables);

        $content = $this->renderBlock($template, 'body', $context);
        $this->renderIntoPageRenderer($template, $context);

        return $content;
    }

    private function getTemplate(array $conf, ContentObjectRenderer $cObj): string
    {
        if ((!empty($conf['template']) || !empty($conf['template.']))) {
            return isset($conf['template.'])
                ? $cObj->stdWrap($conf['template'] ?? '', $conf['template.'])
                : $conf['template'];
        }

        return 'layouts/default.html.twig';
    }

    private function getContentObjectVariables(array $conf, ContentObjectRenderer $cObj): array
    {
        $variables = [];
        $reservedVariables = ['data', 'current', 'settings'];

        // Accumulate the variables to be process and loop them through cObjGetSingle
        $variablesToProcess = (array) $conf['variables.'];
        foreach ($variablesToProcess as $variableName => $cObjType) {
            if (\is_array($cObjType)) {
                continue;
            }

            if (!\in_array($variableName, $reservedVariables, true)) {
                $variables[$variableName] = $cObj->cObjGetSingle($cObjType, $variablesToProcess[$variableName.'.']);
            } else {
                throw new \InvalidArgumentException(
                    'Cannot use reserved name "'.$variableName.'" as variable name in TWIGTEMPLATE.'
                );
            }
        }

        $variables['data'] = $cObj->data;
        $variables['current'] = $cObj->data[$cObj->currentValKey];

        return $variables;
    }

    private function transformSettings(array $conf): array
    {
        if (isset($conf['settings.'])) {
            return $this->typoScriptService->convertTypoScriptArrayToPlainArray($conf['settings.']);
        }

        return [];
    }

    private function renderIntoPageRenderer(TemplateWrapper $template, array $context): void
    {
        $header = $this->renderBlock($template, 'header', $context);
        $footer = $this->renderBlock($template, 'footer', $context);

        if (!empty(trim($header))) {
            $this->pageRenderer->addHeaderData($header);
        }

        if (!empty(trim($footer))) {
            $this->pageRenderer->addFooterData($footer);
        }
    }

    /**
     * Renders a Twig block with error handling.
     *
     * This avoids getting some leaked buffer when an exception occurs.
     * Twig blocks are not taking care of it as they are not meant to be rendered directly.
     *
     * @throws \Exception
     */
    private function renderBlock(TemplateWrapper $template, $block, array $context): string
    {
        $level = ob_get_level();
        ob_start();

        try {
            $rendered = $template->renderBlock($block, $context);
            ob_end_clean();

            return $rendered;
        } catch (\RuntimeException $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        } catch (RuntimeError $e) {
            // '404 not found' exceptions thrown by the controller are converted to a
            // \Twig_Error_Runtime in the 'renderBlock', but the real exception is still available as the previous one.
            // If thrown by a controller, then the previous exception is typically a ImmediateResponseException which
            // includes the rendered error page.
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e->getPrevious() instanceof ImmediateResponseException ? $e->getPrevious() : $e;
        }
    }

    private function getTwigEnvironment(): Environment
    {
        return SymfonyBootstrap::getKernel()->getContainer()->get('twig');
    }
}
