<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Subscription\Application\SubscribeToChannelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscribeToChannelController extends Controller
{
    public function __construct(private readonly SubscribeToChannelService $service) {}

    public function __invoke(Request $request, Channel $channel)
    {
        $this->service->execute($request->user(), $channel);
        return response()->json(['message' => 'Inscrição realizada com sucesso!'], Response::HTTP_CREATED);
    }
}