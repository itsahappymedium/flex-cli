<?php
namespace HappyMedium\Flex\Cli;

use CzProject\GitPhp\Git;

class LibrarySetupCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('library:setup', 'Downloads the flex section module library', array(
      'repoPath',
      'repoUrl'
    ));
  }

  public function execute() {
    $io = $this->app()->io();
    $git = new Git;

    if (!is_dir($this->repoPath)) {
      $io->info("Creating {$this->repoPath}...", true);
      mkdir($this->repoPath);
    }

    $is_dir_empty = !(new \FilesystemIterator($this->repoPath))->valid();

    if ($is_dir_empty) {
      $io->info("Cloning {$this->repoUrl}...", true);
      $repo = $git->cloneRepository($this->repoUrl, $this->repoPath);
    } else {
      $io->info("Loading {$this->repoUrl}...", true);
      $repo = $git->open($this->repoPath);
    }

    $io->info("Validating {$this->repoPath}...", true);

    try {
      $repo->hasChanges();
    } catch (Exception $e) {
      $io->error("Your modules repo appears to be invalid. Run `rm -rf {$this->repoPath}` to start fresh.", true);
    }
  }
}
?>