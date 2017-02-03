<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Date
 *
 * @author Hugo
 */
class Date {
    private $dateTime;
    
    public function __construct($string)
    {
        $dateTime = new DateTime($string);
    }
    
    public static function monday($mod = 0) // the week relative to current week
    {
        $date = new Date('monday this week');
        if($mod != 0)
            $date->modify("+$mod weeks");
        
        return $date;
    }
    
    public function compare($other)
    {
        return strcmp($this->datetime_string(), $other->datetime_string());
    }
    
    public function compareDate($other)
    {
        return strcmp($this->date_string(), $other->date_string());
    }
    
    public function compareHour($other)
    {
        return strcmp($this->time_string(), $other->time_string());
    }
    
    public function nextDay()
    {
        $dateTime->modify("+1 day");
    }
    
    public function datetime_string()// 1988-03-05 00:00:00
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    public function time_string()// 00:00:00
    {
        return $dateTime->format('H:i:s');
    }
    
    public function date_string()// 1988-03-05
    {
        return $dateTime->format('Y-m-d');
    }
    
    public function day_string()// Mon 15/3/2017
    {
        return $dateTime->format('D j/n/Y');
    }
}
