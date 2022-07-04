<?php

namespace Sonder\Models\Comment\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Comment\Interfaces\ICommentForm;

#[IModelFormObject]
#[ICommentForm]
final class CommentForm extends ModelFormObject implements ICommentForm
{
    final public const TEXT_EMPTY_ERROR_MESSAGE = 'Text is empty';

    final public const TEXT_IS_TOO_LONG_ERROR_MESSAGE = 'Text is too long';

    final public const USER_NAME_IS_EMPTY_ERROR_MESSAGE = 'Name is empty';

    final public const USER_NAME_IS_TOO_SHORT_ERROR_MESSAGE = 'Name is too short';

    final public const USER_NAME_IS_TOO_LONG_ERROR_MESSAGE = 'Name is too long';

    final public const USER_EMAIL_IS_EMPTY_ERROR_MESSAGE = 'Email is empty';

    final public const USER_EMAIL_IS_TOO_LONG_ERROR_MESSAGE = 'Email is too long';

    final public const USER_EMAIL_HAS_BAD_FORMAT_ERROR_MESSAGE = 'Email has bad format';

    final public const ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE = 'Article ID Is Not Set';

    final public const COMMENT_NOT_EXISTS_ERROR_MESSAGE = 'Comment with id "%d" not exists';

    final public const PARENT_COMMENT_NOT_EXISTS_ERROR_MESSAGE = 'Parent comment with id "%d" not exists';

    final public const ARTICLE_NOT_EXISTS_ERROR_MESSAGE = 'Article with id "%d" not exists';

    final public const USER_NOT_EXISTS_ERROR_MESSAGE = 'User with id "%d" not exists';

    private const TEXT_MAX_LENGTH = 1024;

    private const USER_NAME_MIN_LENGTH = 3;

    private const USER_NAME_MAX_LENGTH = 255;

    private const USER_EMAIl_MAX_LENGTH = 255;

    private const EMAIL_PATTERN = '/^(.*?)@(.*?)\.(.*?)$/su';

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateTextValue();
        $this->_validateArticleIdValue();
        $this->_validateUserNameValue();
        $this->_validateUserEmailValue();
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getArticleId(): ?int
    {
        if (!$this->has('article_id')) {
            return null;
        }

        $topicId = $this->get('article_id');

        if (empty($topicId)) {
            return null;
        }

        return (int)$topicId;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getUserName(): ?string
    {
        if ($this->has('user_name')) {
            return $this->get('user_name');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getUserEmail(): ?string
    {
        if ($this->has('user_email')) {
            $emailEmail = $this->get('user_email');

            $emailEmail = preg_replace(
                '/(\s+)/u',
                '',
                $emailEmail
            );

            return empty($emailEmail) ? null : $emailEmail;
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
     * @param int|null $parentId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentId(?int $parentId = null): void
    {
        $this->set('parent_id', $parentId);
    }

    /**
     * @param string|null $text
     * @return void
     * @throws ValuesObjectException
     */
    final public function setText(?string $text = null): void
    {
        $this->set('text', $text);
    }

    /**
     * @param int|null $articleId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setArticleId(?int $articleId = null): void
    {
        $this->set('article_id', $articleId);
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserId(?int $userId = null): void
    {
        $this->set('user_id', $userId);
    }

    /**
     * @param string|null $userName
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserName(?string $userName = null): void
    {
        $this->set('user_name', $userName);
    }

    /**
     * @param string|null $userEmail
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserEmail(?string $userEmail = null): void
    {
        $userEmail = preg_replace(
            '/(\s+)/u',
            '',
            $userEmail
        );

        $this->set('user_email', $userEmail);
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
    private function _validateTextValue(): void
    {
        $text = $this->getText();

        if (empty($text)) {
            $this->setError(CommentForm::TEXT_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($text) &&
            mb_strlen($text) > CommentForm::TEXT_MAX_LENGTH
        ) {
            $this->setError(CommentForm::TEXT_IS_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateArticleIdValue(): void
    {
        $articleId = $this->getArticleId();

        if (empty($articleId)) {
            $this->setError(
                CommentForm::ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateUserNameValue(): void
    {
        $userId = $this->getUserId();
        $userName = $this->getUserName();

        if (empty($userId) && empty($userName)) {
            $this->setError(CommentForm::USER_NAME_IS_EMPTY_ERROR_MESSAGE);

            $this->setStatusFail();
        }

        if (
            !empty($userName) &&
            mb_strlen($userName) < CommentForm::USER_NAME_MIN_LENGTH
        ) {
            $this->setError(
                CommentForm::USER_NAME_IS_TOO_SHORT_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }

        if (
            !empty($userName) &&
            mb_strlen($userName) > CommentForm::USER_NAME_MAX_LENGTH
        ) {
            $this->setError(
                CommentForm::USER_NAME_IS_TOO_LONG_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateUserEmailValue(): void
    {
        $userId = $this->getUserId();
        $userEmail = $this->getUserEmail();

        if (empty($userId) && empty($userEmail)) {
            $this->setError(
                CommentForm::USER_EMAIL_IS_EMPTY_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }

        if (
            !empty($userEmail) &&
            mb_strlen($userEmail) > CommentForm::USER_EMAIl_MAX_LENGTH
        ) {
            $this->setError(
                CommentForm::USER_EMAIL_IS_TOO_LONG_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }

        if (
            !empty($userEmail) &&
            !preg_match(CommentForm::EMAIL_PATTERN, $userEmail)
        ) {
            $this->setError(
                CommentForm::USER_EMAIL_HAS_BAD_FORMAT_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }
}
