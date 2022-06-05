<?php

namespace Sonder\Models\Comment;

use Exception;
use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Models\User\UserSimpleValuesObject;

final class CommentSimpleValuesObject extends ModelSimpleValuesObject
{
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
     * @return UserSimpleValuesObject|null
     * @throws Exception
     */
    final public function getUserVO(): ?UserSimpleValuesObject
    {
        if (!$this->has('user_simple_vo')) {
            return null;
        }

        return $this->get('user_simple_vo');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getHtml(): ?string
    {
        if (!$this->has('html')) {
            return null;
        }

        return (string)$this->get('html');
    }

    /**
     * @param UserSimpleValuesObject|null $userSimpleVO
     * @return void
     * @throws Exception
     */
    final public function setUserVO(
        ?UserSimpleValuesObject $userSimpleVO = null
    ): void
    {
        if (!empty($userSimpleVO)) {
            $this->set('user_simple_vo', $userSimpleVO);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        $userName = $this->getUserName();
        $userEmail = $this->getUserEmail();

        $userVO = $this->getUserVO();

        if (!empty($userVO)) {
            $userEmail = empty($userEmail) ? $userVO->getEmail() : $userEmail;
            $userName = empty($userName) ? $userVO->getEmail() : $userName;
        }

        return [
            'id' => $this->getId(),
            'html' => $this->getHtml(),
            'user_name' => $userName,
            'user_email' => $userEmail,
            'user' => empty($userVO) ? null : $userVO->exportRow()
        ];
    }
}
