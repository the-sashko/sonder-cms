<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\BaseController;
use Sonder\Core\ResponseObject;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Article;
use Sonder\Models\Article\ArticleValuesObject;

final class ArticleController extends BaseController
{
    /**
     * @area blog
     * @route /p/([0-9a-z-]+)/
     * @url_params slug=$1
     *
     * @return IResponseObject
     *
     * @throws Exception
     */
    final public function displaySingle(): IResponseObject
    {
        function guidv4()
        {
            $data = $data ?? random_bytes(16);

            assert(strlen($data) == 16);

            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        var_dump(file_get_contents('/proc/sys/kernel/random/uuid'));

        echo guidv4(); die();

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
