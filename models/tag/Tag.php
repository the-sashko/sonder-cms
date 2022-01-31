<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Tag\TagForm;
use Sonder\Models\Tag\TagStore;
use Sonder\Models\Tag\TagValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\TranslitPlugin;
use Throwable;

/**
 * @property TagStore $store
 */
final class Tag extends CoreModel implements IModel
{
    const DEFAULT_SLUG = 'tag';

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?ValuesObject
    {
        $row = $this->store->getTagRowById($id);

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getTagsByPage(int $page): ?array
    {
        $rows = $this->store->getTagRowsByPage($page, $this->itemsOnPage);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getAllTags(): ?array
    {
        $rows = $this->store->getAllTagRows(
            true,
            true
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getTagsPageCount(): int
    {
        $rowsCount = $this->store->getTagRowsCount();

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
    final public function removeTagById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteTagById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreTagById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreTagById($id);
    }

    /**
     * @param TagForm $tagForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(TagForm $tagForm): bool
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
        } catch (Throwable $exp) {
            $tagForm->setStatusFail();
            $tagForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param TagForm $tagForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInTagForm(TagForm $tagForm): bool
    {
        $id = $tagForm->getId();

        if (empty($id)) {
            return true;
        }

        $tagVO = $this->_getVOFromTagForm($tagForm);

        if (empty($tagVO)) {
            $tagForm->setStatusFail();
            //TODO: add id to error, here and in other models
            $tagForm->setError(
                TagForm::TAG_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        return true;
    }

    /**
     * @param TagForm $tagForm
     * @param bool $isCreateVOIfEmptyId
     * @return TagValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getVOFromTagForm(
        TagForm $tagForm,
        bool    $isCreateVOIfEmptyId = false
    ): ?TagValuesObject
    {
        $row = null;

        $id = $tagForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getTagRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $tagVO = new TagValuesObject($row);

        $tagVO->setTitle($tagForm->getTitle());
        $tagVO->setSlug($tagForm->getSlug());
        $tagVO->setIsActive($tagForm->getIsActive());

        $this->_setUniqSlugToVO($tagVO);

        return $tagVO;
    }

    /**
     * @param TagForm $tagForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTitleInTagForm(TagForm $tagForm): void
    {
        $title = $tagForm->getTitle();
        $title = preg_replace('/^\s+$/su', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/su', '', $title);

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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isTitleUniq(?string $title, ?int $id): bool
    {
        $row = $this->store->getTagRowByTitle($title, $id);

        return empty($row);
    }

    /**
     * @param TagValuesObject $tagVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _setUniqSlugToVO(TagValuesObject $tagVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$tagVO->getSlug();

        $slug = preg_replace('/^\s+$/su', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);

        if (empty($slug)) {
            $slug = $tagVO->getTitle();

            $slug = preg_replace('/^\s+$/su', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);
        }

        if (empty($slug)) {
            $slug = Tag::DEFAULT_SLUG;
        }

        $slug = $translitPlugin->getSlug($slug);

        $slug = $this->_makeSlugUniq($slug, $tagVO->getId());

        $tagVO->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param int|null $id
     * @return string|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _makeSlugUniq(string $slug, ?int $id = null): ?string
    {
        if (empty($this->store->getTagRowBySlug($slug, $id))) {
            return $slug;
        }

        $slugCount = 1;

        if (preg_match('/^(.*?)-([0-9]+)$/su', $slug)) {
            $slugCount = (int)preg_match(
                '/^(.*?)-([0-9]+)$/su',
                '$2',
                $slug
            );

            $slug = preg_match(
                '/^(.*?)\-([0-9]+)$/su',
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