<?php

    /**
     * Returns true when we are currently open 
     *
     * @param DateTime $date The date to check
     *
     * @return bool
     */
    if (! function_exists('currently_open')) {

        function currently_open($date = null)
        {
            return app('currently-open')->check($date);     
        }
    }
