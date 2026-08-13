<?php

if (! function_exists('clean_text')) {
    /**
     * Fully decode over-encoded HTML entities so text stays clean.
     *
     * Some fields have been saved with extra escape layers over time (e.g.
     * "&amp;amp;" or "&amp;#039;" instead of "&" / "'"). A single decode pass is
     * not enough for those, so we decode repeatedly until the value is stable.
     * This is a no-op on already-clean strings.
     *
     * @param  mixed  $value
     */
    function clean_text($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        $prev = null;

        while ($value !== $prev) {
            $prev = $value;
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $value;
    }
}
