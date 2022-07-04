<?php

namespace Sonder\CMS\Essentials;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Interfaces\IController;
use Sonder\Core\RequestObject;

abstract class BaseController extends CoreController implements IController
{
    final protected const NOT_FOUND_URL_CONFIG_VALUE = 'not_found_url';

    /**
     * @var int|null
     */
    protected readonly ?int $id;

    /**
     * @var string|null
     */
    protected readonly ?string $slug;

    /**
     * @var int
     */
    protected readonly int $page;

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $this->_setId();
        $this->_setSlug();
        $this->_setPage();
    }

    /**
     * @return string
     * @throws Exception
     */
    final protected function getNotFoundUrl(): string
    {
        return $this->config->getValue(
            ConfigNamesEnum::MAIN,
            static::NOT_FOUND_URL_CONFIG_VALUE
        );
    }

    /**
     * @return void
     */
    private function _setId(): void
    {
        $id = $this->request->getUrlValue('id');

        $this->id = empty($id) ? null : (int)$id;
    }

    /**
     * @return void
     */
    private function _setSlug(): void
    {
        $slug = $this->request->getUrlValue('slug');

        $this->slug = empty($slug) ? null : $slug;
    }

    /**
     * @return void
     */
    private function _setPage(): void
    {
        $page = $this->request->getUrlValue('page');

        $this->page = empty($page) ? 1 : (int)$page;
    }
}
