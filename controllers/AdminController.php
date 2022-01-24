<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Models\Role;
use Sonder\Models\Role\RoleActionForm;
use Sonder\Models\Role\RoleForm;
use Sonder\Models\Role\RoleValuesObject;
use Sonder\Models\User;
use Sonder\Models\User\CredentialsForm;
use Sonder\Models\User\SignInForm;
use Sonder\Models\User\UserForm;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminController extends CoreController implements IController
{
    const SIGN_IN_URL = '/admin/login/';

    const ADMIN_INDEX_URL = '/admin/';

    const USER_ACTION_ADMIN = 'login-to-admin';

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
     * @route /admin/users((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayUsers(): ResponseObject
    {
        $page = $this->request->getUrlValue('page');
        $page = empty($page) ? 1 : (int)$page;

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        $users = $userModel->getUsersByPage($page);
        $pageCount = $userModel->getUsersPageCount();

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
                '#' => 'Users'
            ]
        ]);

        return $this->render('user/list');
    }

    /**
     * @area admin
     * @route /admin/users/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayUser(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        if (empty($id)) {
            return $this->redirect('/admin/users/');
        }

        $userVO = $userModel->getVOById($id);

        if (empty($userVO)) {
            return $this->redirect('/admin/users/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '#' => $userVO->getLogin()
        ];

        $this->assign([
            'user' => $userVO,
            'page_path' => $pagePath
        ]);


        return $this->render('user/view');
    }

    /**
     * @area admin
     * @route /admin/user((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayUserForm(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        $errors = [];

        $login = null;
        $email = null;
        $password = null;
        $roleId = null;
        $isAllowAccessByApi = false;
        $isActive = true;

        $userVO = null;
        $userForm = null;

        $pageTitle = 'new';

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!empty($id)) {
            $userVO = $userModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($userVO)) {
            return $this->redirect('/admin/user/');
        }

        if (!empty($userVO)) {
            $login = $userVO->getLogin();
            $email = $userVO->getEmail();
            $roleId = $userVO->getRoleId();
            $isAllowAccessByApi = !empty($userVO->getApiToken());
            $isActive = $userVO->getIsActive();
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $userForm UserForm|null */
            $userForm = $userModel->getForm(
                $this->request->getPostValues(),
                'user'
            );

            $userModel->save($userForm);
        }

        if (!empty($userForm) && $userForm->getStatus()) {
            $id = $userForm->getId();

            $urlPattern = '/admin/users/view/%d/';
            $url = '/admin/users/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($userForm)) {
            $login = $userForm->getLogin();
            $email = $userForm->getEmail();
            $password = $userForm->getPassword();
            $roleId = $userForm->getRoleId();
            $isAllowAccessByApi = $userForm->getIsAllowAccessByApi();
            $isActive = $userForm->getIsActive();
            $errors = $userForm->getErrors();
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '#' => $pageTitle
        ];

        $roles = $roleModel->getAllRoles();

        $this->assign([
            'id' => $id,
            'login' => $login,
            'email' => $email,
            'password' => $password,
            'role_id' => $roleId,
            'is_allow_access_by_api' => $isAllowAccessByApi,
            'is_active' => $isActive,
            'roles' => $roles,
            'errors' => $errors,
            'page_path' => $pagePath
        ]);

        return $this->render('user/form');
    }

    /**
     * @area admin
     * @route /admin/users/credentials/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayUserCredentialsForm(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        if (empty($id)) {
            return $this->redirect('/admin/users/');
        }

        $errors = [];

        $password = null;

        $credentialsForm = null;

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        $userVO = $userModel->getVOById($id);

        if (empty($userVO)) {
            return $this->redirect('/admin/users/');
        }

        $login = $userVO->getLogin();
        $apiToken = $userVO->getApiToken();
        $isAllowAccessByApi = !empty($userVO->getApiToken());

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $credentialsForm CredentialsForm|null */
            $credentialsForm = $userModel->getForm(
                $this->request->getPostValues(),
                'credentials'
            );

            $userModel->saveCredentials($credentialsForm);
        }

        if (!empty($credentialsForm) && $credentialsForm->getStatus()) {
            return $this->redirect(sprintf(
                '/admin/users/view/%d/',
                $id
            ));
        }

        if (!empty($credentialsForm)) {
            $login = $credentialsForm->getLogin();
            $password = $credentialsForm->getPassword();
            $isAllowAccessByApi = $credentialsForm->getIsAllowAccessByApi();
            $errors = $credentialsForm->getErrors();
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '#' => 'Credentials'
        ];

        $this->assign([
            'id' => $id,
            'login' => $login,
            'password' => $password,
            'api_token' => $apiToken,
            'is_allow_access_by_api' => $isAllowAccessByApi,
            'errors' => $errors,
            'page_path' => $pagePath
        ]);

        return $this->render('user/credentials_form');
    }

    /**
     * @area admin
     * @route /admin/users/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveUser(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        if (!$userModel->removeById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove User With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/');
    }

    /**
     * @area admin
     * @route /admin/users/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreUser(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $userModel User */
        $userModel = $this->getModel('user');

        if (!$userModel->restoreById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore User With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/');
    }

    /**
     * @area admin
     * @route /admin/users/roles((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRoles(): ResponseObject
    {
        $page = $this->request->getUrlValue('page');
        $page = empty($page) ? 1 : (int)$page;

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        $roles = $roleModel->getRolesByPage($page);
        $pageCount = $roleModel->getRolesPageCount();

        if (empty($roles) && $page > 1) {
            return $this->redirect('/admin/users/roles/');
        }

        if (($page > $pageCount) && $page > 1) {
            return $this->redirect('/admin/users/roles/');
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
                '/admin/users/' => 'Users',
                '#' => 'Roles'
            ]
        ]);

        return $this->render('role/list');
    }

    /**
     * @area admin
     * @route /admin/users/roles/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRole(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (empty($id)) {
            return $this->redirect('/admin/users/roles/');
        }

        $roleVO = $roleModel->getRoleVOById($id);

        if (empty($roleVO)) {
            return $this->redirect('/admin/users/roles/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '/admin/users/roles/' => 'Roles',
            '#' => $roleVO->getName()
        ];

        $this->assign([
            'role' => $roleVO,
            'page_path' => $pagePath
        ]);


        return $this->render('role/view');
    }

    /**
     * @area admin
     * @route /admin/users/role((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRoleForm(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        $errors = [];

        $name = null;
        $parentId = null;
        $allowedActions = [];
        $deniedActions = [];
        $isActive = true;

        $roleVO = null;
        $roleForm = null;

        $pageTitle = 'new';

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!empty($id)) {
            /* @var $roleVO RoleValuesObject|null */
            $roleVO = $roleModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($roleVO)) {
            return $this->redirect('/admin/users/role/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $roleForm RoleForm|null */
            $roleForm = $roleModel->getForm(
                $this->request->getPostValues(),
                'role'
            );

            $roleModel->saveRole($roleForm);
        }

        if (!empty($roleForm) && $roleForm->getStatus()) {
            $id = $roleForm->getId();

            $urlPattern = '/admin/users/roles/view/%d/';
            $url = '/admin/users/roles/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($roleForm)) {
            $errors = $roleForm->getErrors();
        }

        if (!empty($roleVO)) {
            $roleId = $roleVO->getId();

            $name = $roleVO->getName();
            $parentId = $roleVO->getParentId();
            $allowedActions = $roleModel->getAllowedActionsByRoleId($roleId);
            $deniedActions = $roleModel->getDeniedActionsByRoleId($roleId);
            $isActive = $roleVO->getIsActive();
        }

        if (!empty($roleForm)) {
            $name = $roleForm->getName();
            $parentId = $roleForm->getParentId();
            $allowedActions = $roleForm->getAllowedActions();
            $deniedActions = $roleForm->getDeniedActions();
            $isActive = $roleForm->getIsActive();
        }

        $roles = $roleModel->getAllRoles();
        $roleActions = $roleModel->getAllRoleActions();

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '/admin/users/roles/' => 'Roles',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'name' => $name,
            'parentId' => $parentId,
            'allowedActions' => (array)$allowedActions,
            'deniedActions' => (array)$deniedActions,
            'is_active' => $isActive,
            'errors' => $errors,
            'roles' => $roles,
            'role_actions' => $roleActions,
            'page_path' => $pagePath
        ]);

        return $this->render('role/form');
    }

    /**
     * @area admin
     * @route /admin/users/roles/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveRole(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!$roleModel->removeRoleById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Role With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/roles/');
    }

    /**
     * @area admin
     * @route /admin/users/roles/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreRole(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!$roleModel->restoreRoleById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Role With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/roles/');
    }

    /**
     * @area admin
     * @route /admin/users/roles/actions((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRoleActions(): ResponseObject
    {
        $page = $this->request->getUrlValue('page');
        $page = empty($page) ? 1 : (int)$page;

        /* @var $roleModel Role */
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
     * @route /admin/users/roles/actions/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRoleAction(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (empty($id)) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        $roleActionVO = $roleModel->getRoleActionVOById($id);

        if (empty($roleActionVO)) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/users/' => 'Users',
            '/admin/users/roles/' => 'Roles',
            '/admin/users/roles/actions/' => 'Actions',
            '#' => $roleActionVO->getName()
        ];

        $this->assign([
            'role_action' => $roleActionVO,
            'page_path' => $pagePath
        ]);


        return $this->render('role/action/view');
    }

    /**
     * @area admin
     * @route /admin/users/roles/action((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRoleActionForm(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        $errors = [];

        $name = null;
        $isActive = true;

        $roleActionVO = null;
        $roleActionForm = null;

        $pageTitle = 'new';

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!empty($id)) {
            $roleActionVO = $roleModel->getRoleActionVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($roleActionVO)) {
            return $this->redirect('/admin/users/roles/action/');
        }

        if (!empty($roleActionVO)) {
            $name = $roleActionVO->getName();
            $isActive = $roleActionVO->getIsActive();
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $roleActionForm RoleActionForm|null */
            $roleActionForm = $roleModel->getForm(
                $this->request->getPostValues(),
                'role_action'
            );

            $roleModel->saveRoleAction($roleActionForm);
        }

        if (!empty($roleActionForm) && $roleActionForm->getStatus()) {
            $id = $roleActionForm->getId();

            $urlPattern = '/admin/users/roles/actions/view/%d/';
            $url = '/admin/users/roles/actions/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($roleActionForm)) {
            $name = $roleActionForm->getName();
            $isActive = $roleActionForm->getIsActive();
            $errors = $roleActionForm->getErrors();
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
            'name' => $name,
            'is_active' => $isActive,
            'errors' => $errors,
            'page_path' => $pagePath
        ]);


        return $this->render('role/action/form');
    }

    /**
     * @area admin
     * @route /admin/users/roles/actions/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveRoleAction(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!$roleModel->removeRoleActionById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Role Action With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/roles/actions/');
    }

    /**
     * @area admin
     * @route /admin/users/roles/actions/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreRoleAction(): ResponseObject
    {
        $id = (int)$this->request->getUrlValue('id');

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        if (!$roleModel->restoreRoleActionById($id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Role Action With "%d"';
            $errorMessage = sprintf($errorMessage, $id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/users/roles/actions/');
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
