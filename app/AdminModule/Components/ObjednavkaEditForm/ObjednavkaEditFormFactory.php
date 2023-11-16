<?php

namespace App\AdminModule\Components\ObjednavkaEditForm;

/**
 * Interface ObjednavkaEditFormFactory
 * @package App\AdminModule\Components\ObjednavkaEditForm
 */
interface ObjednavkaEditFormFactory{

    public function create():ObjednavkaEditForm;

}