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

use Symfony\Component\Templating\EngineInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class TwigTemplateContentObject
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var TypoScriptService
     */
    private $typoScriptService;

    public function __construct(EngineInterface $templating, TypoScriptService $typoScriptService)
    {
        $this->templating = $templating;
        $this->typoScriptService = $typoScriptService;
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
     * 10 = FLUIDTEMPLATE
     * 10.template = layouts/default.html.twig
     * 10.variables {
     *   mylabel = TEXT
     *   mylabel.value = Label from TypoScript coming
     * }
     *
     * @param array $conf Array of TypoScript properties
     *
     * @return string The rendered output
     */
    public function render(array $conf, ContentObjectRenderer $cObj): string
    {
        if (!is_array($conf)) {
            $conf = [];
        }

        $template = $this->getTemplate($conf, $cObj);

        $variables = $this->getContentObjectVariables($conf, $cObj);
        $variables['settings'] = $this->transformSettings($conf);

        return $this->templating->render($template, $variables);
    }

    private function getTemplate(array $conf, ContentObjectRenderer $cObj): string
    {
        if ((!empty($conf['template']) || !empty($conf['template.']))) {
            return isset($conf['template.'])
                ? $cObj->stdWrap(isset($conf['template']) ? $conf['template'] : '', $conf['template.'])
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
            if (is_array($cObjType)) {
                continue;
            }

            if (!in_array($variableName, $reservedVariables, true)) {
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
}
