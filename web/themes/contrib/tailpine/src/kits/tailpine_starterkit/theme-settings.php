<?php

/**
 * @file
 * theme-settings.php
 *
 * Provides theme settings for tailpine_starterkit-based themes.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 */
function tailpine_starterkit_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL)
{
  $form['#attached']['library'][] = 'tailpine_starterkit/color-picker';


  // Color configuration with color schemes and their respective colors.
  $color_config = [
    'colors' => [
      'tailpine_starterkit_base_primary_color' => 'Primary base color',
      'tailpine_starterkit_base_secondary_color' => 'Secondary base color',
      'tailpine_starterkit_base_tertiary_color' => 'Tertiary base color',
      'tailpine_starterkit_body_color' => 'Body color',
      'tailpine_starterkit_body_bg_color' => 'Body background color',
      'tailpine_starterkit_h1_color' => 'H1 color',
      'tailpine_starterkit_h2_color' => 'H2 color',
      'tailpine_starterkit_h3_color' => 'H3 color',
      'tailpine_starterkit_h4_color' => 'H4 color',
      'tailpine_starterkit_success_color' => 'Success color',
      'tailpine_starterkit_warning_color' => 'Warning color',
      'tailpine_starterkit_error_color' => 'Error color',
    ],
    'schemes' => [
      'default' => [
        'label' => 'Default',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#10B981',
          'tailpine_starterkit_base_secondary_color' => '#064E3B',
          'tailpine_starterkit_base_tertiary_color' => '#06B6D4',
          'tailpine_starterkit_body_color' => '#1F2937',
          'tailpine_starterkit_body_bg_color' => '#FFFFFF',
          'tailpine_starterkit_h1_color' => '#1F2937',
          'tailpine_starterkit_h2_color' => '#1F2937',
          'tailpine_starterkit_h3_color' => '#1F2937',
          'tailpine_starterkit_h4_color' => '#1F2937',
          'tailpine_starterkit_success_color' => '#14A44D',
          'tailpine_starterkit_warning_color' => '#E4A11B',
          'tailpine_starterkit_error_color' => '#DC4C64',
        ],
      ],
      'Blue & Gray Theme' => [
        'label' => 'Blue & Gray Theme',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#3B71CA',
          'tailpine_starterkit_base_secondary_color' => '#9FA6B2',
          'tailpine_starterkit_base_tertiary_color' => '#F9FAFB',
          'tailpine_starterkit_body_color' => '#1F2937',
          'tailpine_starterkit_body_bg_color' => '#FFFFFF',
          'tailpine_starterkit_h1_color' => '#1F2937',
          'tailpine_starterkit_h2_color' => '#1F2937',
          'tailpine_starterkit_h3_color' => '#1F2937',
          'tailpine_starterkit_h4_color' => '#1F2937',
          'tailpine_starterkit_success_color' => '#14A44D',
          'tailpine_starterkit_warning_color' => '#E4A11B',
          'tailpine_starterkit_error_color' => '#DC4C64',
        ],
      ],
      'blue_lagoon' => [
        'label' => 'Blue Lagoon',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#1b9ae4',
          'tailpine_starterkit_base_secondary_color' => '#1b9ae0',
          'tailpine_starterkit_base_tertiary_color' => '#1b9a00',
          'tailpine_starterkit_body_color' => '#111827',
          'tailpine_starterkit_body_bg_color' => '#F3F4F6',
          'tailpine_starterkit_h1_color' => '#F3F4F6',
          'tailpine_starterkit_h2_color' => '#F3F4F6',
          'tailpine_starterkit_h3_color' => '#F3F4F6',
          'tailpine_starterkit_h4_color' => '#F3F4F6',
          'tailpine_starterkit_success_color' => '#F3F4F6',
          'tailpine_starterkit_warning_color' => '#F3F4F6',
          'tailpine_starterkit_error_color' => '#F3F4F6',
        ],
      ],
      'ember_flame' => [
        'label' => 'Ember Flame',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#a30f0f',
          'tailpine_starterkit_base_secondary_color' => '#d32f2f',
          'tailpine_starterkit_base_tertiary_color' => '#ff6f61',
          'tailpine_starterkit_body_color' => '#7C2D12',
          'tailpine_starterkit_body_bg_color' => '#FFF5E1',
          'tailpine_starterkit_h1_color' => '#FFF5E1',
          'tailpine_starterkit_h2_color' => '#FFF5E1',
          'tailpine_starterkit_h3_color' => '#FFF5E1',
          'tailpine_starterkit_h4_color' => '#FFF5E1',
          'tailpine_starterkit_success_color' => '#FFF5E1',
          'tailpine_starterkit_warning_color' => '#FFF5E1',
          'tailpine_starterkit_error_color' => '#FFF5E1',
        ],
      ],
      'frozen_glacier' => [
        'label' => 'Frozen Glacier',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#57919e',
          'tailpine_starterkit_base_secondary_color' => '#5FA8D3',
          'tailpine_starterkit_base_tertiary_color' => '#AEEEEE',
          'tailpine_starterkit_body_color' => '#064E3B',
          'tailpine_starterkit_body_bg_color' => '#ECFDF5',
          'tailpine_starterkit_h1_color' => '#ECFDF5',
          'tailpine_starterkit_h2_color' => '#ECFDF5',
          'tailpine_starterkit_h3_color' => '#ECFDF5',
          'tailpine_starterkit_h4_color' => '#ECFDF5',
          'tailpine_starterkit_success_color' => '#ECFDF5',
          'tailpine_starterkit_warning_color' => '#ECFDF5',
          'tailpine_starterkit_error_color' => '#ECFDF5',
        ],
      ],
      'royal_plum' => [
        'label' => 'Royal Plum',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#7a4587',
          'tailpine_starterkit_base_secondary_color' => '#9C27B0',
          'tailpine_starterkit_base_tertiary_color' => '#D1C4E9',
          'tailpine_starterkit_body_color' => '#4C1D95',
          'tailpine_starterkit_body_bg_color' => '#FAE8FF',
          'tailpine_starterkit_h1_color' => '#FAE8FF',
          'tailpine_starterkit_h2_color' => '#FAE8FF',
          'tailpine_starterkit_h3_color' => '#FAE8FF',
          'tailpine_starterkit_h4_color' => '#FAE8FF',
          'tailpine_starterkit_success_color' => '#FAE8FF',
          'tailpine_starterkit_warning_color' => '#FAE8FF',
          'tailpine_starterkit_error_color' => '#FAE8FF',
        ],
      ],
      'deep_slate' => [
        'label' => 'Deep Slate',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#47625b',
          'tailpine_starterkit_base_secondary_color' => '#5E716A',
          'tailpine_starterkit_base_tertiary_color' => '#78909C',
          'tailpine_starterkit_body_color' => '#1F2937',
          'tailpine_starterkit_body_bg_color' => '#E5E7EB',
          'tailpine_starterkit_h1_color' => '#E5E7EB',
          'tailpine_starterkit_h2_color' => '#E5E7EB',
          'tailpine_starterkit_h3_color' => '#E5E7EB',
          'tailpine_starterkit_h4_color' => '#E5E7EB',
          'tailpine_starterkit_success_color' => '#E5E7EB',
          'tailpine_starterkit_warning_color' => '#E5E7EB',
          'tailpine_starterkit_error_color' => '#E5E7EB',
        ],
      ],
      'golden_sunset' => [
        'label' => 'Golden Sunset',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#FF5733',
          'tailpine_starterkit_base_secondary_color' => '#FFC300',
          'tailpine_starterkit_base_tertiary_color' => '#C70039',
          'tailpine_starterkit_body_color' => '#581845',
          'tailpine_starterkit_body_bg_color' => '#FFEDD5',
          'tailpine_starterkit_h1_color' => '#FFEDD5',
          'tailpine_starterkit_h2_color' => '#FFEDD5',
          'tailpine_starterkit_h3_color' => '#FFEDD5',
          'tailpine_starterkit_h4_color' => '#FFEDD5',
          'tailpine_starterkit_success_color' => '#FFEDD5',
          'tailpine_starterkit_warning_color' => '#FFEDD5',
          'tailpine_starterkit_error_color' => '#FFEDD5',
        ],
      ],
      'cyber_neon' => [
        'label' => 'Cyber Neon',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#00F0FF',
          'tailpine_starterkit_base_secondary_color' => '#F900BF',
          'tailpine_starterkit_base_tertiary_color' => '#6F00FF',
          'tailpine_starterkit_body_color' => '#D1D5DB',
          'tailpine_starterkit_body_bg_color' => '#000000',
          'tailpine_starterkit_h1_color' => '#000000',
          'tailpine_starterkit_h2_color' => '#000000',
          'tailpine_starterkit_h3_color' => '#000000',
          'tailpine_starterkit_h4_color' => '#000000',
          'tailpine_starterkit_success_color' => '#000000',
          'tailpine_starterkit_warning_color' => '#000000',
          'tailpine_starterkit_error_color' => '#000000',
        ],
      ],
      'evergreen_forest' => [
        'label' => 'Evergreen Forest',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#1B5E20',
          'tailpine_starterkit_base_secondary_color' => '#4CAF50',
          'tailpine_starterkit_base_tertiary_color' => '#81C784',
          'tailpine_starterkit_body_color' => '#2E7D32',
          'tailpine_starterkit_body_bg_color' => '#E8F5E9',
          'tailpine_starterkit_h1_color' => '#E8F5E9',
          'tailpine_starterkit_h2_color' => '#E8F5E9',
          'tailpine_starterkit_h3_color' => '#E8F5E9',
          'tailpine_starterkit_h4_color' => '#E8F5E9',
          'tailpine_starterkit_success_color' => '#E8F5E9',
          'tailpine_starterkit_warning_color' => '#E8F5E9',
          'tailpine_starterkit_error_color' => '#E8F5E9',
        ],
      ],
      'imperial_purple' => [
        'label' => 'Imperial Purple',
        'colors' => [
          'tailpine_starterkit_base_primary_color' => '#4A148C',
          'tailpine_starterkit_base_secondary_color' => '#7B1FA2',
          'tailpine_starterkit_base_tertiary_color' => '#CE93D8',
          'tailpine_starterkit_body_color' => '#311B92',
          'tailpine_starterkit_body_bg_color' => '#F3E5F5',
          'tailpine_starterkit_h1_color' => '#F3E5F5',
          'tailpine_starterkit_h2_color' => '#F3E5F5',
          'tailpine_starterkit_h3_color' => '#F3E5F5',
          'tailpine_starterkit_h4_color' => '#F3E5F5',
          'tailpine_starterkit_success_color' => '#F3E5F5',
          'tailpine_starterkit_warning_color' => '#F3E5F5',
          'tailpine_starterkit_error_color' => '#F3E5F5',
        ],
      ],
    ],
  ];

  $form['#attached']['drupalSettings']['tailpine_starterkit']['colorSchemes'] = [
    'schemeColors' => $color_config['schemes'],
  ];

  // General "alters" use a form id. Settings should not be set here. The only
  // thing useful about this is if you need to alter the form for the running
  // theme and *not* the theme setting.
  // @see http://drupal.org/node/943212
  if (isset($form_id)) {
    return;
  }

  // Change collapsible fieldsets (now details) to default #open => FALSE.
  $form['theme_settings']['#open'] = FALSE;
  $form['logo']['#open'] = FALSE;
  $form['favicon']['#open'] = FALSE;

  // Vertical tabs.
  $form['tailpine_starterkit'] = [
    '#type' => 'vertical_tabs',
    '#prefix' => '<h2><small>' . t('tailpine_starterkit settings') . '</small></h2>',
    '#weight' => -10,
  ];

  // Colors.
  $form['colors'] = [
    '#type' => 'details',
    '#title' => t('Colors'),
    '#group' => 'tailpine_starterkit',
  ];
  //Header styles
  $form['header_settings'] = [
    '#type' => 'details',
    '#title' => t('Header Styles'),
    '#group' => 'tailpine_starterkit',
  ];

  //Footer styles
  $form['footer_settings'] = [
    '#type' => 'details',
    '#title' => t('Footer Styles'),
    '#group' => 'tailpine_starterkit',
  ];

  $form['colors']['scheme'] = [
    '#type' => 'details',
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#title' => t('tailpine_starterkit Color Scheme Settings'),
  ];

  $form['colors']['scheme']['tailpine_starterkit_enable_color'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable color Scheme'),
    '#config_target' => 'tailpine_starterkit.settings:tailpine_starterkit_enable_color',
    '#ajax' => [
      'callback' => 'colorCallback',
      'wrapper' => 'color_container',
    ],
  ];
  $form['colors']['scheme']['tailpine_starterkit_scheme_description'] = [
    '#type' => 'html_tag',
    '#tag' => 'p',
    '#value' => t('These settings adjust the look and feel of the tailpine_starterkit based themes. Changing the colors below will change the basic color values the tailpine_starterkit based theme uses.'),
  ];
  $form['colors']['scheme']['color_container'] = [
    '#type' => 'container',
    '#attributes' => [
      'id' => 'color_container',
    ],
  ];

  if ($form_state->getValue('tailpine_starterkit_enable_color', theme_get_setting('tailpine_starterkit_enable_color'))) {

    /////////////////////////////////// Status messages /////////////////////////
    // Generate the options dynamically
    $options = [];
    foreach ($color_config['schemes'] as $key => $scheme) {
      $options[$key] = t($scheme['label'] . ($key === 'default' ? ' (Default)' : ''));
    }
    $form['colors']['scheme']['color_container']['tailpine_starterkit_color_scheme'] = [
      '#type' => 'select',
      '#title' => t('tailpine_starterkit Color Scheme'),
      '#empty_option' => t('Custom'),
      '#empty_value' => '',
      '#options' => $options, // Use dynamically generated options
      '#input' => FALSE,
      '#attributes' => [
        'data-drupal-selector' => 'edit-tailpine_starterkit-color-scheme-site',
        'data-category' => 'schemeColors',
      ],
      '#wrapper_attributes' => [
        'style' => 'display:none;',
      ],
    ];
    $index = 0;
    foreach ($color_config['colors'] as $key => $title) {
      $index++;
      $form['colors']['scheme']['color_container'][$key] = [
        '#type' => 'textfield',
        '#maxlength' => 7,
        '#size' => 10,
        '#title' => t($title),
        '#description' => $index === 1 ? t('Enter color in full hexadecimal format (#abc123).') . '<br/>' . t('Derivatives will be formed from this color.') : '',
        '#config_target' => "tailpine_starterkit.settings:$key",
        '#attributes' => [
          'pattern' => '^[#]?([0-9a-fA-F]{3}){1,2}$',
        ],
        '#wrapper_attributes' => [
          'data-drupal-selector' => 'tailpine_starterkit-color-picker',
        ],
      ];
    }
  }
  $form['header_settings']['header_settings_container']['header_class'] = [
    '#type' => 'select',
    '#title' => t('Choose a Header Style'),
    '#default_value' => theme_get_setting('header_class'),
    '#options' => [
      'style1' => t('Style 1: Branding left, Menu and Search right'),
      'style2' => t('Style 2: Branding left, Menu and Actions right'),
      'style3' => t('Style 3: Branding Left, Menu Center, Search right'),
      'style4' => t('Style 4: Branding left, Menu Center, Actions right'),
      'style5' => t('Style 5: Branding and Menu left, Search right'),
      'style6' => t('Style 6: Branding and Menu left, Actions right'),
    ],
  ];
  $form['footer_settings']['footer_settings_container']['footer_class'] = [
    '#type' => 'select',
    '#title' => t('Choose a footer Style'),
    '#default_value' => theme_get_setting('footer_class'),
    '#options' => [
      'style1' => t('Style 1: Style One'),
      'style2' => t('Style 2: Style Two'),
      'style3' => t('Style 3: Style Three'),
      'style4' => t('Style 4: Footer style of Daisy UI'),
    ],
  ];
  $form['header_settings']['header_settings_container']['header_fixed'] = [
    '#type' => 'checkbox',
    '#title' => t('Fix Header to Top'),
    '#default_value' => theme_get_setting('header_fixed'),
    '#description' => t('If checked, the header will stay fixed at the top of the screen.'),
  ];


  $form['daisyui_enable'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable daisyUI Styles'),
    '#description' => t('Check this box to enable the daisyUI CSS plugin. Run this command once to generate the daisyUI.css file. <strong> npm run build:daisy</strong>'),
    '#default_value' => theme_get_setting('daisyui_enable')
  ];
}
/**
 * Handles the color callback for the theme settings form.
 *
 * @param array $form
 *   The form array.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The form state interface.
 *
 * @return mixed
 *   The processed form.
 */
function tailpine_starterkit_colorCallback($form, FormStateInterface $form_state)
{
  return $form['colors']['scheme']['color_container'];
}

