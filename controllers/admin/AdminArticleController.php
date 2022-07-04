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
use Sonder\Models\Article\Forms\ArticleForm;
use Sonder\Models\Article\ValuesObjects\ArticleValuesObject;
use Sonder\Models\ArticleModel;
use Sonder\Models\TagModel;
use Sonder\Models\TopicModel;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

#[IController]
final class AdminArticleController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/articles((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ModelException
     */
    final public function displayArticles(): IResponseObject
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        $articles = $articleModel->getArticlesByPage(
            $this->page,
            false,
            false,
            false
        );

        $pageCount = $articleModel->getArticlesPageCount(false, false);

        if (empty($articles) && $this->page > 1) {
            return $this->redirect('/admin/articles/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/articles/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/articles/'
        );

        $this->assign([
            'articles' => $articles,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Articles'
            ]
        ]);

        return $this->render('article/list');
    }

    /**
     * @area admin
     * @route /admin/articles/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function displayArticle(): IResponseObject
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        if (empty($this->id)) {
            return $this->redirect('/admin/articles/');
        }

        /* @var $articleVO ArticleValuesObject */
        $articleVO = $articleModel->getVOById($this->id, false, false);

        if (empty($articleVO)) {
            return $this->redirect('/admin/articles/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/articles/' => 'Articles',
            '#' => $articleVO->getTitle()
        ];

        $this->assign([
            'article' => $articleVO,
            'page_path' => $pagePath
        ]);

        return $this->render('article/view');
    }

    /**
     * @area admin
     * @route /admin/article((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     * @return IResponseObject
     * @throws CoreException
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ConfigException
     * @throws ControllerException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function displayArticleForm(): IResponseObject
    {
        $id = $this->id;

        $errors = null;
        $articleVO = null;
        $articleForm = null;

        $pageTitle = 'new';

        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        /* @var $topicModel TopicModel */
        $topicModel = $this->getModel('topic');

        /* @var $tagModel TagModel */
        $tagModel = $this->getModel('tag');

        if (!empty($id)) {
            /* @var $articleVO ArticleValuesObject */
            $articleVO = $articleModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($articleVO)) {
            return $this->redirect('/admin/article/');
        }

        if ($this->request->getHttpMethod()->isPost()) {
            /* @var $articleForm ArticleForm */
            $articleForm = $articleModel->getForm(
                $this->request->getPostValues()
            );

            $articleForm?->setUserId($this->request->getUser()->getId());

            $articleModel->save($articleForm);
        }

        if (!empty($articleForm) && $articleForm->getStatus()) {
            $id = $articleForm->getId();

            $urlPattern = '/admin/articles/view/%d/';
            $url = '/admin/articles/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        $title = $articleVO?->getTitle();
        $slug = $articleVO?->getSlug();
        $image = $articleVO?->getImageLink('single_view');
        $imageDir = $articleVO?->getImageDir();
        $text = $articleVO?->getText();
        $summary = $articleVO?->getSummary();
        $metaTitle = $articleVO?->getMetaTitle();
        $metaDescription = $articleVO?->getMetaDescription();
        $topicId = $articleVO?->getTopicId();
        $selectedTags = $articleVO?->getTagIds();
        $isActive = $articleVO?->isActive();

        if (!empty($articleForm)) {
            $errors = $articleForm->getErrors();
            $title = $articleForm->getTitle();
            $slug = $articleForm->getSlug();
            $text = $articleForm->getText();
            $summary = $articleForm->getSummary();
            $metaTitle = $articleForm->getMetaTitle();
            $metaDescription = $articleForm->getMetaDescription();
            $topicId = $articleForm->getTopicId();
            $selectedTags = $articleForm->getTags();
            $isActive = $articleForm->isActive();
        }

        $topics = $topicModel->getAllTopics();
        $tags = $tagModel->getAllTags();

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/articles/' => 'Articles',
            '#' => $pageTitle
        ];

        if (empty($topics)) {
            $errors = is_array($errors) ? $errors : [];
            $errors[] = ArticleForm::TOPICS_ARE_NOT_EXIST_ERROR_MESSAGE;
        }

        if (empty($tags)) {
            $errors = is_array($errors) ? $errors : [];
            $errors[] = ArticleForm::TAGS_ARE_NOT_EXIST_ERROR_MESSAGE;
        }

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'image' => $image,
            'image_dir' => $imageDir,
            'text' => $text,
            'summary' => $summary,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'topic_id' => $topicId,
            'selected_tags' => $selectedTags,
            'is_active' => $isActive,
            'errors' => $errors,
            'topics' => $topics,
            'tags' => $tags,
            'page_path' => $pagePath
        ]);

        return $this->render('article/form');
    }

    /**
     * @area admin
     * @route /admin/articles/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     * @return IResponseObject
     * @throws CoreException
     */
    final public function displayRemoveArticle(): IResponseObject
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        if (!$articleModel->removeArticleById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Article With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/articles/');
    }

    /**
     * @area admin
     * @route /admin/articles/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     * @return IResponseObject
     * @throws CoreException
     */
    final public function displayRestoreArticle(): IResponseObject
    {
        /* @var $articleModel ArticleModel */
        $articleModel = $this->getModel('article');

        if (!$articleModel->restoreArticleById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Article With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/articles/');
    }
}
