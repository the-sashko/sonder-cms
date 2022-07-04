<?php

namespace Sonder\Models;

use ImagickException;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Topic\Exceptions\TopicException;
use Sonder\Models\Topic\Exceptions\TopicModelException;
use Sonder\Models\Topic\Interfaces\ITopicApi;
use Sonder\Models\Topic\Interfaces\ITopicForm;
use Sonder\Models\Topic\Interfaces\ITopicModel;
use Sonder\Models\Topic\Interfaces\ITopicSimpleValuesObject;
use Sonder\Models\Topic\Interfaces\ITopicStore;
use Sonder\Models\Topic\Forms\TopicForm;
use Sonder\Models\Topic\Interfaces\ITopicValuesObject;
use Sonder\Models\Topic\ValuesObjects\TopicSimpleValuesObject;
use Sonder\Models\Topic\ValuesObjects\TopicValuesObject;
use Sonder\Plugins\Image\Exceptions\ImagePluginException;
use Sonder\Plugins\Image\Exceptions\ImageSizeException;
use Sonder\Plugins\ImagePlugin;
use Sonder\Plugins\TranslitPlugin;
use Sonder\Plugins\UploadPlugin;
use Throwable;

/**
 * @property ITopicApi $api
 * @property ITopicStore $store
 */
#[IModel]
#[ITopicModel]
final class TopicModel extends BaseModel implements ITopicModel
{
    final protected const ITEMS_ON_PAGE = 10;

    private const DEFAULT_SLUG = 'topic';

    private const IMAGES_DIR_PATH = '%s/media/topics';

    private const UPLOADS_DIR_PATH = 'uploads/topics';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITopicValuesObject|null
     * @throws ModelException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITopicValuesObject {
        $row = $this->store->getTopicRowById(
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
     * @return ITopicSimpleValuesObject|null
     * @throws ModelException
     */
    final public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITopicSimpleValuesObject {
        $row = $this->store->getTopicRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $topicSimpleVO ITopicSimpleValuesObject */
            $topicSimpleVO = $this->getSimpleVO($row);

            return $topicSimpleVO;
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
    final public function getTopicsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true,
        bool $simplify = true
    ): ?array {
        $rows = $this->store->getTopicRowsByPage(
            $page,
            TopicModel::ITEMS_ON_PAGE,
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
     * @return array|null
     * @throws ModelException
     */
    final public function getAllTopics(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getAllTopicRows(
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getTopicsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getTopicRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / TopicModel::ITEMS_ON_PAGE);

        if ($pageCount * TopicModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeTopicById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteTopicById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws ModelException
     * @throws TopicModelException
     */
    final public function removeTopicImageById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $topicVO = $this->getVOById($id);

        if (empty($topicVO)) {
            return false;
        }

        $topicsImageDirPath = $this->_getImagesDirPath();

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
     */
    final public function restoreTopicById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreTopicById($id);
    }

    /**
     * @param ITopicForm $topicForm
     * @return bool
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function save(ITopicForm $topicForm): bool
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

            if (!$this->_uploadImageFile($topicForm)) {
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
        } catch (Throwable $thr) {
            $this->store->rollback();

            $topicForm->setStatusFail();
            $topicForm->setError($thr->getMessage());

            return false;
        }
    }

    /**
     * @param array|null $row
     * @return ITopicValuesObject
     * @throws ModelException
     */
    final protected function getVO(?array $row = null): ITopicValuesObject
    {
        /* @var $topicVO ITopicValuesObject */
        $topicVO = parent::getVO($row);

        $this->_setParentToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param ITopicValuesObject $topicVO
     * @return void
     * @throws ModelException
     */
    private function _setParentToVO(ITopicValuesObject $topicVO): void
    {
        /* @var $parentVO TopicSimpleValuesObject */
        $parentVO = $this->getSimpleVOById($topicVO->getParentId());

        if (!empty($parentVO)) {
            $topicVO->setParentVO($parentVO);
        }
    }

    /**
     * @param ITopicForm $topicForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInTopicForm(ITopicForm $topicForm): void
    {
        $id = $topicForm->getId();

        if (empty($id)) {
            return;
        }

        $topicVO = $this->_getVOFromTopicForm($topicForm);

        if (empty($topicVO)) {
            $topicForm->setStatusFail();

            $topicForm->setError(
                sprintf(
                    TopicForm::TOPIC_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param ITopicForm $topicForm
     * @param bool $isCreateVOIfEmptyId
     * @return ITopicValuesObject|null
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromTopicForm(
        ITopicForm $topicForm,
        bool $isCreateVOIfEmptyId = false
    ): ?ITopicValuesObject {
        $row = null;

        $id = $topicForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getTopicRowById(
                $id,
                false,
                false
            );
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $topicVO = new TopicValuesObject($row);

        $topicVO->setParentId($topicForm->getParentId());
        $topicVO->setTitle($topicForm->getTitle());
        $topicVO->setSlug($topicForm->getSlug());
        $topicVO->setIsActive($topicForm->isActive());

        $this->_setUniqSlugToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param ITopicForm $topicForm
     * @return void
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _checkParentIdInTopicForm(ITopicForm $topicForm): void
    {
        $parentId = $topicForm->getParentId();

        if (empty($parentId)) {
            return;
        }

        $row = $this->store->getTopicRowById($parentId);

        if (empty($row)) {
            $topicForm->setStatusFail();

            $topicForm->setError(
                TopicForm::PARENT_TOPIC_NOT_EXISTS_ERROR_MESSAGE
            );

            return;
        }

        $topicId = $topicForm->getId();

        if (empty($topicId)) {
            return;
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
    }

    /**
     * @param ITopicForm $topicForm
     * @return void
     */
    private function _checkTitleInTopicForm(ITopicForm $topicForm): void
    {
        $title = $topicForm->getTitle();
        $title = preg_replace('/^\s+$/u', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/u', '', $title);

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
     */
    private function _isTitleUniq(?string $title = null, ?int $id = null): bool
    {
        $row = $this->store->getTopicRowByTitle(
            $title,
            $id,
            false,
            false
        );

        return empty($row);
    }

    /**
     * @param ITopicValuesObject $topicVO
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _setUniqSlugToVO(ITopicValuesObject $topicVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$topicVO->getSlug();

        $slug = preg_replace('/^\s+$/u', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);

        if (empty($slug)) {
            $slug = $topicVO->getTitle();

            $slug = preg_replace('/^\s+$/u', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);
        }

        $slug = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = TopicModel::DEFAULT_SLUG;
        }

        $slug = $this->_makeSlugUniq($slug, $topicVO->getId());

        $topicVO->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param int|null $id
     * @return string|null
     */
    private function _makeSlugUniq(string $slug, ?int $id = null): ?string
    {
        if (empty($this->store->getTopicRowBySlug($slug, $id, false, false))) {
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
     * @param ITopicForm $topicForm
     * @return bool
     * @throws CoreException
     * @throws ImagePluginException
     * @throws ImageSizeException
     * @throws ImagickException
     * @throws TopicModelException
     */
    private function _uploadImageFile(ITopicForm $topicForm): bool
    {
        $id = $topicForm->getId();

        /* @var $uploadPlugin UploadPlugin */
        $uploadPlugin = $this->getPlugin('upload');

        $imageDirPath = $this->_getImagesDirPath();

        $imageFilePath = sprintf('%s/%d-topic.png', $imageDirPath, $id);

        $imageFullFilePath = sprintf(
            '%s/%s-full.png',
            $imageDirPath,
            $id
        );

        $defaultImageFilePath = sprintf(
            '%s/assets/img/broken.png',
            $this->_getPublicDirPath()
        );

        $image = $topicForm->getImage();

        if (
            empty($image) &&
            (
                !file_exists($imageFilePath) ||
                !is_file($imageFilePath)
            )
        ) {
            copy($defaultImageFilePath, $imageFilePath);
        }

        if (empty($image)) {
            return true;
        }

        if (!file_exists($imageDirPath) && !is_dir($imageDirPath)) {
            mkdir($imageDirPath, 0755, true);
        }

        $uploadPlugin->upload(
            TopicForm::IMAGE_EXTENSIONS,
            TopicForm::IMAGE_FILE_MAX_SIZE,
            TopicModel::UPLOADS_DIR_PATH
        );

        $error = $uploadPlugin->getError();

        if (!empty($error)) {
            $errorMessage = sprintf(
                TopicModelException::MESSAGE_MODEL_UPLOAD_FILE_ERROR,
                $error
            );

            throw new TopicModelException(
                $errorMessage,
                TopicException::CODE_MODEL_UPLOAD_FILE_ERROR
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

        if (file_exists($imageFullFilePath) && is_file($imageFullFilePath)) {
            unlink($imageFullFilePath);
        }

        if (file_exists($imageFilePath) && is_file($imageFilePath)) {
            unlink($imageFilePath);
        }

        /* @var $imagePlugin ImagePlugin */
        $imagePlugin = $this->getPlugin('image');

        $imagePlugin->resize(
            $uploadedFilePath,
            $imageDirPath,
            (string)$id,
            TopicValuesObject::IMAGE_FORMAT,
            TopicValuesObject::IMAGE_SIZES
        );

        if (file_exists($imageFullFilePath) && is_file($imageFullFilePath)) {
            unlink($imageFullFilePath);
        }

        unlink($uploadedFilePath);

        return true;
    }

    /**
     * @return string
     * @throws TopicModelException
     */
    private function _getImagesDirPath(): string
    {
        $publicDirPath = $this->_getPublicDirPath();

        return sprintf(TopicModel::IMAGES_DIR_PATH, $publicDirPath);
    }

    /**
     * @return string
     * @throws TopicModelException
     */
    private function _getPublicDirPath(): string
    {
        if (defined('APP_PUBLIC_DIR_PATH')) {
            return APP_PUBLIC_DIR_PATH;
        }

        $publicDirPath = realpath(__DIR__ . '/../../../../public');

        if (empty($publicDirPath)) {
            throw new TopicModelException(
                TopicModelException::MESSAGE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY,
                TopicException::CODE_MODEL_NOT_FOUND_PUBLIC_DIRECTORY
            );
        }

        return $publicDirPath;
    }
}
//TODO: add language
//TODO: missing image when changed slug
