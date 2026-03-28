<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function jkrc_default_data() {
    return [
        'categories' => [
            [
                'id'     => 'smartphone',
                'label'  => 'Smartphone',
                'icon'   => '📱',
                'brands' => [
                    [
                        'name'   => 'Apple',
                        'models' => [
                            [ 'name' => 'iPhone 15 Pro Max', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$189', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$259', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Screen — Original',  'price' => '$329', 'note' => 'Genuine Apple part',  'tier' => 'original' ],
                                [ 'name' => 'Battery',            'price' => '$89',  'note' => '',                    'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Back Glass',         'price' => '$149', 'note' => '',                    'tier' => '' ],
                                [ 'name' => 'Camera Repair',      'price' => '$119', 'note' => '',                    'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 15 Pro', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$179', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$249', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Screen — Original',  'price' => '$299', 'note' => 'Genuine Apple part',  'tier' => 'original' ],
                                [ 'name' => 'Battery',            'price' => '$89',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 15', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$159', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$209', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Screen — Original',  'price' => '$259', 'note' => 'Genuine Apple part',  'tier' => 'original' ],
                                [ 'name' => 'Battery',            'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 14 Pro Max', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$179', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$249', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Battery',            'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 14', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$149', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$199', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Battery',            'price' => '$69',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 13', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$139', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$179', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Battery',            'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 12', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$119', 'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Screen — OEM Spec',  'price' => '$159', 'note' => 'Manufacturer-grade',  'tier' => 'oem' ],
                                [ 'name' => 'Battery',            'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPhone 11', 'repairs' => [
                                [ 'name' => 'Screen — Standard',  'price' => '$99',  'note' => 'Quality aftermarket', 'tier' => 'standard' ],
                                [ 'name' => 'Battery',            'price' => '$49',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port Cleaning', 'price' => '$29', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other iPhone', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => 'Price varies by model', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Other Repair',       'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Samsung',
                        'models' => [
                            [ 'name' => 'Galaxy S24 Ultra', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$219', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$89',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Back Glass',         'price' => '$99',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Galaxy S24', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$179', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Galaxy S23', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$169', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$69',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Galaxy A54', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$109', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other Samsung', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => 'Price varies by model', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Google',
                        'models' => [
                            [ 'name' => 'Pixel 8 Pro', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$189', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Pixel 8', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$169', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$69',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Pixel 7', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$139', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other Pixel', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'id'     => 'laptop',
                'label'  => 'Laptop & Computer',
                'icon'   => '💻',
                'brands' => [
                    [
                        'name'   => 'Apple (MacBook)',
                        'models' => [
                            [ 'name' => 'MacBook Pro 16in (M3)', 'repairs' => [
                                [ 'name' => 'Screen Replacement',   'price' => '$499', 'note' => 'OEM-grade display', 'tier' => '' ],
                                [ 'name' => 'Battery Replacement',  'price' => '$189', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard Replacement', 'price' => '$229', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',     'price' => '$89',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'MacBook Air 13in (M2)', 'repairs' => [
                                [ 'name' => 'Screen Replacement',   'price' => '$389', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery Replacement',  'price' => '$129', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard Replacement', 'price' => '$169', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',     'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'MacBook Air (M1)', 'repairs' => [
                                [ 'name' => 'Screen Replacement',   'price' => '$349', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery Replacement',  'price' => '$119', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard Replacement', 'price' => '$159', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',     'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other MacBook', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Dell',
                        'models' => [
                            [ 'name' => 'XPS 15', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$299', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$99',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard',           'price' => '$89',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'RAM Upgrade',        'price' => '$69+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'SSD Upgrade',        'price' => '$89+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',   'price' => '$69',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Inspiron 15', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$149', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$69',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard',           'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'RAM Upgrade',        'price' => '$59+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'SSD Upgrade',        'price' => '$69+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',   'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other Dell', 'repairs' => [
                                [ 'name' => 'Screen / Repair', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',         'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Lenovo',
                        'models' => [
                            [ 'name' => 'ThinkPad X1 Carbon', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$229', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$89',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard',           'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',   'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'IdeaPad 3', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$129', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$55',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Keyboard',           'price' => '$49',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'RAM Upgrade',        'price' => '$49+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'SSD Upgrade',        'price' => '$59+', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Thermal Cleaning',   'price' => '$45',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other Lenovo', 'repairs' => [
                                [ 'name' => 'Screen / Repair', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',         'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'id'     => 'tablet',
                'label'  => 'Tablet & iPad',
                'icon'   => '📟',
                'brands' => [
                    [
                        'name'   => 'Apple (iPad)',
                        'models' => [
                            [ 'name' => 'iPad Pro 12.9in (M2)', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$349', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$129', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$79',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPad Air 5th gen', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$249', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$99',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$59',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'iPad 10th gen', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$179', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$79',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other iPad', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Samsung',
                        'models' => [
                            [ 'name' => 'Galaxy Tab S9', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$219', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$89',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$49',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Galaxy Tab A8', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$129', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => '$59',  'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$39',  'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Other Samsung Tablet', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Battery',            'price' => 'Contact us', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'id'     => 'console',
                'label'  => 'Gaming Console',
                'icon'   => '🎮',
                'brands' => [
                    [
                        'name'   => 'Sony (PlayStation)',
                        'models' => [
                            [ 'name' => 'PlayStation 5', 'repairs' => [
                                [ 'name' => 'HDMI Port Repair',          'price' => '$89', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Disc Drive Repair',         'price' => '$99', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Overheating / Fan Cleaning','price' => '$69', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'USB Port Repair',           'price' => '$59', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'PlayStation 4 Pro', 'repairs' => [
                                [ 'name' => 'HDMI Port Repair',          'price' => '$69', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Disc Drive Repair',         'price' => '$79', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Overheating / Fan Cleaning','price' => '$49', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'DualSense Controller', 'repairs' => [
                                [ 'name' => 'Joystick Drift Repair', 'price' => '$49', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'USB-C Port Repair',     'price' => '$39', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Button Repair',         'price' => '$35', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Microsoft (Xbox)',
                        'models' => [
                            [ 'name' => 'Xbox Series X', 'repairs' => [
                                [ 'name' => 'HDMI Port Repair',          'price' => '$89', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Disc Drive Repair',         'price' => '$99', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Overheating / Fan Cleaning','price' => '$69', 'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Xbox One', 'repairs' => [
                                [ 'name' => 'HDMI Port Repair',          'price' => '$59', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Disc Drive Repair',         'price' => '$69', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Overheating / Fan Cleaning','price' => '$45', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                    [
                        'name'   => 'Nintendo',
                        'models' => [
                            [ 'name' => 'Nintendo Switch OLED', 'repairs' => [
                                [ 'name' => 'Screen Replacement',        'price' => '$129',       'note' => '', 'tier' => '' ],
                                [ 'name' => 'Joystick Drift (Joy-Con)',  'price' => '$49 per side','note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',             'price' => '$59',         'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Nintendo Switch', 'repairs' => [
                                [ 'name' => 'Screen Replacement',        'price' => '$99',         'note' => '', 'tier' => '' ],
                                [ 'name' => 'Joystick Drift (Joy-Con)',  'price' => '$45 per side','note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',             'price' => '$49',         'note' => '', 'tier' => '' ],
                            ]],
                            [ 'name' => 'Nintendo Switch Lite', 'repairs' => [
                                [ 'name' => 'Screen Replacement', 'price' => '$89', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Joystick Drift',     'price' => '$49', 'note' => '', 'tier' => '' ],
                                [ 'name' => 'Charging Port',      'price' => '$45', 'note' => '', 'tier' => '' ],
                            ]],
                        ],
                    ],
                ],
            ],
        ],
        'settings' => [
            'contact_url'  => '/contact',
            'contact_text' => 'Contact us →',
            'footer_note'  => 'Prices shown are starting rates and may vary depending on device condition. Bring it in for a free diagnosis and exact quote. If we can\'t find the issue, you don\'t pay anything.',
        ],
    ];
}
