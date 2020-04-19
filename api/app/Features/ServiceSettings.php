<?php

namespace App\Features;

use App\Models\Setting;

trait ServiceSettings
{
    /**
     * @param $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function getSetting($key, $default = null)
    {
        $settings = cache()->remember('settings', 60, function () {
            return $this->loadFromDatabase();
        });

        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    /**
     * @return array
     */
    private function loadFromDatabase()
    {
        $settings = Setting::all();

        return $settings
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            })
            ->all();
    }
}
