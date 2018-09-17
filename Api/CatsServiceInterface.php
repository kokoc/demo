<?php


namespace Kokoc\Demo\Api;

interface CatsServiceInterface
{
    /**
     * Create cats
     *
     * @return bool
     */
    public function create();

    /**
     * Remove cats
     *
     * @return bool
     */
    public function remove();
}