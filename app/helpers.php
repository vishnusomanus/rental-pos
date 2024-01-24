<?php
if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

if (!function_exists('activeSegment3')) {
    function activeSegment3($name, $segment = 3, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

if (!function_exists('activeSegmentOpen')) {
    function activeSegmentOpen($name, $segment = 2, $class = 'menu-is-opening menu-open')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}
