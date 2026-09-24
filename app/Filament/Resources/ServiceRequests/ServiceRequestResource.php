<?php

namespace App\Filament\Resources\ServiceRequests;

use App\Filament\Resources\ServiceRequests\Pages\CreateServiceRequest;
use App\Filament\Resources\ServiceRequests\Pages\EditServiceRequest;
use App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use App\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use App\Filament\Resources\ServiceRequests\RelationManagers\DispositionsRelationManager;
use App\Filament\Resources\ServiceRequests\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\ServiceRequests\RelationManagers\StatusHistoriesRelationManager;
use App\Filament\Resources\ServiceRequests\Schemas\ServiceRequestForm;
use App\Filament\Resources\ServiceRequests\Tables\ServiceRequestsTable;
use App\Models\ServiceRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Sosial';

    protected static ?string $modelLabel = 'Pengajuan Layanan';

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Layanan';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereNotIn('status', ['completed', 'rejected'])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            StatusHistoriesRelationManager::class,
            DispositionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequests::route('/'),
            'create' => CreateServiceRequest::route('/create'),
            'view' => ViewServiceRequest::route('/{record}'),
            'edit' => EditServiceRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
