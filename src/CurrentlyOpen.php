<?php

namespace Royscheepens\CurrentlyOpen;

use Illuminate\Support\Str;
use Carbon\Carbon;

class CurrentlyOpen {

    /**
     * The date used to check if we're open
     * @var Carbon
     */
    protected $date;

    /**
     * Are we currently open
     * @var boolean
     */
    protected $isOpen = false;

    /**
     * If we're open, until what date/time
     * @var Carbon
     */
    protected $openUntil = null;

    /**
     * Class Constructor
     */
    function __construct()
    {
        $this->checkConfig();
    }

    /**
     * Returns true when we're open, false when we're not
     * @param  Mixed $date       A valid Carbon instance, a date string, or null
     * @return Mixed             The response as an object
     */
    public function check($date = null)
    {
        $this->setDate($date);

        $this->areWeOpen();

        $response = new \StdClass;

        $response->open = $this->isOpen;
        $response->until = $this->openUntil;

        return $response;
    }

    /**
     * Returns true when we're open, false when we're not
     * @param  Mixed $date A valid Carbon instance, a date string, or null
     * @return bool
     */
    public function checkSimple($date = null)
    {
        $this->setDate($date);

        $this->areWeOpen();

        return $this->isOpen;
    }

    /**
     * Checks if the date set is open based on the config values
     * @return void
     */
    private function areWeOpen()
    {
        $dateStr = $this->date->toDateString();

        $dayOfWeek = $this->date->dayOfWeek;

        $exceptions = config('currently-open.exceptions');

        $weekdays = config('currently-open.weekdays');

        // Is an exception set for this date? If so, it overrules the generic weekdays
        if( in_array($dateStr, array_keys($exceptions)))
        {
            $value = $exceptions[$dateStr];

            // If true, we're open the whole day. If false, closed the whole day
            if(is_bool($value))
            {
                $this->isOpen = $value;

                if($this->isOpen)
                {
                    $this->openUntil = $this->date->copy()->setTimeFromTimeString('00:00');
                }
            }
            else
            {
                list($start, $end) = $value;

                $this->isOpen = $this->isTimeBetween($start, $end);

                if($this->isOpen)
                {
                    $this->openUntil = $this->date->copy()->setTimeFromTimeString($end);
                }
            }
        }
        else
        {
            // We have no opening hours defined for this day of the week, so we're closed
            if(! in_array($dayOfWeek, array_keys($weekdays)))
            {
                return;
            }

            $value = $weekdays[$dayOfWeek];

            // If true, we're open the whole day. If false, closed the whole day
            if(is_bool($value))
            {
                $this->isOpen = $value;

                if($this->isOpen)
                {
                    $this->openUntil = $this->date->copy()->setTimeFromTimeString('00:00');
                }

                return;
            }
            else
            {
                list($start, $end) = $value;

                $this->isOpen = $this->isTimeBetween($start, $end);

                if($this->isOpen)
                {
                    $this->openUntil = $this->date->copy()->setTimeFromTimeString($end);
                }
            }
        }

    }

    /**
     * Tests if a supplied time is between two other times, based on the date
     * @param  String  $start The start time
     * @param  String  $end   The end time
     * @return boolean        True if within time, false if not
     */
    private function isTimeBetween($start, $end)
    {
        // todo: check time format

        $start = $this->date->copy()->setTimeFromTimeString($start);

        $end = $this->date->copy()->setTimeFromTimeString($end)->subSeconds(1);

        return $this->date->between($start, $end);
    }

    /**
     * Sets the date for internal use, also does some validation
     * @param Mixed $date A valid Carbon instance, a date string, or null
     * @return void
     */
    private function setDate($date = null)
    {
        if(! $date)
        {
            $date = Carbon::now();
        }
        else
        {
            // Try to parse the string as a date
            if( is_string($date) )
            {
                $date = Carbon::parse($date);
            }

            // Parsing failed, or something else was supplied
            if(! $date instanceof Carbon)
            {
                throw new Exception('Unable to parse supplied date. Please provide a parseable date string or a Carbon instance');
            }
        }

        $this->date = $date;
    }

    /**
     * Check if we have any valid config to work with
     * @return void
     */
    private function checkConfig()
    {
        // todo: implement
    }
}