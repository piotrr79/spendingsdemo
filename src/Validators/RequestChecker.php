<?php
declare(strict_types=1);

namespace App\Validators;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Request Checker - checks request for mandatory params
 * @package  Spendings
 * @author   Piotr Rybinski
 */
class RequestChecker
{
    /** @var ValidatorInterface $validator */
    private $validator;

    /** @param ValidatorInterface $validator */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Check request for required param
     * @param Request $request
     * @param String $param
     * @param String $dataType
     * @return String
     */
    public function checkParam(Request $request, String $param, String $dataType): ?String
    {
        $response = $request->request->get($param);

        if (!isset($response)) {
            throw new HttpException(400, ucfirst($param). ' parameter is mandatory');
        }

        if ($dataType == 'uuid') {
            $this->validateUuid($response);
        }

        if ($dataType == 'number') {
            $this->validateNumber($response);
        }

        return $response;
    }

    /**
     * Validate uuid
     * @param $input
     * @return void
     */
    public function validateUuid(string $input): void
    {
        $violations = $this->validator->validate($input, [new Assert\NotBlank(), new Assert\Uuid()]);
        
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                throw new HttpException(400, 'Uuid: '.$violation->getMessage());
            }
        }
    }

    /**
     * Validate number
     * @param $input
     * @return void
     */
    public function validateNumber(string $input): void
    {
        $violations = $this->validator->validate($input, [new Assert\NotBlank(), new Assert\Type('numeric'), new Assert\Positive()]);
        
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                throw new HttpException(400, 'Number: '.$violation->getMessage());
            }
        }
    }
}