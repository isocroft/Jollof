<?php

namespace Providers\Tools;

class SchemaObject {

    public function __construct(array $arguments = array()) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                if(PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 4){
                    if ($argument instanceOf Closure) {
                        $this->{$property} = $argument;
                    } else {
                        $this->{$property} = $argument;
                    }
                }else{
                    $this->{$property} = $argument;
                }    
            }
        }
    }

    public function __call($method, $arguments) {
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            if(PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 4){
                return call_user_func_array($this->{$method}->bindTo($this), $arguments);
            }
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new \BadMethodException("Call to undefined method SchemaObject::{$method}()");
        }
    }
}