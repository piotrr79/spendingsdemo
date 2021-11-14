<?php
declare(strict_types=1);

namespace App\Validators;

/**
 * Response Setter - set response to null if no output provided
 * @package  Spendings
 * @author   Piotr Rybinski
 */
class ResponseSetter
{
    /**
     * Set response for method
     * @param string $status_code
     * @param string $content
     * @return array
     */
    public function setResponse($status_code, $content)
    {
        return ['code' => $status_code, 'message' => $content];
    }
}
