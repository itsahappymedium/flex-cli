<?php
namespace HappyMedium\Flex\Cli;

use Ahc\Cli\IO\Interactor;

class ModuleListCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('module:list', 'Lists the modules available for export', array(
      'contentPath',
      'modulesPath',
      'themePath'
    ));
  }

  public function interact(Interactor $io): void {
    if (!$this->themePath) {
      $this->themePath = $this->get_theme_path();
    }
  }

  public function execute() {
    $io = $this->app()->io();

    $src_path = $this->path($this->themePath, $this->modulesPath);
    $modules = array_filter(glob($src_path . '/*'), 'is_dir');

    foreach($modules as $module) {
      $io->white(basename($module), true);
    }
  }
}
?>