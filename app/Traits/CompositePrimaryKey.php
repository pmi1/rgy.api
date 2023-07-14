<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CompositePrimaryKey {

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyName()
    {
        return parent::getKeyName()[0];
    }

    public function getFullKeyName()
    {
        return parent::getKeyName();
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = parent::getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }
    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
    /**
   * Perform the actual delete query on this model instance.
   *
   * @return void
   */
  protected function runSoftDelete()
  {
    $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->attributes[$this->getKeyName()])
    ->where(parent::getKeyName()[1], $this->attributes[parent::getKeyName()[1]]);
    $time = $this->freshTimestamp();
    $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];
    $this->{$this->getDeletedAtColumn()} = $time;
    if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
      $this->{$this->getUpdatedAtColumn()} = $time;

      $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
    }
    $query->update($columns);
  }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  array  $ids Array of keys, like [column => value].
     * @param  array  $columns
     * @return mixed|static
     */
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->getFullKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }
        return $query->first($columns);
    }
}