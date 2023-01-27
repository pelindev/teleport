<?php
/**
 * CreatePaymentRequest file
 * php version 8.0
 *
 * @category Class
 * @package  FormRequest
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * CreatePaymentRequest class
 * php version 8.0
 *
 * @category Class
 * @package  FormRequest
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */
class CreatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required',
            'action'  => 'required|in:replenishment,write-off',
            'status'  => 'nullable|in:successed,failed,canceled'
        ];
    }

    /**
     * Overrides errors text
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'       => 'field is required',
            'user_id.exists' => 'user not found',
            'action.in'      => "Action doesn't exist. " .
                'Existing actions: replenishment, write-off',
            'status.in'      => "Status doesn't exist. " .
                'Existing statuses: successed, failed, canceled'
        ];
    }

    /**
     * Overrides failed validation exception
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator validator
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => 'false',
                    'message' => 'validation failed',
                    'errors'  => $validator->errors()
                ],
                400
            )
        );
    }
}
