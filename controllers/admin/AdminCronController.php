<?php

namespace Sonder\Controllers;

use Exception;
use ReflectionException;
use Sonder\CMS\Essentials\AdminBaseController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Cron;
use Sonder\Models\Cron\CronForm;
use Sonder\Models\Cron\CronValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class AdminCronController extends AdminBaseController
{
    /**
     * @area admin
     * @route /admin/settings/cron((/page-([0-9]+)/)|/)
     * @url_params page=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCronJobs(): ResponseObject
    {
        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        $cronJobs = $cronModel->getCronJobsByPage($this->page);
        $pageCount = $cronModel->getCronJobsPageCount();

        if (empty($cronJobs) && $this->page > 1) {
            return $this->redirect('/admin/settings/cron/');
        }

        if (($this->page > $pageCount) && $this->page > 1) {
            return $this->redirect('/admin/settings/cron/');
        }

        $pagination = $this->getPlugin('paginator')->getPagination(
            $pageCount,
            $this->page,
            '/admin/settings/cron/'
        );

        $this->assign([
            'cron_jobs' => $cronJobs,
            'pagination' => $pagination,
            'page_path' => [
                '/admin/' => 'Admin',
                '/admin/settings/' => 'Settings',
                '#' => 'Cron'
            ]
        ]);

        return $this->render('settings/cron/list');
    }

    /**
     * @area admin
     * @route /admin/settings/cron/job/view/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayCronJob(): ResponseObject
    {
        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        if (empty($this->id)) {
            return $this->redirect('/admin/settings/cron/');
        }

        /* @var $cronVO CronValuesObject */
        $cronVO = $cronModel->getVOById($this->id);

        if (empty($cronVO)) {
            return $this->redirect('/admin/settings/cron/');
        }

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/settings/' => 'Users',
            '/admin/settings/cron/' => 'Cron',
            '#' => sprintf('ID: %d', $cronVO->getId())
        ];

        $this->assign([
            'cron_job' => $cronVO,
            'page_path' => $pagePath
        ]);

        return $this->render('settings/cron/view');
    }

    /**
     * @area admin
     * @route /admin/settings/cron/job((/([0-9]+)/)|/)
     * @url_params id=$3
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws ReflectionException
     * @throws Exception
     */
    final public function displayCronJobForm(): ResponseObject
    {
        $id = $this->id;

        $errors = [];

        $alias = null;
        $controller = null;
        $method = null;
        $interval = null;
        $isActive = true;

        $cronVO = null;
        $cronForm = null;

        $pageTitle = 'new';

        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        if (!empty($id)) {
            /* @var $cronVO CronValuesObject */
            $cronVO = $cronModel->getVOById($id);
            $pageTitle = 'Edit';
        }

        if (!empty($id) && empty($cronVO)) {
            return $this->redirect('/admin/settings/cron/job/');
        }

        if ($this->request->getHttpMethod() == 'post') {
            /* @var $cronForm CronForm */
            $cronForm = $cronModel->getForm($this->request->getPostValues());

            $cronModel->save($cronForm);
        }

        if (!empty($cronForm) && $cronForm->getStatus()) {
            $id = $cronForm->getId();

            $urlPattern = '/admin/settings/cron/job/view/%d/';
            $url = '/admin/settings/cron/';

            $url = empty($id) ? $url : sprintf($urlPattern, $id);

            return $this->redirect($url);
        }

        if (!empty($cronForm)) {
            $errors = $cronForm->getErrors();
        }

        if (!empty($cronVO)) {
            $alias = $cronVO->getAlias();
            $controller = $cronVO->getController();
            $method = $cronVO->getMethod();
            $interval = $cronVO->getInterval();
            $isActive = $cronVO->isActive();
        }

        if (!empty($cronForm)) {
            $alias = $cronForm->getAlias();
            $controller = $cronForm->getController();
            $method = $cronForm->getMethod();
            $interval = $cronForm->getInterval();
            $isActive = $cronForm->isActive();
        }

        $jobs = $cronModel->getAvailableJobs();

        $pagePath = [
            '/admin/' => 'Admin',
            '/admin/settings/' => 'Settings',
            '/admin/settings/cron/' => 'Cron',
            '#' => $pageTitle
        ];

        $this->assign([
            'id' => $id,
            'alias' => $alias,
            'controller' => $controller,
            'method' => $method,
            'interval' => $interval,
            'is_active' => $isActive,
            'errors' => $errors,
            'jobs' => $jobs,
            'page_path' => $pagePath
        ]);

        return $this->render('settings/cron/form');
    }

    /**
     * @area admin
     * @route /admin/settings/cron/job/remove/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRemoveCronJob(): ResponseObject
    {
        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        if (!$cronModel->removeCronJobById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Remove Cron Job With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/settings/cron/');
    }

    /**
     * @area admin
     * @route /admin/settings/cron/job/restore/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRestoreCronJob(): ResponseObject
    {
        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        if (!$cronModel->restoreCronJobById($this->id)) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Can Not Restore Cron Job With "%d"';
            $errorMessage = sprintf($errorMessage, $this->id);

            $loggerPlugin->logError($errorMessage);
        }

        return $this->redirect('/admin/settings/cron/');
    }

    /**
     * @area admin
     * @route /admin/settings/cron/job/run/([0-9]+)/
     * @url_params id=$1
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRunCronJob(): ResponseObject
    {
        /* @var $cronModel Cron */
        $cronModel = $this->getModel('cron');

        /* @var $cronJob CronValuesObject */
        $cronJob = $cronModel->getVOById($this->id);

        if ($cronJob->isActive() && !$cronJob->isRemoved()) {
            $cronModel->runJob($cronJob);
        }

        return $this->redirect('/admin/settings/cron/');
    }
}
