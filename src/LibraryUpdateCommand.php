<?php
namespace HappyMedium\Flex\Cli;

use CzProject\GitPhp\Git;

class LibraryUpdateCommand extends BaseCommand {
  public function __construct() {
    parent::__construct('library:update', 'Makes sure the flex section module library is up to date', array(
      'repoPath'
    ));
  }

  public function execute() {
    if (!is_dir($this->repoPath)) {
      throw new \Error("The directory {$this->repoPath} does not exist");
    }

    $io = $this->app()->io();
    $git = new Git;
    $repo = $git->open($this->repoPath);

    $io->info('Updating flex section module library...');

    $repo->pull('origin');
  }
}
?>