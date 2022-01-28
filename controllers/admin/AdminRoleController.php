<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\ResponseObject;
use Sonder\Models\Role;
use Sonder\Models\Role\RoleActionForm;
use Sonder\Models\Role\RoleForm;
use Sonder\Models\Role\RoleValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminRoleController extends AdminBaseController
{
    /**
     * @var string|null
     */
    protected ?string $renderTheme = 'admin';

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
        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        $roles = $roleModel->getRolesByPage($this->page);
        $pageCount = $roleModel->getRolesPageCount();

        if (empty($roles) && $this->page > 1) {
            return $this->redirect('/admin/users/roles/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/users/roles/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
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
        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        $roleActions = $roleModel->getRoleActionsByPage($this->page);
        $pageCount = $roleModel->getRoleActionsPageCount();

        if (empty($roleActions) && $this->page > 1) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/users/roles/actions/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
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
}
