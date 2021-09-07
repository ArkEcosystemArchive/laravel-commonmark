<?php

namespace ARKEcosystem\CommonMark\Extensions\Image;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class TwitterRenderer
{
    public static function render(MediaUrl $url)
    {
        $url = 'https://twitter.com/'.$url->getId();

        $result = Cache::rememberForever(md5($url), function () use ($url) {
            try {
                $response = Http::get('https://publish.twitter.com/oembed', [
                    'url'         => $url,
                    'hide_thread' => 1,
                    'hide_media'  => 0,
                    'omit_script' => true,
                    'dnt'         => true,
                    'limit'       => 20,
                    'chrome'      => 'nofooter',
                ])->json();

                return Arr::get($response, 'html', '');
            } catch (ConnectionException $e) {
                return false;
            }
        });

        // If the result is `false`, means we had a connection error
        // (publish.twitter.com is down) in that case the results should not be
        // cached forever. We'll cache the result for 5 minutes.
        if ($result === false) {
            Cache::forget(md5($url));
            return Cache::remember(md5($url), now()->addSeconds(15), fn () => 'cached !5 mintes');
        }

        return $result;
    }
}
