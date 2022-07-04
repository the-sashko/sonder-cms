<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Topic\Exceptions\TopicModelException;
use Sonder\Models\Topic\Forms\TopicForm;
use Sonder\Models\Topic\ValuesObjects\TopicValuesObject;
use Sonder\Models\TopicModel;

#[IController]
final class AdminTopicController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/topics((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ModelException
     */
    final public function displayTopics(): IResponseObject
    {
        /* @var $topicModel TopicModel */
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
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function displayTopic(): IResponseObject
    {
        /* @var $topicModel TopicModel */
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
     * @return IResponseObject
     * @throws CoreException
     * @throws ModelException
     * @throws ConfigException
     * @throws ControllerException
     * @throws ValuesObjectException
     */
    final public function displayTopicForm(): IResponseObject
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

        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        if (!empty($id)) {
            /* @var $topicVO TopicValuesObject */
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

        if ($this->request->getHttpMethod()->isPost()) {
            /* @var $topicForm TopicForm */
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
     * @return IResponseObject
     * @throws CoreException
     */
    final public function displayRemoveTopic(): IResponseObject
    {
        /* @var $topicModel TopicModel */
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
     * @return IResponseObject
     * @throws CoreException
     */
    final public function displayRestoreTopic(): IResponseObject
    {
        /* @var $topicModel TopicModel */
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
     * @return IResponseObject
     * @throws CoreException
     * @throws ModelException
     * @throws TopicModelException
     */
    final public function displayRemoveImage(): IResponseObject
    {
        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        $topicModel->removeTopicImageById($this->id);

        $url = sprintf('/admin/taxonomy/topic/%d/', $this->id);

        return $this->redirect($url);
    }
}
