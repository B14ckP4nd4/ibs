<?php


    namespace blackpanda\ibs;


    use Illuminate\Support\Facades\Facade;

    class IBSngFacade extends Facade
    {

        protected static function getFacadeAccessor()
        {
            return IBSng::class;
        }

    }
