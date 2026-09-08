<?php

namespace Drupal\Tests\field_validation\Kernel\Plugin\FieldValidationRule;

use Drupal\Core\Form\FormState;
use Drupal\Tests\field_validation_legacy\Kernel\Plugin\FieldValidationRule\FieldValidationRuleBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests RegexConstraintFieldValidationRule.
 *
 * @package Drupal\Tests\field_validation\Kernel
 */
#[Group('field_validation')]
class RegexConstraintFieldValidationRuleTest extends FieldValidationRuleBase {

  /**
   * Field name.
   */
  const FIELD_NAME = 'field_regex_constraint_text';

  /**
   * Rule id.
   */
  const RULE_ID = 'regex_constraint_rule';

  /**
   * Rule title.
   */
  const RULE_TITLE = 'validation rule regex constraint';

  /**
   * Entity interface.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Stores mock ruleset.
   *
   * @var \Drupal\field_validation\Entity\FieldValidationRuleSet
   */
  protected $ruleSet;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->setupTestArticle(self::FIELD_NAME);

    $this->ruleSet = $this->ruleSetStorage->create([
      'name' => 'regex_constraint_test',
      'entity_type' => 'node',
      'bundle' => 'article',
    ]);
    $this->ruleSet->addFieldValidationRule([
      'id' => self::RULE_ID,
      'title' => self::RULE_TITLE,
      'weight' => 1,
      'field_name' => self::FIELD_NAME,
      'column' => 'value',
      'error_message' => 'Pattern does not match!',
      'data' => [
        'pattern' => '/[A-Za-z]/',
      ],
    ]);
    $this->ruleSet->save();

    $this->entity = $this->nodeStorage->create([
      'type' => 'article',
      'title' => 'test',
      self::FIELD_NAME => '',
    ]);
    $this->entity->get(self::FIELD_NAME)
      ->getFieldDefinition()
      ->addConstraint(
        'FieldValidationConstraint',
        ['ruleset_name' => $this->ruleSet->getName()]
      );
  }

  /**
   * A well-formed pattern still validates correctly.
   */
  public function testValidPattern() {
    $this->updateSettings(
      ['pattern' => '/[A-Za-z]/'],
      self::RULE_ID,
      self::RULE_TITLE,
      $this->ruleSet,
      self::FIELD_NAME
    );
    $this->assertConstraintPass($this->entity, self::FIELD_NAME, 'abcdefg');
    $this->assertConstraintFail(
      $this->entity,
      self::FIELD_NAME,
      '1234',
      $this->ruleSet
    );
  }

  /**
   * A pattern missing its delimiters must not leak a raw PHP warning.
   *
   * See https://www.drupal.org/project/field_validation/issues/3390907.
   */
  public function testInvalidPatternDoesNotLeakWarning() {
    $this->updateSettings(
      // Missing delimiters - invalid in PHP 8's PCRE engine.
      ['pattern' => '(.*),(.*)'],
      self::RULE_ID,
      self::RULE_TITLE,
      $this->ruleSet,
      self::FIELD_NAME
    );

    // Fail the test loudly if the malformed pattern triggers a raw PHP
    // warning instead of being handled by the module.
    set_error_handler(static function (int $errno, string $errstr): bool {
      throw new \RuntimeException("Unexpected PHP warning leaked: $errstr");
    }, E_WARNING);

    try {
      $this->entity->get(self::FIELD_NAME)->value = 'anything';
      $violations = $this->entity->validate();
    }
    finally {
      restore_error_handler();
    }

    $this->assertCount(1, $violations);
  }

  /**
   * Tests that a malformed pattern is rejected with a clean form error.
   *
   * Instead of a raw PHP warning leaking through.
   */
  public function testValidateConfigurationFormRejectsMalformedPattern() {
    $plugin_manager = \Drupal::service('plugin.manager.field_validation.field_validation_rule');
    $rule = $plugin_manager->createInstance(self::RULE_ID, []);

    $form = [];
    $form_state = (new FormState())->setValues(['pattern' => '(.*),(.*)']);

    set_error_handler(static function (int $errno, string $errstr): bool {
      throw new \RuntimeException("Unexpected PHP warning leaked: $errstr");
    }, E_WARNING);

    try {
      $rule->validateConfigurationForm($form, $form_state);
    }
    finally {
      restore_error_handler();
    }

    $this->assertNotEmpty($form_state->getErrors());
  }

  /**
   * A well-formed pattern raises no form error.
   */
  public function testValidateConfigurationFormAcceptsValidPattern() {
    $plugin_manager = \Drupal::service('plugin.manager.field_validation.field_validation_rule');
    $rule = $plugin_manager->createInstance(self::RULE_ID, []);

    $form = [];
    $form_state = (new FormState())->setValues(['pattern' => '/[A-Za-z]/']);
    $rule->validateConfigurationForm($form, $form_state);

    $this->assertEmpty($form_state->getErrors());
  }

}
