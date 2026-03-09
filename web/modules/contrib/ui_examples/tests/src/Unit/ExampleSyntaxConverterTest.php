<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_examples\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\ui_examples\ExampleSyntaxConverter;

/**
 * @coversDefaultClass \Drupal\ui_examples\ExampleSyntaxConverter
 *
 * @group ui_examples
 */
final class ExampleSyntaxConverterTest extends UnitTestCase {

  /**
   * @covers ::convertArray
   *
   * @dataProvider provideConversionData
   */
  public function testConvertArray(array $value, array $expected): void {
    $converter = new ExampleSyntaxConverter();
    $converted = $converter->convertArray($value);
    $this->assertEquals($expected, $converted);
  }

  /**
   * Provide data for testConvertArray.
   */
  public static function provideConversionData(): \Generator {
    $data = [
      'Not convertible' => self::notConvertible(),
      'Status messages' => self::statusMessages(),
      'Table' => self::table(),
      'Theme image' => self::themeImage(),
      'Theme layout' => self::themeLayout(),
      'Props special words' => self::componentSpecialProps(),
    ];
    foreach ($data as $label => $test) {
      yield $label => [
        $test['value'],
        $test['expected'],
      ];
    }
  }

  /**
   * Only values which must not be converted.
   *
   * Some of them are not "slot" values.
   */
  protected static function notConvertible(): array {
    $value = [
      'empty' => [],
      'list' => [
        'one',
        'two',
        'free',
      ],
      'no_render_properties' => [
        'foo' => 'bar',
        'bar' => 'foo',
      ],
      'two_render_properties' => [
        'type' => 'component',
        'markup' => 'Lorem ipsum',
      ],
      'twice_same_property' => [
        'type' => 'component',
        '#type' => 'component',
      ],
      'already_converted' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => 'Lorem ipsum',
      ],
    ];
    return [
      'value' => $value,
      'expected' => $value,
    ];
  }

  /**
   * Status messages.
   */
  protected static function statusMessages(): array {
    $value = [
      [
        'theme' => 'status_messages',
        'message_list' => [
          'error' => [
            'Error message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                'type' => 'html_tag',
                'tag' => 'a',
                'value' => 'Examples page',
                'attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'warning' => [
            'Warning message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                'type' => 'html_tag',
                'tag' => 'a',
                'value' => 'Examples page',
                'attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'status' => [
            'Status message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                'type' => 'html_tag',
                'tag' => 'a',
                'value' => 'Examples page',
                'attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
        ],
      ],
      [
        'theme' => 'status_messages',
        '#message_list' => [
          'error' => [
            'Error message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                'tag' => 'a',
                'value' => 'Examples page',
                'attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'warning' => [
            'Warning message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                'type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                'attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'status' => [
            'Status message:',
            [
              [
                'markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                'tag' => 'a',
                'value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
        ],
      ],
    ];
    $expected = [
      [
        '#theme' => 'status_messages',
        '#message_list' => [
          'error' => [
            'Error message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'warning' => [
            'Warning message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'status' => [
            'Status message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
        ],
      ],
      [
        '#theme' => 'status_messages',
        '#message_list' => [
          'error' => [
            'Error message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'warning' => [
            'Warning message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
          'status' => [
            'Status message:',
            [
              [
                '#markup' => 'With link: ',
              ],
              [
                '#type' => 'html_tag',
                '#tag' => 'a',
                '#value' => 'Examples page',
                '#attributes' => [
                  'href' => '/examples',
                ],
              ],
            ],
          ],
        ],
      ],
    ];
    return [
      'value' => $value,
      'expected' => $expected,
    ];
  }

  /**
   * Table.
   */
  protected static function table(): array {
    $value = [
      [
        'theme' => 'table',
        'header' => [
          'foo' => [
            'data' => [
              'markup' => 'foo',
            ],
          ],
          'bar' => [
            'data' => [
              'markup' => 'bar',
            ],
          ],
        ],
        'rows' => [
          [
            'data' => [
              'markup' => 'baz',
            ],
          ],
        ],
        'footer' => [
          [
            'data' => [
              'markup' => 'baz',
            ],
          ],
        ],
        'empty' => [
          'markup' => 'baz',
        ],
        'caption' => [
          'markup' => 'baz',
        ],
      ],
      [
        'theme' => 'table',
        '#header' => [
          'foo' => [
            'data' => [
              'markup' => 'foo',
            ],
          ],
          'bar' => [
            'data' => [
              '#markup' => 'bar',
            ],
          ],
        ],
        'rows' => [
          [
            'data' => [
              'markup' => 'baz',
            ],
          ],
        ],
        '#footer' => [
          [
            'data' => [
              '#markup' => 'baz',
            ],
          ],
        ],
        'empty' => [
          '#markup' => 'baz',
        ],
        '#caption' => [
          'markup' => 'baz',
        ],
      ],
    ];
    $expected = [
      [
        '#theme' => 'table',
        '#header' => [
          'foo' => [
            'data' => [
              '#markup' => 'foo',
            ],
          ],
          'bar' => [
            'data' => [
              '#markup' => 'bar',
            ],
          ],
        ],
        '#rows' => [
          [
            'data' => [
              '#markup' => 'baz',
            ],
          ],
        ],
        '#footer' => [
          [
            'data' => [
              '#markup' => 'baz',
            ],
          ],
        ],
        '#empty' => [
          '#markup' => 'baz',
        ],
        '#caption' => [
          '#markup' => 'baz',
        ],
      ],
      [
        '#theme' => 'table',
        '#header' => [
          'foo' => [
            'data' => [
              '#markup' => 'foo',
            ],
          ],
          'bar' => [
            'data' => [
              '#markup' => 'bar',
            ],
          ],
        ],
        '#rows' => [
          [
            'data' => [
              '#markup' => 'baz',
            ],
          ],
        ],
        '#footer' => [
          [
            'data' => [
              '#markup' => 'baz',
            ],
          ],
        ],
        '#empty' => [
          '#markup' => 'baz',
        ],
        '#caption' => [
          '#markup' => 'baz',
        ],
      ],
    ];
    return [
      'value' => $value,
      'expected' => $expected,
    ];
  }

  /**
   * Theme image.
   */
  protected static function themeImage(): array {
    $value = [
      [
        'theme' => 'image',
        'uri' => 'data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iYmQtcGxhY2Vob2xkZXItaW1nIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGFyaWEtaGlkZGVuPSJ0cnVlIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBzbGljZSIgZm9jdXNhYmxlPSJmYWxzZSI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iIzc3NyI+PC9yZWN0Pjwvc3ZnPgo=',
        'attributes' => [
          'width' => '100%',
          'height' => '100%',
        ],
      ],
      [
        'theme' => 'image',
        '#uri' => 'data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iYmQtcGxhY2Vob2xkZXItaW1nIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGFyaWEtaGlkZGVuPSJ0cnVlIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBzbGljZSIgZm9jdXNhYmxlPSJmYWxzZSI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iIzc3NyI+PC9yZWN0Pjwvc3ZnPgo=',
        'attributes' => [
          'width' => '100%',
          'height' => '100%',
        ],
      ],
    ];
    $expected = [
      [
        '#theme' => 'image',
        '#uri' => 'data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iYmQtcGxhY2Vob2xkZXItaW1nIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGFyaWEtaGlkZGVuPSJ0cnVlIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBzbGljZSIgZm9jdXNhYmxlPSJmYWxzZSI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iIzc3NyI+PC9yZWN0Pjwvc3ZnPgo=',
        '#attributes' => [
          'width' => '100%',
          'height' => '100%',
        ],
      ],
      [
        '#theme' => 'image',
        '#uri' => 'data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iYmQtcGxhY2Vob2xkZXItaW1nIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGFyaWEtaGlkZGVuPSJ0cnVlIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBzbGljZSIgZm9jdXNhYmxlPSJmYWxzZSI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iIzc3NyI+PC9yZWN0Pjwvc3ZnPgo=',
        '#attributes' => [
          'width' => '100%',
          'height' => '100%',
        ],
      ],
    ];
    return [
      'value' => $value,
      'expected' => $expected,
    ];
  }

  /**
   * Theme layout.
   */
  protected static function themeLayout(): array {
    $value = [
      [
        'theme' => 'layout',
        'my_region' => [
          'markup' => 'my markup',
        ],
        'attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        'theme' => 'layout__my_theme',
        'my_region' => [
          'markup' => 'my markup',
        ],
        'attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        '#theme' => 'layout',
        'my_region' => [
          'markup' => 'my markup',
        ],
        'attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        'theme' => 'layout__my_theme',
        'my_region' => [
          'markup' => 'my markup',
        ],
        '#attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
    ];
    $expected = [
      [
        '#theme' => 'layout',
        'my_region' => [
          '#markup' => 'my markup',
        ],
        '#attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        '#theme' => 'layout__my_theme',
        'my_region' => [
          '#markup' => 'my markup',
        ],
        '#attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        '#theme' => 'layout',
        'my_region' => [
          '#markup' => 'my markup',
        ],
        '#attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
      [
        '#theme' => 'layout__my_theme',
        'my_region' => [
          '#markup' => 'my markup',
        ],
        '#attributes' => [
          'class' => [
            'my-class',
          ],
        ],
      ],
    ];
    return [
      'value' => $value,
      'expected' => $expected,
    ];
  }

  /**
   * Fake component with props with reserved words.
   */
  protected static function componentSpecialProps(): array {
    $slots = [
      'component' => [
        'type' => 'component',
        'component' => 'foo:bar',
        'props' => [
          'attributes' => [
            'class' => ['bar'],
            'type' => 'bar',
          ],
          'tag' => 'bar',
          'type' => 'bar',
          'value' => 'bar',
          'attached' => 'bar',
        ],
      ],
      'component_mixed' => [
        '#type' => 'component',
        'component' => 'foo:bar',
        'props' => [
          'attributes' => [
            'class' => ['bar'],
            'type' => 'bar',
          ],
          'tag' => 'bar',
          'type' => 'bar',
          'value' => 'bar',
          'attached' => 'bar',
        ],
      ],
    ];
    $expected = [
      'component' => [
        '#type' => 'component',
        '#component' => 'foo:bar',
        '#props' => [
          'attributes' => [
            'class' => [
              0 => 'bar',
            ],
            'type' => 'bar',
          ],
          'tag' => 'bar',
          'type' => 'bar',
          'value' => 'bar',
          'attached' => 'bar',
        ],
      ],
      'component_mixed' => [
        '#type' => 'component',
        '#component' => 'foo:bar',
        '#props' => [
          'attributes' => [
            'class' => [
              0 => 'bar',
            ],
            'type' => 'bar',
          ],
          'tag' => 'bar',
          'type' => 'bar',
          'value' => 'bar',
          'attached' => 'bar',
        ],
      ],
    ];
    return [
      'value' => $slots,
      'expected' => $expected,
    ];
  }

}
