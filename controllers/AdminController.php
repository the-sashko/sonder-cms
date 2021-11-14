<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;

final class AdminController extends CoreController implements IController
{
    const SIGN_IN_URL = '/admin/login/';

    const ADMIN_INDEX_URL = '/admin/';

    const USER_ACTION_ADMIN = 'login_to_admin';

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
     * @route /admin/
     * @url_params test=$1&page=$2
     * @no_cache true
     *
     * @return ResponseObject
     */
    final public function displayIndex(): ResponseObject
    {
        die('aaa');
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
        return $this->render('login');
    }
}

/*
    final public function displayPosts(): void
    {
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
        $id = $this->getValueFromUrl('id');

        $post = $this->getModel('post');

        $postVO = empty($id) ? null : $post->getVOById((int)$id);

        if (empty($postVO)) {
            $this->redirect('/admin/posts/');
        }

        $post->restore($postVO);

        $this->redirect('/admin/posts/');
    }

    final public function displayTopics(): void
    {
        $this->assign([
            'topics' => $this->getModel('topic')->getAll(),
            'pagePath' => [
                '/admin/' => 'Admin',
                '#' => 'Topics'
            ]
        ]);

        $this->render('topic/list');
    }

    final public function displayNewTopic(): void
    {
        $this->assign([
            'pagePath' => [
                '/admin/' => 'Admin',
                '/admin/topics/' => 'Topics',
                '#' => 'New'
            ]
        ]);

        $this->displayTopic();
    }

    final public function displayTopic(): void
    {
        $id = $this->getValueFromUrl('id');

        $title = null;
        $slug = null;
        $language = null;

        $topicForm = null;
        $errors = null;

        $topic = $this->getModel('topic');

        $topicVO = empty($id) ? null : $topic->getVOById((int)$id);

        if (!empty($id) && empty($topicVO)) {
            $this->redirect('/admin/topic/');
        }

        if (!empty($id)) {
            $this->assign([
                'pagePath' => [
                    '/admin/' => 'Admin',
                    '/admin/topics/' => 'Topics',
                    '#' => 'Edit'
                ]
            ]);
        }

        if (!empty($topicVO)) {
            $title = $topicVO->getTitle();
            $slug = $topicVO->getSlug();
            $language = $topicVO->getLanguage();
        }

        if (!empty($this->post)) {
            $topicForm = $topic->save($this->post);

            $errors = empty($topicForm) ? null : $topicForm->getErrors();
        }

        if ($topicForm != null && $topicForm->getStatus()) {
            $this->redirect('/admin/topics/');
        }

        if (!empty($topicForm) && !empty($topicForm->getTitle())) {
            $title = $topicForm->getTitle();
        }

        if (!empty($topicForm) && !empty($topicForm->getSlug())) {
            $slug = $topicForm->getSlug();
        }

        if (!empty($topicForm) && !empty($topicForm->getLanguage())) {
            $language = $topicForm->getLanguage();
        }

        $locales = (array)$this->getConfig('locale');
        $locales = array_keys($locales);

        $this->assign([
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'language' => $language,
            'locales' => $locales,
            'errors' => $errors
        ]);

        $this->render('topic/form');
    }

    final public function displayRemoveTopic(): void
    {
        $id = $this->getValueFromUrl('id');

        $topic = $this->getModel('topic');

        $topicVO = empty($id) ? null : $topic->getVOById((int)$id);

        if (empty($topicVO)) {
            $this->redirect('/admin/topics/');
        }

        $topic->remove($topicVO);

        $this->redirect('/admin/topics/');
    }

    final public function displayRestoreTopic(): void
    {
        $id = $this->getValueFromUrl('id');

        $topic = $this->getModel('topic');

        $topicVO = empty($id) ? null : $topic->getVOById((int)$id);

        if (empty($topicVO)) {
            $this->redirect('/admin/topics/');
        }

        $topic->restore($topicVO);

        $this->redirect('/admin/topics/');
    }

    final public function displayHits(): void
    {
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

    final public function displayLogin(): void
    {
        $isSignedIn = false;

        $errors = null;

        $signInForm = null;

        $admin = $this->getModel('admin');

        if (!empty($this->post)) {
            $signInForm = $admin->getSignInForm($this->post);

            $errors = $signInForm->getErrors();
        }

        if (!empty($signInForm) && $signInForm->getStatus()) {
            $isSignedIn = $admin->signInByLoginAndPassword(
                $signInForm->getLogin(),
                $signInForm->getPassword(),
            );

            if (!$isSignedIn) {
                $errors = [
                    $signInForm::INVALID_LOGIN_OR_PASSWORD
                ];
            }
        }

        if ($isSignedIn) {
            $this->redirect(static::ADMIN_INDEX_URL);
        }

        $this->assign([
            'errors' => $errors,
            'isHideNavigation' => true,
            'pagePath' => [
                '/admin/' => 'Admin',
                '#' => 'Login'
            ]
        ]);

        $this->render('login');
    }

    final public function displayLogout(): void
    {
        $admin = $this->getModel('admin');

        $admin->signOut();

        $this->redirect(static::SIGN_IN_URL);
    }
}
*/
