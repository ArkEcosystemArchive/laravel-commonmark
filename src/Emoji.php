<?php

declare(strict_types=1);

namespace ARKEcosystem\CommonMark;

use UnicornFail\Emoji\Converter;
use UnicornFail\Emoji\Emojibase\DatasetInterface;
use UnicornFail\Emoji\Emojibase\ShortcodeInterface;

final class Emoji
{
    public static function convert(string $contents): string
    {
        /** @phpstan-ignore-next-line */
        $converter = new Converter([
            'convertEmoticons'  => false,
            'exclude'           => [
                'shortcodes' => [],
            ],
            'locale'            => 'en',
            'native'            => false,
            'presentation'      => DatasetInterface::EMOJI,
            'preset'            => ShortcodeInterface::DEFAULT_PRESETS,
        ]);

        return $converter->convertToUnicode($contents);
    }
}
