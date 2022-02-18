<?php

namespace Sonder\Models\Comment;

use Exception;
use Sonder\CMS\Essentials\ModelValuesObject;
use Sonder\Models\User\UserValuesObject;

final class CommentValuesObject extends ModelValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/comment/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/comment/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/comment/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/comment/view/%d/';

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

        return empty($parentId) ? null : (int)$parentId;
    }

    /**
     * @return int|null
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @return UserValuesObject|null
     * @throws Exception
     */
    final public function getUserVO(): ?UserValuesObject
    {
        if (!$this->has('user_vo')) {
            return null;
        }

        return $this->get('user_vo');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getText(): string
    {
        return (string)$this->get('text');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getHtml(): string
    {
        return (string)$this->get('html');
    }

    /**
     * @param int|null $parentId
     * @return void
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setUserIp(?string $userIp = null): void
    {
        if (!empty($userIp)) {
            $this->set('user_ip', $userIp);
        }
    }

    /**
     * @param UserValuesObject|null $userVO
     * @return void
     * @throws Exception
     */
    final public function setUserVO(?UserValuesObject $userVO = null): void
    {
        if (!empty($userVO)) {
            $this->set('user_vo', $userVO);
        }
    }

    /**
     * @param string|null $text
     * @return void
     * @throws Exception
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
     * @throws Exception
     */
    final public function setHtml(?string $html = null): void
    {
        if (!empty($html)) {
            $this->set('html', $html);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     */
    final public function exportRow(?array $params = null): ?array
    {
        $row = parent::exportRow($params);

        if (empty($row)) {
            return null;
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
