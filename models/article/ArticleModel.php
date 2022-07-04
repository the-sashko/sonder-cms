<?php

namespace Sonder\Models;

use ImagickException;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Article\Exceptions\ArticleException;
use Sonder\Models\Article\Exceptions\ArticleModelException;
use Sonder\Models\Article\Forms\ArticleForm;
use Sonder\Models\Article\Interfaces\IArticleApi;
use Sonder\Models\Article\Interfaces\IArticleForm;
use Sonder\Models\Article\Interfaces\IArticleModel;
use Sonder\Models\Article\Interfaces\IArticleSimpleValuesObject;
use Sonder\Models\Article\Interfaces\IArticleStore;
use Sonder\Models\Article\Interfaces\IArticleValuesObject;
use Sonder\Models\Article\ValuesObjects\ArticleValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicModel;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;
use Sonder\Models\User\ValuesObjects\UserSimpleValuesObject;
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
 * @property IArticleApi $api
 * @property IArticleStore $store
 */
#[IModel]
#[IArticleModel]
final class ArticleModel extends BaseModel implements IArticleModel
{
    final protected const ITEMS_ON_PAGE = 10;

    private const DEFAULT_SLUG = 'article';

    private const IMAGES_DIR_PATH = '%s/media/articles/%s';

    private const UPLOADS_DIR_PATH = 'uploads/articles';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleValuesObject|null
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleValuesObject {
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
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleSimpleValuesObject|null
     * @throws ModelException
     */
    final public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleSimpleValuesObject {
        $row = $this->store->getArticleRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $articleSimpleVO IArticleSimpleValuesObject */
            $articleSimpleVO = $this->getSimpleVO($row);

            return $articleSimpleVO;
        }

        return null;
    }

    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleValuesObject|null
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleValuesObject {
        $row = $this->store->getArticleRowBySlug(
            $slug,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IArticleSimpleValuesObject|null
     * @throws ModelException
     */
    final public function getSimpleVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IArticleSimpleValuesObject {
        $row = $this->store->getArticleRowBySlug(
            $slug,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $articleSimpleVO IArticleSimpleValuesObject */
            $articleSimpleVO = $this->getSimpleVO($row);

            return $articleSimpleVO;
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @param bool $simplify
     * @return array|null
     * @throws ModelException
     */
    final public function getArticlesByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true,
        bool $simplify = true
    ): ?array {
        $rows = $this->store->getArticleRowsByPage(
            $page,
            ArticleModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        if ($simplify) {
            return $this->getSimpleVOArray($rows);
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getArticlesPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getArticleRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / ArticleModel::ITEMS_ON_PAGE);

        if ($pageCount * ArticleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $topicId
     * @param int $page
     * @return array|null
     * @throws ModelException
     */
    final public function getArticlesByTopicId(
        ?int $topicId = null,
        int $page = 1
    ): ?array {
        if (empty($topicId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByTopicId(
            $topicId,
            $page,
            ArticleModel::ITEMS_ON_PAGE
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $topicId
     * @return int
     */
    final public function getArticlesPageCountByTopicId(
        ?int $topicId = null
    ): int {
        if (empty($topicId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByTopicId($topicId);

        $pageCount = (int)($rowsCount / ArticleModel::ITEMS_ON_PAGE);

        if ($pageCount * ArticleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $tagId
     * @param int $page
     * @return array|null
     * @throws ModelException
     */
    final public function getArticlesByTagId(
        ?int $tagId = null,
        int $page = 1
    ): ?array {
        if (empty($tagId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByTagId(
            $tagId,
            $page,
            ArticleModel::ITEMS_ON_PAGE
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $tagId
     * @return int
     */
    final public function getArticlesPageCountByTagId(?int $tagId = null): int
    {
        if (empty($tagId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByTopicId($tagId);

        $pageCount = (int)($rowsCount / ArticleModel::ITEMS_ON_PAGE);

        if ($pageCount * ArticleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     * @throws ModelException
     */
    final public function getArticlesByUserId(
        ?int $userId = null,
        int $page = 1
    ): ?array {
        if (empty($userId)) {
            return null;
        }

        $rows = $this->store->getArticleRowsByUserId(
            $userId,
            $page,
            ArticleModel::ITEMS_ON_PAGE
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $userId
     * @return int
     */
    final public function getArticlesPageCountByUserId(?int $userId = null): int
    {
        if (empty($userId)) {
            return 0;
        }

        $rowsCount = $this->store->getArticleRowsCountByUserId($userId);

        $pageCount = (int)($rowsCount / ArticleModel::ITEMS_ON_PAGE);

        if ($pageCount * ArticleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
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
     */
    final public function restoreArticleById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreArticleById($id);
    }

    /**
     * @param IArticleForm $articleForm
     * @return bool
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function save(IArticleForm $articleForm): bool
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
     * @return IArticleValuesObject
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getVO(?array $row = null): IArticleValuesObject
    {
        /* @var $articleVO ArticleValuesObject */
        $articleVO = parent::getVO($row);

        $this->_setUserVOToVO($articleVO);
        $this->_setTopicVOToVO($articleVO);
        $this->_setTagsToVO($articleVO);
        $this->_setCommentsToVO($articleVO);

        return $articleVO;
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _setUserVOToVO(IArticleValuesObject $articleVO): void
    {
        /* @var $userModel UserModel */
        $userModel = $this->getModel('user');

        /* @var $userVO UserSimpleValuesObject */
        $userVO = $userModel->getSimpleVOById($articleVO->getUserId());

        if (!empty($userVO)) {
            $articleVO->setUserVO($userVO);
        }
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     */
    private function _setTopicVOToVO(IArticleValuesObject $articleVO): void
    {
        /* @var $topicModel ITopicModel */
        $topicModel = $this->getModel('topic');

        /* @var $topicVO ITopicSimpleValuesObject */
        $topicVO = $topicModel->getSimpleVOById($articleVO->getUserId());

        if (!empty($topicVO)) {
            $articleVO->setTopicVO($topicVO);
        }
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ValuesObjectException
     */
    private function _setTagsToVO(IArticleValuesObject $articleVO): void
    {
        /* @var $tagModel TagModel */
        $tagModel = $this->getModel('tag');

        $tagVOs = $tagModel->getTagsByArticleId($articleVO->getId());

        if (!empty($tagVOs)) {
            $articleVO->setTags($tagVOs);
        }
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ValuesObjectException
     */
    private function _setCommentsToVO(IArticleValuesObject $articleVO): void
    {
        /* @var $commentModel CommentModel */
        $commentModel = $this->getModel('comment');

        $commentVOs = $commentModel->getCommentsByArticleId(
            $articleVO->getId()
        );

        if (!empty($commentVOs)) {
            $articleVO->setComments($commentVOs);
        }
    }

    /**
     * @param IArticleForm $articleForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInArticleForm(IArticleForm $articleForm): void
    {
        $id = $articleForm->getId();

        if (empty($id)) {
            return;
        }

        $articleVO = $this->_getVOFromArticleForm($articleForm);

        if (empty($articleVO)) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                sprintf(
                    ArticleForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param IArticleForm $articleForm
     * @param bool $isCreateVOIfEmptyId
     * @return IArticleValuesObject|null
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromArticleForm(
        IArticleForm $articleForm,
        bool $isCreateVOIfEmptyId = false
    ): ?IArticleValuesObject {
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
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     */
    private function _setSummaryToVO(IArticleValuesObject $articleVO): void
    {
        $summary = $articleVO->getSummary();
        $summary = preg_replace('/^\s+$/u', ' ', $summary);
        $summary = preg_replace('/((^\s)|(\s$))/u', '', $summary);

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
     * @param IArticleValuesObject $articleVO
     * @return void
     */
    private function _setMetaTitleToVO(IArticleValuesObject $articleVO): void
    {
        $metaTitle = $articleVO->getMetaTitle();
        $metaTitle = preg_replace('/^\s+$/u', ' ', $metaTitle);
        $metaTitle = preg_replace('/((^\s)|(\s$))/u', '', $metaTitle);

        if (empty($metaTitle)) {
            $metaTitle = $articleVO->getTitle();
        }

        $articleVO->setMetaTitle($metaTitle);
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     */
    private function _setMetaDescriptionToVO(
        IArticleValuesObject $articleVO
    ): void {
        $metaDescription = $articleVO->getMetaDescription();
        $metaDescription = preg_replace('/^\s+$/u', ' ', $metaDescription);
        $metaDescription = preg_replace(
            '/((^\s)|(\s$))/u',
            '',
            $metaDescription
        );

        if (empty($metaDescription)) {
            $metaDescription = $articleVO->getSummary();
        }

        $articleVO->setMetaDescription($metaDescription);
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     */
    private function _setHtmlToVO(IArticleValuesObject $articleVO): void
    {
        $text = $articleVO->getText();
        $html = empty($text) ? null : $this->_text2html($text);

        $articleVO->setHtml($html);
    }

    /**
     * @param string|null $text
     * @return string|null
     * @throws CoreException
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
     * @param IArticleForm $articleForm
     * @return void
     * @throws CoreException
     */
    private function _checkTopicIdInArticleForm(IArticleForm $articleForm): void
    {
        $topicId = $articleForm->getTopicId();

        if (empty($topicId)) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                ArticleForm::TOPIC_IS_NOT_SET_ERROR_MESSAGE
            );

            return;
        }

        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        if (empty($topicModel->getVOById($topicId))) {
            $articleForm->setStatusFail();

            $articleForm->setError(
                sprintf(
                    ArticleForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                    $topicId
                )
            );
        }
    }

    /**
     * @param IArticleForm $articleForm
     * @return void
     */
    private function _checkTitleInArticleForm(IArticleForm $articleForm): void
    {
        $title = $articleForm->getTitle();
        $title = preg_replace('/^\s+$/u', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/u', '', $title);

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
     */
    private function _isTitleUniq(?string $title = null, ?int $id = null): bool
    {
        $row = $this->store->getArticleRowByTitle($title, $id);

        return empty($row);
    }

    /**
     * @param IArticleForm $articleForm
     * @return void
     */
    private function _checkMetaTitleInArticleForm(
        IArticleForm $articleForm
    ): void {
        $metaTitle = $articleForm->getMetaTitle();

        $metaTitle = preg_replace(
            '/^\s+$/u',
            ' ',
            $metaTitle
        );

        $metaTitle = preg_replace(
            '/((^\s)|(\s$))/u',
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
     */
    private function _isMetaTitleUniq(
        ?string $title = null,
        ?int $id = null
    ): bool {
        $row = $this->store->getArticleRowByMetaTitle($title, $id);

        return empty($row);
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _setUniqSlugToVO(IArticleValuesObject $articleVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$articleVO->getSlug();

        $slug = preg_replace('/^\s+$/u', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);

        if (empty($slug)) {
            $slug = $articleVO->getTitle();

            $slug = preg_replace('/^\s+$/u', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);
        }

        $slug = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = ArticleModel::DEFAULT_SLUG;
        }

        $slug = $this->_makeSlugUniq($slug, $articleVO->getId());

        $articleVO->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param int|null $id
     * @return string|null
     */
    private function _makeSlugUniq(string $slug, ?int $id = null): ?string
    {
        if (empty(
        $this->store->getArticleRowBySlug(
            $slug,
            $id,
            false,
            false
        )
        )) {
            return $slug;
        }

        $slugCount = 1;

        if (preg_match('/^(.*?)-(\d+)$/su', $slug)) {
            $slugCount = (int)preg_match(
                '/^(.*?)-(\d+)$/su',
                '$2',
                $slug
            );

            $slug = preg_match(
                '/^(.*?)-(\d+)$/su',
                '$1',
                $slug
            );

            $slugCount++;
        }

        $slug = sprintf('%s-%d', $slug, $slugCount);

        return $this->_makeSlugUniq($slug, $id);
    }

    /**
     * @param IArticleValuesObject $articleVO
     * @param IArticleForm $articleForm
     * @return bool
     * @throws ArticleModelException
     * @throws CoreException
     * @throws ImagePluginException
     * @throws ImageSizeException
     * @throws ImagickException
     * @throws ValuesObjectException
     */
    private function _uploadImageFile(
        IArticleValuesObject $articleVO,
        IArticleForm $articleForm
    ): bool {
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
            ArticleModel::UPLOADS_DIR_PATH
        );

        $error = $uploadPlugin->getError();

        if (!empty($error)) {
            $errorMessage = sprintf(
                ArticleModelException::MESSAGE_MODEL_UPLOAD_FILE_ERROR,
                $error
            );

            throw new ArticleModelException(
                $errorMessage,
                ArticleException::CODE_MODEL_UPLOAD_FILE_ERROR
            );
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

    /**
     * @param string $slug
     * @return string
     */
    private function _getImagesDirName(string $slug): string
    {
        return sprintf('%s/%s', date('Y/m/d/h/i/s'), $slug);
    }

    /**
     * @param string $imageDirName
     * @return string
     * @throws ArticleModelException
     */
    private function _getImagesDirPath(string $imageDirName): string
    {
        $publicDirPath = $this->_getPublicDirPath();

        return sprintf(
            ArticleModel::IMAGES_DIR_PATH,
            $publicDirPath,
            $imageDirName
        );
    }

    /**
     * @return string
     * @throws ArticleModelException
     */
    private function _getPublicDirPath(): string
    {
        if (defined('APP_PUBLIC_DIR_PATH')) {
            return APP_PUBLIC_DIR_PATH;
        }

        $publicDirPath = realpath(__DIR__ . '/../../../../public');

        if (empty($publicDirPath)) {
            throw new ArticleModelException(
                ArticleModelException::MESSAGE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY,
                ArticleException::CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY
            );
        }

        return $publicDirPath;
    }

    /**
     * @param IArticleForm $articleForm
     * @return bool
     */
    private function _saveSelectedTags(IArticleForm $articleForm): bool
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

