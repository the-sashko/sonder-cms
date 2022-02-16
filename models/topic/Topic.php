<?php

namespace Sonder\Models;

use Exception;
use ImagickException;
use Sonder\Core\ValuesObject;
use Sonder\Models\Topic\TopicForm;
use Sonder\Models\Topic\TopicStore;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\Image\Exceptions\ImagePluginException;
use Sonder\Plugins\Image\Exceptions\ImageSizeException;
use Sonder\Plugins\ImagePlugin;
use Sonder\Plugins\TranslitPlugin;
use Sonder\Plugins\UploadPlugin;
use Throwable;

/**
 * @property TopicStore $store
 */
final class Topic extends BaseModel
{
    const DEFAULT_SLUG = 'topic';

    const TOPICS_IMAGES_DIR_PATH = '%s/media/topics';

    const UPLOADS_DIR_PATH = 'uploads/topics';

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @return TopicValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?TopicValuesObject
    {
        $row = $this->store->getTopicRowById($id);

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
    final public function getTopicsByPage(int $page): ?array
    {
        $rows = $this->store->getTopicRowsByPage($page, $this->itemsOnPage);

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
    final public function getAllTopics(): ?array
    {
        $rows = $this->store->getAllTopicRows(
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
    final public function getTopicsPageCount(): int
    {
        $rowsCount = $this->store->getTopicRowsCount();

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
    final public function removeTopicById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteTopicById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function removeTopicImageById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        $topicVO = $this->getVOById($id);

        if (empty($topicVO)) {
            return false;
        }

        $topicsImageDirPath = $this->_getTopicsImagesDirPath();

        $topicsImageFilePath = sprintf(
            '%s/%s-topic.png',
            $topicsImageDirPath,
            $topicVO->getSlug()
        );

        $defaultImageFilePath = sprintf(
            '%s/assets/img/broken.png',
            $this->_getPublicDirPath()
        );

        if (
            file_exists($topicsImageFilePath) &&
            is_file($topicsImageFilePath)
        ) {
            unlink($topicsImageFilePath);
        }

        copy($defaultImageFilePath, $topicsImageFilePath);

        return true;
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreTopicById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreTopicById($id);
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(TopicForm $topicForm): bool
    {
        $topicForm->checkInputValues();

        if (!$topicForm->getStatus()) {
            return false;
        }

        $this->_checkIdInTopicForm($topicForm);
        $this->_checkParentIdInTopicForm($topicForm);
        $this->_checkTitleInTopicForm($topicForm);

        if (!$topicForm->getStatus()) {
            return false;
        }

        $topicVO = $this->_getVOFromTopicForm($topicForm, true);

        $this->store->start();

        try {
            if (!$this->store->insertOrUpdateTopic($topicVO)) {
                $topicForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            $id = $this->store->getTopicIdBySlug($topicVO->getSlug());

            if (empty($id)) {
                $topicForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            $topicForm->setId($id);

            if (!$this->_uploadImageFile($topicVO->getSlug(), $topicForm)) {
                $topicForm->setError(
                    TopicForm::UPLOAD_IMAGE_FILE_ERROR_MESSAGE
                );

                $topicForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            if (!$topicForm->getStatus()) {
                $this->store->rollback();

                return false;
            }

            $this->store->commit();

            return true;
        } catch (Throwable $exp) {
            $this->store->rollback();

            $topicForm->setStatusFail();
            $topicForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param array|null $row
     * @return TopicValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $topicVO TopicValuesObject */
        $topicVO = parent::getVO($row);

        $this->_setParentToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param TopicValuesObject $topicVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setParentToVO(TopicValuesObject $topicVO): void
    {
        /* @var $parentVO TopicValuesObject */
        $parentVO = $this->getVOById($topicVO->getParentId());

        if (!empty($parentVO)) {
            $topicVO->setParentVO($parentVO);
        }
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInTopicForm(TopicForm $topicForm): bool
    {
        $id = $topicForm->getId();

        if (empty($id)) {
            return true;
        }

        $topicVO = $this->_getVOFromTopicForm($topicForm);

        if (empty($topicVO)) {
            $topicForm->setStatusFail();

            $topicForm->setError(sprintf(
                TopicForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));

            return false;
        }

        return true;
    }

    /**
     * @param TopicForm $topicForm
     * @param bool $isCreateVOIfEmptyId
     * @return TopicValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getVOFromTopicForm(
        TopicForm $topicForm,
        bool      $isCreateVOIfEmptyId = false
    ): ?TopicValuesObject
    {
        $row = null;

        $id = $topicForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getTopicRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $topicVO = new TopicValuesObject($row);

        $topicVO->setParentId($topicForm->getParentId());
        $topicVO->setTitle($topicForm->getTitle());
        $topicVO->setSlug($topicForm->getSlug());
        $topicVO->setIsActive($topicForm->getIsActive());

        $this->_setUniqSlugToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkParentIdInTopicForm(TopicForm $topicForm): bool
    {
        $parentId = $topicForm->getParentId();

        if (empty($parentId)) {
            return true;
        }

        $row = $this->store->getTopicRowById(
            $parentId,
            true,
            true
        );

        if (empty($row)) {
            $topicForm->setStatusFail();

            $topicForm->setError(
                TopicForm::PARENT_TOPIC_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        $topicId = $topicForm->getId();

        if (empty($topicId)) {
            return true;
        }

        /* @var $parentVO TopicValuesObject */
        $parentVO = $this->getVO($row);

        while (!empty($parentVO)) {
            if ($parentVO->getId() == $topicId) {
                $topicForm->setStatusFail();

                $topicForm->setError(
                    TopicForm::TOPIC_HAVE_CIRCULAR_DEPENDENCY_ERROR_MESSAGE
                );

                break;
            }

            $parentVO = $parentVO->getParentVO();
        }

        return true;
    }

    /**
     * @param TopicForm $topicForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTitleInTopicForm(TopicForm $topicForm): void
    {
        $title = $topicForm->getTitle();
        $title = preg_replace('/^\s+$/su', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/su', '', $title);

        $topicForm->setTitle($title);

        if (empty($title)) {
            $topicForm->setStatusFail();
            $topicForm->setError(TopicForm::TITLE_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($title) &&
            !$this->_isTitleUniq($title, $topicForm->getId())
        ) {
            $topicForm->setStatusFail();
            $topicForm->setError(TopicForm::TITLE_EXISTS_ERROR_MESSAGE);
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
        $row = $this->store->getTopicRowByTitle($title, $id);

        return empty($row);
    }

    /**
     * @param TopicValuesObject $topicVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _setUniqSlugToVO(TopicValuesObject $topicVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$topicVO->getSlug();

        $slug = preg_replace('/^\s+$/su', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);

        if (empty($slug)) {
            $slug = $topicVO->getTitle();

            $slug = preg_replace('/^\s+$/su', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);
        }

        $slug = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = Topic::DEFAULT_SLUG;
        }

        $slug = $this->_makeSlugUniq($slug, $topicVO->getId());

        $topicVO->setSlug($slug);
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
        if (empty($this->store->getTopicRowBySlug($slug, $id))) {
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
                '/^(.*?)\-([0-9]+)$/su',
                '$1',
                $slug
            );

            $slugCount++;
        }

        $slug = sprintf('%s-%d', $slug, $slugCount);

        return $this->_makeSlugUniq($slug, $id);
    }

    /**
     * @param string $slug
     * @param TopicForm $topicForm
     * @return bool
     * @throws ImagickException
     * @throws ImagePluginException
     * @throws ImageSizeException
     * @throws Exception
     */
    private function _uploadImageFile(string $slug, TopicForm $topicForm): bool
    {
        /* @var $uploadPlugin UploadPlugin */
        $uploadPlugin = $this->getPlugin('upload');

        $topicsImageDirPath = $this->_getTopicsImagesDirPath();

        $topicsImageFilePath = sprintf(
            '%s/%s-topic.png',
            $topicsImageDirPath,
            $slug
        );

        $topicsImageFullFilePath = sprintf(
            '%s/%s-full.png',
            $topicsImageDirPath,
            $slug
        );

        $defaultImageFilePath = sprintf(
            '%s/assets/img/broken.png',
            $this->_getPublicDirPath()
        );

        $image = $topicForm->getImage();

        if (
            empty($image) &&
            (
                !file_exists($topicsImageFilePath) ||
                !is_file($topicsImageFilePath)
            )
        ) {
            copy($defaultImageFilePath, $topicsImageFilePath);
        }

        if (empty($image)) {
            return true;
        }

        if (!file_exists($topicsImageDirPath) && !is_dir($topicsImageDirPath)) {
            mkdir($topicsImageDirPath, 0755, true);
        }

        $uploadPlugin->upload(
            TopicForm::IMAGE_EXTENSIONS,
            TopicForm::IMAGE_FILE_MAX_SIZE,
            Topic::UPLOADS_DIR_PATH
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

        if (
            file_exists($topicsImageFullFilePath) &&
            is_file($topicsImageFullFilePath)
        ) {
            unlink($topicsImageFullFilePath);
        }

        if (
            file_exists($topicsImageFilePath) &&
            is_file($topicsImageFilePath)
        ) {
            unlink($topicsImageFilePath);
        }

        /* @var $imagePlugin ImagePlugin */
        $imagePlugin = $this->getPlugin('image');

        $imagePlugin->resize(
            $uploadedFilePath,
            $topicsImageDirPath,
            $slug,
            TopicValuesObject::IMAGE_FORMAT,
            TopicValuesObject::IMAGE_SIZES
        );

        if (
            file_exists($topicsImageFullFilePath) &&
            is_file($topicsImageFullFilePath)
        ) {
            unlink($topicsImageFullFilePath);
        }

        unlink($uploadedFilePath);

        return true;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function _getTopicsImagesDirPath(): string
    {
        $publicDirPath = $this->_getPublicDirPath();

        return sprintf(Topic::TOPICS_IMAGES_DIR_PATH, $publicDirPath);
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
     * @return string
     * @throws Exception
     */
    private function _getProtectedDirPath(): string
    {
        if (defined('APP_PROTECTED_DIR_PATH')) {
            return APP_PROTECTED_DIR_PATH;
        }

        $protectedDirPath = realpath(__DIR__ . '/../../..');

        if (empty($protectedDirPath)) {
            throw new Exception(
                'Can not find protected directory path'
            );
        }

        return $protectedDirPath;
    }
}
//TODO: add language
