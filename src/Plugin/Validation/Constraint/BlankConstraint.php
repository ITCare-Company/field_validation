<?php

namespace Drupal\field_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\Blank;

/**
 * Blank constraint.
 *
 * Overrides the symfony constraint to use Drupal-style replacement patterns.
 *
 * @Constraint(
 *   id = "Blank",
 *   label = @Translation("Blank", context = "Validation")
 * )
 */
class BlankConstraint extends Blank {

  public $message = 'This value should be blank.';

  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return '\Symfony\Component\Validator\Constraints\BlankValidator';
  }

}
