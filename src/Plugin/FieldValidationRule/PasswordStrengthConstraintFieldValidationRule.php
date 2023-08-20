<?php

namespace Drupal\field_validation\Plugin\FieldValidationRule;

use Drupal\Core\Form\FormStateInterface;
use Drupal\field_validation\ConstraintFieldValidationRuleBase;
use Drupal\field_validation\FieldValidationRuleSetInterface;

/**
 * Provides funcationality for PasswordStrengthConstraintFieldValidationRule.
 *
 * @FieldValidationRule(
 *   id = "password_strength_constraint_rule",
 *   label = @Translation("PasswordStrength constraint"),
 *   description = @Translation("PasswordStrength constraint.")
 * )
 */
class PasswordStrengthConstraintFieldValidationRule extends ConstraintFieldValidationRuleBase {

  /**
   * {@inheritdoc}
   */
  public function getConstraintName(): string{
    return "PasswordStrength";
  }

  /**
   * {@inheritdoc}
   */
  public function isPropertyConstraint(): bool{
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'minScore' => 2,
      'message' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    //copied from core.
    $message = 'The password strength is too low. Please use a stronger password.';

    $min_score_options = [
      1 => $this->t('Weak'),
      2 => $this->t('Medium'),
      3 => $this->t('Strong'),
      4 => $this->t('Very Strong'),	
    ];

    $form['minScore'] = [
      '#type' => 'select',
      '#title' => $this->t('Min score'),
      '#options' => $min_score_options,	  
      '#default_value' => $this->configuration['minScore'],
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message'),
      '#default_value' => $this->configuration['message'] ?? $message,
      '#maxlength' => 255,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['minScore'] = $form_state->getValue('minScore');
    $this->configuration['message'] = $form_state->getValue('message');
  }

}
