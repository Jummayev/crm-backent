<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;

trait TranslationTrait
{
    public array $translation;

    public function getAttribute($key)
    {
        dd($key);
        if (in_array($key, $this->translation)) {
            $currentLang = Lang::locale();
            $translatedField = "{$key}_{$currentLang}";

            if (isset($this->attributes[$translatedField]) && ! empty($this->attributes[$translatedField])) {
                return $this->attributes[$translatedField];
            }

            return null;
        }

        return parent::getAttribute($key);
    }
}
