<?php

namespace Sonder\Models;

use Exception;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Comment\CommentForm;
use Sonder\Models\Comment\CommentStore;
use Sonder\Models\Comment\CommentValuesObject;
use Sonder\Models\User\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\IpPlugin;
use Sonder\Plugins\LinkPlugin;
use Sonder\Plugins\MarkupPlugin;
use Throwable;

/**
 * @property CommentStore $store
 */
final class Comment extends BaseModel
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?ValuesObject
    {
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
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getCommentsByPage(
        int  $page,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $rows = $this->store->getCommentRowsByPage(
            $page,
            $this->itemsOnPage,
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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCommentsPageCount(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $rowsCount = $this->store->getCommentRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getCommentsByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
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

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $articleId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getArticlesPageCountByArticleId(
        ?int $articleId = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        if (empty($articleId)) {
            return 0;
        }

        $rowsCount = $this->store->getCommentRowsCountByArticleId(
            $articleId,
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $userId
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getCommentsByUserId(
        ?int $userId = null,
        int  $page = 1
    ): ?array
    {
        if (empty($userId)) {
            return null;
        }

        $rows = $this->store->getCommentRowsByUserId(
            $userId,
            $page,
            $this->itemsOnPage
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $userId
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCommentsPageCountByUserId(?int $userId = null): int
    {
        if (empty($userId)) {
            return 0;
        }

        $rowsCount = $this->store->getCommentRowsCountByUserId($userId);

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
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
     * @throws DatabasePluginException
     */
    final public function restoreCommentById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreCommentById($id);
    }

    /**
     * @param CommentForm $commentForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(CommentForm $commentForm): bool
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
        } catch (Throwable $exp) {
            $commentForm->setStatusFail();
            $commentForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $commentVO CommentValuesObject */
        $commentVO = parent::getVO($row);

        $this->_setUserVOToVO($commentVO);

        return $commentVO;
    }

    /**
     * @param CommentValuesObject $commentVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setUserVOToVO(CommentValuesObject $commentVO): void
    {
        /* @var $userModel User */
        $userModel = $this->getModel('user');

        /* @var $userVO UserValuesObject */
        $userVO = $userModel->getVOById($commentVO->getUserId());

        if (!empty($userVO)) {
            $commentVO->setUserVO($userVO);
        }
    }

    /**
     * @param CommentForm $commentForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInCommentForm(CommentForm $commentForm): bool
    {
        $id = $commentForm->getId();

        if (empty($id)) {
            return true;
        }

        $commentVO = $this->_getVOFromCommentForm($commentForm);

        if (empty($commentVO)) {
            $commentForm->setStatusFail();

            $commentForm->setError(sprintf(
                CommentForm::COMMENT_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));

            return false;
        }

        return true;
    }

    /**
     * @param CommentForm $commentForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkParentIdInCommentForm(CommentForm $commentForm): bool
    {
        $parentId = $commentForm->getParentId();

        if (empty($parentId)) {
            return true;
        }

        if (empty($this->getVOById($parentId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(sprintf(
                CommentForm::PARENT_COMMENT_NOT_EXISTS_ERROR_MESSAGE,
                $parentId
            ));

            return false;
        }

        return true;
    }

    /**
     * @param CommentForm $commentForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkUserIdInCommentForm(CommentForm $commentForm): bool
    {
        $userId = $commentForm->getUserId();

        if (empty($userId)) {
            return true;
        }

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        if (empty($userModel->getVOById($userId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(sprintf(
                CommentForm::USER_NOT_EXISTS_ERROR_MESSAGE,
                $userId
            ));

            return false;
        }

        return true;
    }

    /**
     * @param CommentForm $commentForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _checkArticleIdInCommentForm(
        CommentForm $commentForm
    ): bool
    {
        $articleId = $commentForm->getArticleId();

        if (empty($articleId)) {
            $commentForm->setStatusFail();

            $commentForm->setError(
                CommentForm::ARTICLE_ID_IS_NOT_SET_ERROR_MESSAGE
            );

            return false;
        }

        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        if (empty($articleModel->getVOById($articleId))) {
            $commentForm->setStatusFail();

            $commentForm->setError(sprintf(
                CommentForm::ARTICLE_NOT_EXISTS_ERROR_MESSAGE,
                $articleId
            ));

            return false;
        }

        return true;
    }

    /**
     * @param CommentForm $commentForm
     * @param bool $isCreateVOIfEmptyId
     * @return CommentValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromCommentForm(
        CommentForm $commentForm,
        bool        $isCreateVOIfEmptyId = false
    ): ?CommentValuesObject
    {
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
     * @param CommentValuesObject $commentVO
     * @return void
     * @throws Exception
     */
    private function _setHtmlToVO(CommentValuesObject $commentVO): void
    {
        $text = $commentVO->getText();
        $html = empty($text) ? null : $this->_text2html($text);

        $commentVO->setHtml($html);
    }

    /**
     * @param CommentValuesObject $commentVO
     * @return void
     * @throws Exception
     */
    private function _setUserIpToVO(CommentValuesObject $commentVO): void
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
     * @throws Exception
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
