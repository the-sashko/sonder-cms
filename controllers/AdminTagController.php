<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Models\Tag;
use Sonder\Models\Tag\TagForm;
use Sonder\Models\Tag\TagValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminTagController extends CoreController implements IController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    final public function __construct(RequestObject $request)
    {
        parent::__construct($request);
    }

    /**
     * @area admin
     * @route /admin/tags((/page-([0-9]+)/)|/)
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
        $page = $this->request->getUrlValue('page');
        $page = empty($page) ? 1 : (int)$page;

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        $tags = $tagModel->getTagsByPage($page);
        $pageCount = $tagModel->getTagsPageCount();

        if (empty($tags) && $page > 1) {
            return $this->redirect('/admin/tags/');
        }

        if (($page > $pageCount) && $page > 1) {
            return $this->redirect('/admin/tags/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $page,
            '/admin/tags/'
        );

        $this->assign([
            'tags' => $tags,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Tags'
            ]
        ]);

        return $this->render('tag/list');
    }

    /**
     * @area admin
     * @route /admin/tags/view/([0-9]+)/
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
        $id = (int)$this->request->getUrlValue('id');

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (empty($id)) {
            return $this->redirect('/admin/tags/');
        }

        /* @var $tagVO TagValuesObject */
        $tagVO = $tagModel->getVOById($id);

        if (empty($tagVO)) {
            return $this->redirect('/admin/tags/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/tags/' => 'Tags',
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
     * @route /admin/tag((/([0-9]+)/)|/)
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
        $id = (int)$this->request->getUrlValue('id');

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
            /* @var $tagVO TagValuesObject | null */
            $tagVO = $tagModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($tagVO)) {
            return $this->redirect('/admin/tag/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $tagForm TagForm | null */
            $tagForm = $tagModel->getForm(
                $this->request->getPostValues(),
                'tag'
            );

            $tagModel->save($tagForm);
        }

        if (!empty($tagForm) && $tagForm->getStatus()) {
            $id = $tagForm->getId();

            $urlPattern = '/admin/tags/view/%d/';
            $url = '/admin/tags/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($tagForm)) {
            $errors = $tagForm->getErrors();
        }

        if (!empty($tagVO)) {
            $title = $tagVO->getTitle();
            $slug = $tagVO->getSlug();
            $isActive = $tagVO->getIsActive();
        }

        if (!empty($tagForm)) {
            $title = $tagForm->getTitle();
            $slug = $tagForm->getSlug();
            $isActive = $tagForm->getIsActive();
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/tags/' => 'Tags',
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
     * @route /admin/tags/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveTag(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!$tagModel->removeTagById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Tag With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/tags/');
    }

    /**
     * @area admin
     * @route /admin/tags/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreTag(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!$tagModel->restoreTagById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Tag With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/tags/');
    }
}
