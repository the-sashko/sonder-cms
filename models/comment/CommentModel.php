<?php

namespace Sonder\Models;

use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Comment\Forms\CommentForm;
use Sonder\Models\Comment\Interfaces\ICommentApi;
use Sonder\Models\Comment\Interfaces\ICommentForm;
use Sonder\Models\Comment\Interfaces\ICommentModel;
use Sonder\Models\Comment\Interfaces\ICommentSimpleValuesObject;
use Sonder\Models\Comment\Interfaces\ICommentStore;
use Sonder\Models\Comment\Interfaces\ICommentValuesObject;
use Sonder\Models\Comment\ValuesObjects\CommentSimpleValuesObject;
use Sonder\Models\Comment\ValuesObjects\CommentValuesObject;
use Sonder\Models\User\ValuesObjects\UserSimpleValuesObject;
use Sonder\Models\User\ValuesObjects\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\IpPlugin;
use Sonder\Plugins\LinkPlugin;
use Sonder\Plugins\MarkupPlugin;
use Throwable;

/**
 * @property ICommentApi $api
 * @property ICommentStore $store
 */
#[IModel]
#[ICommentModel]
final class CommentModel extends BaseModel implements ICommentModel
{
    final protected const ITEMS_ON_PAGE = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ICommentValuesObject|null
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ICommentValuesObject {
        $row = $this->store->getCommentRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ICommentSimpleValuesObject|null
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ICommentSimpleValuesObject {
        $row = $this->store->getCommentRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getSimpleVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getCommentsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getCommentRowsByPage(
            $page,
            CommentModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getCommentsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getCommentRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / CommentModel::ITEMS_ON_PAGE);

        if ($pageCount * CommentModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getCommentsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($articleId)) {
            return null;
        }

        $rows = $this->store->getCommentRowsByArticleId(
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getArticlesPageCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        if (empty($articleId)) {
            return 0;
        }

        $rowsCount = $this->store->getCommentRowsCountByArticleId(
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / CommentModel::ITEMS_ON_PAGE);

        if ($pageCount * CommentModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     * @throws ModelException
     */
    final public function getCommentsByUserId(
        ?int $userId = null,
        int $page = 1
    ): ?array {
        if (empty($userId)) {
            return null;
        }

        $rows = $this->store->getCommentRowsByUserId(
            $userId,
            $page,
            CommentModel::ITEMS_ON_PAGE
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $userId
     * @return int
     */
    final public function getCommentsPageCountByUserId(?int $userId = null): int
    {
        if (empty($userId)) {
            return 0;
        }

        $rowsCount = $this->store->getCommentRowsCountByUserId($userId);

        $pageCount = (int)($rowsCount / CommentModel::ITEMS_ON_PAGE);

        if ($pageCount * CommentModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeCommentById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteCommentById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreCommentById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreCommentById($id);
    }

    /**
     * @param ICommentForm $commentForm
     * @return bool
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function save(ICommentForm $commentForm): bool
    {
        $commentForm->checkInputValues();

        if (!$commentForm->getStatus()) {
            return false;
        }

        $this->_checkIdInCommentForm($commentForm);
        $this->_checkParentIdInCommentForm($commentForm);
        $this->_checkArticleIdInCommentForm($commentForm);
        $this->_checkUserIdInCommentForm($commentForm);

        if (!$commentForm->getStatus()) {
            return false;
        }

        $commentVO = $this->_getVOFromCommentForm(
            $commentForm,
            true
        );

        try {
            if (!$this->store->insertOrUpdateComment($commentVO)) {
                $commentForm->setStatusFail();

                return false;
            }
        } catch (Throwable $thr) {
            $commentForm->setStatusFail();
            $commentForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param array|null $row
     * @return ICommentValuesObject
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getVO(?array $row = null): ICommentValuesObject
    {
        /* @var $commentVO CommentValuesObject */
        $commentVO = parent::getVO($row);

        $this->_setUserVOToVO($commentVO);

        return $commentVO;
    }

    /**
     * @param array|null $row
     * @return ICommentSimpleValuesObject
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getSimpleVO(
        ?array $row = null
    ): ICommentSimpleValuesObject {
        /* @var $commentSimpleVO CommentSimpleValuesObject */
        $commentSimpleVO = parent::getVO($row);

        $this->_setSimpleUserVOToSimpleVO($commentSimpleVO);

        return $commentSimpleVO;
    }

    /**
     * @param ICommentValuesObject $commentVO
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _setUserVOToVO(ICommentValuesObject $commentVO): void
    {
        /* @var $userModel UserModel */
        $userModel = $this->getModel('user');

        /* @var $userVO UserValuesObject */
        $userVO = $userModel->getVOById($commentVO->getUserId());

        if (!empty($userVO)) {
            $commentVO->setUserVO($userVO);
        }
    }

    /**
     * @param ICommentSimpleValuesObject $commentSimpleVO
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _setSimpleUserVOToSimpleVO(
        ICommentSimpleValuesObject $commentSimpleVO
    ): void {
        /* @var $userModel UserModel */
        $userModel = $this->getModel('user');

        /* @var $userVO UserSimpleValuesObject */
        $userVO = $userModel->getSimpleVOById($commentSimpleVO->getUserId());

        if (!empty($userVO)) {
            $commentSimpleVO->setUserVO($userVO);
        }
    }

    /**
     * @param ICommentForm $commentForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInCommentForm(ICommentForm $commentForm): void
    {
        $id = $commentForm->getId();

        if (empty($id)) {
            return;
        }

        $commentVO = $this->_getVOFromCommentForm($commentForm);

        if (empty($commentVO)) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                sprintf(
                    CommentForm::COMMENT_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param ICommentForm $commentForm
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _checkParentIdInCommentForm(
        ICommentForm $commentForm
    ): void {
        $parentId = $commentForm->getParentId();

        if (empty($parentId)) {
            return;
        }

        if (empty($this->getVOById($parentId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                sprintf(
                    CommentForm::PARENT_COMMENT_NOT_EXISTS_ERROR_MESSAGE,
                    $parentId
                )
            );
        }
    }

    /**
     * @param ICommentForm $commentForm
     * @return void
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _checkUserIdInCommentForm(ICommentForm $commentForm): void
    {
        $userId = $commentForm->getUserId();

        if (empty($userId)) {
            return;
        }

        /* @var $userModel UserModel */
        $userModel = $this->getModel('user');

        if (empty($userModel->getVOById($userId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                sprintf(
                    CommentForm::USER_NOT_EXISTS_ERROR_MESSAGE,
                    $userId
                )
            );
        }
    }

    /**
     * @param ICommentForm $commentForm
     * @return void
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _checkArticleIdInCommentForm(
        ICommentForm $commentForm
    ): void {
        $articleId = $commentForm->getArticleId();

        if (empty($articleId)) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                CommentForm::ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE
            );

            return;
        }

        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        if (empty($articleModel->getVOById($articleId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                sprintf(
                    CommentForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                    $articleId
                )
            );
        }
    }

    /**
     * @param ICommentForm $commentForm
     * @param bool $isCreateVOIfEmptyId
     * @return ICommentValuesObject|null
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromCommentForm(
        ICommentForm $commentForm,
        bool $isCreateVOIfEmptyId = false
    ): ?ICommentValuesObject {
        $row = null;

        $id = $commentForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getCommentRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $commentVO = new CommentValuesObject($row);

        $commentVO->setText($commentForm->getText());
        $commentVO->setParentId($commentForm->getParentId());
        $commentVO->setArticleId($commentForm->getArticleId());
        $commentVO->setUserId($commentForm->getUserId());
        $commentVO->setIsActive($commentForm->isActive());

        if (empty($commentVO->getUserId())) {
            $commentVO->setUserName($commentForm->getUserName());
            $commentVO->setUserEmail($commentForm->getUserEmail());
        }

        $this->_setHtmlToVO($commentVO);
        $this->_setUserIpToVO($commentVO);

        return $commentVO;
    }

    /**
     * @param ICommentValuesObject $commentVO
     * @return void
     * @throws CoreException
     */
    private function _setHtmlToVO(ICommentValuesObject $commentVO): void
    {
        $text = $commentVO->getText();
        $html = empty($text) ? null : $this->_text2html($text);

        $commentVO->setHtml($html);
    }

    /**
     * @param ICommentValuesObject $commentVO
     * @return void
     * @throws CoreException
     */
    private function _setUserIpToVO(ICommentValuesObject $commentVO): void
    {
        if (empty($commentVO->getUserId()) && empty($commentVO->getUserIp())) {
            /* @var $ipPlugin IpPlugin */
            $ipPlugin = $this->getPlugin('ip');

            $commentVO->setUserIp($ipPlugin->getIp());
        }
    }

    /**
     * @param string|null $text
     * @return string|null
     * @throws CoreException
     */
    private function _text2html(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        /* @var $markupPlugin MarkupPlugin */
        $markupPlugin = $this->getPlugin('markup');

        /* @var $linkPlugin LinkPlugin */
        $linkPlugin = $this->getPlugin('link');

        $text = str_replace('\\\'', '\'', $text);
        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        $text = $markupPlugin->markup2html($text);
        $text = $linkPlugin->parseLinkShortCodes($text);

        return htmlspecialchars($text, ENT_QUOTES);
    }
}
