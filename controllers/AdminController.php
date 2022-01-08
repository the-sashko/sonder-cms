<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Models\User\SignInForm;

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
            return $this->redirect(AdminController::ADMIN_INDEX_URL);
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

        return $this->redirect(AdminController::SIGN_IN_URL);
    }

    /**
     * @area admin
     * @route /admin/users/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayUsers(): ResponseObject
    {
        //TODO
        $page = 1;

        $userModel = $this->getModel('user');

        $users = [];//$userModel->getAll($page);
        $pageCount = 1;//$userModel->getPageCount();

        if (empty($users) && $page > 1) {
            return $this->redirect('/admin/users/');
        }

        if (($page > $pageCount) && $page > 1) {
            return $this->redirect('/admin/users/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $page,
            '/admin/users/'
        );

        $this->assign([
            'users' => $users,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Posts'
            ]
        ]);

        return $this->render('user/list');
    }

    /**
     * @area admin
     * @route /admin/users/roles/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayRoles(): ResponseObject
    {
        //TODO
        $page = 1;

        $roleModel = $this->getModel('role');

        $roles = [];//$roleModel->getAll($page);
        $pageCount = 1;//$roleModel->getPageCount();

        if (empty($roles) && $page > 1) {
            return $this->redirect('/admin/roles/');
        }

        if (($page > $pageCount) && $page > 1) {
            return $this->redirect('/admin/roles/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $page,
            '/admin/users/roles/'
        );

        $this->assign([
            'roles' => $roles,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Posts'
            ]
        ]);

        return $this->render('role/list');
    }

    /**
     * @area admin
     * @route /admin/users/roles/actions((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayRoleActions(): ResponseObject
    {
        $page = $this->request->getUrlValue('page');
        $page = empty($page) ? 1 : (int)$page;

        $roleModel = $this->getModel('role');

        $roleActions = $roleModel->getRoleActionsByPage($page);
        $pageCount = $roleModel->getRoleActionsPageCount();

        if (empty($roleActions) && $page > 1) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        if (($page > $pageCount) && $page > 1) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $page,
            '/admin/users/roles/actions/'
        );

        $this->assign([
            'role_actions' => $roleActions,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/users/' => 'Users',
                '/admin/users/roles/' => 'Roles',
                '#' => 'Actions'
            ]
        ]);

        return $this->render('role/action/list');
    }

    /**
     * @area admin
     * @route /admin/users/roles/action((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayRoleAction(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        $errors = [];

        $name = null;
        $isActive = true;

        $roleActionVO = null;
        $roleActionForm = null;

        $pageTitle = 'new';

        $roleModel = $this->getModel('role');

        if (!empty($id)) {
            $roleActionVO = $roleModel->getRoleActionVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($roleActionVO)) {
            return $this->redirect('/admin/users/roles/action/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            $roleActionForm = $roleModel->getForm(
                $this->request->getPostValues(),
                'role_action'
            );

            $roleModel->saveRoleAction($roleActionForm);
        }

        if (!empty($roleActionForm) && $roleActionForm->getStatus()) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        if (!empty($roleActionForm)) {
            $errors = $roleActionForm->getErrors();
        }

        if (!empty($roleActionVO)) {
            $name = $roleActionVO->getName();
            $isActive = $roleActionVO->getIsActive();
        }

        if (!empty($roleActionForm) && !empty($roleActionForm->getName())) {
            $name = $roleActionForm->getName();
        }

        if (!empty($roleActionForm) && !empty($roleActionForm->getIsActive())) {
            $isActive = $roleActionForm->getIsActive();
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '/admin/users/roles/' => 'Roles',
            '/admin/users/roles/actions/' => 'Actions',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'errors' => $errors,
            'name' => $name,
            'is_active' => $isActive,
            'page_path' => $pagePath
        ]);


        return $this->render('role/action/form');
    }

    final public function displayNewRoleAction(): void
    {
        //TODO
    }

    final public function displayRemoveRoleAction(): void
    {
        //TODO
    }

    final public function displayNewRole(): void
    {
        //TODO
    }

    final public function displayRole(): void
    {
        //TODO
    }

    final public function displayRemoveRole(): void
    {
        //TODO
    }

    final public function displayRestoreRole(): void
    {
        //TODO
    }

    final public function displayNewUser(): void
    {
        //TODO
    }

    final public function displayUser(): void
    {
        //TODO
    }

    final public function displayRemoveUser(): void
    {
        //TODO
    }

    final public function displayRestoreUser(): void
    {
        //TODO
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

    final public function displayTopics(): void
    {
        //TODO
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
        //TODO
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
        //TODO
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
        //TODO
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
        //TODO
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
