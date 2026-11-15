<?php

declare(strict_types=1);

namespace App\Actions;

use App\Ai\Agents\ContentWriter;
use App\Data\FieldGenerationData;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Lorisleiva\Actions\Concerns\AsAction;

use function Safe\set_time_limit;

final class GenerateFieldContent {
    use AsAction;

    public function handle(FieldGenerationData $data): string {
        set_time_limit(240);

        $language = config()->string("laravellocalization.supportedLocales.{$data->locale}.name", $data->locale);

        $user_prompt = View::make('ai.field-generation-prompt', ['data' => $data, 'language' => $language])->render();

        $response = resolve(ContentWriter::class)->prompt($user_prompt);

        $text = \trim(Arr::string($response->toArray(), 'text'));
        $max_characters = $data->field->getMaxCharacters();

        return $max_characters === null ? $text : \mb_substr($text, 0, $max_characters);
    }
}
