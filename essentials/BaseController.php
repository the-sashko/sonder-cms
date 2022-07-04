<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\CoreController;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Interfaces\IController;
use Sonder\Core\RequestObject;
use Sonder\Interfaces\IRequestObject;

#[IController]
abstract class BaseController extends CoreController implements IController
{
    final protected const NOT_FOUND_URL_CONFIG_VALUE = 'not_found_url';

    /**
     * @var int|null
     */
    protected ?int $id;

    /**
     * @var string|null
     */
    protected ?string $slug;

    /**
     * @var int
     */
    protected int $page;

    /**
     * @param RequestObject $request
     * @throws ConfigException
     * @throws ControllerException
     */
    public function __construct(IRequestObject $request)
    {
        parent::__construct($request);

        $this->_setId();
        $this->_setSlug();
        $this->_setPage();
    }

    /**
     * @return string
     * @throws ConfigException
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
