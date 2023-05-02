<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('category', function($row) {
                return $row->category->name;
            })
            ->addColumn('seller', function($row) {
                return $row->seller->name;
            })
            ->addColumn('price', function($row) {
                return 'Rp. ' . number_format($row->price, 2, ',', '.');
            })
            ->addColumn('image', function ($row) {
                return "<a href='#' id='imageTrigger' data-bs-toggle='modal' data-bs-target='#imageModal' data-name='$row->name' data-img='" . asset("storage/product/$row->filename") . "'>
                            <img style='height: 60px;' src='" . asset("storage/product/$row->filename") . "'/>
                        </a>";
            })
            ->addColumn('description', function($row) {
                return "<a href='#' id='descTrigger' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#descriptionModal' data-name='$row->name' data-content='$row->description'>Klik Disini</a>";
            })
            ->rawColumns(['category', 'seller', 'price', 'image', 'description'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('name'),
            Column::make('price'),
            Column::make('image'),
            Column::make('category'),
            Column::make('seller'),
            Column::make('description')->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Product_' . date('YmdHis');
    }
}
