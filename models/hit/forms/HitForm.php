<?php

namespace Sonder\Models\Hit;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Hit\Enums\HitTypesEnum;
use Sonder\Models\Hit\Interfaces\IHitForm;

#[IModelFormObject]
#[IHitForm]
final class HitForm extends ModelFormObject implements IHitForm
{
    final public const INVALID_TYPE_ERROR_MESSAGE = 'Invalid hit type "%s';

    final public const INVALID_AGGREGATION_TYPE_ERROR_MESSAGE = 'Invalid aggregation type "%s"';

    final public const HITS_NOT_EXISTS_ERROR_MESSAGE = 'Hit with id "%d" not exists';

    final public const HITS_AGGREGATION_NOT_EXISTS_ERROR_MESSAGE = 'Hits aggregation with id "%d" not exists';

    final public const ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE = 'Article ID Is Not Set';

    final public const TOPIC_ID_IS_NOT_SET_ERROR_MESSAGE = 'Topic ID Is Not Set';

    final public const TAG_ID_IS_NOT_SET_ERROR_MESSAGE = 'Topic ID Is Not Set';

    final public const ARTICLE_NOT_EXISTS_ERROR_MESSAGE = 'Article with id "%d" not exists';

    final public const TOPIC_NOT_EXISTS_ERROR_MESSAGE = 'Topic with id "%d" not exists';

    final public const TAG_NOT_EXISTS_ERROR_MESSAGE = 'Tag with id "%d" not exists';

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateTypeValue();

        switch ($this->getType()) {
            case HitTypesEnum::ARTICLE->value:
                $this->_validateArticleIdValue();
                break;
            case HitTypesEnum::TOPIC->value:
                $this->_validateTopicIdValue();
                break;
            case HitTypesEnum::TAG->value:
                $this->_validateTagIdValue();
                break;
        }
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
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getArticleId(): ?int
    {
        if (!$this->has('article_id')) {
            return null;
        }

        $articleId = $this->get('article_id');

        if (empty($articleId)) {
            return null;
        }

        return (int)$articleId;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getTopicId(): ?int
    {
        if (!$this->has('topic_id')) {
            return null;
        }

        $topicId = $this->get('topic_id');

        if (empty($topicId)) {
            return null;
        }

        return (int)$topicId;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getTagId(): ?int
    {
        if (!$this->has('tag_id')) {
            return null;
        }

        $tagId = $this->get('tag_id');

        if (empty($tagId)) {
            return null;
        }

        return (int)$tagId;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getType(): ?string
    {
        $type = $this->get('type');

        if (empty($type)) {
            return null;
        }

        return (string)$type;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getCount(): ?int
    {
        if (!$this->has('count')) {
            return null;
        }

        return (int)$this->get('count');
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
     * @param int|null $count
     * @return void
     * @throws ValuesObjectException
     */
    final public function setCount(?int $count = null): void
    {
        $this->set('count', (int)$count);
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
    private function _validateTypeValue(): void
    {
        $type = $this->getType();

        if (!HitTypesEnum::tryFrom($type)) {
            $errorMessage = sprintf(
                HitForm::INVALID_TYPE_ERROR_MESSAGE,
                $type
            );

            $this->setError($errorMessage);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateArticleIdValue(): void
    {
        if (empty($this->getArticleId())) {
            $this->setError(HitForm::ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateTopicIdValue(): void
    {
        if (empty($this->getTopicId())) {
            $this->setError(HitForm::TOPIC_ID_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateTagIdValue(): void
    {
        if (empty($this->getTagId())) {
            $this->setError(HitForm::TAG_ID_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
