<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Models\Tag;
use Sonder\Models\Tag\TagForm;
use Sonder\Models\Tag\TagValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTagController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/taxonomy/tags((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTags(): ResponseObject
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        $tags = $tagModel->getTagsByPage($this->page);
        $pageCount = $tagModel->getTagsPageCount();

        if (empty($tags) && $this->page > 1) {
            return $this->redirect('/admin/taxonomy/tags/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/taxonomy/tags/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/taxonomy/tags/'
        );

        $this->assign([
            'tags' => $tags,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/taxonomy/' => 'Taxonomy',
                '#' => 'Tags'
            ]
        ]);

        return $this->render('tag/list');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/tags/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTag(): ResponseObject
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (empty($this->id)) {
            return $this->redirect('/admin/taxonomy/tags/');
        }

        /* @var $tagVO TagValuesObject */
        $tagVO = $tagModel->getVOById($this->id);

        if (empty($tagVO)) {
            return $this->redirect('/admin/taxonomy/tags/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/taxonomy/' => 'Taxonomy',
            '/admin/taxonomy/tags/' => 'Tags',
            '#' => $tagVO->getTitle()
        ];

        $this->assign([
            'tag' => $tagVO,
            'page_path' => $pagePath
        ]);


        return $this->render('tag/view');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/tag((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayTagForm(): ResponseObject
    {
        $id = $this->id;

        $errors = [];

        $title = null;
        $slug = null;
        $isActive = true;

        $tagVO = null;
        $tagForm = null;

        $pageTitle = 'new';

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!empty($id)) {
            /* @var $tagVO TagValuesObject|null */
            $tagVO = $tagModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($tagVO)) {
            return $this->redirect('/admin/taxonomy/tag/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $tagForm TagForm|null */
            $tagForm = $tagModel->getForm(
                $this->request->getPostValues(),
                'tag'
            );

            $tagModel->save($tagForm);
        }

        if (!empty($tagForm) && $tagForm->getStatus()) {
            $id = $tagForm->getId();

            $urlPattern = '/admin/taxonomy/tags/view/%d/';
            $url = '/admin/taxonomy/tags/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($tagForm)) {
            $errors = $tagForm->getErrors();
        }

        if (!empty($tagVO)) {
            $title = $tagVO->getTitle();
            $slug = $tagVO->getSlug();
            $isActive = $tagVO->isActive();
        }

        if (!empty($tagForm)) {
            $title = $tagForm->getTitle();
            $slug = $tagForm->getSlug();
            $isActive = $tagForm->isActive();
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/taxonomy/' => 'Taxonomy',
            '/admin/taxonomy/tags/' => 'Tags',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'is_active' => $isActive,
            'errors' => $errors,
            'page_path' => $pagePath
        ]);

        return $this->render('tag/form');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/tags/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveTag(): ResponseObject
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!$tagModel->removeTagById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Tag With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/taxonomy/tags/');
    }

    /**
     * @area admin
     * @route /admin/taxonomy/tags/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreTag(): ResponseObject
    {
        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!$tagModel->restoreTagById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Tag With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/taxonomy/tags/');
    }
}
