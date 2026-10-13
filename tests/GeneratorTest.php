<?php

declare(strict_types=1);

/*
 * tests/GeneratorTest.php
 *
 * Proves the committed enum is exactly what bin/generate-enum.php produces.
 *
 * WHY: the enum is generated, never hand-edited. If someone edits it by hand
 * or the generator drifts, regenerating would silently undo or change cases.
 */

use Composer\InstalledVersions;

function committedEnum(): string
{
    return str_replace("\r\n", "\n", (string) file_get_contents(dirname(__DIR__) . '/src/Support/Icons/Tabler.php'));
}

/**
 * True when the enum was generated from the upstream version that is installed
 * now. With a newer upstream the coverage test in TablerIconResolutionTest
 * reports what is missing instead.
 */
function enumMatchesInstalledUpstream(): bool
{
    $installed = ltrim((string) InstalledVersions::getPrettyVersion('secondnetwork/blade-tabler-icons'), 'v');

    return str_contains(committedEnum(), "Generated from secondnetwork/blade-tabler-icons {$installed} ");
}

it('regenerates the committed enum byte for byte', function () {
    $output = (string) tempnam(sys_get_temp_dir(), 'tabler');
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(dirname(__DIR__) . '/bin/generate-enum.php') . ' ' . escapeshellarg('--output=' . $output);

    exec($command, $lines, $exitCode);

    expect($exitCode)->toBe(0)
        ->and(str_replace("\r\n", "\n", (string) file_get_contents($output)))->toBe(committedEnum());

    unlink($output);
})->skip(fn (): bool => ! enumMatchesInstalledUpstream(), 'The enum was generated from another upstream version than the installed one.');
