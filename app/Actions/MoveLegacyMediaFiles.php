<?php

declare(strict_types=1);

namespace App\Actions;

use App\Media\MediaPathGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MoveLegacyMediaFiles {
    use AsAction;

    public function handle(): int {
        $generator = new MediaPathGenerator;

        $groups = Media::query()
            ->whereNotNull('legacy_model_id')
            ->get()
            ->groupBy(fn (Media $media): string => serialize([
                $media->model_type,
                $media->getAttribute('legacy_model_id'),
                $media->collection_name,
            ]));

        return $groups->sum(fn (Collection $group): int => $this->moveGroup($group, $generator));
    }

    /**
     * @param  Collection<int, Media>  $group
     */
    private function moveGroup(Collection $group, MediaPathGenerator $generator): int {
        $first = $group->firstOrFail();

        $current_media = $first->replicate();
        $current_media->setAttribute('legacy_model_id', null);

        $legacy_base = $generator->getPath($first);
        $current_base = $generator->getPath($current_media);

        $disk_names = $group
            ->flatMap(fn (Media $media): array => [$media->disk, $media->conversions_disk ?? $media->disk])
            ->unique()
            ->values();

        foreach ($disk_names as $disk_name) {
            $disk = Storage::disk($disk_name);

            foreach ($disk->allFiles($legacy_base) as $path) {
                $target = $current_base.mb_substr($path, mb_strlen($legacy_base));

                if (! $disk->exists($target)) {
                    $disk->copy($path, $target);
                }
            }
        }

        DB::transaction(fn (): int => Media::query()
            ->whereIn('id', $group->pluck('id')->all())
            ->update(['legacy_model_id' => null]));

        foreach ($disk_names as $disk_name) {
            Storage::disk($disk_name)->deleteDirectory($legacy_base);
        }

        return $group->count();
    }
}
