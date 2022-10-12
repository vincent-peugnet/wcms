<?php

/**
 * Create a folder with an auto-generated naùe, in OS temp directory
 *
 * @param string $prefix                    A prefix to suit your case (It is nice to precise that is is related to W)
 * @return string                           Absolute created path
 */
function mktempdir(string $prefix): string
{
    $tmp = sys_get_temp_dir();
    $randstr = dechex(mt_rand() % (2 << 16));
    $path = "$tmp/$prefix-$randstr";
    if (!mkdir($path)) {
        throw new LogicException("cannot create tmp dir '$path'");
    }
    return $path;
}
