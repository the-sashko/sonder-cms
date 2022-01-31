<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Models\Topic;
use Sonder\Models\Topic\TopicForm;
use Sonder\Models\Topic\TopicValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTopicController extends AdminBaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';

    /**
     * @area admin
     * @route /admin/topics((/page-([0-9]+)/)|/)
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

        $topics = $topicModel->getTopicsByPage($this->page);
        $pageCount = $topicModel->getTopicsPageCount();

        if (empty($topics) && $this->page > 1) {
            return $this->redirect('/admin/topics/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/topics/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/topics/'
        );

        $this->assign([
            'topics' => $topics,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Topics'
            ]
        ]);

        return $this->render('topic/list');
    }

    /**
     * @area admin
     * @route /admin/topics/view/([0-9]+)/
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
        $id = (int)$this->request->getUrlValue('id');

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (empty($id)) {
            return $this->redirect('/admin/topics/');
        }

        /* @var $topicVO TopicValuesObject */
        $topicVO = $topicModel->getVOById($id);

        if (empty($topicVO)) {
            return $this->redirect('/admin/topics/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/topics/' => 'Topics',
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
     * @route /admin/topic((/([0-9]+)/)|/)
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
        $id = (int)$this->request->getUrlValue('id');

        $errors = [];

        $title = null;
        $slug = null;
        $parentId = null;
        $isActive = true;

        $topicVO = null;
        $topicForm = null;

        $pageTitle = 'new';

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!empty($id)) {
            /* @var $topicVO TopicValuesObject | null */
            $topicVO = $topicModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($topicVO)) {
            return $this->redirect('/admin/topic/');
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

            $urlPattern = '/admin/topics/view/%d/';
            $url = '/admin/topics/';

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
            $isActive = $topicVO->getIsActive();
        }

        if (!empty($topicForm)) {
            $title = $topicForm->getTitle();
            $slug = $topicForm->getSlug();
            $parentId = $topicForm->getParentId();
            $isActive = $topicForm->getIsActive();
        }

        $topics = $topicModel->getAllTopics();

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/topics/' => 'Topics',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'parent_id' => $parentId,
            'is_active' => $isActive,
            'errors' => $errors,
            'topics' => $topics,
            'page_path' => $pagePath
        ]);

        return $this->render('topic/form');
    }

    /**
     * @area admin
     * @route /admin/topics/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveTopic(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!$topicModel->removeTopicById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Topic With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/topics/');
    }

    /**
     * @area admin
     * @route /admin/topics/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreTopic(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        if (!$topicModel->restoreTopicById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Topic With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/topics/');
    }
}
