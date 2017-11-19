<?php

namespace Royscheepens\CurrentlyOpen;

use Illuminate\Support\Facades\Facade;

class CurrentlyOpenFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() 
    { 
      return 'currently-open'; 
  }
}