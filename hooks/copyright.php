<?php

namespace Sonder\Hooks;

use Exception;
use Sonder\Core\ConfigObject;
use Sonder\Core\CoreHook;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\HookException;
use Sonder\Interfaces\IHook;

final class CopyrightHook extends CoreHook implements IHook
{
    private const LAUNCH_DATE_CONFIG_VALUE = 'launch_date';

    /**
     * @return void
     * @throws ConfigException
     * @throws HookException
     */
    final public function onBeforeRender(): void
    {
        $launchDate = (new ConfigObject)->getValue(
            ConfigNamesEnum::MAIN,
            CopyrightHook::LAUNCH_DATE_CONFIG_VALUE
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
