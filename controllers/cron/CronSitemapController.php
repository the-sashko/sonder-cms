<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\CronBaseController;
use Sonder\Core\RequestObject;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IController;
use Sonder\Models\Article\Interfaces\IArticleValuesObject;
use Sonder\Models\ArticleModel;
use Sonder\Models\Tag\Interfaces\ITagValuesObject;
use Sonder\Models\Tag\ValuesObjects\TagValuesObject;
use Sonder\Models\TagModel;
use Sonder\Models\Topic\Interfaces\ITopicValuesObject;
use Sonder\Models\Topic\ValuesObjects\TopicValuesObject;
use Sonder\Models\TopicModel;
use Sonder\Plugins\SitemapPlugin;

#[IController]
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

    /**
     * @param RequestObject $request
     * @throws ConfigException
     * @throws CoreException
     * @throws ControllerException
     */
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
     * @throws ConfigException
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
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
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _generateArticleSitemaps(array &$sitemaps): void
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        $page = 1;

        $articleVOs = $articleModel->getArticlesByPage($page);

        while (!empty($articleVOs)) {
            $links = array_map(function (IArticleValuesObject $articleVO) {
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

            $articleVOs = $articleModel->getArticlesByPage($page);
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _generateTopicsSitemaps(array &$sitemaps): void
    {
        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        $page = 1;

        $topicVOs = $topicModel->getTopicsByPage($page);

        while (!empty($topicVOs)) {
            $links = array_map(function (ITopicValuesObject $topicVO) {
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

            $topicVOs = $topicModel->getTopicsByPage($page);
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _generateTagsSitemaps(array &$sitemaps): void
    {
        /* @var $tagModel TagModel */
        $tagModel = $this->getModel('tag');

        $page = 1;

        $tagVOs = $tagModel->getTagsByPage($page);

        while (!empty($tagVOs)) {
            $links = array_map(function (ITagValuesObject $tagVO) {
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

            $tagVOs = $tagModel->getTagsByPage($page);
        }
    }

    /**
     * @param array $sitemaps
     * @return void
     * @throws ConfigException
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
