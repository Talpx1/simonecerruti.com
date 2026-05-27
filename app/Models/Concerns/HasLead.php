<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Lead;
use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

trait HasLead {
    /**
     * @return Attribute<string, never>
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
        /** @var class-string $infolist */
        $infolist = $this->infolist ?? '\\App\\Filament\\Resources\\'.Str::plural(class_basename(__CLASS__)).'\\Schemas\\'.class_basename(__CLASS__).'Infolist';

        return $infolist;
    }

    /**
     * @return list<Entry>
     */
    abstract public function getInfolistComponents(): array;

    abstract public static function getLeadSourceName(): string;
}
