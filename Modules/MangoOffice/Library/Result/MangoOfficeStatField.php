<?php
namespace Modules\MangoOffice\Library\Result;

use Carbon\Carbon;

Class MangoOfficeStatField extends MangoOfficeFieldData {

    /**
     * Задание переменной и приведение ее к нужному формату
     *
     * @param $k
     * @param $v
     *
     * @return $this
     */
    public function set($k, $v) {
        if (isset($this->available_fields[$k])) {
            switch ($this->available_fields[$k]) {
                case 'array':
                    $explode                 = explode(',', $v);
                    $this->append_fields[$k] = array_filter(
                        $explode,
                        function ($v) {
                            return (bool)$v;
                        }
                    );
                    break;
                case 'timestamp':
                    $this->append_fields[$k] = Carbon::createFromTimestamp($v, 'Europe/Moscow');
                    break;
                default:
                    $this->append_fields[$k] = (string)$v;
                    break;
            }
        }
        return $this;
    }
}