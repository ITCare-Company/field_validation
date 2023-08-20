<?php

namespace Drupal\field_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\Isin;
use Symfony\Component\Validator\Constraints\IsinValidator;

/**
 * Isin constraint.
 *
 * @Constraint(
 *   id = "Isin",
 *   label = @Translation("Isin", context = "Validation"),
 * )
 */
class IsinConstraint extends Isin {

  /**
   * {@inheritdoc}
   */
  public function validatedBy(): string {
    return IsinValidator::class;
  }

}
