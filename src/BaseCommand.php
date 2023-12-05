<?php
namespace HappyMedium\Flex\Cli;

use Ahc\Cli\Input\Command;

abstract class BaseCommand extends Command {
  public function __construct($command, $description, $used_globals = array()) {
    parent::__construct($command, $description);

    if (in_array('contentPath', $used_globals)) {
      $this->option('-c --content-path [path]', 'The path to the wp-content directory for the current project', 'strval', 'content');
    }

    if (in_array('jsonFile', $used_globals)) {
      $this->option('-j --json-file [path]', 'The path to the flex.json file relative to the content-path', 'strval', 'acf-json/flex.json');
    }

    if (in_array('modulesPath', $used_globals)) {
      $this->option('-m --modules-path [path]', 'The path where flex modules should be located in the current project relative to the content-path', 'strval', 'flex');
    }

    if (in_array('themePath', $used_globals)) {
      $this->option('-p --theme-path [path]', 'The path to the theme for the current project', 'strval');
    }

    if (in_array('repoUrl', $used_globals)) {
      $this->option('-r --repo-url [url]', 'The URL to the flex modules library repo', 'strval', 'git@github.com:itsahappymedium/flex-library.git');
    }

    if (in_array('repoPath', $used_globals)) {
      $sanitize_repo_path = function($path) { return $this->sanitize_repo_path($path); };
      $this->option('-t --repo-path [path]', 'The path to where the flex modules library should be stored', $sanitize_repo_path, $this->sanitize_repo_path('~/flex_modules'));
    }

    if (in_array('repoModulesPath', $used_globals)) {
      $this->option('-u --repo-modules-path [path]', 'The path inside of the flex modules library repo where the modules are stored', 'strval', 'modules');
    }
  }

  public function get_active_modules() {
    if (!$this->themePath) {
      $this->themePath = $this->get_theme_path();
    }

    $modules_path = $this->path($this->themePath, $this->modulesPath);
    $modules = glob($this->path($modules_path, '*'), GLOB_ONLYDIR);

    if (empty($modules)) {
      throw new \Error("Could not find any directories inside of {$modules_path}");
    }

    return $modules;
  }

  public function get_available_modules() {
    $modules_path = $this->path($this->repoPath, $this->repoModulesPath);
    $modules = glob($this->path($modules_path, '*'), GLOB_ONLYDIR);

    if (empty($modules)) {
      throw new \Error("Could not find any directories inside of {$modules_path}");
    }

    return $modules;
  }

  public function get_home_path() {
    foreach(array('HOME', 'HOMEDRIVE', 'HOMEPATH') as $prop) {
      if (isset($_SERVER[$prop]) && !empty($_SERVER[$prop])) {
        return $_SERVER[$prop];
      }
    }

    throw new \Error('Unable to determine home directory path.');
  }

  public function get_theme_path() {
    $io = $this->app()->io();

    $themes_path = $this->path($this->contentPath, 'themes');
    $themes = glob($this->path($themes_path, '*'), GLOB_ONLYDIR);
    $theme_count = count($themes);

    if ($theme_count === 1) {
      return $themes[0];
    } elseif ($theme_count > 1) {
      $choices = array_combine(array_map('basename', $themes), $themes);
      $theme_name = $io->choice('Select a theme', $choices, array_keys($choices)[0]);
      return $choices[$theme_name];
    } else {
      throw new \Error("Could not find any directories inside of `{$themes_path}`.");
    }
  }

  public function path(...$parts) {
    return implode(DIRECTORY_SEPARATOR, $parts);
  }

  public function sanitize_repo_path($path) {
    if (str_starts_with($path, '~')) {
      $path = $this->get_home_path() . substr($path, 1);
    }

    return $path;
  }
}
?>