<?php declare(strict_types=1);

namespace App\AdminModule\Components\ResourceEditForm;

use App\Model\Entities\Resource;

interface ResourceEditFormFactory {

  public function create(?Resource $resource, bool $create): ResourceEditForm;
}


