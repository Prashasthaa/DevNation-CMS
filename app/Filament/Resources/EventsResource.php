<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventsResource\Pages;
use App\Filament\Resources\EventsResource\RelationManagers;
use App\Filament\Resources\EventsResource\RelationManagers\EventRegistrationsRelationManager;
use App\Models\Events;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EventsResource extends Resource
{
    protected static ?string $model = Events::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationLabel = 'Events';

    // protected static ?string $activeNavigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationBadgeTooltip = 'The number of events';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Event Information')->schema([
                    TextInput::make('name')->required()->label('Event Name')->placeholder('Enter the event name'),
                    Select::make('event_type')->options([
                        'workshop' => 'Workshop',
                        'webinar' => 'Webinar',
                        'seminar' => 'Seminar',
                        'conference' => 'Conference',
                        'expo' => 'Expo',
                        'meetup' => 'Meetup',
                        'hackathon' => 'Hackathon',
                    ])->required()->default('workshop'),
                    RichEditor::make('description')->columnSpanFull()->required()->label('Event Description')->placeholder('Enter the event description'),
                    FileUpload::make('banner')->required()->label('Event Banner')->image()->acceptedFileTypes(['image/*'])
                    ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                    ->directory('events')->downloadable()->preserveFilenames()->openable(),
                    TextInput::make('location')->required()->label('Event Location')->placeholder('Enter the event location'),
                    Select::make('status')->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                    ])->required()->default('draft'),
                    TextInput::make('max_attendees')->required()->label('Max Attendees')->placeholder('Enter the maximum number of attendees'),
                ])->columns(2)->collapsible(),
                Section::make('Speaker Information')->schema([
                    TextInput::make('speaker')->required()->label('Speaker Name')->placeholder('Enter the speaker name'),
                    TextInput::make('speaker_mail')->required()->label('Speaker Email')->placeholder('Enter the speaker email'),
                ])->columns(2)->collapsible(),
                Section::make('Event Settings')->schema([
                    ToggleButtons::make('is_featured')->label('is Featured?')->boolean()->grouped()->default(false),
                    ToggleButtons::make('requires_registration')->label('Requires Registration?')->boolean()->grouped()->default(false),
                    ToggleButtons::make('has_certificate')->label('Has Certificate?')->boolean()->grouped()->default(false),
                    ToggleButtons::make('notify_attendees')->label('Notify Attendees?')->boolean()->grouped()->default(false),
                    ToggleButtons::make('notify_attendance')->label('Notify Attendance?')->boolean()->grouped()->default(false),
                    ])->columns(5)->collapsible(),
                Section::make('Event Dates')->schema([
                DatePicker::make('start_date')->required()->label('Start Date')->placeholder('Select the start date')->default(now()),
                DatePicker::make('end_date')->required()->label('End Date')->placeholder('Select the end date')->default(now()),
                ])->columns(2)->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Event Name')->searchable()->sortable(),
                TextColumn::make('event_type')->label('Event Type')->searchable()->sortable(),
                TextColumn::make('location')->label('Location')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->searchable()->sortable(),
                TextColumn::make('max_attendees')->label('Max Attendees')->searchable()->sortable(),
                TextColumn::make('start_date')->label('Start Date')->searchable()->sortable(),
                TextColumn::make('end_date')->label('End Date')->searchable()->sortable(),
                TextColumn::make('speaker')->label('Speaker')->searchable()->sortable(),
                TextColumn::make('speaker_mail')->label('Speaker Email')->searchable()->sortable(),
                TextColumn::make('is_featured')->label('Featured')->searchable()->sortable(),
                TextColumn::make('requires_registration')->label('Requires Registration')->searchable()->sortable(),
                TextColumn::make('has_certificate')->label('Has Certificate')->searchable()->sortable(),
                TextColumn::make('notify_attendees')->label('Notify Attendees')->searchable()->sortable(),
                TextColumn::make('notify_attendance')->label('Notify Attendance')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Created At')->searchable()->sortable(),
                TextColumn::make('updated_at')->label('Updated At')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvents::route('/create'),
            'edit' => Pages\EditEvents::route('/{record}/edit'),
        ];
    }
}