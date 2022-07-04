<?php

namespace Sonder\Models\Hit\ValuesObjects;

use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Hit\Enums\HitTypesEnum;
use Sonder\Models\Hit\Exceptions\HitException;
use Sonder\Models\Hit\Exceptions\HitSimpleValuesObjectException;
use Sonder\Models\Hit\Interfaces\IHitSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IHitSimpleValuesObject]
final class HitSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IHitSimpleValuesObject
{
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

        return empty($articleId) ? null : (int)$articleId;
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

        return empty($topicId) ? null : (int)$topicId;
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

        return empty($tagId) ? null : (int)$tagId;
    }

    /**
     * @return string
     * @throws HitSimpleValuesObjectException
     * @throws ValuesObjectException
     */
    final public function getType(): string
    {
        if (!empty($this->getArticleId())) {
            return HitTypesEnum::ARTICLE->value;
        }

        if (!empty($this->getTagId())) {
            return HitTypesEnum::TAG->value;
        }

        if (!empty($this->getTopicId())) {
            return HitTypesEnum::TOPIC->value;
        }

        throw new HitSimpleValuesObjectException(
            HitSimpleValuesObjectException::MESSAGE_SIMPLE_VALUES_OBJECT_INVALID_TYPE,
            HitException::CODE_SIMPLE_VALUES_OBJECT_INVALID_TYPE,
        );
    }

    /**
     * @return array
     * @throws HitSimpleValuesObjectException
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
    {
        $id = null;
        $id = empty($this->getTopicId()) ? $this->getTopicId() : $id;
        $id = empty($this->getTagId()) ? $this->getTagId() : $id;
        $id = empty($this->getArticleId()) ? $this->getTopicId() : $id;

        return [
            'id' => $id,
            'type' => $this->getType()
        ];
    }
}
