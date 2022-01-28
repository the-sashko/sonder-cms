<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Models\Role;
use Sonder\Models\User;
use Sonder\Models\User\CredentialsForm;
use Sonder\Models\User\UserForm;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminUserController extends AdminBaseController
{
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
        /* @var $userModel User */
        $userModel = $this->getModel('user');

        $users = $userModel->getUsersByPage($this->page);
        $pageCount = $userModel->getUsersPageCount();

        if (empty($users) && $this->page > 1) {
            return $this->redirect('/admin/users/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/users/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
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
}
