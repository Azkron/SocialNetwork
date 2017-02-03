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
    private $dateTime = NULL;
    
    public function __construct($string = '')
    {
        $this->dateTime = new DateTime($string);
    }
    
    public static function monday($mod = 0) // the week relative to current week
    {
        $date = new Date('monday this week');
        if($mod != 0)
            $date->modify("+$mod weeks");
        
        return $date;
    }
    
    public static function sunday($mod = 0) // the week relative to current week
    {
        $date = new Date('sunday this week');
        if($mod != 0)
            $date->modify("+$mod weeks");
        
        return $date;
    }
    
    public function modify($string)
    {
        $this->dateTime->modify($string);
    }
    
    public function copy()
    {
        $date = new Date();
        $date->set_timestamp($this->get_timestamp());
        return $date;
    }
    
    public function get_timestamp()
    {
        return $this->dateTime->getTimeStamp();
    }
    
    public function set_timestamp()
    {
        return $this->dateTime->setTimeStamp();
    }
    
    public function compare($other)
    {
        return strcmp($this->datetime_string(), $other->datetime_string());
    }
    
    public function compare_date($other)
    {
        return strcmp($this->date_string(), $other->date_string());
    }
    
    public function compare_hour($other)
    {
        return strcmp($this->time_string(), $other->time_string());
    }
    
    public function nextDay()
    {
        $this->dateTime->modify("+1 day");
    }
    
    public function add_days($days)
    {
        $this->dateTime->modify("+$days day");
    }
    
    public function datetime_string()// 1988-03-05 00:00:00
    {
        return $this->dateTime->format('Y-m-d H:i:s');
    }
    
    public function time_string()// 00:00:00
    {
        return $this->dateTime->format('H:i:s');
    }
    
    public function date_string()// 1988-03-05
    {
        return $this->dateTime->format('Y-m-d');
    }
    
    public function day_string()// Mon 15/3/2017
    {
        return $this->dateTime->format('D j/n/Y');
    }
}
