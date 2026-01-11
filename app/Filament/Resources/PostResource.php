<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('create post')
                    ->description('create post over here')
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->required(),
                        ColorPicker::make('color')->required(),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'slug')
                            ->required(),
                        RichEditor::make('content')->required()->columnSpanFull(),
                    ])->columnSpan(2)->columns(2),

                Group::make()->schema([
                    Section::make('Image')
                        ->collapsible()
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->disk('public')
                                ->directory('thumbnails')
                                ->required(),
                        ]),
                    Section::make('Meta')
                        ->schema([
                            TagsInput::make('tags')->required(),
                            Checkbox::make('published')->required(),
                        ])->columnSpan(1),

                    Section::make('Authors')
                        ->collapsible()
                        ->schema([
                            CheckboxList::make('authors')
                                ->relationship('authors', 'name')
                        ])
                ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->label('Post title'),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->toggleable(),

                ColorColumn::make('color')
                    ->toggleable(),
                ImageColumn::make('thumbnail')->disk('public')
                    ->toggleable(),

                ColumnGroup::make('Publishing info', [
                    CheckboxColumn::make('published'),
                    TextColumn::make('created_at')
                        ->label('Published On')
                        ->date()
                ]),
            ])
            ->filters([
                // Filter::make('Published posts')->query(
                //     function (Builder $query): Builder {
                //         return $query->where('published', true);
                //     }
                // ),

                TernaryFilter::make('published'),

                SelectFilter::make('category_id')
                ->label('category')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
