<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValueWhole;

final class ReplaceValueWholeTest extends TestCase
{
    public function testBase(): void
    {
        $a = (new ArrayCollection())
            ->withModifier(new ReplaceValueWhole('options'))
            ->withData([
                'name' => 'Yii',
                'version' => '1.0',
                'options' => [
                    'namespace' => false,
                    'unittest' => false,
                ],
            ]);
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $this->assertEquals($expected, ArrayHelper::merge($a, $b));
    }

    public function testForKey(): void
    {
        $modifier = new ReplaceValueWhole('x');
        $modifier = $modifier->forKey('y');

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['x' => [5], 'y' => [6]],
        ];

        $this->assertSame(
            ['x' => [1, 2, 5], 'y' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithoutKeysInAllArrays(): void
    {
        $modifier = new ReplaceValueWhole('z');

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4], 'z' => [5, 6]]))->withModifier($modifier),
            ['x' => [5], 'y' => [6]],
        ];

        $this->assertSame(
            ['x' => [1, 2, 5], 'y' => [3, 4, 6], 'z' => [5, 6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithKeysInArrayBeforeCurrent(): void
    {
        $modifier = new ReplaceValueWhole('y');

        $arrays = [
            ['y' => [5]],
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['z' => [6]],
        ];


        $this->assertSame(
            ['y' => [3, 4], 'x' => [1, 2], 'z' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithKeysInArrayBeforeAndAfterCurrent(): void
    {
        $modifier = new ReplaceValueWhole('y');

        $arrays = [
            ['y' => [5]],
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['z' => [6], 'y' => [7]],
        ];

        $this->assertSame(
            ['y' => [7], 'x' => [1, 2], 'z' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }
}
