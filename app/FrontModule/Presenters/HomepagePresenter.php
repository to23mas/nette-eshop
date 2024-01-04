<?php

namespace App\FrontModule\Presenters;

class HomepagePresenter extends BasePresenter{

  public function renderDefault(){
      $this->redirect('Product:list');
  }
}
