<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\NewsletterRegistration;
use App\Repository\NewsletterRegistrationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

#[\Attribute]
class UniqueNewsletterEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly NewsletterRegistrationRepository $newsletterRegistrationRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueNewsletterEmail) {
            throw new UnexpectedTypeException($constraint, UniqueNewsletterEmail::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ($this->newsletterRegistrationRepository->findOneByEmail($value) instanceof NewsletterRegistration) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
        }
    }
}
