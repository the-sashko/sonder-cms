<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Article\ArticleForm;
use Sonder\Models\Article\ArticleStore;
use Sonder\Models\Article\ArticleValuesObject;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Models\User\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\LinkPlugin;
use Sonder\Plugins\MarkupPlugin;
use Sonder\Plugins\TranslitPlugin;
use Throwable;

/**
 * @property ArticleStore $store
 */
final class Article extends CoreModel implements IModel
{
    const DEFAULT_SLUG = 'article';

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @return ArticleValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?ValuesObject
    {
        $row = $this->store->getArticleRowById($id);

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getArticlesByPage(int $page): ?array
    {
        $rows = $this->store->getArticleRowsByPage($page, $this->itemsOnPage);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAllArticles(): ?array
    {
        $rows = $this->store->getAllArticleRows(
            true,
            true
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCount(): int
    {
        $rowsCount = $this->store->getArticleRowsCount();

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $topicId
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getArticlesByTopicId(
        ?int $topicId = null,
        int  $page = 1
    ): ?array
    {
        if (empty($topicId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByTopicId(
            $topicId,
            $page,
            $this->itemsOnPage
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $topicId
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCountByTopicId(
        ?int $topicId = null
    ): int
    {
        if (empty($topicId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByTopicId($topicId);

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $tagId
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getArticlesByTagId(
        ?int $tagId = null,
        int  $page = 1
    ): ?array
    {
        if (empty($tagId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByTagId(
            $tagId,
            $page,
            $this->itemsOnPage
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $tagId
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCountByTagId(?int $tagId = null): int
    {
        if (empty($tagId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByTopicId($tagId);

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getArticlesByUserId(
        ?int $userId = null,
        int  $page = 1
    ): ?array
    {
        if (empty($userId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByUserId(
            $userId,
            $page,
            $this->itemsOnPage
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $userId
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCountByUserId(?int $userId = null): int
    {
        if (empty($userId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByTopicId($userId);

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeArticleById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteArticleById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreArticleById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreArticleById($id);
    }

    /**
     * @param ArticleForm $articleForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(ArticleForm $articleForm): bool
    {
        $articleForm->checkInputValues();

        if (!$articleForm->getStatus()) {
            return false;
        }

        $this->_checkIdInArticleForm($articleForm);
        $this->_checkTopicIdInArticleForm($articleForm);
        $this->_checkTitleInArticleForm($articleForm);
        $this->_checkMetaTitleInArticleForm($articleForm);

        if (!$articleForm->getStatus()) {
            return false;
        }

        $articleVO = $this->_getVOFromArticleForm($articleForm, true);

        $this->store->start();

        try {
            if (!$this->store->insertOrUpdateArticle($articleVO)) {
                $this->store->rollback();
                $articleForm->setStatusFail();

                return false;
            }

            $id = $this->store->getArticleIdBySlug($articleForm->getSlug());

            if (empty($id)) {
                $this->store->rollback();
                $articleForm->setStatusFail();

                return false;
            }

            $articleForm->setId($id);
        } catch (Throwable $exp) {
            $this->store->rollback();

            $articleForm->setStatusFail();
            $articleForm->setError($exp->getMessage());

            return false;
        }

        $this->store->commit();

        return true;
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $articleVO ArticleValuesObject */
        $articleVO = parent::getVO($row);

        $this->_setUserVOToVO($articleVO);
        $this->_setTopicVOToVO($articleVO);

        return $articleVO;
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setUserVOToVO(ArticleValuesObject $articleVO): void
    {
        /* @var $userModel User */
        $userModel = $this->getModel('user');

        /* @var $userVO UserValuesObject */
        $userVO = $userModel->getVOById($articleVO->getUserId());

        if (!empty($userVO)) {
            $articleVO->setUserVO($userVO);
        }
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setTopicVOToVO(ArticleValuesObject $articleVO): void
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        /* @var $topicVO TopicValuesObject */
        $topicVO = $topicModel->getVOById($articleVO->getUserId());

        if (!empty($topicVO)) {
            $articleVO->setTopicVO($topicVO);
        }
    }

    /**
     * @param ArticleForm $articleForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _checkIdInArticleForm(ArticleForm $articleForm): bool
    {
        $id = $articleForm->getId();

        if (empty($id)) {
            return true;
        }

        $articleVO = $this->_getVOFromArticleForm($articleForm);

        if (empty($articleVO)) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                ArticleForm::ARTICLE_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        return true;
    }

    /**
     * @param ArticleForm $articleForm
     * @param bool $isCreateVOIfEmptyId
     * @return ArticleValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromArticleForm(
        ArticleForm $articleForm,
        bool        $isCreateVOIfEmptyId = false
    ): ?ArticleValuesObject
    {
        $row = null;

        $id = $articleForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getArticleRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $articleVO = new ArticleValuesObject($row);

        $articleVO->setTitle($articleForm->getTitle());
        $articleVO->setSlug($articleForm->getSlug());
        $articleVO->setSummary($articleForm->getSummary());
        $articleVO->setText($articleForm->getText());
        $articleVO->setMetaTitle($articleForm->getMetaTitle());
        $articleVO->setMetaDescription($articleForm->getMetaDescription());
        $articleVO->setUserId($articleForm->getUserId());
        $articleVO->setTopicId($articleForm->getTopicId());
        $articleVO->setIsActive($articleForm->getIsActive());

        if (empty($articleVO->getUserId())) {
            $articleVO->setUserId($articleForm->getUserId());
        }

        $this->_setUniqSlugToVO($articleVO);
        $this->_setSummaryToVO($articleVO);
        $this->_setMetaTitleToVO($articleVO);
        $this->_setMetaDescriptionToVO($articleVO);
        $this->_setHtmlToVO($articleVO);

        return $articleVO;
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws Exception
     */
    private function _setSummaryToVO(ArticleValuesObject $articleVO): void
    {
        $summary = $articleVO->getSummary();
        $summary = preg_replace('/^\s+$/su', ' ', $summary);
        $summary = preg_replace('/((^\s)|(\s$))/su', '', $summary);

        if (empty($summary)) {
            $summary = $articleVO->getText();
            $summary = explode("\n", $summary);
            $summary = array_shift($summary);
            $summary = $this->_text2html($summary);
            $summary = str_replace('\\\'', '\'', $summary);
            $summary = htmlspecialchars_decode($summary, ENT_QUOTES);
            $summary = strip_tags($summary);
        }

        $articleVO->setSummary($summary);
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws Exception
     */
    private function _setMetaTitleToVO(ArticleValuesObject $articleVO): void
    {
        $metaTitle = $articleVO->getMetaTitle();
        $metaTitle = preg_replace('/^\s+$/su', ' ', $metaTitle);
        $metaTitle = preg_replace('/((^\s)|(\s$))/su', '', $metaTitle);

        if (empty($metaTitle)) {
            $metaTitle = $articleVO->getTitle();
        }

        $articleVO->setMetaTitle($metaTitle);
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws Exception
     */
    private function _setMetaDescriptionToVO(
        ArticleValuesObject $articleVO
    ): void
    {
        $metaDescription = $articleVO->getMetaDescription();
        $metaDescription = preg_replace('/^\s+$/su', ' ', $metaDescription);
        $metaDescription = preg_replace('/((^\s)|(\s$))/su', '', $metaDescription);

        if (empty($metaDescription)) {
            $metaDescription = $articleVO->getSummary();
        }

        $articleVO->setMetaDescription($metaDescription);
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws Exception
     */
    private function _setHtmlToVO(ArticleValuesObject $articleVO): void
    {
        $text = $articleVO->getText();
        $html = empty($text) ? null : $this->_text2html($text);

        $articleVO->setHtml($html);
    }

    /**
     * @param string|null $text
     * @return string|null
     * @throws Exception
     */
    private function _text2html(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        /* @var $markupPlugin MarkupPlugin */
        $markupPlugin = $this->getPlugin('markup');

        /* @var $linkPlugin LinkPlugin */
        $linkPlugin = $this->getPlugin('link');

        $text = str_replace('\\\'', '\'', $text);
        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        $text = $markupPlugin->markup2html($text);
        $text = $linkPlugin->parseLinkShortCodes($text);

        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * @param ArticleForm $articleForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTopicIdInArticleForm(ArticleForm $articleForm): bool
    {
        $topicId = $articleForm->getTopicId();

        if (empty($topicId)) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                ArticleForm::TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (empty($topicModel->getVOById($topicId))) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                ArticleForm::TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        return true;
    }

    /**
     * @param ArticleForm $articleForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTitleInArticleForm(ArticleForm $articleForm): void
    {
        $title = $articleForm->getTitle();
        $title = preg_replace('/^\s+$/su', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/su', '', $title);

        $articleForm->setTitle($title);

        if (empty($title)) {
            $articleForm->setStatusFail();
            $articleForm->setError(ArticleForm::TITLE_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($title) &&
            !$this->_isTitleUniq($title, $articleForm->getId())
        ) {
            $articleForm->setStatusFail();
            $articleForm->setError(ArticleForm::TITLE_EXISTS_ERROR_MESSAGE);
        }
    }

    /**
     * @param string|null $title
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isTitleUniq(?string $title, ?int $id): bool
    {
        $row = $this->store->getArticleRowByTitle($title, $id);

        return empty($row);
    }

    /**
     * @param ArticleForm $articleForm
     * @return void
     * @throws Exception
     */
    private function _checkMetaTitleInArticleForm(
        ArticleForm $articleForm
    ): void
    {
        $metaTitle = $articleForm->getMetaTitle();

        $metaTitle = preg_replace(
            '/^\s+$/su',
            ' ',
            $metaTitle
        );

        $metaTitle = preg_replace(
            '/((^\s)|(\s$))/su',
            '',
            $metaTitle
        );

        $articleForm->setMetaTitle($metaTitle);

        if (
            !empty($metaTitle) &&
            !$this->_isMetaTitleUniq($metaTitle, $articleForm->getId())
        ) {
            $articleForm->setStatusFail();
            $articleForm->setError(
                ArticleForm::META_TITLE_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param string|null $title
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isMetaTitleUniq(?string $title, ?int $id): bool
    {
        $row = $this->store->getArticleRowByMetaTitle($title, $id);

        return empty($row);
    }

    /**
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setUniqSlugToVO(ArticleValuesObject $articleVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$articleVO->getSlug();

        $slug = preg_replace('/^\s+$/su', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);

        if (empty($slug)) {
            $slug = $articleVO->getTitle();

            $slug = preg_replace('/^\s+$/su', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);
        }

        $slug = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = Article::DEFAULT_SLUG;
        }

        $slug = $this->_makeSlugUniq($slug, $articleVO->getId());

        $articleVO->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param int|null $id
     * @return string|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _makeSlugUniq(string $slug, ?int $id = null): ?string
    {
        if (empty($this->store->getArticleRowBySlug($slug, $id))) {
            return $slug;
        }

        $slugCount = 1;

        if (preg_match('/^(.*?)-([0-9]+)$/su', $slug)) {
            $slugCount = (int)preg_match(
                '/^(.*?)-([0-9]+)$/su',
                '$2',
                $slug
            );

            $slug = preg_match(
                '/^(.*?)-([0-9]+)$/su',
                '$1',
                $slug
            );

            $slugCount++;
        }

        $slug = sprintf('%s-%d', $slug, $slugCount);

        return $this->_makeSlugUniq($slug, $id);
    }
}
//TODO: add imag + language + id in error messages from form + format text
// form fields
