<?php

/*********************************************************************
 * SPDX-FileCopyrightText: Copyright (c) 2021-2022 Kinori Tech (Mexico)
 * SPDX-License-Identifier: No-License
 * This program and the accompanying materials are private.
 * All rights reserved
 *
 *********************************************************************/

namespace KinoriTech\LostInTranslation\Helpers;

use Illuminate\Translation\Translator as LaravelTranslator;
use KinoriTech\LostInTranslation\Console\Translation\Key;


class Translator extends LaravelTranslator
{

    public function getKeys() {
        $keys = [];
        foreach ($this->loaded as $namespace => $nsVals) {
            foreach ($nsVals as $group => $gVals) {
                $found = false;
                foreach ($gVals as $locale) {
                    foreach ($locale as $key => $line) {
                        $found = true;
                        $keys[] = new Key(
                            [],
                            $namespace,
                            $group,
                            $key);
                    }
                }
                if(!$found) {
                    $keys[] = new Key(
                        [],
                        $namespace,
                        '*',
                        $group);
                }
            }
        }
        return $keys;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param  array  $segments
     * @return array
     */
    protected function parseBasicSegments(array $segments)
    {
        // The first segment in a basic array will always be the group, so we can go
        // ahead and grab that segment. If there is only one total segment we are
        // just pulling an entire group out of the array and not a single item.
        $group = $segments[0];

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a group and will need to return this item name
        // as well as the group so we know which item to pull from the arrays.
        // If the last segment is empty, it means the key ended in '.', we need
        // to restore it.
        if(count($segments) === 1) {
            $item = null;
        } else {
            $item = implode('.', array_slice($segments, 1));
            if (empty(end($segments))) {
                $item .= ".";
            }
        }
        /*$item = count($segments) === 1
            ? null
            : implode('.', array_slice($segments, 1));*/
        return [null, $group, $item];
    }
}
