<?php

/*********************************************************************
 * SPDX-FileCopyrightText: Copyright (c) 2021-2022 Kinori Tech (Mexico)
 * SPDX-License-Identifier: No-License
 * This program and the accompanying materials are private.
 * All rights reserved
 *
 *********************************************************************/

namespace KinoriTech\LostInTranslation\Console\Translation;

class Key
{

    /**
     * @param string $file
     * @param string $group
     * @param string $namespace
     * @param string $key
     * @param array $translations
     */
    public function __construct(array $files, string $namespace, string $group, string $key, array $translations = [])
    {
        $this->group = $group;
        $this->namespace = $namespace;
        $this->key = $key;
        $this->files = $files;
        $this->translations = $translations;
    }

    public function key() {
        return $this->key;
    }

    public function group() {
        return $this->group;
    }

    public function namespace() {
        return $this->namespace;
    }

    public function addTranslation($locale, $value)
    {
        $newTranslations = array_merge($this->translations, [$locale => $value]);
        return new Key($this->files, $this->namespace, $this->group, $this->key, $newTranslations);
    }

    public function addFile($file) {
        $newFiles = array_merge($this->files, [$file]);
        return new Key($newFiles, $this->namespace, $this->group, $this->key, $this->translations);
    }

    /**
     * Two Keys match if the share the namespace, group and key.
     * @param Key $that
     */
    public function matches(Key $that)
    {

        return ($this->key === $that->key)
            && ($this->group === $that->group)
            && ($this->namespace === $that->namespace);
    }

    public function toRow($locales, $missing) {
        $key = $this->key;
        if (strlen($key) > 40) {
            $key = substr($key, 0, 40)." ...";
        }
        $row = [$key, $this->group, $this->namespace];
        if ($missing) {
            $missing = "";
            $separator = "";
            foreach ($locales as $locale) {
                if(!array_key_exists($locale, $this->translations)) {
                   $missing .= $separator;
                   $missing .= $locale;
                   $separator = ", ";
                }
            }
            $row[] = $missing;
        } else {
            foreach ($locales as $locale) {
                if (array_key_exists($locale, $this->translations)) {
                    $row[] = mb_convert_encoding('&#10004;', 'UTF-8', 'HTML-ENTITIES');

                } else {
                    $row[] = mb_convert_encoding('&#8212;', 'UTF-8', 'HTML-ENTITIES');
                }
            }
            $row[] = implode(",", $this->files);
        }
        return $row;
    }

    public function missingTranslations($locales) : bool
    {
        foreach ($locales as $locale) {
            if (!array_key_exists($locale, $this->translations)) {
                return true;
            }
        }
        return false;
    }

    private string $group;
    private string $namespace;
    private string $key;
    private string $file;
    private array $translations;


}
