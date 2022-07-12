<?php

namespace Sonder\Models\Comment\ValuesObjects;

use Sonder\CMS\Essentials\BaseModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Comment\Interfaces\ICommentSimpleValuesObject;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[ICommentSimpleValuesObject]
final class CommentSimpleValuesObject
    extends BaseModelSimpleValuesObject
    implements ICommentSimpleValuesObject
{
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
     * @return IUserSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getUserVO(): ?IUserSimpleValuesObject
    {
        if (!$this->has('user_simple_vo')) {
            return null;
        }

        return $this->get('user_simple_vo');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getHtml(): ?string
    {
        if (!$this->has('html')) {
            return null;
        }

        return (string)$this->get('html');
    }

    /**
     * @param IUserSimpleValuesObject|null $userSimpleVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUserVO(
        ?IUserSimpleValuesObject $userSimpleVO = null
    ): void
    {
        if (!empty($userSimpleVO)) {
            $this->set('user_simple_vo', $userSimpleVO);
        }
    }

    /**
     * @return array
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
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
