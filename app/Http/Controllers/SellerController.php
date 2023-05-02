<?php

namespace App\Http\Controllers;

use App\DataTables\SellerDataTable;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(SellerDataTable $dataTable)
    {
        return $dataTable->render('admin.seller.index');
    }
}
