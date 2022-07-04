<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Page\Exceptions\PageException;
use Sonder\Plugins\Page\PageValuesObject;

final class PagePlugin
{
    private const DEFAULT_TEMPLATE_NAME = 'default';

    private const DEFAULT_TEMPLATE_PAGE = 'page';

    private const DEFAULT_NOT_FOUND_URL = '/error/404/';

    private const TEMPLATES_DIR = __DIR__ . '/../../../res/tpl';

    private const STATIC_PAGES_DIR = __DIR__ . '/../../../res/pages';

    private const STATIC_PAGE_DATA_SECTIONS = 2;

    /**
     * @param string|null $staticPageName
     * @param string|null $templateName
     * @param string|null $templatePage
     * @return PageValuesObject
     * @throws PageException
     */
    final public function getVO(
        ?string $staticPageName = null,
        ?string $templateName = null,
        ?string $templatePage = null
    ): PageValuesObject {
        $staticPagePath = $this->_getStaticPagePath($staticPageName);

        if (!$this->_isTemplatePageExists($templateName, $templatePage)) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_TEMPLATE_NOT_EXISTS,
                PageException::CODE_PLUGIN_TEMPLATE_NOT_EXISTS
            );
        }

        $staticPageData = $this->_getStaticPageData($staticPagePath);

        return new PageValuesObject($staticPageData);
    }

    /**
     * @param string|null $staticPageName
     * @return string
     * @throws PageException
     */
    private function _getStaticPagePath(?string $staticPageName = null): string
    {
        if (empty($staticPageName)) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET,
                PageException::CODE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET
            );
        }

        $notFoundUrl = PagePlugin::DEFAULT_NOT_FOUND_URL;

        if (
            defined('APP_NOT_FOUND_URL') &&
            !empty(APP_NOT_FOUND_URL) &&
            $_SERVER['REQUEST_URI'] != APP_NOT_FOUND_URL
        ) {
            $notFoundUrl = APP_NOT_FOUND_URL;
        }

        $staticPagePath = sprintf(
            '%s/%s.md',
            PagePlugin::STATIC_PAGES_DIR,
            $staticPageName
        );

        if (!file_exists($staticPagePath) || !is_file($staticPagePath)) {
            header(sprintf('Location: %s', $notFoundUrl));
            exit(0);
        }

        return $staticPagePath;
    }

    /**
     * @param string|null $templateName
     * @param string|null $templatePage
     * @return bool
     */
    private function _isTemplatePageExists(
        ?string $templateName = null,
        ?string $templatePage = null
    ): bool {
        if (empty($templateName)) {
            $templateName = PagePlugin::DEFAULT_TEMPLATE_NAME;
        }

        if (empty($templatePage)) {
            $templatePage = PagePlugin::DEFAULT_TEMPLATE_PAGE;
        }

        $templatePagePath = sprintf(
            '%s/%s/pages/%s.phtml',
            PagePlugin::TEMPLATES_DIR,
            $templateName,
            $templatePage
        );

        return file_exists($templatePagePath) && is_file($templatePagePath);
    }

    /**
     * @param string $staticPagePath
     * @return array
     * @throws PageException
     */
    private function _getStaticPageData(string $staticPagePath): array
    {
        $markupPlugin = new MarkupPlugin();

        $staticPageData = (string)file_get_contents($staticPagePath);
        $staticPageData = explode("\n\n===\n\n", $staticPageData);

        if (
            empty($staticPageData) ||
            count($staticPageData) != PagePlugin::STATIC_PAGE_DATA_SECTIONS
        ) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT,
                PageException::CODE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT
            );
        }

        $staticPageData = [
            'title' => $staticPageData[0],
            'content' => $staticPageData[1]
        ];

        $staticPageData['title'] = (new SecurityPlugin)->escapeInput(
            $staticPageData['title']
        );

        $staticPageData['content'] = $markupPlugin->normalizeText(
            $staticPageData['content']
        );

        $staticPageData['content'] = $markupPlugin->normalizeSyntax(
            $staticPageData['content']
        );

        $staticPageData['content'] = $markupPlugin->markup2HTML(
            $staticPageData['content']
        );

        return $staticPageData;
    }
}
