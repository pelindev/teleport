<?php
/**
 * PaymentController file
 * php version 8.0
 *
 * @category Class
 * @package  Controller
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;

use App\Http\Requests\CreatePaymentRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

/**
 * PaymentController class
 * php version 8.0
 *
 * @category Class
 * @package  Controller
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */
class PaymentController extends Controller
{
    /**
     * Creates new payment for user and stores to database
     *
     * @param \App\Http\Requests\CreatePaymentRequest $request request
     *
     * @return \App\Models\Payment|\Illuminate\Http\JsonResponse
     **/
    public function create(
        CreatePaymentRequest $request
    ) : Payment|JsonResponse {
        try {
            $validatedData = $request->validated();

            $payment = new Payment($validatedData);

            $transaction = $this->makeTransaction(
                $request->user_id,
                $payment->amount,
                $payment->action
            );

            if (!$transaction) {
                $payment->status = 'failed';
            }

            $user = User::find($request->user_id);
            $payment->total = $user->balance;
            $payment->save();

            return Payment::find($payment->id);
        } catch (\Throwable $e) {
            report($e);
            return $this->sendError(
                'Something went wrong',
                500
            );
        }
    }

    /**
     * Changes user's balance if user's status is active
     *
     * @param int|string $id     user_id
     * @param int|string $amount transaction amount
     * @param int|string $action transaction action
     *
     * @return bool|\App\Models\Payment|\Illuminate\Http\JsonResponse
     **/
    private function makeTransaction(
        int|string $id,
        int|string $amount,
        string $action = 'replenishment'
    ) : bool|JsonResponse {
        try {
            $user = User::find($id);

            if ($user->status === 'blocked') {
                return false;
            }

            switch ($action) {
                case 'replenishment':
                    $user->balance += $amount;
                    break;
                case 'write-off':
                    $user->balance -= $amount;
                    break;
                default:
                    return false;
            }
    
            $user->save();

            return true;
        } catch (\Throwable $e) {
            report($e);
            return $this->sendError(
                'Something went wrong',
                500
            );
        }
    }

    /**
     * Cancels transaction by id
     *
     * @param int|string $id transaction id
     *
     * @return bool|\App\Models\Payment|\Illuminate\Http\JsonResponse
     **/
    public function cancelTransaction($id) : Payment|JsonResponse
    {
        $payment = Payment::find($id);

        if ($payment->status !== 'successed') {
            return $this->sendError(
                "Forbidden because the transaction $id " .
                "was $payment->status",
                403
            );
        }

        $user = User::find($payment->user_id);

        $user->balance = $payment->action === 'replenishment'
            ? $user->balance - $payment->amount
            : $user->balance + $payment->amount;

        $user->save();

        $payment->total = $user->balance;
        $payment->status = 'canceled';
        $payment->save();

        return $payment;
    }

    /**
     * Returns all filtered payments
     *
     * @param \Illuminate\Http\Request $request request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     **/
    public function getAll(Request $request) : Collection
    {
        return Payment::filter($request->query())->get();
    }

    /**
     * Returns payment by id
     *
     * @param int|string $id transaction id
     *
     * @return \App\Models\Payment
     **/
    public function getById(int|string $id) : Payment
    {
        return Payment::findOrFail($id);
    }
}
