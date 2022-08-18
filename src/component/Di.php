<?php

namespace Slink\component;

/**
 * 依赖注入类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/14
 */
class Di {
    use Single;

    private $container = array();

    public function set($key, $obj, ...$arg) : void
    {
        $this->container[$key] = array(
            'obj' => $obj,
            'params' => $arg
        );
    }

    public function delete($key) : void
    {
        unset($this->container[$key]);
    }

    public function clear() : void
    {
        $this->container = array();
    }

    public function get($key)
    {
        if (isset($this->container[$key])) {
            $obj = $this->container[$key]['obj'];
            $params = $this->container[$key]['params'];
            if (is_object($obj) || is_callable($obj)) {
                return $obj;
            } else if (is_string($obj) && class_exists($obj)) {
                try {
                    $this->container[$key]['obj'] = new $obj(...$params);
                    return $this->container[$key]['obj'];
                } catch (\Throwable $throwable) {
                    throw $throwable;
                }
            } else {
                return $obj;
            }
        } else {
            return null;
        }
    }
}