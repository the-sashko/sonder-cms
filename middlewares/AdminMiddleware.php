<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Controllers\AdminController;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Core\ResponseObject;

final class AdminMiddleware extends CoreMiddleware implements IMiddleware
{
    final public function run(): void
    {
        $isSignedIn = false;

        $user = $this->request->getUser();

        if (
            !empty($user) &&
            !empty($user->getId()) &&
            !empty($user->getRole()) &&
            $user->getRole()->can(
                AdminController::USER_ACTION_ADMIN
            )
        ) {
            $isSignedIn = true;
        }

        if (
            !$isSignedIn &&
            $this->request->getUrl() != AdminController::SIGN_IN_URL
        ) {
            $this->_redirect(AdminController::SIGN_IN_URL);
        }

        if (
            $isSignedIn &&
            $this->request->getUrl() == AdminController::SIGN_IN_URL
        ) {
            $this->_redirect(AdminController::ADMIN_INDEX_URL);
        }
    }

    /**
     * @param string $url
     */
    private function _redirect(string $url): void
    {
        $this->response = new ResponseObject();
        $this->response->redirect->setUrl($url);
    }
}
