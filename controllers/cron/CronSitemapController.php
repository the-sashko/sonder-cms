<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\CronBaseController;
use Sonder\Core\RequestObject;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Models\Article;
use Sonder\Models\Article\ArticleValuesObject;
use Sonder\Models\Tag;
use Sonder\Models\Tag\TagValuesObject;
use Sonder\Models\Topic;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\SitemapPlugin;

final class CronSitemapController extends CronBaseController
{
    private const SITEMAP_STATIC_PAGE_FREQUENCY = 'weekly';
    private const SITEMAP_STATIC_PAGE_PRIORITY = '0.7';

    private const SITEMAP_MAIN_PAGE_FREQUENCY = 'hourly';
    private const SITEMAP_MAIN_PAGE_PRIORITY = '0.8';

    private const SITEMAP_TOPIC_FREQUENCY = 'hourly';
    private const SITEMAP_TOPIC_PRIORITY = '0.9';

    private const SITEMAP_TAG_FREQUENCY = 'hourly';
    private const SITEMAP_TAG_PRIORITY = '0.9';

    private const SITEMAP_ARTICLE_FREQUENCY = 'daily';
    private const SITEMAP_ARTICLE_PRIORITY = '1.0';

    private const PAGINATION_PATTERN = 'page-';

    /**
     * @var SitemapPlugin
     */
    private SitemapPlugin $_sitemapPlugin;

    final public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        /**
         * @var $sitemapPlugin SitemapPlugin
         */
        $sitemapPlugin = $this->getPlugin('sitemap');

        $this->_sitemapPlugin = $sitemapPlugin;
    }

    /**
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function jobGenerate(): void
    {
        $sitemaps = [];

        $this->_generateArticleSitemaps($sitemaps);
        $this->_generateTopicsSitemaps($sitemaps);
        $this->_generateTagsSitemaps($sitemaps);
        $this->_generateStaticPagesSitemaps($sitemaps);

        $mainPageLinks = [
            sprintf('%s/', $this->request->getHost()),
            sprintf(
                '%s%s',
                $this->request->getHost(),
                TopicValuesObject::TOPICS_LINK
            ),
            sprintf(
                '%s%s',
                $this->request->getHost(),
                TagValuesObject::TAGS_LINK
            ),
        ];

        $sitemap = 'main';

        $this->_sitemapPlugin->saveLinksToSitemap(
            $sitemap,
            $mainPageLinks,
            CronSitemapController::SITEMAP_MAIN_PAGE_FREQUENCY,
            CronSitemapController::SITEMAP_MAIN_PAGE_PRIORITY
        );

        $sitemaps[] = $sitemap;

        if (!empty($sitemaps)) {
            $this->_sitemapPlugin->saveSummarySitemap(
                'sitemap',
                $sitemaps,
                $this->request->getHost()
            );
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _generateArticleSitemaps(array &$sitemaps): void
    {
        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        $page = 1;

        $articleVOs = $articleModel->getArticlesByPage(
            $page,
            true,
            true
        );

        while (!empty($articleVOs)) {
            $links = array_map(function (ArticleValuesObject $articleVO) {
                return sprintf(
                    '%s%s',
                    $this->request->getHost(),
                    $articleVO->getLink()
                );
            }, $articleVOs);

            if ($page > 1) {
                $links[] = sprintf(
                    '%s%s%s%d/',
                    $this->request->getHost(),
                    TopicValuesObject::TOPICS_LINK,
                    CronSitemapController::PAGINATION_PATTERN,
                    $page
                );
            }

            $sitemap = sprintf('article_%d', $page);

            $this->_sitemapPlugin->saveLinksToSitemap(
                $sitemap,
                $links,
                CronSitemapController::SITEMAP_ARTICLE_FREQUENCY,
                CronSitemapController::SITEMAP_ARTICLE_PRIORITY
            );

            $sitemaps[] = $sitemap;

            $page++;

            $articleVOs = $articleModel->getArticlesByPage(
                $page,
                true,
                true
            );
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _generateTopicsSitemaps(array &$sitemaps): void
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        $page = 1;

        $topicVOs = $topicModel->getTopicsByPage(
            $page,
            true,
            true
        );

        while (!empty($topicVOs)) {
            $links = array_map(function (TopicValuesObject $topicVO) {
                return sprintf(
                    '%s%s',
                    $this->request->getHost(),
                    $topicVO->getLink()
                );
            }, $topicVOs);

            if ($page > 1) {
                $links[] = sprintf(
                    '%s%s%s%d/',
                    $this->request->getHost(),
                    TopicValuesObject::TOPICS_LINK,
                    CronSitemapController::PAGINATION_PATTERN,
                    $page
                );
            }

            $sitemap = sprintf('topics_%d', $page);

            $this->_sitemapPlugin->saveLinksToSitemap(
                $sitemap,
                $links,
                CronSitemapController::SITEMAP_TOPIC_FREQUENCY,
                CronSitemapController::SITEMAP_TOPIC_PRIORITY
            );

            $sitemaps[] = $sitemap;

            $page++;

            $topicVOs = $topicModel->getTopicsByPage(
                $page,
                true,
                true
            );
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _generateTagsSitemaps(array &$sitemaps): void
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        $page = 1;

        $tagVOs = $tagModel->getTagsByPage(
            $page,
            true,
            true
        );

        while (!empty($tagVOs)) {
            $links = array_map(function (TopicValuesObject $tagVO) {
                return sprintf(
                    '%s%s',
                    $this->request->getHost(),
                    $tagVO->getLink()
                );
            }, $tagVOs);

            if ($page > 1) {
                $links[] = sprintf(
                    '%s%s%s%d/',
                    $this->request->getHost(),
                    TagValuesObject::TAGS_LINK,
                    CronSitemapController::PAGINATION_PATTERN,
                    $page
                );
            }

            $sitemap = sprintf('tags_%d', $page);

            $this->_sitemapPlugin->saveLinksToSitemap(
                $sitemap,
                $links,
                CronSitemapController::SITEMAP_TAG_FREQUENCY,
                CronSitemapController::SITEMAP_TAG_PRIORITY
            );

            $sitemaps[] = $sitemap;

            $page++;

            $tagVOs = $tagModel->getTagsByPage(
                $page,
                true,
                true
            );
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws Exception
     */
    private function _generateStaticPagesSitemaps(array &$sitemaps): void
    {
        $staticPages = $this->config->get(ConfigNamesEnum::PAGES);

        if (!empty($staticPages)) {
            $links = array_map(function ($staticPage) {
                return sprintf(
                    '%s%s',
                    $this->request->getHost(),
                    $staticPage
                );
            }, $staticPages);

            $sitemap = 'pages';

            $this->_sitemapPlugin->saveLinksToSitemap(
                $sitemap,
                $links,
                CronSitemapController::SITEMAP_STATIC_PAGE_FREQUENCY,
                CronSitemapController::SITEMAP_STATIC_PAGE_PRIORITY
            );

            $sitemaps[] = $sitemap;
        }
    }
}
