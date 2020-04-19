<?php

namespace App\Features;

use Illuminate\Database\Eloquent\Builder;

trait BinaryUuids
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('binaryUuids', function (Builder $builder) {
            $builder
                ->select('*')
                ->addSelect(\DB::raw('BIN_TO_UUID(uid) as uuid_text'));
        });
    }

    /**
     * @param Builder $builder
     *
     * @param string $uuid
     */
    public function scopeUuid(Builder $builder, string $uuid)
    {
        $builder->whereRaw('uid = UUID_TO_BIN(?)', [$uuid]);
    }

    /**
     * @return string
     */
    public function getUidAttribute()
    {
        if (isset($this->attributes['uuid_text'])) {
            return $this->attributes['uuid_text'];
        }

        $hex = bin2hex($this->attributes['uid']);

        $hex = substr_replace($hex, '-', 20, 0);
        $hex = substr_replace($hex, '-', 16, 0);
        $hex = substr_replace($hex, '-', 12, 0);
        $hex = substr_replace($hex, '-', 8, 0);

        return $hex;
    }

    /**
     * string $uuid
     *
     * @return void
     */
    public function setUidAttribute(string $uuid)
    {
        $this->attributes['uid'] = \DB::raw('UUID_TO_BIN(\'' . $uuid . '\')');
    }
}
