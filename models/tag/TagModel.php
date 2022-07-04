<?php

namespace Sonder\Models;

use Sonder\CMS\Essentials\BaseModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Tag\Interfaces\ITagApi;
use Sonder\Models\Tag\Interfaces\ITagForm;
use Sonder\Models\Tag\Interfaces\ITagModel;
use Sonder\Models\Tag\Interfaces\ITagSimpleValuesObject;
use Sonder\Models\Tag\Interfaces\ITagStore;
use Sonder\Models\Tag\Forms\TagForm;
use Sonder\Models\Tag\Interfaces\ITagValuesObject;
use Sonder\Models\Tag\ValuesObjects\TagSimpleValuesObject;
use Sonder\Models\Tag\ValuesObjects\TagValuesObject;
use Sonder\Plugins\TranslitPlugin;
use Throwable;

/**
 * @property ITagApi $api
 * @property ITagStore $store
 */
#[IModel]
#[ITagModel]
final class TagModel extends BaseModel implements ITagModel
{
    final protected const ITEMS_ON_PAGE = 10;

    private const DEFAULT_SLUG = 'tag';

    /**
     * @param string|null $slug
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagValuesObject|null
     * @throws ModelException
     */
    final public function getVOBySlug(
        ?string $slug = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagValuesObject {
        $row = $this->store->getTagRowBySlug(
            $slug,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $tagVO TagValuesObject */
            $tagVO = $this->getVO($row);

            return $tagVO;
        }

        return null;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagValuesObject|null
     * @throws ModelException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagValuesObject {
        $row = $this->store->getTagRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $tagVO TagValuesObject */
            $tagVO = $this->getVO($row);

            return $tagVO;
        }

        return null;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ITagSimpleValuesObject|null
     * @throws ModelException
     */
    final public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?ITagSimpleValuesObject {
        $row = $this->store->getTagRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            /* @var $tagSimpleVO TagSimpleValuesObject */
            $tagSimpleVO = $this->getSimpleVO($row);

            return $tagSimpleVO;
        }

        return null;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @param bool $simplify
     * @return array|null
     * @throws ModelException
     */
    final public function getTagsByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true,
        bool $simplify = true
    ): ?array {
        $rows = $this->store->getTagRowsByPage(
            $page,
            TagModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        if ($simplify) {
            return $this->getSimpleVOArray($rows);
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getAllTags(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $rows = $this->store->getAllTagRows(
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getTagsPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getTagRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / TagModel::ITEMS_ON_PAGE);

        if ($pageCount * TagModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $articleId
     * @return array|null
     * @throws ModelException
     */
    final public function getTagsByArticleId(?int $articleId = null): ?array
    {
        if (empty($articleId)) {
            return null;
        }

        $rows = $this->store->getTagsByArticleId($articleId);

        if (empty($rows)) {
            return null;
        }

        return $this->getSimpleVOArray($rows);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeTagById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteTagById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreTagById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreTagById($id);
    }

    /**
     * @param ITagForm $tagForm
     * @return bool
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function save(ITagForm $tagForm): bool
    {
        $tagForm->checkInputValues();

        if (!$tagForm->getStatus()) {
            return false;
        }

        $this->_checkIdInTagForm($tagForm);
        $this->_checkTitleInTagForm($tagForm);

        if (!$tagForm->getStatus()) {
            return false;
        }

        $tagVO = $this->_getVOFromTagForm($tagForm, true);

        try {
            if (!$this->store->insertOrUpdateTag($tagVO)) {
                $tagForm->setStatusFail();

                return false;
            }

            $id = $this->store->getTagIdBySlug($tagVO->getSlug());

            if (!empty($id)) {
                $tagForm->setId($id);
            }
        } catch (Throwable $thr) {
            $tagForm->setStatusFail();
            $tagForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param TagForm $tagForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInTagForm(TagForm $tagForm): void
    {
        $id = $tagForm->getId();

        if (empty($id)) {
            return;
        }

        $tagVO = $this->_getVOFromTagForm($tagForm);

        if (empty($tagVO)) {
            $tagForm->setStatusFail();
            $tagForm->setError(
                sprintf(
                    TagForm::TAG_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param TagForm $tagForm
     * @param bool $isCreateVOIfEmptyId
     * @return TagValuesObject|null
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromTagForm(
        TagForm $tagForm,
        bool $isCreateVOIfEmptyId = false
    ): ?TagValuesObject {
        $row = null;

        $id = $tagForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getTagRowById($id, false, false);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $tagVO = new TagValuesObject($row);

        $tagVO->setTitle($tagForm->getTitle());
        $tagVO->setSlug($tagForm->getSlug());
        $tagVO->setIsActive($tagForm->isActive());

        $this->_setUniqSlugToVO($tagVO);

        return $tagVO;
    }

    /**
     * @param ITagForm $tagForm
     * @return void
     */
    private function _checkTitleInTagForm(ITagForm $tagForm): void
    {
        $title = $tagForm->getTitle();
        $title = preg_replace('/^\s+$/u', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/u', '', $title);

        $tagForm->setTitle($title);

        if (empty($title)) {
            $tagForm->setStatusFail();
            $tagForm->setError(TagForm::TITLE_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($title) &&
            !$this->_isTitleUniq($title, $tagForm->getId())
        ) {
            $tagForm->setStatusFail();
            $tagForm->setError(TagForm::TITLE_EXISTS_ERROR_MESSAGE);
        }
    }

    /**
     * @param string|null $title
     * @param int|null $id
     * @return bool
     */
    private function _isTitleUniq(?string $title = null, ?int $id = null): bool
    {
        $row = $this->store->getTagRowByTitle($title, $id, false, false);

        return empty($row);
    }

    /**
     * @param TagValuesObject $tagVO
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _setUniqSlugToVO(TagValuesObject $tagVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$tagVO->getSlug();

        $slug = preg_replace('/^\s+$/u', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);

        if (empty($slug)) {
            $slug = $tagVO->getTitle();

            $slug = preg_replace('/^\s+$/u', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/u', '', $slug);
        }

        $slug = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = TagModel::DEFAULT_SLUG;
        }

        $slug = $this->_makeSlugUniq($slug, $tagVO->getId());

        $tagVO->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param int|null $id
     * @return string|null
     */
    private function _makeSlugUniq(string $slug, ?int $id = null): ?string
    {
        if (empty($this->store->getTagRowBySlug($slug, $id, false, false))) {
            return $slug;
        }

        $slugCount = 1;

        if (preg_match('/^(.*?)-(\d+)$/su', $slug)) {
            $slugCount = (int)preg_match(
                '/^(.*?)-(\d+)$/su',
                '$2',
                $slug
            );

            $slug = preg_match(
                '/^(.*?)-(\d+)$/su',
                '$1',
                $slug
            );

            $slugCount++;
        }

        $slug = sprintf('%s-%d', $slug, $slugCount);

        return $this->_makeSlugUniq($slug, $id);
    }
}
//TODO: add language