<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Config;

final class AdminSettingsController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displaySettings(): ResponseObject
    {
        $this->assign([
            'page_path' => [
                '/admin/' => 'Admin',
                '#' => 'Settings'
            ]
        ]);

        return $this->render('settings/list');
    }

    /**
     * @area admin
     * @route /admin/settings/configs/
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayConfigs(): ResponseObject
    {
        /* @var $configModel Config */
        $configModel = $this->getModel('config');

        $this->assign([
            'configs' => $configModel->getConfigs(),
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/settings/' => 'Settings',
                '#' => 'Configs'
            ]
        ]);

        return $this->render('settings/config/list');
    }

    /**
     * @area admin
     * @route /admin/settings/configs/view/([a-z-_]+)/
     * @url_params config_name=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayConfig(): ResponseObject
    {
        $name = $this->request->getUrlValue('config_name');

        if (empty($name)) {
            return $this->redirect('/admin/settings/configs/');
        }

        /* @var $configModel Config */
        $configModel = $this->getModel('config');

        $configVO = $configModel->getConfig($name);

        if (empty($configVO)) {
            return $this->redirect('/admin/settings/configs/');
        }

        $this->assign([
            'config' => $configVO,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/settings/' => 'Settings',
                '/admin/settings/configs/' => 'Configs',
                '#' => 'View'
            ]
        ]);

        return $this->render('settings/config/view');
    }

    /**
     * @area admin
     * @route /admin/settings/configs/edit/([a-z-_]+)/
     * @url_params config_name=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayEditConfig(): ResponseObject
    {
        $errors = null;

        $name = $this->request->getUrlValue('config_name');

        if (empty($name)) {
            return $this->redirect('/admin/settings/configs/');
        }

        /* @var $configModel Config */
        $configModel = $this->getModel('config');

        $configVO = $configModel->getConfig($name);

        if (empty($configVO)) {
            return $this->redirect('/admin/settings/configs/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            $errors = $configModel->updateConfig(
                $name,
                $this->request->getPostValues()
            );

            if (empty($errors)) {
                return $this->redirect($configVO->getViewLink());
            }
        }

        $this->assign([
            'config' => $configVO,
            'errors' => $errors,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/settings/' => 'Settings',
                '/admin/settings/configs/' => 'Configs',
                '#' => 'Edit'
            ]
        ]);

        return $this->render('settings/config/form');
    }
}