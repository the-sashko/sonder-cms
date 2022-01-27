<?php

namespace Sonder\Models\Topic;

use Exception;
use Sonder\Core\ModelFormObject;

final class TopicForm extends ModelFormObject
{
    const TITLE_MIN_LENGTH = 3;

    const TITLE_MAX_LENGTH = 128;

    const TITLE_EMPTY_ERROR_MESSAGE = 'Title is empty';

    const TITLE_TOO_SHORT_ERROR_MESSAGE = 'Title is too short';

    const TITLE_TOO_LONG_ERROR_MESSAGE = 'Title is too long';

    const TITLE_EXISTS_ERROR_MESSAGE = 'Topic with this title already exists';

    const TOPIC_HAVE_CIRCULAR_DEPENDENCY_ERROR_MESSAGE = 'Topic can not have ' .
    'a circular dependencies';

    const TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE = 'Topic with id "%d" is not ' .
    'exists';

    const PARENT_TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE = 'Parent Topic with id ' .
    '"%d" is not exists';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateTitleValue();
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
    final public function getIsActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
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
}
