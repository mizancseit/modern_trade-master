<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class Module extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {module} {--m}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module HMVC';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createModule();
        $this->info('Module created successfully.');
        
        if ($this->option('m')) {
            $class = 'Create' . singularToPlural($this->module()) . 'Table';
            $module = preg_replace('/([A-Z])/', '_$1', $this->module());
            $table = strtolower(singularToPlural(substr($module, 1)));
            $migration = date('Y_m_d_His') . '_create_' . $table . '_table';

            $this->createMigration($migration, $class, $table);
            $this->info('Created Migration: ' . $migration);
        }
    }

    /**
     * Get root directory.
     * 
     * @return string
     */
    public function rootDirectory()
    {
        return app_path('Modules/');
    }

    /**
     * Get argument from the console command.
     * 
     * @return string
     */
    private function module()
    {
        return $this->argument('module');
    }

    /**
     * Get module directory. 
     * 
     * @param  string $module
     * @return string
     */
    private function modulePath($module)
    {
        return $this->rootDirectory() . $module;
    }

    /**
     * Create module template.
     * 
     * @return void
     */
    private function createModule()
    {
        File::makeDirectory($this->rootDirectory() . $this->module());
        File::makeDirectory($this->rootDirectory() . $this->module() . '/Models');
        File::makeDirectory($this->rootDirectory() . $this->module() . '/Views');
        File::makeDirectory($this->rootDirectory() . $this->module() . '/Controllers');

        $this->createRoute();
        $this->createModel();
        $this->createController();
    }

    /**
     * Create file route template.
     * 
     * @return void
     */
    private function createRoute()
    {
        $path = $this->modulePath($this->module()) . '/routes.php';
        $stub = File::get($this->rootDirectory() . 'route.stub');
        $content = str_replace('DummyModule', $this->module(), $stub);

        File::put($path, $content);
    }

    /**
     * Create file model template.
     * 
     * @return void
     */
    private function createModel()
    {
        $path = $this->modulePath($this->module()) . '/Models//' . $this->module() . '.php';
        $stub = File::get($this->rootDirectory() . 'model.stub');
        $content = str_replace('DummyClass', $this->module(), $stub);

        File::put($path, $content);
    }

    /**
     * Create file controller template.
     * 
     * @return void
     */
    private function createController()
    {
        $path = $this->modulePath($this->module()) . '/Controllers//' . $this->module() . 'Controller.php';
        $stub = File::get($this->rootDirectory() . 'controller.stub');
        $content = str_replace(
            ['DummyModule', 'DummyRootNamespaceHttp', 'DummyClass'],
            [$this->module(), 'App\Http', $this->module() . 'Controller'],
            $stub
        );

        File::put($path, $content);
    }

    /**
     * Create file migration template.
     * 
     * @return void
     */
    private function createMigration($migration, $class, $table)
    {
        $path = database_path('migrations/' . $migration . '.php');
        $stub = File::get($this->rootDirectory() . 'migration.stub');
        $content = str_replace(['DummyClass', 'DummyTable'], [$class, $table], $stub);

        File::put($path, $content);
    }
}
