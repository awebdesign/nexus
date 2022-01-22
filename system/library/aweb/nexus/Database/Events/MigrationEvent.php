<?php

namespace Aweb\Nexus\Database\Events;

use Aweb\Nexus\Database\Migrations\Migration;
use Illuminate\Contracts\Database\Events\MigrationEvent as MigrationEventContract;

abstract class MigrationEvent implements MigrationEventContract
{
    /**
     * An migration instance.
     *
     * @var \Aweb\Nexus\Database\Migrations\Migration
     */
    public $migration;

    /**
     * The migration method that was called.
     *
     * @var string
     */
    public $method;

    /**
     * Create a new event instance.
     *
     * @param  \Aweb\Nexus\Database\Migrations\Migration  $migration
     * @param  string  $method
     * @return void
     */
    public function __construct(Migration $migration, $method)
    {
        $this->method = $method;
        $this->migration = $migration;
    }
}
