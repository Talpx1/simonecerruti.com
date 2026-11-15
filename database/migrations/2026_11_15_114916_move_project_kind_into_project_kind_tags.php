<?php

declare(strict_types=1);

use App\Enums\TagTypes;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Safe\json_decode;

return new class extends Migration {
    public function up(): void {
        $kind_type = TagTypes::PROJECT_KIND->value;

        DB::table('projects')->whereNotNull('kind')->orderBy('id')->each(function (object $row) use ($kind_type): void {
            $decoded_translations = \is_string($row->kind) ? json_decode($row->kind, true) : [];
            $translations = \array_filter(
                \is_array($decoded_translations) ? $decoded_translations : [],
                fn (mixed $name): bool => \is_string($name) && \trim($name) !== ''
            );

            if ($translations === []) {
                return;
            }

            $existing_tag = Tag::query()
                ->where('type', $kind_type)
                ->get()
                ->first(function (Tag $tag) use ($translations): bool {
                    foreach ($translations as $locale => $name) {
                        if ($tag->getTranslation('name', (string) $locale, false) === $name) {
                            return true;
                        }
                    }

                    return false;
                });

            $kind_tag = $existing_tag ?? Tag::query()->create(['name' => $translations, 'type' => $kind_type]);

            Project::query()->where('id', $row->id)->firstOrFail()->attachTag($kind_tag);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('kind');
        });
    }
};
