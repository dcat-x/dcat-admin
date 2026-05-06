<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Condition;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class ConditionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockForm(): Form
    {
        $form = Mockery::mock(Form::class);

        return $form;
    }

    public function test_constructor_creates_instance(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);
        $this->assertInstanceOf(Condition::class, $condition);
    }

    public function test_is_with_true_value(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);
        $this->assertTrue($condition->is());
    }

    public function test_is_with_false_value(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(false, $form);
        $this->assertFalse($condition->is());
    }

    public function test_is_with_truthy_value(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(1, $form);
        $this->assertTrue($condition->is());
    }

    public function test_is_with_closure_condition(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(function ($f) {
            return true;
        }, $form);
        $this->assertTrue($condition->is());
    }

    public function test_is_with_closure_returning_false(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(function ($f) {
            return false;
        }, $form);
        $this->assertFalse($condition->is());
    }

    public function test_get_result_returns_evaluation(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);
        $condition->is();
        $this->assertTrue($condition->getResult());
    }

    public function test_get_result_returns_null_before_evaluation(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);
        $this->assertNull($condition->getResult());
    }

    public function test_then_returns_this(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);
        $result = $condition->then(function () {});
        $this->assertSame($condition, $result);
    }

    public function test_process_executes_callbacks_when_true(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);

        $executed = false;
        $condition->then(function ($f) use (&$executed) {
            $executed = true;
        });

        $condition->process();
        $this->assertTrue($executed);
    }

    public function test_process_does_not_execute_when_false(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(false, $form);

        $executed = false;
        $condition->then(function ($f) use (&$executed) {
            $executed = true;
        });

        $condition->process();
        $this->assertFalse($executed);
    }

    public function test_process_only_executes_once(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);

        $count = 0;
        $condition->then(function ($f) use (&$count) {
            $count++;
        });

        $condition->process();
        $condition->process();
        $this->assertSame(1, $count);
    }

    public function test_now_calls_process(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);

        $executed = false;
        $condition->then(function ($f) use (&$executed) {
            $executed = true;
        });

        $condition->now();
        $this->assertTrue($executed);
    }

    public function test_process_with_additional_closure(): void
    {
        $form = $this->createMockForm();
        $condition = new Condition(true, $form);

        $executed = false;
        $condition->process(function ($f) use (&$executed) {
            $executed = true;
        });
        $this->assertTrue($executed);
    }
}
