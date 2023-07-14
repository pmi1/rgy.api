<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property $typeId
 * @property $statusId
 * @property $stateId
 * @property $priorityId
 * @property $responsibleId
 * @property $authorId
 * @property $orderId
 * @property $comment
 * @property $date
 * @method static search()
 * Class TodoManager
 * @package App
 */
class TodoManager extends Model
{
    protected $table = 'todo_managers';
    protected $fillable = [
        'todo_type_id',
        'todo_status_id',
        'todo_state_id',
        'todo_priority_id',
        'responsible_id',
        'author_id',
        'order_id',
        'comment',
        'date'
    ];

    /**
     * @param int $value
     * @return mixed
     */
    public function getTypeIdAttribute()
    {
        return $this->attributes['todo_type_id'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setTypeIdAttribute(int $value)
    {
        $this->attributes['todo_type_id'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return mixed
     */
    public function getStatusIdAttribute($value)
    {
        return $this->attributes['todo_status_id'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStatusIdAttribute(int $value)
    {
        $this->attributes['todo_status_id'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getStateIdAttribute($value)
    {
        return $this->attributes['todo_state_id'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStateIdAttribute(int $value)
    {
        $this->attributes['todo_state_id'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getPriorityIdAttribute($value)
    {
        return $this->attributes['todo_priority_id'];
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPriorityIdAttribute(int $value)
    {
        $this->attributes['todo_priority_id'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getResponsibleIdAttribute($value)
    {
        return $this->attributes['responsible_id'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setResponsibleIdAttribute($value)
    {
        $this->attributes['responsible_id'] = $value;
        return $this;
    }

    /**
     * @param int $value
     * @return mixed
     */
    public function getAuthorIdAttribute($value)
    {
        return $this->attributes['author_id'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAuthorIdAttribute($value)
    {
        $this->attributes['author_id'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getOrderIdAttribute($value)
    {
        return $this->attributes['order_id'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOrderIdAttribute($value)
    {
        $this->attributes['order_id'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getCommentAttribute($value)
    {
        return $this->attributes['comment'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDateAttribute($value)
    {
        return $this->attributes['date'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value;
        return $this;
    }

    public function getCreatedAtAttribute($value)
    {
        $column = $this->getCreatedAtColumn();
        return $this->attributes[$column];
    }


    public function responsible ()
    {
        return $this->hasOne(User::class, 'user_id', 'responsible_id');
    }

    /**
     * @return array
     */
    public static function getList() {
        $todoManagers = self::all();
        $response = [];
        foreach ($todoManagers as $todoManager) {
            $response[] = [
                'id' => $todoManager->id,
                'typeId'  => $todoManager->typeId,
                'statusId'  => $todoManager->statusId,
                'stateId'  => $todoManager->stateId,
                'priorityId'  => $todoManager->priorityId,
                'responsible'  => $todoManager
                    ->responsible()
                    ->first([
                        'user_id as id',
                        'name',
                        'lastname as lastName',
                        'secondname as secondName',
                        'phone',
                        'email',
                    ]),
                'date'  => $todoManager->date,
                'authorId'  => $todoManager->authorId,
                'createdAt'  => $todoManager->getCreatedAtColumn(),
                'orderId'  => $todoManager->orderId,
                'comment'  => $todoManager->comment
            ];
        }

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getById($id)
    {
        $todoManager = self::find($id);

        return [
            'id' => $todoManager->id,
            'typeId'  => $todoManager->typeId,
            'statusId'  => $todoManager->statusId,
            'stateId'  => $todoManager->typeId,
            'priorityId'  => $todoManager->priorityId,
            'responsible'  => $todoManager
                ->responsible()
                ->first([
                    'user_id as id',
                    'name',
                    'lastname as lastName',
                    'secondname as secondName',
                    'phone',
                    'email',
                ]),
            'date'  => $todoManager->date,
            'authorId'  => $todoManager->authorId,
            'createdAt'  => $todoManager->getCreatedAtColumn(),
            'orderId'  => $todoManager->orderId,
            'comment'  => $todoManager->comment
        ];
    }

    /**
     * @param array $options
     * @return bool|mixed
     */
    public static function create($options)
    {
        $object = new self;
        return $object->save($options);
    }

    public function save(array $options = [])
    {
        if (isset($options['typeId'])) {
            $this->typeId = $options['typeId'];
        }

        if (isset($options['stateId'])) {
            $this->stateId = $options['stateId'];
        }

        if (isset($options['priorityId'])) {
            $this->priorityId = $options['priorityId'];
        }

        if (isset($options['statusId'])) {
            $this->statusId = $options['statusId'];
        }

        if (isset($options['responsibleId'])) {
            $this->responsibleId = $options['responsibleId'];
        }

        if (isset($options['authorId'])) {
            $this->authorId = $options['authorId'];
        }

        if (isset($options['orderId'])) {
            $this->orderId = $options['orderId'];
        }

        if (isset($options['comment'])) {
            $this->comment = $options['comment'];
        }

        if (isset($options['date'])) {
            $this->date = $options['date'];
        }

        return parent::save();
    }

    /**
     * @param array $options
     * @return Builder
     */
    public function scopeSearch(Builder $query, array $options = [])
    {
        if (isset($options['statusesId'])) {
            $query->whereIn('todo_status_id', json_decode($options['statusesId']));
            //$query->where('todo_status_id', $options['statusId']);
        }
        if (isset($options['dateFrom'])) {
            $query->where('date', '>=', $options['dateFrom']);
        }
        if (isset($options['dateTo'])) {
            $query->where('date', '<=', $options['dateTo']);
        }
        if (isset($options['responsibleIds'])) {
            $responsibleIds = json_decode($options['responsibleIds']);
            if (!is_array($responsibleIds)) {
                $responsibleIds = [$responsibleIds];
            }
            $query->whereIn('responsible_id', $responsibleIds);
        }

        if (isset($options['authorsId'])) {
            $query->whereIn('author_id', json_decode($options['authorsId']));
        }

        if (isset($options['typesId'])) {
            $query->whereIn('todo_type_id', json_decode($options['typesId']));
        }

        if (isset($options['orderId'])) {
            $query->where('order_id', $options['orderId']);
        }
        return $query;
    }

    public function hasAttribute($attr)
    {
        return isset($this->$attr);
    }
}
