<?php

namespace Sonder\Middlewares;

use Sonder\Controllers\AdminMainController;
use Sonder\Core\CoreMiddleware;
use Sonder\Interfaces\IMiddleware;
use Sonder\Core\ResponseObject;

final class AdminMiddleware extends CoreMiddleware implements IMiddleware
{
    /**
     * @return void
     */
    final public function run(): void
    {
        $isSignedIn = false;

        $user = $this->request->getUser();

        if (
            !empty($user) &&
            !empty($user->getId()) &&
            !empty($user->getRole()) &&
            $user->getRole()->can(
                AdminMainController::USER_ACTION_ADMIN
            )
        ) {
            $isSignedIn = true;
        }

        if (
            !$isSignedIn &&
            $this->request->getUrl() != AdminMainController::SIGN_IN_URL
        ) {
            $this->_redirect(AdminMainController::SIGN_IN_URL);
        }

        if (
            $isSignedIn &&
            $this->request->getUrl() == AdminMainController::SIGN_IN_URL
        ) {
            $this->_redirect(AdminMainController::ADMIN_INDEX_URL);
        }
    }

    /**
     * @param string $url
     * @return void
     */
    private function _redirect(string $url): void
    {
        $this->response = new ResponseObject();

        $this->response->redirect->setUrl($url);
    }
}
