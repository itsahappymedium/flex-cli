<?php
namespace HappyMedium\Flex\Cli;

use Ahc\Cli\IO\Interactor;

class ModuleImportCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('module:import', 'Imports a flex section module into the current project', array(
      'contentPath',
      'jsonFile',
      'modulesPath',
      'repoPath',
      'repoModulesPath',
      'themePath'
    ));

    $this
      ->argument('[module]', 'The name of the module to import');
  }

  public function interact(Interactor $io): void {
    if (!$this->themePath) {
      $this->themePath = $this->get_theme_path();
    }

    if (!$this->module) {
      $modules = $this->get_available_modules();
      $choices = array_combine(array_map('basename', $modules), $modules);
      $this->module = $io->choice('Select a module', $choices, array_keys($choices)[0]);
    }
  }

  public function execute() {
    $io = $this->app()->io();

    $src_path = $this->path($this->repoPath, $this->repoModulesPath, $this->module);
    $dest_path = $this->path($this->themePath, $this->modulesPath, $this->module);

    if (!is_dir($src_path)) {
      throw new \Error("Could not find module in {$src_path}");
    }

    if (is_dir($dest_path)) {
      throw new \Error("The {$dest_path} directory already exists");
    }

    mkdir($dest_path);

    $files = glob("{$src_path}/*.*");

    foreach($files as $file) {
      if (str_ends_with($file, '.json')) continue;

      $dest_file = $this->path($dest_path, basename($file));

      $io->info("Copying {$file} to {$dest_file}...", true);

      copy($file, $dest_file);
    }

    $json_path = $this->path($src_path, $this->module . '.json');
    $json = json_decode(file_get_contents($json_path), true);
    $acf_json_path = $this->path($this->themePath, $this->jsonFile);
    $acf_json = json_decode(file_get_contents($acf_json_path), true);

    // @TODO: Make this dynamic/configurable
    if ($acf_json['key'] !== 'group_658da618097e1') {
      throw new \Error('Invalid ACF json');
    }

    if (count($acf_json['fields']) > 1) {
      throw new \Error('ACF json fields value has more than one field which is not supported');
    }

    $acf_json['fields'][0]['layouts'][$json['key']] = $json;
    $acf_json['modified'] = time();

    $io->info("Writing {$json['key']} layout to {$acf_json_path}...", true);

    file_put_contents($acf_json_path, json_encode($acf_json, JSON_PRETTY_PRINT));
  }
}
?>