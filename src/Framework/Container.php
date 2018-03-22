<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11.12.2017
 * Time: 18:18
 */

namespace Framework;


class Container
{
     private $services = [];
     public function set($key, $service){
         $this->services[$key]=$service;
         return $this;
     }

     public function get($key){
         if (!isset($this->services[$key])){
             throw new \Exception("Service {$key} not found");
         }
         return $this->services[$key];
     }
}