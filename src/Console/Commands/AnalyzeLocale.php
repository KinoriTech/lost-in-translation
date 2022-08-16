<?php
namespace KinoriTech\LostInTranslation\Console\Commands;


use AppendIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use KinoriTech\LostInTranslation\Console\Translation\Key;
use KinoriTech\LostInTranslation\Console\Translation\Project;
use KinoriTech\LostInTranslation\Console\Translation\Translation;
use RegexIterator;
use Symfony\Component\Console\Helper\Table;
use function Symfony\Component\String\s;
use function Symfony\Component\Translation\t;

class AnalyzeLocale extends Command {

    // TO Debug php -dxdebug.mode=debug -dxdebug.client_host=127.0.0.1 -dxdebug.client_port=9003 -dxdebug.start_with_request=yes artisan locale:scan

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'locale:scan
	                    {--T|table : Show key information as table }
	                    {--debug : Show debug information}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scan your project for invalid, untranslated and/or unused locale keys.';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $phpFiles = $this->findPHPFiles();
        $allKeys = $this->findTranslationKeysInFiles($phpFiles);
        $count = count($allKeys);
        $this->info("\nFound ". $count ." translations.");

        $locales = $this->findLocales();

        // Compile the information
        $project = $this->findTranslations($locales, $allKeys);
        if($this->option('table')) {
            $this->showAllResults($project);
        }
        if($project->missingTranslations()) {
            $this->line('<fg=red>There are missing translations.</>');
            $this->showMissing($project);
        }
        $translator = app('translator');
        $allKeys = $translator->getKeys();
        $unused = $project->findUnused($allKeys);
        if(!empty($unused)) {
            $this->line('<fg=yellow>There are unused translations.</>');
            foreach ($unused as $item) {
                $key = $item->key();
                $group = $item->group();
                $namesapce = $item->namespace();
                $this->line("<options=bold>$namesapce.$group.$key</>");
            }
        }
        if($project->missingTranslations()) {
            return 1;
        }
        return 0;
	}

    /**
     *
     */
    private function findPHPFiles()
    {
        $this->info('Preparing files');
        $this->info("Looking in ".app_path().' and '.base_path('resources'));
        $appDirectory = new \RecursiveDirectoryIterator(app_path());
        $resDirectory = new \RecursiveDirectoryIterator(base_path('resources'));
        $iterator = new AppendIterator();
        $iterator->append(new \RecursiveIteratorIterator($appDirectory));
        $iterator->append(new \RecursiveIteratorIterator($resDirectory));
        return new \RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);
    }

    /**
     * Scan all file contents for the lang functions off laravel.
     * @lang(key), lang(key) and Lang::get(key[, [something something])
     */
    private function findTranslationKeysInFiles($phpFiles)
    {
        //Count total files.
        $count = iterator_count($phpFiles);
        //Search for the translation keys in file.
        $this->info('Searching translationkeys in '. $count .' files');
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        $all = [];
        foreach ($phpFiles as $file => $a) {
            $keys = [];
            $matches = [];
            $fileContent = file_get_contents($file);

            // Match all localization helper function calls in files.
            // The regex searches for '__(' and 'trans_choice' function calls
            $localePattern = "/((?<=__\(['\"])|(?<=trans\(['\"])|(?<=trans_choice\(['|\"])).*?(?=['\"][,)])/";
            if (preg_match_all($localePattern, $fileContent, $matches)) {
                foreach($matches[0] as $match) {
                    $keys[$match] = $file;
                }

                $all = array_merge($all, $keys);
            }
            $bar->advance();
        }
        $bar->finish();
        return $all;
    }

    /**
     * Search for used locales. These can be folders or json files.
     */
    private function findLocales(): array
    {
        $langDirectory = base_path('lang');
        $langDirs = glob($langDirectory . '/*' , GLOB_ONLYDIR);
        $langFiles = glob($langDirectory . '/*.json');
        $locales = [];
        foreach ($langDirs as $langDir) {
            $locales[] = pathinfo($langDir)['filename'];
        }
        foreach ($langFiles as $langFile) {
            $locales[] = pathinfo($langFile)['filename'];
        }
        return $locales;
    }

    /**
     *
     */
    private function showAllResults(Project $project)
    {
        $table = $project->asTable($this->output);
        $table->render();
    }

    /**
     *
     */
    private function showMissing(Project $project)
    {
        $table = $project->asTable($this->output, true);
        $table->render();
    }

    /**
     * @param array $locales
     * @param array $allKeys
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function findTranslations(array $locales, array $allKeys): Project
    {
        $project = new Project(config('app.name'), $locales);
        foreach ($allKeys as $key => $file) {
            $translator = app('translator');
            $has = $translator->has($key, $translator->getLocale(), false);
            $line = $translator->get($key, [], $translator->getLocale(), false);
            $info = $translator->parseKey($key);
            // We need to differentiate between Short Keys and Sentences
            if ($has) {
                // If the key has a translation it is NOT a Sentence
                array_unshift($info, [$file]);
                $keyEntry = $project->getByKey($key);
                if (isset($keyEntry)) {
                    $keyEntry = $keyEntry->addFile($file);
                } else {
                    $keyEntry = new Key(...$info);
                }
                $keyEntry = $keyEntry->addTranslation($translator->getLocale(), $line);
            } else {
                // To match the Translator algorithm, we use the group as key
                $info[2] = $info[1];
                $info[1] = '*';
                $key = $info[2];
                array_unshift($info, [$file]);
                $keyEntry = $project->getByKey($key);
                if (isset($keyEntry)) {
                    $keyEntry = $keyEntry->addFile($file);
                } else {
                    $keyEntry = new Key(...$info);
                }
                $keyEntry = $keyEntry->addTranslation($translator->getLocale(), $line);
            }
            foreach ($locales as $locale) {
                $has = $translator->has($key, $locale, false);
                $line = $translator->get($key, [], $locale, false);
                // We need to differentiate between Short Keys and Sentences
                if ($has) {
                    $keyEntry = $keyEntry->addTranslation($locale, $line);
                }
            }
            $project = $project->addKey($key, $keyEntry);

        }
        return $project;
    }



}
