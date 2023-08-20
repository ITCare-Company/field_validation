<?php

namespace Drupal\field_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\CountryValidator;

/**
 * Country constraint.
 *
 * @Constraint(
 *   id = "Country",
 *   label = @Translation("Country", context = "Validation"),
 * )
 */
class CountryConstraint extends Country {

  /**
   * {@inheritdoc}
   */
  public function validatedBy(): string {
    return CountryValidator::class;
  }

}
