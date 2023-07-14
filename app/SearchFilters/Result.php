<?php
namespace App\SearchFilter;
use Illuminate\Database\Eloquent\Builder;

abstract class Result
{
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    abstract public function get();
}