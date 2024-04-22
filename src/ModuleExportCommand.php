<?php
namespace HappyMedium\Flex\Cli;

use Ahc\Cli\IO\Interactor;

class ModuleExportCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('module:export', 'Exports a module from the current website project into the modules library repository', array(
      'contentPath',
      'jsonFile',
      'modulesPath',
      'repoPath',
      'repoModulesPath',
      'themePath'
    ));

    $this
      ->argument('[module]', 'The name of the module to export');
  }

  public function interact(Interactor $io): void {
    if (!$this->themePath) {
      $this->themePath = $this->get_theme_path();
    }

    if (!$this->module) {
      $modules = $this->get_active_modules();
      $choices = array_combine(array_map('basename', $modules), $modules);
      $this->module = $io->choice('Select a module', $choices, array_keys($choices)[0]);
    }
  }

  public function execute() {
    $io = $this->app()->io();

    $src_path = $this->path($this->themePath, $this->modulesPath, $this->module);
    $dest_path = $this->path($this->repoPath, $this->repoModulesPath, $this->module);

    if (!is_dir($src_path)) {
      throw new \Error("Could not find module in {$src_path}");
    }

    if (is_dir($dest_path)) {
      $confirm = $io->confirm("The {$dest_path} directory already exists. Continue?", 'n');

      if (!$confirm) {
        $io->info('Module export cancelled.', true);
        return;
      }
    } else {
      mkdir($dest_path);
    }

    $files = glob("{$src_path}/*.*");

    foreach($files as $file) {
      if (str_ends_with($file, '.json')) continue;

      $dest_file = $this->path($dest_path, basename($file));

      $io->info("Copying {$file} to {$dest_file}...", true);

      copy($file, $dest_file);
    }

    $acf_json_path = $this->path($this->themePath, $this->jsonFile);
    $acf_json = json_decode(file_get_contents($acf_json_path), true);
    $json_path = $this->path($dest_path, $this->module . '.json');
    $key = array_search($this->module, array_column($acf_json['fields'][0]['layouts'], 'name', 'key'));
    $json = $acf_json['fields'][0]['layouts'][$key];
    $json['key'] = 'flex_' . $this->module;

    $io->info("Writing {$json['key']} layout to {$json_path}...", true);

    file_put_contents($json_path, json_encode($json, JSON_PRETTY_PRINT));
  }
}
?>