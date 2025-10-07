<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Subscription\Application\CancelSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CancelSubscriptionController extends Controller
{
    public function __construct(private readonly CancelSubscriptionService $service) {}

    public function __invoke(Request $request, Channel $channel)
    {
        $this->service->execute($request->user(), $channel);
        return response()->noContent();
    }
}