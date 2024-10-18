<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Messages\Message;
use App\Messages\Session as MessageSession;
use Exception;
use Illuminate\Support\Facades\Log;


class AddressController extends Controller
{
    public function edit($item_id)
    {
        $user = auth()->user();
        $message = MessageSession::exists('message');
        return view('address', compact('user', 'item_id', 'message'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        $validated = $request->validated();
        $user = auth()->user();

        try {
            $user->update($validated);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()
                ->withInput()
                ->with('message', Message::get('address.failed'));
        }

        return redirect()
            ->route('purchase', $item_id)
            ->with('message', Message::get('address.success'));
    }
}
