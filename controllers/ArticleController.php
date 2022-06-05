<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\BaseController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Article;
use Sonder\Models\Article\ArticleValuesObject;

final class ArticleController extends BaseController
{
    /**
     * @area blog
     * @route /p/([0-9a-z-]+)/
     * @url_params slug=$1
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final public function displaySingle(): ResponseObject
    {
        $slug = $this->request->getUrlValue('slug');

        if (empty($slug)) {
            return $this->redirect($this->getNotFoundUrl(), true);
        }

        /* @var $articleModel Article|null */
        $articleModel = $this->getModel('article');

        $article = $articleModel->getVOBySlug(
            $slug,
            true,
            true
        );

        if (empty($article)) {
            return $this->redirect($this->getNotFoundUrl(), true);
        }

        $this->assign([
            'article' => $article
        ]);

        return $this->render('article');
    }
}
