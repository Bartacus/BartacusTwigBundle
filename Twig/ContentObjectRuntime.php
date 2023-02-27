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

namespace Bartacus\Bundle\TwigBundle\Twig;

use Psr\Http\Message\ServerRequestInterface;
use Twig\Extension\RuntimeExtensionInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentObjectRuntime implements RuntimeExtensionInterface
{
    private array $typoScriptSetup;
    private ?TypoScriptFrontendController $tsfeBackup = null;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->typoScriptSetup = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
    }

    /**
     * Renders the TypoScript object in the given TypoScript setup path.
     *
     * @param string              $typoScriptObjectPath The TypoScript setup path of the TypoScript object to render
     * @param object|array|string $data                 The data to be used for rendering the cObject. Can be an
     *                                                  object, array or string.
     * @param string              $currentValueKey      The key of the value mapped as current in the data. It will be
     *                                                  used when using current=1
     * @param string              $table                The table name associated with "data" argument. Typically
     *                                                  tt_content or one of your custom tables. This argument should
     *                                                  be set if rendering a FILES cObject where file references are
     *                                                  used, or if the data argument is a database record.
     */
    public function cObject(string $typoScriptObjectPath, object|array|string $data = [], string $currentValueKey = '', string $table = ''): string
    {
        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $isBackendMode = ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();
        } else {
            $isBackendMode = true;
        }

        if ($isBackendMode) {
            $this->simulateFrontendEnvironment();
        }

        $currentValue = null;

        if (\is_object($data)) {
            $data = ObjectAccess::getGettableProperties($data);
        } elseif (\is_string($data) || is_numeric($data)) {
            $currentValue = (string) $data;
            $data = [$data];
        }

        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObj->start($data, $table);

        if (null !== $currentValue) {
            $cObj->setCurrentVal($currentValue);
        } elseif (null !== $currentValueKey && isset($data[$currentValueKey])) {
            $cObj->setCurrentVal($data[$currentValueKey]);
        }

        $pathSegments = GeneralUtility::trimExplode('.', $typoScriptObjectPath);
        $lastSegment = array_pop($pathSegments);
        $setup = $this->typoScriptSetup;

        foreach ($pathSegments as $segment) {
            if (!\array_key_exists($segment.'.', $setup)) {
                throw new \InvalidArgumentException(sprintf('TypoScript object path "%s" does not exist', $typoScriptObjectPath));
            }

            $setup = $setup[$segment.'.'];
        }

        $content = $cObj->cObjGetSingle($setup[$lastSegment], $setup[$lastSegment.'.']);

        if ($isBackendMode) {
            $this->resetFrontendEnvironment();
        }

        return $content;
    }

    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by {@see simulateFrontendEnvironment()}.
     *
     * @see simulateFrontendEnvironment()
     */
    protected function resetFrontendEnvironment(): void
    {
        $GLOBALS['TSFE'] = $this->tsfeBackup;
    }

    /**
     * Sets the $TSFE->cObjectDepthCounter in backend mode.
     *
     * This somewhat hacky work around is currently needed because the {@see ContentObjectRenderer::cObjGetSingle()}
     * function relies on this setting.
     */
    private function simulateFrontendEnvironment(): void
    {
        $this->tsfeBackup = $GLOBALS['TSFE'] ?? null;

        $GLOBALS['TSFE'] = new \stdClass();
    }
}
