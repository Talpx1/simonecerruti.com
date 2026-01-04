<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogArticlesTable {
    public static function configure(Table $table): Table {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')
                    ->disk(config()->string('blog_article.featured_image.disk'))
                    ->visibility(config()->string('blog_article.featured_image.visibility'))
                    ->label(__('Featured image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label(__('Published at'))
                    ->dateTime('l d/m/Y H:i:s'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
