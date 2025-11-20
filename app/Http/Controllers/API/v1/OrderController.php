<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\InvoiceService;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderController extends Controller
{
    protected $service;
    protected $orders;
    protected $invoice;

    public function __construct(
        OrderService $service,
        OrderRepositoryInterface $orders,
        InvoiceService $invoice
    ) {
        $this->service = $service;
        $this->orders = $orders;
        $this->invoice = $invoice;
    }

    public function show($id)
    {
        return $this->orders->find($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity'   => 'required|integer|min:1'
        ]);

        $order = $this->service->createOrder(
            $request->user(),
            $data['items']
        );

        return response()->json($order, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = $this->orders->find($id);

        $order = $this->service->processStatusChange($order, $request->status);

        return response()->json($order);
    }

    public function invoice($id)
    {
        $order = $this->orders->find($id);
        $pdfContent = $this->invoice->generate($order); // returns PDF as string

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header("Content-Disposition", "attachment; filename=invoice-{$order->id}.pdf");
        }
}
