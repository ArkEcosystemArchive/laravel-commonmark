<?php

namespace ARKEcosystem\CommonMark\View;

use Illuminate\Support\Str;
use Livewire\LivewireViewCompilerEngine;

class BladeEngine extends LivewireViewCompilerEngine
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $contents = parent::get($path, $data);

        if (Str::contains($path, ['vendor/arkecosystem/ui', 'views/livewire'])) {
            $contents = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", '', $contents);
        }

        return $contents;
    }
}
