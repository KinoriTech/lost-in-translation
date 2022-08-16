<?php

/*********************************************************************
 * SPDX-FileCopyrightText: Copyright (c) 2021-2022 Kinori Tech (Mexico)
 * SPDX-License-Identifier: No-License
 * This program and the accompanying materials are private.
 * All rights reserved
 *
 *********************************************************************/

namespace KinoriTech\LostInTranslation\Console\Translation;

use Symfony\Component\Console\Helper\Table;

class Project
{
    public function __construct(string $name, $locales, array $keys = [])
    {
        $this->name = $name;
        $this->keys = $keys;
        $this->locales = $locales;
    }

    public function addKey(string $key, Key $keyEntry)
    {
        $newKeys = array_merge($this->keys, [$key => $keyEntry]);
        return new Project($this->name, $this->locales, $newKeys);
    }

    public function getByKey($key) {
        if (array_key_exists($key, $this->keys)) {
            return $this->keys[$key];
        }
        return null;
    }

    public function missingTranslations() : bool
    {
        $keysMissing = array_filter($this->keys, function ($key) {
           return $key->missingTranslations($this->locales);
        });
        return !empty($keysMissing);
    }

    public function findUnused(array $allKeys) {
        return array_filter($allKeys, function ($definedKey) {
            $matched = array_filter($this->keys, function ($key) use ($definedKey) {
                return $key->matches($definedKey);
            });
            return empty($matched);
        });
    }

    public function asTable($output, $missing=false) :Table
    {
        $table = new Table($output);
        $table->setHeaders($this->getHeaders($missing));
        $table->setRows($this->getRows($missing));
        return $table;
    }

    private function getHeaders($missing) : array
    {
        $headers = [
            'Key',
            'Group',
            'Namespace'
        ];
        if ($missing) {
            $headers[] = 'Missing';
        } else {
            foreach ($this->locales as $locale) {
                $headers[] = $locale;
            }
            $headers[] = 'File';
        }
        return $headers;
    }

    private function getRows($missing) : array
    {
        $rows = [];
        foreach ($this->keys as $key => $value) {
            $rows[] = $value->toRow($this->locales, $missing);
        }
        return $rows;
    }

    private string $name;
    private array $locales;
    private array $keys;
}
