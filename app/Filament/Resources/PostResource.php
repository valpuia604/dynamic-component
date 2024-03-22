<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Language;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs(static::formTabs())
                    ->columnSpanFull(),

                /* This below FileUpload which is not dynamic is partially working
                ** If I remove `->required()` then the rules is not working
                ** But if I put `->required()` like below then it's working fine
                */
                // Forms\Components\FileUpload::make('img')
                //     ->label('img')
                //     ->image()
                //     // ->required()
                //     ->rules([
                //         'dimensions:width=100,height=200',
                //     ]),
                // ->nestedRecursiveRules([
                //     'dimensions:width=100,height=200',
                // ]),
                // ->nestedRecursiveRules('required|dimensions:width=100,height=200'),
            ]);
    }

    protected static function formTabs(): array
    {
        $fields = [];

        foreach (Language::all() as $language) {
            $fields[] = Forms\Components\Tabs\Tab::make($language->id)
                ->label($language->name)
                ->schema([
                    // TextInput is working
                    Forms\Components\TextInput::make('trans.'.$language->id.'.title')
                        ->label('Title')
                        ->rules([
                            'required', 'min:3',
                        ]),

                    // FileUpload is not working
                    /* This below FileUpload which is dynamic components is not working
                    ** Whether `->required()` is put or not, the nestedRecursiveRules() is not working.
                    */
                    Forms\Components\FileUpload::make('trans.'.$language->id.'.image')
                        ->label('image')
                        ->image()
                        // ->required() // this is working
                        ->hiddenLabel()
                        // below rules() is not working
                        // ->rules([
                        //     'dimensions:width=100,height=200',
                        // ]),
                        ->nestedRecursiveRules([
                            'required',
                            'dimensions:width=100,height=200',
                            // Rule::dimensions()->width(10)->height(50),
                        ]),
                ]);
        }

        return $fields;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePosts::route('/'),
        ];
    }
}
