<?php

namespace Sonder\Controllers;

use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\User\Forms\SignInForm;

#[IController]
final class AdminMainController extends AdminBaseController
{
    final public const SIGN_IN_URL = '/admin/login/';

    final public const ADMIN_INDEX_URL = '/admin/';

    final public const USER_ACTION_ADMIN = 'login-to-admin';

    /**
     * @area admin
     * @route /admin/
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     */
    final public function displayIndex(): IResponseObject
    {
        return $this->render('main');
    }

    /**
     * @area admin
     * @route /admin/login/
     * @no_cache true
     * @return IResponseObject
     * @throws ConfigException
     * @throws ControllerException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function displayLogin(): IResponseObject
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
                    SignInForm::INVALID_LOGIN_OR_PASSWORD_ERROR_MESSAGE
                ];
            }
        }

        if ($isSignedIn) {
            return $this->redirect(AdminMainController::ADMIN_INDEX_URL);
        }

        $this->assign([
            'errors' => $errors,
            'is_hide_navigation' => true,
            'form' => $signInForm,
            'page_path' => [
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
     * @return IResponseObject
     */
    final public function displayLogout(): IResponseObject
    {
        $user = $this->request->getUser();

        if (!empty($user)) {
            $user->signOut();
        }

        return $this->redirect(AdminMainController::SIGN_IN_URL);
    }
}
