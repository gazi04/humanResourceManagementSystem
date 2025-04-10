<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class EmployeeRetrievalException extends Exception
{
    /**
     * Render the exception as an HTTP response
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => 'EMPLOYEE_RETRIEVAL_ERROR',
        ], 500);
    }
}
