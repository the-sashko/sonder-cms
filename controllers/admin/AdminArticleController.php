<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Models\Article;
use Sonder\Models\Article\ArticleForm;
use Sonder\Models\Article\ArticleValuesObject;
use Sonder\Models\Tag;
use Sonder\Models\Topic;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminArticleController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/articles((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayArticles(): ResponseObject
    {
        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        $articles = $articleModel->getArticlesByPage($this->page);
        $pageCount = $articleModel->getArticlesPageCount();

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
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayArticle(): ResponseObject
    {
        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        if (empty($this->id)) {
            return $this->redirect('/admin/articles/');
        }

        /* @var $articleVO ArticleValuesObject */
        $articleVO = $articleModel->getVOById($this->id);

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
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayArticleForm(): ResponseObject
    {
        $id = $this->id;

        $errors = [];

        $title = null;
        $slug = null;
        $summary = null;
        $text = null;
        $metaTitle = null;
        $metaDescription = null;
        $topicId = null;
        $checkedTags = null;
        $isActive = true;

        $articleVO = null;

        $pageTitle = 'new';

        /* @var $articleModel Article */
        $articleModel = $this->getModel('article');

        /* @var $topicModel Topic */
        $topicModel = $this->getModel('topic');

        /* @var $tagModel Tag */
        $tagModel = $this->getModel('tag');

        if (!empty($id)) {
            /* @var $articlesVO ArticleValuesObject|null */
            $articlesVO = $articleModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($articlesVO)) {
            return $this->redirect('/admin/article/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $articleForm ArticleForm|null */
            $articleForm = $articleModel->getForm(
                $this->request->getPostValues()
            );

            if (!empty($articleForm)) {
                $articleForm->setUserId($this->request->getUser()->getId());
            }

            $articleModel->save($articleForm);
        }

        if (!empty($articleForm) && $articleForm->getStatus()) {
            $id = $articleForm->getId();

            $urlPattern = '/admin/articles/view/%d/';
            $url = '/admin/articles/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($articleForm)) {
            $errors = $articleForm->getErrors();
        }

        if (!empty($articleVO)) {
            $title = $articleVO->getTitle();
            $slug = $articleVO->getSlug();
            $text = $articleVO->getText();
            $summary = $articleVO->getSummary();
            $metaTitle = $articleVO->getMetaTitle();
            $metaDescription = $articleVO->getMetaDescription();
            $topicId = $articleVO->getTopicId();
            $isActive = $articleVO->isActive();
        }

        if (!empty($articleForm)) {
            $title = $articleForm->getTitle();
            $slug = $articleForm->getSlug();
            $text = $articleForm->getText();
            $summary = $articleForm->getSummary();
            $metaTitle = $articleForm->getMetaTitle();
            $metaDescription = $articleForm->getMetaDescription();
            $topicId = $articleForm->getTopicId();
            $checkedTags = $articleForm->getTags();
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
            'text' => $text,
            'summary' => $summary,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'topic_id' => $topicId,
            'checked_tags' => $checkedTags,
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
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveArticle(): ResponseObject
    {
        /* @var $articleModel Article */
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
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreArticle(): ResponseObject
    {
        /* @var $articleModel Article */
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
