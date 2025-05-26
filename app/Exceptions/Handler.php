<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        
        // Custom handling for ModelNotFoundException
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $modelName = basename(str_replace('\\', '/', $e->getModel()));
                
                $message = match ($modelName) {
                    'Patient' => 'Patient not found',
                    'Anamnesis' => 'Anamnesis not found',
                    default => 'Resource not found',
                };
                
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }
        });
    }
} 