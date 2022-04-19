<?php

namespace Sonder\Models;

use Exception;
use ImagickException;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Article\ArticleForm;
use Sonder\Models\Article\ArticleStore;
use Sonder\Models\Article\ArticleValuesObject;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Models\User\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\Image\Exceptions\ImagePluginException;
use Sonder\Plugins\Image\Exceptions\ImageSizeException;
use Sonder\Plugins\ImagePlugin;
use Sonder\Plugins\LinkPlugin;
use Sonder\Plugins\MarkupPlugin;
use Sonder\Plugins\TranslitPlugin;
use Sonder\Plugins\UploadPlugin;
use Throwable;

/**
 * @property ArticleStore $store
 */
final class Article extends BaseModel
{
    const DEFAULT_SLUG = 'article';

    const IMAGES_DIR_PATH = '%s/media/articles/%s';

    const UPLOADS_DIR_PATH = 'uploads/articles';

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?ValuesObject
    {
        $row = $this->store->getArticleRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getArticlesByPage(
        int  $page,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $rows = $this->store->getArticleRowsByPage(
            $page,
            $this->itemsOnPage,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCount(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $rowsCount = $this->store->getArticleRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

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

        $rowsCount = $this->store->getArticleRowsCountByUserId($userId);

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
    final public function removeArticleById(?int $id = null): bool
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
    final public function restoreArticleById(?int $id = null): bool
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

        try {
            if (!$this->store->insertOrUpdateArticle($articleVO)) {
                $this->store->rollback();
                $articleForm->setStatusFail();

                return false;
            }

            $id = $this->store->getArticleIdBySlug($articleVO->getSlug());

            if (empty($id)) {
                $this->store->rollback();
                $articleForm->setStatusFail();

                return false;
            }

            $articleForm->setId($id);

            if (!$this->_uploadImageFile($articleVO, $articleForm)) {
                $articleForm->setError(
                    ArticleForm::UPLOAD_IMAGE_FILE_ERROR_MESSAGE
                );

                $articleForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            if (!$this->_saveSelectedTags($articleForm)) {
                $articleForm->setError(
                    ArticleForm::TAGS_SAVING_ERROR_MESSAGE
                );

                $articleForm->setStatusFail();

                $this->store->rollback();

                return false;
            }
        } catch (Throwable $thr) {
            $this->store->rollback();

            $articleForm->setStatusFail();
            $articleForm->setError($thr->getMessage());

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
        $this->_setTagsToVO($articleVO);

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
     * @param ArticleValuesObject $articleVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _setTagsToVO(ArticleValuesObject $articleVO): void
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        $tagVOs = $tagModel->getTagsByArticleId($articleVO->getId());

        if (!empty($tagVOs)) {
            $articleVO->setTags($tagVOs);
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

            $articleForm->setError(sprintf(
                ArticleForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));

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
        $articleVO->setIsActive($articleForm->isActive());

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
                ArticleForm::TOPIC_IS_NOT_SET_ERROR_MESSAGE
            );

            return false;
        }

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (empty($topicModel->getVOById($topicId))) {
            $articleForm->setStatusFail();

            $articleForm->setError(sprintf(
                ArticleForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                $topicId
            ));

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
    private function _isTitleUniq(?string $title = null, ?int $id = null): bool
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
    private function _isMetaTitleUniq(
        ?string $title = null,
        ?int    $id = null
    ): bool
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

    /**
     * @param ArticleValuesObject $articleVO
     * @param ArticleForm $articleForm
     * @return bool
     * @throws DatabasePluginException
     * @throws ImagePluginException
     * @throws ImageSizeException
     * @throws ImagickException
     * @throws Exception
     */
    private function _uploadImageFile(
        ArticleValuesObject $articleVO,
        ArticleForm         $articleForm
    ): bool
    {
        $image = $articleForm->getImage();

        if (empty($image)) {
            $articleVO->setImageDir($articleForm->getImageDir());

            //TODO: remove image dir

            $this->store->updateArticleById(
                $articleVO->exportRow(),
                $articleVO->getId()
            );

            return true;
        }

        /* @var $uploadPlugin UploadPlugin */
        $uploadPlugin = $this->getPlugin('upload');

        $imageDirName = $this->_getImagesDirName($articleVO->getSlug());

        $imageDirPath = $this->_getImagesDirPath($imageDirName);

        if (!file_exists($imageDirPath) && !is_dir($imageDirPath)) {
            mkdir($imageDirPath, 0755, true);
        }

        $uploadPlugin->upload(
            ArticleForm::IMAGE_EXTENSIONS,
            ArticleForm::IMAGE_FILE_MAX_SIZE,
            Article::UPLOADS_DIR_PATH
        );

        $error = $uploadPlugin->getError();

        if (!empty($error)) {
            throw new Exception($error);
        }

        $uploadedFiles = $uploadPlugin->getFiles();

        if (
            empty($uploadedFiles) ||
            !array_key_exists('image', $uploadedFiles) ||
            empty($uploadedFiles['image']) ||
            !is_array($uploadedFiles['image'])
        ) {
            return false;
        }

        $uploadedFilePath = array_shift($uploadedFiles['image']);

        if (!file_exists($uploadedFilePath) || !is_file($uploadedFilePath)) {
            return false;
        }

        /* @var $imagePlugin ImagePlugin */
        $imagePlugin = $this->getPlugin('image');

        $imagePlugin->resize(
            $uploadedFilePath,
            $imageDirPath,
            $articleVO->getSlug(),
            ArticleValuesObject::IMAGE_FORMAT,
            ArticleValuesObject::IMAGE_SIZES
        );

        unlink($uploadedFilePath);

        $articleVO->setImageDir($imageDirName);

        $this->store->updateArticleById(
            $articleVO->exportRow(),
            $articleVO->getId()
        );

        return true;
    }

    private function _getImagesDirName(string $slug): string
    {
        return sprintf('%s/%s', date('Y/m/d/h/i/s'), $slug);
    }

    /**
     * @param string $imageDirName
     * @return string
     * @throws Exception
     */
    private function _getImagesDirPath(string $imageDirName): string
    {
        $publicDirPath = $this->_getPublicDirPath();

        return sprintf(
            Article::IMAGES_DIR_PATH,
            $publicDirPath,
            $imageDirName
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    private function _getPublicDirPath(): string
    {
        if (defined('APP_PUBLIC_DIR_PATH')) {
            return APP_PUBLIC_DIR_PATH;
        }

        $publicDirPath = realpath(__DIR__ . '/../../../../public');

        if (empty($publicDirPath)) {
            throw new Exception('Can not find public directory path');
        }

        return $publicDirPath;
    }

    /**
     * @param ArticleForm $articleForm
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _saveSelectedTags(ArticleForm $articleForm): bool
    {
        $this->store->deleteArticle2TagRelationsByArticleId(
            $articleForm->getId()
        );

        foreach ($articleForm->getTags() as $tagId) {
            $this->store->insertArticle2TagRelation(
                (int)$tagId,
                $articleForm->getId()
            );
        }

        return true;
    }
}
//TODO: language
//TODO: format text form fields
//TODO: missing image when created
//TODO: missing image when changed slug

