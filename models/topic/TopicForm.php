<?php

namespace Sonder\Models\Topic;

use Exception;
use Sonder\Core\ModelFormObject;

final class TopicForm extends ModelFormObject
{
    const TITLE_MIN_LENGTH = 3;

    const TITLE_MAX_LENGTH = 64;

    const SLUG_MAX_LENGTH = 128;

    const IMAGE_FILE_MAX_SIZE = 1024 * 1024 * 16; //16MB

    const IMAGE_EXTENSIONS = ['jpg', 'png', 'gif'];

    const TITLE_EMPTY_ERROR_MESSAGE = 'Title is empty';

    const TITLE_TOO_SHORT_ERROR_MESSAGE = 'Title is too short';

    const TITLE_TOO_LONG_ERROR_MESSAGE = 'Title is too long';

    const TITLE_EXISTS_ERROR_MESSAGE = 'Topic with this title already exists';

    const SLUG_TOO_LONG_ERROR_MESSAGE = 'Slug is too long';

    const TOPIC_HAVE_CIRCULAR_DEPENDENCY_ERROR_MESSAGE = 'Topic can not have ' .
    'a circular dependencies';

    const TOPIC_NOT_EXISTS_ERROR_MESSAGE = 'Topic with id "%d" not exists';

    const PARENT_TOPIC_NOT_EXISTS_ERROR_MESSAGE = 'Parent Topic with id "%d" ' .
    'not exists';

    const IMAGE_FILE_TOO_LARGE_ERROR_MESSAGE = 'Image file in too large';

    const IMAGE_FILE_HAS_BAD_EXTENSION_ERROR_MESSAGE = 'Image file has bad ' .
    'extension';

    const UPLOAD_IMAGE_FILE_ERROR_MESSAGE = 'Can not upload image file';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_setImageFileFromRequest();

        $this->_validateTitleValue();
        $this->_validateSlugValue();
        $this->_validateImage();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        $id = $this->get('id');

        if (empty($id)) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getParentId(): ?int
    {
        if (!$this->has('parent_id')) {
            return null;
        }

        $parentId = $this->get('parent_id');

        if (empty($parentId)) {
            return null;
        }

        return (int)$parentId;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getTitle(): ?string
    {
        if ($this->has('title')) {
            return $this->get('title');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getSlug(): ?string
    {
        if ($this->has('slug')) {
            return $this->get('slug');
        }

        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    final public function isActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getImage(): ?array
    {
        if (!$this->has('image')) {
            return null;
        }

        $image = $this->get('image');

        if (empty($image) || !is_array($image)) {
            return null;
        }

        return $image;
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $title
     * @return void
     * @throws Exception
     */
    final public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * @param string|null $slug
     * @return void
     * @throws Exception
     */
    final public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @param array|null $image
     * @return void
     * @throws Exception
     */
    final public function setImage(?array $image = null): void
    {
        $this->set('image', $image);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateTitleValue(): void
    {
        $title = $this->getTitle();

        if (empty($title)) {
            $this->setError(TopicForm::TITLE_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($title) && mb_strlen($title) > TopicForm::TITLE_MAX_LENGTH) {
            $this->setError(TopicForm::TITLE_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($title) && mb_strlen($title) < TopicForm::TITLE_MIN_LENGTH) {
            $this->setError(TopicForm::TITLE_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateSlugValue(): void
    {
        $slug = $this->getSlug();

        if (!empty($slug) && mb_strlen($slug) > TopicForm::SLUG_MAX_LENGTH) {
            $this->setError(TopicForm::SLUG_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateImage(): void
    {
        $image = $this->getImage();

        $isImageSet = !empty($image) &&
            array_key_exists('size', $image) &&
            $image['size'] > 0;

        if (!$isImageSet) {
            $this->setImage();
        }

        if ($isImageSet && $image['error']) {
            $this->setError(TopicForm::UPLOAD_IMAGE_FILE_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if ($isImageSet && $image['size'] > TopicForm::IMAGE_FILE_MAX_SIZE) {
            $this->setError(TopicForm::IMAGE_FILE_TOO_LARGE_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            $isImageSet &&
            !in_array($image['extension'], TopicForm::IMAGE_EXTENSIONS)
        ) {
            $this->setError(
                TopicForm::IMAGE_FILE_HAS_BAD_EXTENSION_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _setImageFileFromRequest(): void
    {
        $image = $this->getFileValueFromRequest('image');

        $this->set('image', $image);
    }
}
