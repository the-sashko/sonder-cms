<?php

namespace Sonder\CMS\Essentials;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;

abstract class BaseController extends CoreController implements IController
{
    const MAIN_CONFIG_NAME = 'main';

    const NOT_FOUND_URL_CONFIG_VALUE = 'not_found_url';

    /* @var int|null $id */
    protected ?int $id = null;

    /* @var string|null $slug */
    protected ?string $slug = null;

    /* @var int $page */
    protected int $page = 1;

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $id = $this->request->getUrlValue('id');
        $slug = $this->request->getUrlValue('slug');
        $page = $this->request->getUrlValue('page');

        $this->id = empty($id) ? null : (int)$id;
        $this->slug = empty($slug) ? null : $slug;
        $this->page = empty($page) ? 1 : (int)$page;
    }

    /**
     * @return string
     * @throws Exception
     */
    final protected function getNotFoundUrl(): string
    {
        return $this->config->getValue(
            static::MAIN_CONFIG_NAME,
            static::NOT_FOUND_URL_CONFIG_VALUE
        );
    }
}
