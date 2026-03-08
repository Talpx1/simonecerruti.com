<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

trait HasLead {
    /**
     * @return Attribute<non-falsy-string, never>
     */
    abstract protected function leadTitle(): Attribute;

    /**
     * @return MorphOne<Lead, $this>
     */
    public function lead(): MorphOne {
        return $this->morphOne(Lead::class, 'leadable');
    }

    /**
     * @return class-string
     */
    public function getInfolist(): string {
        return $this->infolist ?? '\\App\\Filament\\Resources\\'.Str::plural(class_basename(__CLASS__)).'\\Schemas\\'.class_basename(__CLASS__).'Infolist';
    }

    /**
     * @return list<\Filament\Infolists\Components\Entry>
     */
    abstract public function getInfolistComponents(): array;

    /**
     * @return non-falsy-string
     */
    abstract public static function getLeadSourceName(): string;
}
