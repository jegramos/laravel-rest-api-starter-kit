<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

/** @deprecated - I've ditched this approach for schema query check and caching */
trait HasSortableAttributes
{
    /**
     * @Internal - This method is used internally by Eloquent
     * Add `sortable_fields` to the appends attribute of the target class
     * if `appendSortableFields` is set to true
     *
     * @return void
     */
    public function initializeHasSortableAttributes(): void
    {
        static::retrieved(function ($model) {
            if ($this->appendSortableFields) {
                $model->appends = array_merge($model->fillable, ['sortable_fields']);
            }
        });
    }

    /**
     * @Attribute
     * Get the sortable fields of a model
     */
    public function sortableFields(): Attribute
    {
        return Attribute::get(function () {
            if (empty($this->sortableFields)) {
                // send the fillable properties with timestamps
                // if sortableFields is empty
                $timestamps = ['created_at', 'updated_at'];

                return
                    is_array($this->fillable) && !empty($this->fillable)
                        ? array_merge($this->fillable, $timestamps)
                        : [];
            }

            return $this->sortableFields;
        });
    }
}
