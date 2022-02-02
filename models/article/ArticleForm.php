<?php

namespace Sonder\Models\Article;

use Exception;
use Sonder\Core\ModelFormObject;

final class ArticleForm extends ModelFormObject
{
    const TITLE_MIN_LENGTH = 3;

    const TITLE_MAX_LENGTH = 64;

    const SLUG_MAX_LENGTH = 128;

    const TEXT_MIN_LENGTH = 3;

    const SUMMARY_MAX_LENGTH = 512;

    const META_TITLE_MAX_LENGTH = 255;

    const META_DESCRIPTION_MAX_LENGTH = 512;

    const TITLE_EMPTY_ERROR_MESSAGE = 'Title is empty';

    const TITLE_TOO_SHORT_ERROR_MESSAGE = 'Title is too short';

    const TITLE_TOO_LONG_ERROR_MESSAGE = 'Title is too long';

    const TITLE_EXISTS_ERROR_MESSAGE = 'Article with this title already exists';

    const TEXT_EMPTY_ERROR_MESSAGE = 'Text is empty';

    const TEXT_IS_TOO_SHORT_ERROR_MESSAGE = 'Text is too short';

    const SUMMARY_TOO_LONG_ERROR_MESSAGE = 'Summary is too long';

    const META_TITLE_TOO_LONG_ERROR_MESSAGE = 'Meta Title is too long';

    const META_TITLE_EXISTS_ERROR_MESSAGE = 'Article with this meta title ' .
    'already exists';

    const SLUG_TOO_LONG_ERROR_MESSAGE = 'Slug is too long';

    const TOPIC_IS_NOT_SET_ERROR_MESSAGE = 'Topic is not set';

    const TOPICS_ARE_NOT_EXISTS_ERROR_MESSAGE = 'Any active topics exists. ' .
    'You need to add first one for creating articles';

    const TAGS_ARE_NOT_SET_ERROR_MESSAGE = 'Tags are not set';

    const TAGS_ARE_NOT_EXISTS_ERROR_MESSAGE = 'Any active tags exists. You ' .
    'need to add first one for creating articles';

    const TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE = 'Topic with id "%d" is not ' .
    'exists';

    const ARTICLE_IS_NOT_EXISTS_ERROR_MESSAGE = 'Article with id "%d" is not ' .
    'exists';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateTitleValue();
        $this->_validateTextValue();
        $this->_validateSlugValue();
        $this->_validateTopicIdValue();
        $this->_validateTags();
        $this->_validateSummaryValue();
        $this->_validateMetaTitleValue();
        $this->_validateMetaDescriptionValue();
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
     * @return string|null
     * @throws Exception
     */
    final public function getImage(): ?string
    {
        if ($this->has('image')) {
            return $this->get('image');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getSummary(): ?string
    {
        if ($this->has('summary')) {
            return $this->get('summary');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getText(): ?string
    {
        if ($this->has('text')) {
            return $this->get('text');
        }

        return null;
    }

    /**
     * @return int|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function getUserId(): ?int
    {
        if (!$this->has('user_id')) {
            return null;
        }

        $userId = $this->get('user_id');

        if (empty($userId)) {
            return null;
        }

        return (int)$userId;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getMetaTitle(): ?string
    {
        if ($this->has('meta_title')) {
            return $this->get('meta_title');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getMetaDescription(): ?string
    {
        if ($this->has('meta_description')) {
            return $this->get('meta_description');
        }

        return null;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getTags(): ?array
    {
        if (!$this->has('tags')) {
            return null;
        }

        $tags = $this->get('tags');

        if (!empty($tags) && is_array($tags)) {
            return $tags;
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
     * @param string|null $image
     * @return void
     * @throws Exception
     */
    final public function setImage(?string $image = null): void
    {
        $this->set('image', $image);
    }

    /**
     * @param string|null $summary
     * @return void
     * @throws Exception
     */
    final public function setSummary(?string $summary = null): void
    {
        $this->set('summary', $summary);
    }

    /**
     * @param string|null $text
     * @return void
     * @throws Exception
     */
    final public function setText(?string $text = null): void
    {
        $this->set('text', $text);
    }

    /**
     * @param int|null $topicId
     * @return void
     * @throws Exception
     */
    final public function setTopicId(?int $topicId = null): void
    {
        $this->set('topic_id', $topicId);
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws Exception
     */
    final public function setUserId(?int $userId = null): void
    {
        $this->set('user_id', $userId);
    }


    /**
     * @param string|null $metaTitle
     * @return void
     * @throws Exception
     */
    final public function setMetaTitle(?string $metaTitle = null): void
    {
        $this->set('meta_title', $metaTitle);
    }

    /**
     * @param string|null $metaDescription
     * @return void
     * @throws Exception
     */
    final public function setMetaDescription(
        ?string $metaDescription = null
    ): void
    {
        $this->set('meta_description', $metaDescription);
    }

    /**
     * @param array|null $tags
     * @return void
     * @throws Exception
     */
    final public function setTags(?array $tags = null): void
    {
        $this->set('tags', $tags);
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
            $this->setError(ArticleForm::TITLE_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($title) &&
            mb_strlen($title) > ArticleForm::TITLE_MAX_LENGTH
        ) {
            $this->setError(ArticleForm::TITLE_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($title) &&
            mb_strlen($title) < ArticleForm::TITLE_MIN_LENGTH
        ) {
            $this->setError(ArticleForm::TITLE_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateTextValue(): void
    {
        $text = $this->getText();

        if (empty($text)) {
            $this->setError(ArticleForm::TEXT_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($text) &&
            mb_strlen($text) < ArticleForm::TEXT_MIN_LENGTH
        ) {
            $this->setError(ArticleForm::TEXT_IS_TOO_SHORT_ERROR_MESSAGE);
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

        if (!empty($slug) && mb_strlen($slug) > ArticleForm::SLUG_MAX_LENGTH) {
            $this->setError(ArticleForm::SLUG_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateTopicIdValue(): void
    {
        $topicId = $this->getTopicId();

        if (empty($topicId)) {
            $this->setError(ArticleForm::TOPIC_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateTags(): void
    {
        $tags = $this->getTags();

        if (empty($tags)) {
            $this->setError(ArticleForm::TAGS_ARE_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateSummaryValue(): void
    {
        $summary = $this->getSummary();

        if (
            !empty($summary) &&
            mb_strlen($summary) > ArticleForm::SUMMARY_MAX_LENGTH
        ) {
            $this->setError(ArticleForm::SUMMARY_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateMetaTitleValue(): void
    {
        $metaTitle = $this->getMetaTitle();

        if (
            !empty($metaTitle) &&
            mb_strlen($metaTitle) > ArticleForm::META_TITLE_MAX_LENGTH
        ) {
            $this->setError(
                ArticleForm::META_TITLE_TOO_LONG_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateMetaDescriptionValue(): void
    {
        $metaDescription = $this->getMetaDescription();

        $metaDescriptionMaxLength = ArticleForm::META_DESCRIPTION_MAX_LENGTH;

        if (
            !empty($metaDescription) &&
            mb_strlen($metaDescription) > $metaDescriptionMaxLength
        ) {
            $this->setError(
                ArticleForm::META_TITLE_TOO_LONG_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }
}
