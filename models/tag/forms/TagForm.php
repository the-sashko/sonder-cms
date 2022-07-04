<?php

namespace Sonder\Models\Tag\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Tag\Interfaces\ITagForm;

#[IModelFormObject]
#[ITagForm]
final class TagForm extends ModelFormObject implements ITagForm
{
    final public const TITLE_EMPTY_ERROR_MESSAGE = 'Title is empty';

    final public const TITLE_TOO_SHORT_ERROR_MESSAGE = 'Title is too short';

    final public const TITLE_TOO_LONG_ERROR_MESSAGE = 'Title is too long';

    final public const TITLE_EXISTS_ERROR_MESSAGE = 'Tag with this title already exists';

    final public const SLUG_TOO_LONG_ERROR_MESSAGE = 'Slug is too long';

    final public const TAG_NOT_EXISTS_ERROR_MESSAGE = 'Tag with id "%d" not exists';

    private const TITLE_MIN_LENGTH = 3;

    private const TITLE_MAX_LENGTH = 64;

    private const SLUG_MAX_LENGTH = 128;

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateTitleValue();
        $this->_validateSlugValue();
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @return string|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function isActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws ValuesObjectException
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $title
     * @return void
     * @throws ValuesObjectException
     */
    final public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * @param string|null $slug
     * @return void
     * @throws ValuesObjectException
     */
    final public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateTitleValue(): void
    {
        $title = $this->getTitle();

        if (empty($title)) {
            $this->setError(TagForm::TITLE_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($title) && mb_strlen($title) > TagForm::TITLE_MAX_LENGTH) {
            $this->setError(TagForm::TITLE_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($title) && mb_strlen($title) < TagForm::TITLE_MIN_LENGTH) {
            $this->setError(TagForm::TITLE_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateSlugValue(): void
    {
        $slug = $this->getSlug();

        if (!empty($slug) && mb_strlen($slug) > TagForm::SLUG_MAX_LENGTH) {
            $this->setError(TagForm::SLUG_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
