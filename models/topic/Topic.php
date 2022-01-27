<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Topic\TopicForm;
use Sonder\Models\Topic\TopicStore;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\TranslitPlugin;
use Throwable;

/**
 * @property TopicStore $store
 */
final class Topic extends CoreModel implements IModel
{
    const DEFAULT_SLUG = 'topic';

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @return TopicValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?TopicValuesObject
    {
        $row = $this->store->getTopicRowById($id);

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
     */
    final public function getTopicsByPage(int $page): ?array
    {
        $rows = $this->store->getTopicRowsByPage($page, $this->itemsOnPage);

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
    final public function getAllTopics(): ?array
    {
        $rows = $this->store->getAllTopicRows(
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
    final public function getTopicsPageCount(): int
    {
        $rowsCount = $this->store->getTopicRowsCount();

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
    final public function removeTopicById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteTopicById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreTopicById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreTopicById($id);
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(TopicForm $topicForm): bool
    {
        $topicForm->checkInputValues();

        if (!$topicForm->getStatus()) {
            return false;
        }

        $this->_checkIdInTopicForm($topicForm);
        $this->_checkParentIdInTopicForm($topicForm);
        $this->_checkTitleInTopicForm($topicForm);

        if (!$topicForm->getStatus()) {
            return false;
        }

        $topicVO = $this->_getVOFromTopicForm($topicForm, true);

        try {
            if (!$this->store->insertOrUpdateTopic($topicVO)) {
                $topicForm->setStatusFail();

                return false;
            }

            $id = $this->store->getTopicIdBySlug($topicVO->getSlug());

            if (!empty($id)) {
                $topicForm->setId($id);
            }
        } catch (Throwable $exp) {
            $topicForm->setStatusFail();
            $topicForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param array|null $row
     * @return TopicValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $topicVO TopicValuesObject */
        $topicVO = parent::getVO($row);

        $this->_setParentToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param TopicValuesObject $topicVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _setParentToVO(TopicValuesObject $topicVO): void
    {
        /* @var $parentVO TopicValuesObject */
        $parentVO = $this->getVOById($topicVO->getParentId());

        if (!empty($parentVO)) {
            $topicVO->setParentVO($parentVO);
        }
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInTopicForm(TopicForm $topicForm): bool
    {
        $id = $topicForm->getId();

        if (empty($id)) {
            return true;
        }

        $topicVO = $this->_getVOFromTopicForm($topicForm);

        if (empty($topicVO)) {
            $topicForm->setStatusFail();

            $topicForm->setError(
                TopicForm::TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        return true;
    }

    /**
     * @param TopicForm $topicForm
     * @param bool $isCreateVOIfEmptyId
     * @return TopicValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _getVOFromTopicForm(
        TopicForm $topicForm,
        bool      $isCreateVOIfEmptyId = false
    ): ?TopicValuesObject
    {
        $row = null;

        $id = $topicForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getTopicRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $topicVO = new TopicValuesObject($row);

        $topicVO->setParentId($topicForm->getParentId());
        $topicVO->setTitle($topicForm->getTitle());
        $topicVO->setSlug($topicForm->getSlug());
        $topicVO->setIsActive($topicForm->getIsActive());

        $this->_setUniqSlugToVO($topicVO);

        return $topicVO;
    }

    /**
     * @param TopicForm $topicForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkParentIdInTopicForm(TopicForm $topicForm): bool
    {
        $parentId = $topicForm->getParentId();

        if (empty($parentId)) {
            return true;
        }

        $row = $this->store->getTopicRowById(
            $parentId,
            true,
            true
        );

        if (empty($row)) {
            $topicForm->setStatusFail();

            $topicForm->setError(
                TopicForm::PARENT_TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        $topicId = $topicForm->getId();

        if (empty($topicId)) {
            return true;
        }

        /* @var $parentVO TopicValuesObject */
        $parentVO = $this->getVO($row);

        while (!empty($parentVO)) {
            if ($parentVO->getParentId() == $topicId) {
                $topicForm->setStatusFail();

                $topicForm->setError(
                    TopicForm::TOPIC_HAVE_CIRCULAR_DEPENDENCY_ERROR_MESSAGE
                );

                break;
            }

            $parentVO = $parentVO->getParentVO();
        }

        return true;
    }

    /**
     * @param TopicForm $topicForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkTitleInTopicForm(TopicForm $topicForm): void
    {
        $title = $topicForm->getTitle();
        $title = preg_replace('/^\s+$/su', ' ', $title);
        $title = preg_replace('/((^\s)|(\s$))/su', '', $title);

        $topicForm->setTitle($title);

        if (empty($title)) {
            $topicForm->setStatusFail();
            $topicForm->setError(TopicForm::TOPIC_IS_NOT_EXISTS_ERROR_MESSAGE);
        }

        if (
            !empty($title) &&
            !$this->_isTitleUniq($title, $topicForm->getId())
        ) {
            $topicForm->setStatusFail();
            $topicForm->setError(TopicForm::TITLE_EXISTS_ERROR_MESSAGE);
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
        $row = $this->store->getTopicRowByTitle($title, $id);

        return empty($row);
    }

    /**
     * @param TopicValuesObject $topicVO
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _setUniqSlugToVO(TopicValuesObject $topicVO): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $slug = (string)$topicVO->getSlug();

        $slug = preg_replace('/^\s+$/su', '', $slug);
        $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);

        if (empty($slug)) {
            $slug = $topicVO->getTitle();

            $slug = preg_replace('/^\s+$/su', '', $slug);
            $slug = preg_replace('/((^\s)|(\s$))/su', '', $slug);
        }

        if (empty($slug)) {
            $slug = Topic::DEFAULT_SLUG;
        }

        $slug = $translitPlugin->getSlug($slug);

        $slug = $this->_makeSlugUniq($slug, $topicVO->getId());

        $topicVO->setSlug($slug);
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
        if (empty($this->store->getTopicRowBySlug($slug, $id))) {
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
//TODO: add image and language + add parent in form.phtml
