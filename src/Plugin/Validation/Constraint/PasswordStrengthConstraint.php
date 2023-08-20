<?php

namespace Drupal\field_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\PasswordStrengthValidator;

/**
 * PasswordStrength constraint.
 *
 * @Constraint(
 *   id = "PasswordStrength",
 *   label = @Translation("PasswordStrength", context = "Validation"),
 * )
 */
class PasswordStrengthConstraint extends PasswordStrength {

  /**
   * {@inheritdoc}
   */
  public function validatedBy(): string {
    return PasswordStrengthValidator::class;
  }

}
