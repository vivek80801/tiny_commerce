<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use function App\Helpers\orderInvoicePath;
use function App\Helpers\orderInvoicePathTemp;
use function App\Helpers\pathForInvoice;

class GenerateOrderInvoce implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     * @param Collection<OrderItem> $orderItems
     */
    public function __construct(
        private Collection $orderItems,
        private Order $order,
        private int $userId,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $orderItems = $this->orderItems;
        $user = User::find(
            $this->userId
        );

        if(
            !file_exists(
                pathForInvoice(
                    orderInvoicePath(
                        $order
                    )
                )
            )
        )
        {
            $html = view(
                "user.receipt.invoice",
                compact(
                    "orderItems",
                    "order",
                    "user",
                )
            )
                ->render();

            $tempFilePathForInvoice = pathForInvoice(
                orderInvoicePathTemp(
                    $order
                )
            );

            $filePathForInvoice = pathForInvoice(
                orderInvoicePath(
                    $order
                )
            );

            Pdf::loadHTML($html)
                ->setPaper(
                    "a4",
                    "landscape"
                )
                ->setWarnings(false)
                ->save(
                    $tempFilePathForInvoice
                );

            File::copy(
                $tempFilePathForInvoice,
                $filePathForInvoice
            );

            File::delete(
                $tempFilePathForInvoice
            );

            Log::info(
                "Invoice is generated for order id: "
                    . $order->order_id
                    .". check here "
                    . $filePathForInvoice
            );
        } else {
            Log::info(
                "Invoice is already generated for order id: "
                    . $order->order_id
            );
        }
    }

    public function failed(): void
    {
        Log::error(
            "generate invoice job failed with order id: "
            .$this->order->id
        );
    }
}
