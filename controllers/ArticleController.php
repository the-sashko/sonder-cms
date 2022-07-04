<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\BaseController;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\ArticleModel;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

#[IController]
final class ArticleController extends BaseController
{
    /**
     * @area blog
     * @route /p/([0-9a-z-]+)/
     * @url_params slug=$1
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function displaySingle(): IResponseObject
    {
        if (empty($this->slug)) {
            return $this->redirect($this->getNotFoundUrl(), true);
        }

        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        $article = $articleModel->getVOBySlug($this->slug);

        if (empty($article)) {
            return $this->redirect($this->getNotFoundUrl(), true);
        }

        $this->assign([
            'article' => $article
        ]);

        return $this->render('article');
    }
}
