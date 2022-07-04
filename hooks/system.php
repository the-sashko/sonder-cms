<?php

namespace Sonder\Hooks;

use Exception;
use Sonder\Enums\ContentTypesEnum;
use Sonder\Core\CoreHook;
use Sonder\Interfaces\IHook;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;

final class SystemHook extends CoreHook implements IHook
{
    /**
     * @return void
     */
    final public function onAppRun(): void
    {
        require_once __DIR__ . '/../essentials/autoload.php';
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function onBeforeMiddlewares(): void
    {
        $redirectUrl = null;

        /* @var $request RequestObject */
        $request = $this->get('request');

        if (
            preg_match('/^(.*?)\/page-0\/$/sui', $request->getFullUrl()) ||
            preg_match('/^(.*?)\/page-1\/$/sui', $request->getFullUrl())
        ) {
            $redirectUrl = preg_replace(
                '/^(.*?)\/page-([0-9]+)\/$/sui',
                '$1/',
                $request->getFullUrl()
            );
        }

        if (!preg_match('/^(.*?)\/$/sui', $request->getFullUrl())) {
            $redirectUrl = sprintf('%s/', $request->getFullUrl());
        }

        if (empty($redirectUrl)) {
            return;
        }

        /* @var $response ResponseObject|null */
        $response = $this->get('response');

        if (empty($response)) {
            $response = new ResponseObject();
        }

        $response->redirect->setUrl($redirectUrl);
        $response->redirect->setIsPermanent(true);

        $this->set('response', $response);
    }
}
