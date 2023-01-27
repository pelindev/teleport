<?php
/**
 * UpdateUserRequest file
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
 * UpdateUserRequest class
 * php version 8.0
 *
 * @category Class
 * @package  FormRequest
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $regex = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/';

        return [
            'name'     => 'nullable|string|min:3|max:50',
            'phone'    => [
                'nullable',
                'string',
                'unique:users,phone',
                "regex:$regex",
            ],
            'balance' => 'nullable|integer',
            'status'   => 'nullable|in:active,blocked'
        ];
    }

    /**
     * Overrides errors text
     *
     * @return array
     */
    public function messages() : array
    {
        return [
            'status.in' => 'The selected status is invalid. ' .
                'Existing statuses: active, blocked'
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
