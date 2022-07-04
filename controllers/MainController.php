<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\BaseController;
use Sonder\Enums\HttpCodesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\ArticleModel;

#[IController]
final class MainController extends BaseController
{
    private const MAIN_PAGE_URL = '/';

    /**
     * @area blog
     * @route /
     * @url_params page=$3
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ModelException
     */
    final public function displayIndex(): IResponseObject
    {
        /* @var ArticleModel $articleModel */
        $articleModel = $this->getModel('article');

        $articles = $articleModel->getArticlesByPage($this->page);

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
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     */
    final public function displayDemo(): IResponseObject
    {
        return $this->render('demo');
    }

    /**
     * @area blog
     * @route /not-found/
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
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
