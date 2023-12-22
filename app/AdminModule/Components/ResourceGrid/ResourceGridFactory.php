<?php declare(strict_types=1);

namespace App\AdminModule\Components\ResourceGrid;

interface ResourceGridFactory {

  public function create(): ResourceGrid;
}


