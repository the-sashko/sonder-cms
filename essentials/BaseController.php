<?php

namespace Sonder\CMS\Essentials;

use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\RequestObject;

abstract class BaseController extends CoreController implements IController
{
    protected ?int $id = null;

    protected ?string $slug = null;

    protected int $page = 1;

    public function __construct(RequestObject $request)
    {
        parent::__construct($request);

        $id = $this->request->getUrlValue('id');
        $slug = $this->request->getUrlValue('slug');
        $page = $this->request->getUrlValue('page');

        $this->id = empty($id) ? null : (int)$id;
        $this->slug = empty($slug) ? null : $slug;
        $this->page = empty($page) ? 1 : $page;
    }
}
