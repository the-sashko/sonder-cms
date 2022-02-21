<?php

namespace Sonder\Models;

use Exception;
use Sonder\CMS\Essentials\BaseModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Shortener\ShortenerForm;
use Sonder\Models\Shortener\ShortenerStore;
use Sonder\Models\Shortener\ShortenerValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\MathPlugin;
use Throwable;

/**
 * @property ShortenerStore $store
 */
final class Shortener extends BaseModel
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
     * @throws Exception
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?ValuesObject
    {
        $row = $this->store->getShortenerRowById(
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
     * @param string|null $code
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOByCode(
        ?string $code = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?ValuesObject
    {
        $row = $this->store->getShortenerRowByCode(
            $code,
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
    final public function getShortenerVOsByPage(
        int  $page,
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $rows = $this->store->getShortenerRowsByPage(
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
    final public function getShortLinksPageCount(
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): int
    {
        $rowsCount = $this->store->getShortenerRowsCount(
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
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeShortenerById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteShortenerById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreShortenerById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreShortenerById($id);
    }

    /**
     * @param ShortenerForm $shortenerForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(ShortenerForm $shortenerForm): bool
    {
        $shortenerForm->checkInputValues();

        if (!$shortenerForm->getStatus()) {
            return false;
        }

        $this->_checkIdInShortenerForm($shortenerForm);
        $this->_checkUrlInShortenerForm($shortenerForm);

        if (!$shortenerForm->getStatus()) {
            return false;
        }

        $shortenerVO = $this->_getVOFromShortenerForm($shortenerForm, true);

        $this->store->start();

        try {
            if (!$this->store->insertOrUpdateShortener($shortenerVO)) {
                $this->store->rollback();
                $shortenerForm->setStatusFail();

                return false;
            }

            $row = $this->store->getShortenerRowByUrl($shortenerForm->getUrl());

            if (empty($row)) {
                $this->store->rollback();
                $shortenerForm->setStatusFail();

                return false;
            }

            /* @var $shortenerVO ShortenerValuesObject */
            $shortenerVO = $this->getVO($row);

            $shortenerForm->setId($shortenerVO->getId());
            $shortenerForm->setCode($shortenerVO->getCode());
        } catch (Throwable $exp) {
            $this->store->rollback();

            $shortenerForm->setStatusFail();
            $shortenerForm->setError($exp->getMessage());

            return false;
        }

        $this->store->commit();

        return true;
    }

    /**
     * @param ShortenerForm $shortenerForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInShortenerForm(ShortenerForm $shortenerForm): void
    {
        $id = $shortenerForm->getId();

        if (empty($id)) {
            return;
        }

        $shortenerVO = $this->_getVOFromShortenerForm($shortenerForm);

        if (empty($shortenerVO)) {
            $shortenerForm->setStatusFail();

            $shortenerForm->setError(sprintf(
                ShortenerForm::SHORTENER_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));
        }
    }

    /**
     * @param ShortenerForm $shortenerForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkUrlInShortenerForm(
        ShortenerForm $shortenerForm
    ): void
    {
        $row = $this->store->getShortenerRowByUrl($shortenerForm->getUrl());

        if (!empty($row)) {
            $shortenerVO = new ShortenerValuesObject($row);

            $shortenerForm->setStatusFail();

            $shortenerForm->setError(sprintf(
                ShortenerForm::SHORTENER_ALREADY_EXISTS_ERROR_MESSAGE,
                $shortenerForm->getUrl(),
                $shortenerVO->getCode()
            ));
        }
    }

    /**
     * @param ShortenerForm $shortenerForm
     * @param bool $isCreateVOIfEmptyId
     * @return ShortenerValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromShortenerForm(
        ShortenerForm $shortenerForm,
        bool          $isCreateVOIfEmptyId = false
    ): ?ShortenerValuesObject
    {
        $row = null;

        $id = $shortenerForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getShortenerRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $shortenerVO = new ShortenerValuesObject($row);

        $shortenerVO->setUrl($shortenerForm->getUrl());
        $shortenerVO->setIsActive($shortenerForm->isActive());

        $this->_setCodeToVO($shortenerVO);

        return $shortenerVO;
    }

    /**
     * @param ShortenerValuesObject $shortenerVO
     * @return void
     * @throws Exception
     */
    private function _setCodeToVO(ShortenerValuesObject $shortenerVO): void
    {
        if (empty($shortenerVO->getCode())) {
            /* @var MathPlugin MathPlugin */
            $mathPlugin = $this->getPlugin('math');

            $maxId = $this->store->getMaxId();

            $code = $mathPlugin->dec2base64($maxId + 1, 62);

            $shortenerVO->setCode($code);
        }
    }
}
