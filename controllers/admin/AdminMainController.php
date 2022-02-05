<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Models\User\SignInForm;

final class AdminMainController extends AdminBaseController
{
    const SIGN_IN_URL = '/admin/login/';

    const ADMIN_INDEX_URL = '/admin/';

    const USER_ACTION_ADMIN = 'login-to-admin';

    /**
     * @area admin
     * @route /admin/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayIndex(): ResponseObject
    {
        return $this->render('main');
    }

    /**
     * @area admin
     * @route /admin/login/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayLogin(): ResponseObject
    {
        $isSignedIn = false;

        $user = $this->request->getUser();

        $postValues = $this->request->getPostValues();

        /* @var $signInForm SignInForm */
        $signInForm = $user->getForm($postValues, 'sign_in');
        $errors = empty($postValues) ? null : $signInForm->getErrors();

        if (!empty($signInForm) && $signInForm->getStatus()) {
            $isSignedIn = $user->signInByLoginAndPassword(
                $signInForm->getLogin(),
                $signInForm->getPassword(),
            );

            if (!$isSignedIn) {
                $errors = [
                    SignInForm::INVALID_LOGIN_OR_PASSWORD
                ];
            }
        }

        if ($isSignedIn) {
            return $this->redirect(AdminMainController::ADMIN_INDEX_URL);
        }

        $this->assign([
            'errors' => $errors,
            'is_hide_navigation' => true,
            'form' => $signInForm, 'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Login'
            ]
        ]);

        return $this->render('login');
    }

    /**
     * @area admin
     * @route /admin/logout/
     * @no_cache true
     *
     * @return ResponseObject
     */
    final public function displayLogout(): ResponseObject
    {
        $user = $this->request->getUser();

        if (!empty($user)) {
            $user->signOut();
        }

        return $this->redirect(AdminMainController::SIGN_IN_URL);
    }

    final public function displayPosts(): void
    {
        //TODO
        $postModel = $this->getModel('post');

        $posts = $postModel->getAll($this->page);
        $pageCount = $postModel->getPageCount();

        if (empty($posts) && $this->page > 1) {
            $this->redirect('/admin/posts/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            $this->redirect('/admin/posts/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/posts/'
        );

        $this->assign([
            'posts' => $posts,
            'pagination' => $pagination,
            'pagePath' => [
                '/admin/' => 'Admin',
                '#' => 'Posts'
            ]
        ]);

        $this->render('post/list');
    }

    final public function displayNewPost(): void
    {
        //TODO
        $this->assign([
            'pagePath' => [
                '/admin/' => 'Admin',
                '/admin/posts/' => 'Posts',
                '#' => 'New'
            ]
        ]);

        $this->displayPost();
    }

    final public function displayPost(): void
    {
        //TODO
        $id = $this->getValueFromUrl('id');

        $title = null;
        $slug = null;
        $text = null;
        $shortText = null;
        $topicId = null;
        $language = null;

        $postForm = null;
        $errors = null;

        $post = $this->getModel('post');

        $postVO = empty($id) ? null : $post->getVOById((int)$id);

        if (!empty($id) && empty($postVO)) {
            $this->redirect('/admin/post/');
        }

        if (!empty($id)) {
            $this->assign([
                'pagePath' => [
                    '/admin/' => 'Admin',
                    '/admin/posts/' => 'Posts',
                    '#' => 'Edit'
                ]
            ]);
        }

        if (!empty($postVO)) {
            $title = $postVO->getTitle();
            $slug = $postVO->getSlug();
            $text = $postVO->getText();
            $shortText = $postVO->getShortText();
            $topicId = $postVO->getTopicId();
            $language = $postVO->getLanguage();
        }

        if (!empty($this->post)) {
            $postForm = $post->save($this->post);

            $errors = empty($postForm) ? null : $postForm->getErrors();
        }

        if ($postForm != null && $postForm->getStatus()) {
            $this->redirect('/admin/posts/');
        }

        if (!empty($postForm) && !empty($postForm->getTitle())) {
            $title = $postForm->getTitle();
        }

        if (!empty($postForm) && !empty($postForm->getSlug())) {
            $slug = $postForm->getSlug();
        }

        if (!empty($postForm) && !empty($postForm->getText())) {
            $text = $postForm->getText();
        }

        if (!empty($postForm) && !empty($postForm->getShortText())) {
            $shortText = $postForm->getShortText();
        }

        if (!empty($postForm) && !empty($postForm->getTopicId())) {
            $topicId = $postForm->getTopicId();
        }

        if (!empty($postForm) && !empty($postForm->getLanguage())) {
            $language = $postForm->getLanguage();
        }

        $topics = $this->getModel('topic')->getAll();

        foreach ($topics as $key => $topic) {
            if (!empty($topic->getDdate())) {
                unset($topics[$key]);
            }
        }

        // FIX LINKS FOR TINYMCE EDITOR
        $text = preg_replace(
            '/\[Link:(.*?):((")|(&quot;))(.*?)((")|(&quot;))\]/su',
            '[url=$1]$5[/url]',
            (string)$text
        );

        $text = empty($text) ? null : $text;

        $locales = (array)$this->getConfig('locale');
        $locales = array_keys($locales);

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'text' => $text,
            'shortText' => $shortText,
            'topicId' => $topicId,
            'topics' => $topics,
            'language' => $language,
            'locales' => $locales,
            'errors' => $errors
        ]);

        $this->render('post/form');
    }

    final public function displayRemovePost(): void
    {
        //TODO
        $id = $this->getValueFromUrl('id');

        $post = $this->getModel('post');

        $postVO = empty($id) ? null : $post->getVOById((int)$id);

        if (empty($postVO)) {
            $this->redirect('/admin/posts/');
        }

        $post->remove($postVO);

        $this->redirect('/admin/posts/');
    }

    final public function displayRestorePost(): void
    {
        //TODO
        $id = $this->getValueFromUrl('id');

        $post = $this->getModel('post');

        $postVO = empty($id) ? null : $post->getVOById((int)$id);

        if (empty($postVO)) {
            $this->redirect('/admin/posts/');
        }

        $post->restore($postVO);

        $this->redirect('/admin/posts/');
    }

    final public function displayHits(): void
    {
        //TODO
        $hitModel = $this->getModel('hit');

        $hits = $hitModel->getAll($this->page);
        $pageCount = $hitModel->getPageCount();

        if (empty($hits) && $this->page > 1) {
            $this->redirect('/admin/hits/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            $this->redirect('/admin/hits/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/hits/'
        );

        $this->assign([
            'hits' => $hits,
            'pagination' => $pagination,
            'pagePath' => [
                '/admin/' => 'Admin',
                '#' => 'Hits'
            ]
        ]);

        $this->render('hits');
    }

    final public function displayRemoveHit(): void
    {
        //TODO
        $id = $this->getValueFromUrl('id');

        $hit = $this->getModel('hit');

        $hitVO = empty($id) ? null : $hit->getVOById((int)$id);

        if (empty($hitVO)) {
            $this->redirect('/admin/hits/');
        }

        $hit->remove($hitVO);

        $this->redirect('/admin/hits/');
    }

    final public function displayRestoreHit(): void
    {
        //TODO
        $id = $this->getValueFromUrl('id');

        $hit = $this->getModel('hit');

        $hitVO = empty($id) ? null : $hit->getVOById((int)$id);

        if (empty($hitVO)) {
            $this->redirect('/admin/hits/');
        }

        $hit->restore($hitVO);

        $this->redirect('/admin/hits/');
    }

    final public function displayCron(): void
    {
        //TODO
        $cronConfig = $this->getConfig('cron');
        $cronToken = null;

        if (
            !empty($cronConfig) &&
            array_key_exists('token', $cronConfig) &&
            !empty($cronConfig['token']) &&
            is_scalar($cronConfig['token'])
        ) {
            $cronToken = $cronConfig['token'];
        }

        $this->assign([
            'cronToken' => $cronToken,
            'cronJobs' => (new Cron())->getAll(),
            'pagePath' => [
                '/admin/' => 'Admin',
                '#' => 'Cron Jobs'
            ]
        ]);

        $this->render('cron');
    }
}
