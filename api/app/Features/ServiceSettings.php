<?php

namespace App\Features;

use App\Models\Setting;

trait ServiceSettings
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * ServiceSettings constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->settings = cache()->remember('settings', 60, function () {
            return $this->loadFromDatabase();
        });
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function getSetting($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * @return array
     */
    public function loadFromDatabase()
    {
        $settings = Setting::all();

        return $settings
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            })
            ->all();
    }
}
