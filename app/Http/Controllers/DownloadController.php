<?php

namespace App\Http\Controllers;
use App\Http\Resources\TaskCollection;

//csv & excel
use App\Exports\TaskExport;
use Maatwebsite\Excel\Facades\Excel;

//view to pdf
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function csv(){
        return Excel::download(
            new TaskExport(auth()->user()->id),
            "Task.csv",
        );
    }

    public function invoicePdf($order_id, $customer_id){
        $totalDue = auth()->user()->customer()->find($customer_id)->product()->sum('price');
        $invoice = auth()->user()->customer()->find($customer_id)->invoice()->where('order_number', $order_id)->first();
        $product = auth()->user()->customer()->find($customer_id)->product()->where('order_number', $order_id)->get();
        $pdf = Pdf::loadView('invoicePdf', ['invoice' => $invoice, 'totalDue' => $totalDue, 'products' => $product]);
        return $pdf->stream($invoice['customer_name'].'Task-Flow invoice.pdf');
    }

    public function pdf(){
        $tasks = new TaskCollection(auth()->user()->task);
        $user = auth()->user();
        $pdf = Pdf::loadView('pdf', ['tasks' => $tasks, 'user' => $user]);
        return $pdf->stream('task.pdf');
    }

    public function xlsx(){
        return Excel::download(
            new TaskExport(auth()->user()->id),
            "Task.xlsx"
        );
    }

}
