<?php
namespace HappyMedium\Flex\Cli;

class LibraryListCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('library:list', 'Lists the modules available for import', array(
      'repoPath',
      'repoModulesPath'
    ));
  }

  public function execute() {
    $io = $this->app()->io();

    $src_path = $this->path($this->repoPath, $this->repoModulesPath);
    $modules = array_filter(glob($src_path . '/*'), 'is_dir');

    foreach($modules as $module) {
      $io->white(basename($module), true);
    }
  }
}
?>