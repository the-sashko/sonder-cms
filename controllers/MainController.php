<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\BaseController;
use Sonder\Enums\HttpCodesEnum;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Article;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class MainController extends BaseController
{
    private const MAIN_PAGE_URL = '/';

    /**
     * @area blog
     * @route /
     * @url_params page=$3
     * @return IResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws CoreException
     */
    final public function displayIndex(): IResponseObject
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
     * @return IResponseObject
     * @throws Exception
     */
    final public function displayDemo(): IResponseObject
    {
        return $this->render('demo');
    }

    /**
     * @area blog
     * @route /not-found/
     * @return IResponseObject
     * @throws Exception
     */
    final public function displayNotFound(): IResponseObject
    {
        if ($this->request->getUrl() != $this->getNotFoundUrl()) {
            return $this->redirect($this->getNotFoundUrl());
        }

        $this->response->setHttpCode(HttpCodesEnum::NOT_FOUND);

        return $this->render('not_found');
    }
}
