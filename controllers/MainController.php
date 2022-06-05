<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\BaseController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Article;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class MainController extends BaseController
{
    const MAIN_PAGE_URL = '/';

    /**
     * @area blog
     * @route /
     * @url_params page=$3
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayIndex(): ResponseObject
    {
        /* @var Article $articleModel */
        $articleModel = $this->getModel('article');

        $articles = $articleModel->getArticlesByPage(
            $this->page,
            true,
            true
        );

        if (empty($articles) && $this->page > 1) {
            return $this->redirect(MainController::MAIN_PAGE_URL);
        }

        if (empty($articles)) {
            return $this->redirect($this->getNotFoundUrl());
        }

        $this->assign([
            'current_page' => $this->page,
            'articles' => $articles
        ]);

        return $this->render('home_page');
    }

    /**
     * @area blog
     * @route /demo/
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final public function displayDemo(): ResponseObject
    {
        return $this->render('demo');
    }

    /**
     * @area blog
     * @route /not-found/
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final public function displayNotFound(): ResponseObject
    {
        if ($this->request->getUrl() != $this->getNotFoundUrl()) {
            return $this->redirect($this->getNotFoundUrl());
        }

        $this->response->setHttpCode(ResponseObject::NOT_FOUND_HTTP_CODE);

        return $this->render('not_found');
    }
}
