<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\CMS\Essentials\CronBaseController;

final class CronRssController extends CronBaseController
{
    /**
     * @return void
     * @throws Exception
     */
    final public function jobGenerate(): void
    {
        $locales = $this->getConfig('locale');

        $mainConfig = $this->getConfig('main');
        $mainLocale = $mainConfig['site_locale'];

        foreach ($locales as $lang => $locale) {
            $links = array_map(function ($postVO) {
                return [
                    'title' => $postVO->getTitle(),
                    'link' => sprintf(
                        '%s%s',
                        $this->currentHost,
                        $postVO->getLink()
                    ),
                    'timestamp' => $postVO->getCdate(),
                    'description' => $postVO->getShortText()
                ];
            },
                (array)$this->getModel('post')->getAll(
                    1,
                    true,
                    $lang
                ));

            if (empty($links)) {
                continue;
            }

            $seoConfig = $this->getConfig('seo');

            $siteLink = $this->currentHost;

            $siteDescription = null;

            $siteTitle = $seoConfig['title'] ?? null;
            $siteDescription = $seoConfig['description'] ?? null;

            $siteImage = null;

            if (array_key_exists('image', $seoConfig)) {
                $siteImage = $seoConfig['image'];

                $siteImage = sprintf(
                    '%s%s',
                    $this->currentHost,
                    $siteImage
                );
            }

            $rssPlugin = $this->getPlugin('rss');

            $rssPlugin->load(
                $siteTitle,
                $siteLink,
                $siteImage,
                $siteDescription,
                $links
            );

            $rssFileName = 'rss';

            if ($locale != $mainLocale) {
                $rssFileName = sprintf('%s_%s', $rssFileName, $lang);
            }

            $rssPlugin->save($rssFileName);
        }
    }
}
