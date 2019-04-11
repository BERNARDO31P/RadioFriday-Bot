<?php

use Jenner\SimpleFork\Cache\SharedMemory;

final class bot
{

    private $cache, $bot;

    public final function __construct(ts3admin &$query)
    {
        $this->cache = new SharedMemory();
        $this->cache->set("pids", array());
        $this->bot = $query;
    }

    public final function getClients()
    {
        $pids = $this->cache->get("pids");
        array_push($pids, getmypid());
        $this->cache->set("pids", $pids);
    }

    public final function afkMover()
    {
        $pids = $this->cache->get("pids");
        array_push($pids, getmypid());
        $this->cache->set("pids", $pids);
        while (true) ;
    }

    public final function supportNotify()
    {
        $pids = $this->cache->get("pids");
        array_push($pids, getmypid());
        $this->cache->set("pids", $pids);
    }

    public final function chatSystem()
    {
        $pids = $this->cache->get("pids");
        array_push($pids, getmypid());
        $this->cache->set("pids", $pids);
    }

    public final function getPids()
    {
        return $this->cache->get("pids");
    }
}