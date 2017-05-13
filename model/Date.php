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
        date_default_timezone_set("Europe/Brussels");
        $this->dateTime = new DateTime($string);
    }
    
    public static function monday($mod = 0) // the week relative to current week
    {
        date_default_timezone_set("Europe/Brussels");
        if(date('N') != 7) // There is a bug in DateTime lib that returns next week if on sunday
            $date = new Date('monday this week');
        else 
            $date = new Date('monday last week');
        
        //$date->modify("-1 day");// for some reason it gives 1 day after( Tuesday)
        if($mod != 0)
            $date->modify("+$mod weeks");
        
        return $date;
    }
    
    public static function sunday($mod = 0) // the week relative to current week
    {
        date_default_timezone_set("Europe/Brussels");
        if(date('N') != 7) // There is a bug in DateTime lib that returns next week if on sunday
            $date = new Date('sunday this week');
        else 
            $date = new Date('sunday last week');
        
        //$date->modify("-1 day");// for some reason it gives 1 day after( monday)
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
    
    public function set_timestamp($timestamp)
    {
        return $this->dateTime->setTimeStamp($timestamp);
    }
    
    public function compare($other)
    {
        return strcmp($this->datetime_string(), $other->datetime_string());
    }
    
    public function compare_date($other)
    {
        return strcmp($this->date_string(), $other->date_string());
    }
    
    public function compare_time($other)
    {
        return strcmp($this->time_string(), $other->time_string());
    }
    
    public function next_day()
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
    
    public function fullcalendar_string()// 1988-03-05 00:00:00
    {
        return $this->dateTime->format('Y-m-d\TH:i:s');
    }
    
    public function date_input_string()// 1988-03-05 
    {
        return $this->dateTime->format('Y-m-d');
    }
    
    public function hour_input_string()//  00:00:00
    {
        return $this->dateTime->format('H:i:s');
    }
    
    public function time_string()// 00:00:00
    {
        return $this->dateTime->format('H\hi');
    }
    
    public function date_string()// 1988-03-05
    {
        return $this->dateTime->format('Y-m-d');
    }
    
    public function date_string_normal()// 1988/03/05
    {
        return $this->dateTime->format('d/m/Y');
    }
    
    public function day_string()// Mon 15/3/2017
    {
        return $this->dateTime->format('D j/n/Y');
    }
    
    public function week_string()// Mon 15/3/2017
    {
        $sunday = $this->copy();
        $sunday->add_days(6);
        return "From ".$this->date_string_normal()." to ".$sunday->date_string_normal();
    }
}
