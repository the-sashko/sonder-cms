<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Topic;
use Sonder\Models\Topic\TopicForm;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTopicController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/topics((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTopics(): ResponseObject
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        $topics = $topicModel->getTopicsByPage(
            $this->page,
            false,
            false,
            false);

        $pageCount = $topicModel->getTopicsPageCount(false, false);

        if (empty($topics) && $this->page > 1) {
            return $this->redirect('/admin/taxonomy/topics/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/taxonomy/topics/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/taxonomy/topics/'
        );

        $this->assign([
            'topics' => $topics,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/taxonomy/' => 'Taxonomy',
                '#' => 'Topics'
            ]
        ]);

        return $this->render('topic/list');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/topics/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTopic(): ResponseObject
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (empty($this->id)) {
            return $this->redirect('/admin/taxonomy/topics/');
        }

        /* @var $topicVO TopicValuesObject */
        $topicVO = $topicModel->getVOById(
            $this->id,
            false,
            false
        );

        if (empty($topicVO)) {
            return $this->redirect('/admin/taxonomy/topics/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/taxonomy/topics/' => 'Topics',
            '#' => $topicVO->getTitle()
        ];

        $this->assign([
            'topic' => $topicVO,
            'page_path' => $pagePath
        ]);

        return $this->render('topic/view');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/topic((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTopicForm(): ResponseObject
    {
        $id = $this->id;

        $errors = [];

        $title = null;
        $slug = null;
        $parentId = null;
        $image = null;
        $isActive = true;

        $topicVO = null;
        $topicForm = null;

        $pageTitle = 'new';

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!empty($id)) {
            /* @var $topicVO TopicValuesObject | null */
            $topicVO = $topicModel->getVOById(
                $id,
                false,
                false
            );

            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($topicVO)) {
            return $this->redirect('/admin/taxonomy/topic/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $topicForm TopicForm|null */
            $topicForm = $topicModel->getForm(
                $this->request->getPostValues(),
                'topic'
            );

            $topicModel->save($topicForm);
        }

        if (!empty($topicForm) && $topicForm->getStatus()) {
            $id = $topicForm->getId();

            $urlPattern = '/admin/taxonomy/topics/view/%d/';
            $url = '/admin/taxonomy/topics/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($topicForm)) {
            $errors = $topicForm->getErrors();
        }

        if (!empty($topicVO)) {
            $title = $topicVO->getTitle();
            $slug = $topicVO->getSlug();
            $parentId = $topicVO->getParentId();
            $image = $topicVO->getImageLink();
            $isActive = $topicVO->isActive();
        }

        if (!empty($topicForm)) {
            $title = $topicForm->getTitle();
            $slug = $topicForm->getSlug();
            $parentId = $topicForm->getParentId();
            $isActive = $topicForm->isActive();
        }

        $topics = $topicModel->getAllTopics();

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/taxonomy/' => 'Taxonomy',
            '/admin/taxonomy/topics/' => 'Topics',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'parent_id' => $parentId,
            'image' => $image,
            'is_active' => $isActive,
            'errors' => $errors,
            'topics' => $topics,
            'page_path' => $pagePath
        ]);

        return $this->render('topic/form');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/topics/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveTopic(): ResponseObject
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!$topicModel->removeTopicById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Topic With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/taxonomy/topics/');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/topics/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreTopic(): ResponseObject
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!$topicModel->restoreTopicById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Topic With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/taxonomy/topics/');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/topics/remove-image/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveImage(): ResponseObject
    {
        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        $topicModel->removeTopicImageById($this->id);

        $url = sprintf('/admin/taxonomy/topic/%d/', $this->id);

        return $this->redirect($url);
    }
}
