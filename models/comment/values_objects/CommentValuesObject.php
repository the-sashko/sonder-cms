<?php

namespace Sonder\Models\Comment\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Comment\Interfaces\ICommentValuesObject;
use Sonder\Models\User\Interfaces\IUserValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[ICommentValuesObject]
final class CommentValuesObject
    extends BaseModelValuesObject
    implements ICommentValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/comment/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/comment/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/comment/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/comment/view/%d/';

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

        return empty($parentId) ? null : (int)$parentId;
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

        return empty($articleId) ? null : (int)$articleId;
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

        return empty($userId) ? null : (int)$userId;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getUserName(): ?string
    {
        if (!$this->has('user_name')) {
            return null;
        }

        $userName = $this->get('user_name');

        return empty($userName) ? null : (string)$userName;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getUserEmail(): ?string
    {
        if (!$this->has('user_email')) {
            return null;
        }

        $userEmail = $this->get('user_email');

        return empty($userEmail) ? null : (string)$userEmail;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getUserIp(): ?string
    {
        if (!$this->has('user_ip')) {
            return null;
        }

        $userIp = $this->get('user_ip');

        return empty($userIp) ? null : (string)$userIp;
    }

    /**
     * @return IUserValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getUserVO(): ?IUserValuesObject
    {
        if (!$this->has('user_vo')) {
            return null;
        }

        return $this->get('user_vo');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getText(): string
    {
        return (string)$this->get('text');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getHtml(): string
    {
        return (string)$this->get('html');
    }

    /**
     * @param int|null $parentId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentId(?int $parentId = null): void
    {
        if (!empty($parentId)) {
            $this->set('parent_id', $parentId);
        }
    }

    /**
     * @param int|null $articleId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setArticleId(?int $articleId = null): void
    {
        if (!empty($userId)) {
            $this->set('article_id', $articleId);
        }
    }

    /**
     * @param int|null $userId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserId(?int $userId = null): void
    {
        if (!empty($userId)) {
            $this->set('user_id', $userId);
        }
    }

    /**
     * @param string|null $userName
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserName(?string $userName = null): void
    {
        if (!empty($userName)) {
            $this->set('user_name', $userName);
        }
    }

    /**
     * @param string|null $userEmail
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserEmail(?string $userEmail = null): void
    {
        if (!empty($userEmail)) {
            $this->set('user_email', $userEmail);
        }
    }

    /**
     * @param string|null $userIp
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserIp(?string $userIp = null): void
    {
        if (!empty($userIp)) {
            $this->set('user_ip', $userIp);
        }
    }

    /**
     * @param IUserValuesObject|null $userVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserVO(?IUserValuesObject $userVO = null): void
    {
        if (!empty($userVO)) {
            $this->set('user_vo', $userVO);
        }
    }

    /**
     * @param string|null $text
     * @return void
     * @throws ValuesObjectException
     */
    final public function setText(?string $text = null): void
    {
        if (!empty($text)) {
            $this->set('text', $text);
        }
    }

    /**
     * @param string|null $html
     * @return void
     * @throws ValuesObjectException
     */
    final public function setHtml(?string $html = null): void
    {
        if (!empty($html)) {
            $this->set('html', $html);
        }
    }

    /**
     * @return array
     */
    final public function exportRow(): array
    {
        $row = parent::exportRow();

        if (empty($row)) {
            return $row;
        }

        if (array_key_exists('user_vo', $row) && empty($row['user_vo'])) {
            unset($row['user_vo']);
        }

        if (array_key_exists('user_ip', $row)) {
            unset($row['user_ip']);
        }

        return $row;
    }
}
