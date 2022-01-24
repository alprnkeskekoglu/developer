<?php

namespace Dawnstar\Developer\Http\Services;

/**
 * Class BreadcrumbService
 * @package Dawnstar\Developer\Http\Services
 */
class BreadcrumbService
{
    /**
     * @return array[]
     */
    public function getVcsBreadcrumb(): array
    {
        return [
            [
                'name' => __('DeveloperLang::general.title'),
                'url' => route('dawnstar.developer.index'),
            ],
            [
                'name' => __('DeveloperLang::general.box.vcs'),
                'url' => 'javascript:void(0)',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function getEnvBreadcrumb(): array
    {
        return [
            [
                'name' => __('DeveloperLang::general.title'),
                'url' => route('dawnstar.developer.index'),
            ],
            [
                'name' => __('DeveloperLang::general.box.env_edit'),
                'url' => 'javascript:void(0)',
            ],
        ];
    }
}
