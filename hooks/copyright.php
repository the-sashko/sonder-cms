<?php

namespace Sonder\Hooks;

use Exception;
use Sonder\Core\ConfigObject;
use Sonder\Core\CoreHook;
use Sonder\Core\Interfaces\IHook;

final class CopyrightHook extends CoreHook implements IHook
{
    /**
     * @return void
     * @throws Exception
     */
    final public function onBeforeRender(): void
    {
        $launchDate = (new ConfigObject)->getValue(
            'main',
            'launch_date'
        );

        $launchDate = sprintf('%s 00:00:00', $launchDate);

        $launchYear = date('Y', strtotime($launchDate));
        $currentYear = date('Y');

        $copyright = $launchYear;

        if ($launchYear != $currentYear) {
            $copyright = sprintf('%s-%s', $copyright, $currentYear);
        }

        $renderValues = [];

        if ($this->has('render_values')) {
            $renderValues = $this->get('render_values');
        }

        if (
            is_array($renderValues) &&
            array_key_exists('meta', $renderValues) &&
            is_array($renderValues['meta'])
        ) {
            $renderValues['meta']['copyright'] = $copyright;

            $this->set('render_values', $renderValues);
        }
    }
}
