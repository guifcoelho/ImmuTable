<?php

namespace guifcoelho\ImmuTable\Testing\Support;

use PHPUnit\Framework\Assert as PHPUnit;

trait ArrayAssertions{

    /**
     * Asserts that two associative arrays are similar.
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $expected
     * @param array $array
     */
    protected function assertSimilarArrays(array $expected, array $array, string $upper_key = "")
    {
        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                $upper_key = $upper_key == "" ? $key : "{$upper_key}.{$key}";
                $this->assertSimilarArrays($value, $array[$key], $upper_key);
            } else {
                PHPUnit::assertArrayHasKey($key, $array, 
                    "Array '{$upper_key}' does not have key '{$key}'"
                );
                PHPUnit::assertTrue(
                    $array[$key] == $value,
                    "Key '{$key}' in array '{$upper_key}': Expected '{$array[$key]}' | Actual '{$value}'"
                );
            }
        }
    }

}