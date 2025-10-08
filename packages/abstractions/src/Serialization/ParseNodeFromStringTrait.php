<?php

namespace Microsoft\Kiota\Abstractions\Serialization;


use DateInterval;
use Exception;

/**
 * This trait contains utility functions for parsing strings to different types.
 */
trait ParseNodeFromStringTrait
{
    /**
     * @throws Exception
     */
    private function parseDateIntervalFromString(?string $value): DateInterval
    {
        // Provide a default zero interval if null or empty string
        if ($value === null || $value === '') {
            $value = 'P0D';
        }

        $negativeValPosition = strpos($value, '-');
        $invert = 0;
        $str = $value;

        if ($negativeValPosition !== false && $negativeValPosition === 0) {
            // Invert the interval
            $invert = 1;
            $str = substr($value, 1);
        }

        // Strip fractional seconds, e.g., PT33.48S => PT33S
        $str = preg_replace('/(\d+)\.\d+S$/', '$1S', $str);

        $dateInterval = new DateInterval($str);
        $dateInterval->invert = $invert;

        return $dateInterval;
    }
}
