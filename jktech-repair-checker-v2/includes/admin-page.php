<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function jkrc_admin_page() {
    $data     = jkrc_get_data();
    $settings = $data['settings'] ?? [];
    $tab      = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'categories';
    ?>
    <div class="wrap jkrc-wrap">
        <h1>🔧 Repair Price Checker V2</h1>
        <p class="jkrc-subtitle">Manage device categories, brands, models and repair prices. V2 includes inline booking flow.</p>

        <div class="jkrc-tabs">
            <button class="jkrc-tab <?php echo $tab === 'categories' ? 'active' : ''; ?>" data-tab="categories">Categories &amp; Prices</button>
            <button class="jkrc-tab <?php echo $tab === 'bookings' ? 'active' : ''; ?>" data-tab="bookings">Bookings <span class="jkrc-live-indicator" title="Live updates every 15s"></span></button>
            <button class="jkrc-tab <?php echo $tab === 'settings' ? 'active' : ''; ?>" data-tab="settings">Settings</button>
            <button class="jkrc-tab <?php echo $tab === 'shortcode' ? 'active' : ''; ?>" data-tab="shortcode">Shortcode</button>
            <button class="jkrc-tab <?php echo $tab === 'docs' ? 'active' : ''; ?>" data-tab="docs">📖 Docs</button>
        </div>

        <!-- CATEGORIES TAB -->
        <div class="jkrc-tab-content <?php echo $tab === 'categories' ? 'active' : ''; ?>" id="tab-categories">
            <div class="jkrc-toolbar">
                <button class="jkrc-btn jkrc-btn-primary" id="jkrc-add-category">+ Add Category</button>
                <button class="jkrc-btn jkrc-btn-success" id="jkrc-save-all">💾 Save All Changes</button>
                <span class="jkrc-save-status" id="jkrc-save-status"></span>
            </div>
            <div id="jkrc-categories-list">
                <?php foreach ( $data['categories'] as $cat_index => $category ) : ?>
                    <?php jkrc_render_category( $cat_index, $category ); ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- BOOKINGS TAB -->
        <div class="jkrc-tab-content <?php echo $tab === 'bookings' ? 'active' : ''; ?>" id="tab-bookings">
            <?php jkrc_render_bookings_table(); ?>
        </div>

        <!-- SETTINGS TAB -->
        <div class="jkrc-tab-content <?php echo $tab === 'settings' ? 'active' : ''; ?>" id="tab-settings">
            <div class="jkrc-card">
                <h2>Widget Settings</h2>
                <table class="form-table jkrc-settings-table">
                    <tr>
                        <th>Contact Page URL</th>
                        <td><input type="text" id="setting-contact_url" class="jkrc-setting regular-text" value="<?php echo esc_attr( $settings['contact_url'] ?? '/contact' ); ?>" placeholder="/contact"></td>
                        <td class="jkrc-td-note">Used for "Contact us" links when price is not set</td>
                    </tr>
                    <tr>
                        <th>Contact Link Text</th>
                        <td><input type="text" id="setting-contact_text" class="jkrc-setting regular-text" value="<?php echo esc_attr( $settings['contact_text'] ?? 'Contact us →' ); ?>"></td>
                        <td class="jkrc-td-note">Label shown for contact-us prices</td>
                    </tr>
                    <tr>
                        <th>Notification Email</th>
                        <td><input type="email" id="setting-notify_email" class="jkrc-setting regular-text" value="<?php echo esc_attr( $settings['notify_email'] ?? 'info@jktechsolutions.ca' ); ?>"></td>
                        <td class="jkrc-td-note">Where booking notifications are sent</td>
                    </tr>
                    <tr>
                        <th>Footer Note</th>
                        <td colspan="2"><textarea id="setting-footer_note" class="jkrc-setting large-text" rows="3"><?php echo esc_textarea( $settings['footer_note'] ?? '' ); ?></textarea></td>
                    </tr>
                </table>
                <button class="jkrc-btn jkrc-btn-success" id="jkrc-save-settings">💾 Save Settings</button>
                <span class="jkrc-save-status" id="jkrc-save-settings-status"></span>
            </div>

            <!-- TIER MANAGER -->
            <div class="jkrc-card" style="margin-top:20px;">
                <h2>Tier Labels</h2>
                <p style="color:#5a6475;font-size:13px;margin-bottom:18px;">Tiers appear as badges next to repair names in the price table. Add, rename, reorder, or remove tiers. Each tier has a <strong>slug</strong> (internal key, no spaces) and a <strong>label</strong> (shown to customers). The color is applied automatically based on position.</p>

                <div id="jkrc-tiers-list">
                    <?php
                    $tiers = $settings['tiers'] ?? [
                        [ 'slug' => 'standard', 'label' => 'Standard' ],
                        [ 'slug' => 'oem',      'label' => 'OEM'      ],
                        [ 'slug' => 'original', 'label' => 'Original' ],
                    ];
                    foreach ( $tiers as $ti => $tier ) :
                    ?>
                    <div class="jkrc-tier-row-admin" data-ti="<?php echo $ti; ?>">
                        <span class="jkrc-drag-handle">⠿</span>
                        <div class="jkrc-tier-color-dot jkrc-tier-dot-<?php echo $ti; ?>"></div>
                        <input type="text" class="jkrc-field jkrc-tier-slug" value="<?php echo esc_attr( $tier['slug'] ); ?>" placeholder="slug (e.g. oem)" style="width:130px;">
                        <input type="text" class="jkrc-field jkrc-tier-label" value="<?php echo esc_attr( $tier['label'] ); ?>" placeholder="Label shown to customers">
                        <button class="jkrc-btn jkrc-btn-danger jkrc-btn-sm jkrc-delete-tier">✕ Remove</button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button class="jkrc-btn jkrc-btn-primary jkrc-btn-sm" id="jkrc-add-tier">+ Add Tier</button>
                    <button class="jkrc-btn jkrc-btn-success" id="jkrc-save-tiers">💾 Save Tiers</button>
                    <span class="jkrc-save-status" id="jkrc-save-tiers-status"></span>
                </div>

                <div class="jkrc-doc-tip" style="margin-top:16px;">
                    <span class="jkrc-doc-tip-icon">💡</span>
                    <span>After saving tiers, the tier pill buttons in each repair row will update automatically on the next page load. The <strong>slug</strong> is stored in the data — if you rename a slug, existing repairs with the old slug will lose their tier assignment.</span>
                </div>
            </div>
        </div>

        <!-- SHORTCODE TAB -->
        <div class="jkrc-tab-content <?php echo $tab === 'shortcode' ? 'active' : ''; ?>" id="tab-shortcode">
            <div class="jkrc-card">
                <h2>How to use</h2>
                <p>Add this shortcode to any page or post:</p>
                <div class="jkrc-shortcode-box"><code>[repair_price_checker]</code></div>
                <h2 style="margin-top:28px;">What is new in V2</h2>
                <ul class="jkrc-tips-list">
                    <li>✅ Each repair row now has a <strong>"Book this repair →"</strong> button</li>
                    <li>✅ Clicking it opens a booking form pre-filled with device, brand, model and repair</li>
                    <li>✅ Customers can choose <strong>In-store, Mail-in, or At-home</strong> visit</li>
                    <li>✅ Mail-in shows an address field automatically</li>
                    <li>✅ Booking triggers an <strong>email notification to the shop</strong> and a <strong>confirmation to the customer</strong></li>
                    <li>✅ All bookings are saved and viewable in the <strong>Bookings tab</strong></li>
                </ul>
            </div>
        </div>
    </div>


        <!-- DOCS TAB -->
        <div class="jkrc-tab-content <?php echo $tab === 'docs' ? 'active' : ''; ?>" id="tab-docs">
            <?php jkrc_render_docs(); ?>
        </div>

    <!-- TEMPLATES (hidden, cloned by JS) -->
    <div id="jkrc-templates" style="display:none !important;visibility:hidden;position:absolute;left:-9999px;top:-9999px;width:0;height:0;overflow:hidden;">
        <?php jkrc_render_category( '__CAT__', [ 'id' => '', 'label' => '', 'icon' => '', 'brands' => [] ], true ); ?>
        <?php jkrc_render_brand( '__CAT__', '__BRAND__', [ 'name' => '', 'models' => [] ], true ); ?>
        <?php jkrc_render_model( '__CAT__', '__BRAND__', '__MODEL__', [ 'name' => '', 'repairs' => [] ], true ); ?>
        <table style="display:none;"><tbody>
        <?php jkrc_render_repair( '__CAT__', '__BRAND__', '__MODEL__', '__REPAIR__', [ 'name' => '', 'price' => '', 'note' => '', 'tier' => '' ], true ); ?>
        </tbody></table>
    </div>
    <?php
}

/* ── Bookings table ── */
function jkrc_render_bookings_table() {
    global $wpdb;
    $table    = $wpdb->prefix . JKRC_BOOK_TABLE;
    $bookings = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 200" );

    $status_colors = [
        'new'         => '#2da5de',
        'confirmed'   => '#2369b1',
        'in-progress' => '#f39c12',
        'completed'   => '#27ae60',
        'cancelled'   => '#e74c3c',
    ];

    $statuses = [ 'new', 'confirmed', 'in-progress', 'completed', 'cancelled' ];
    ?>
    <div class="jkrc-card">
        <h2>Repair Bookings <span style="font-size:14px;font-weight:400;color:#5a6475;">(<?php echo count($bookings); ?> total)</span></h2>

        <?php if ( empty( $bookings ) ) : ?>
            <p style="color:#5a6475;padding:20px 0;">No bookings yet. They will appear here once customers submit the booking form.</p>
        <?php else : ?>
        <table class="wp-list-table widefat fixed striped jkrc-bookings-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th style="width:130px">Date</th>
                    <th>Customer</th>
                    <th>Device &amp; Repair</th>
                    <th style="width:110px">Visit Type</th>
                    <th style="width:130px">Pref. Date/Time</th>
                    <th style="width:140px">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $bookings as $b ) :
                    $color = $status_colors[ $b->status ] ?? '#aaa';
                    $visit_labels = [
                        'in-store' => '🏪 In-store',
                        'mail-in'  => '📦 Mail-in',
                        'at-home'  => '🏠 At-home',
                    ];
                    $visit_label = $visit_labels[ $b->visit_type ] ?? esc_html( $b->visit_type );
                    $time_labels = [ 'morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening' ];
                    $time_label  = $b->pref_time ? ( $time_labels[ $b->pref_time ] ?? $b->pref_time ) : '—';
                ?>
                <tr>
                    <td><strong><?php echo $b->id; ?></strong></td>
                    <td style="font-size:12px;"><?php echo esc_html( date( 'M j, Y g:ia', strtotime( $b->created_at ) ) ); ?></td>
                    <td>
                        <strong><?php echo esc_html( $b->full_name ); ?></strong><br>
                        <a href="mailto:<?php echo esc_attr( $b->email ); ?>" style="font-size:12px;color:#2da5de;"><?php echo esc_html( $b->email ); ?></a><br>
                        <span style="font-size:12px;color:#5a6475;"><?php echo esc_html( $b->phone ); ?></span>
                        <?php if ( $b->description ) : ?>
                            <div style="font-size:11px;color:#5a6475;margin-top:4px;font-style:italic;"><?php echo esc_html( mb_strimwidth( $b->description, 0, 80, '…' ) ); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong style="font-size:13px;"><?php echo esc_html( $b->device ); ?></strong><br>
                        <span style="font-size:12px;color:#5a6475;"><?php echo esc_html( $b->repair ); ?></span>
                        <?php if ( $b->address ) : ?>
                            <div style="font-size:11px;color:#5a6475;margin-top:4px;">📍 <?php echo esc_html( $b->address ); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;"><?php echo $visit_label; ?></td>
                    <td style="font-size:12px;">
                        <?php echo $b->pref_date ? esc_html( date( 'M j, Y', strtotime( $b->pref_date ) ) ) : '—'; ?><br>
                        <span style="color:#5a6475;"><?php echo esc_html( $time_label ); ?></span>
                    </td>
                    <td>
                        <select class="jkrc-status-select" data-id="<?php echo $b->id; ?>" style="border-left:3px solid <?php echo $color; ?>;">
                            <?php foreach ( $statuses as $s ) : ?>
                                <option value="<?php echo $s; ?>" <?php selected( $b->status, $s ); ?>><?php echo ucfirst( str_replace('-', ' ', $s) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="jkrc-status-saved" id="jkrc-saved-<?php echo $b->id; ?>" style="display:none;font-size:11px;color:#27ae60;">✓ Saved</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <style>
    .jkrc-bookings-table td { vertical-align: top; padding: 12px 10px; }
    .jkrc-status-select { font-size: 12px; padding: 5px 8px; border-radius: 6px; border: 1px solid #dce4f0; cursor: pointer; width: 120px; }
    </style>

    <script>
    (function() {
        document.querySelectorAll('.jkrc-status-select').forEach(function(sel) {
            sel.addEventListener('change', function() {
                var id     = this.getAttribute('data-id');
                var status = this.value;
                var fd     = new FormData();
                fd.append('action', 'jkrc_update_status');
                fd.append('nonce', jkrcAdmin.nonce);
                fd.append('id', id);
                fd.append('status', status);
                fetch(jkrcAdmin.ajaxurl, { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            var saved = document.getElementById('jkrc-saved-' + id);
                            if (saved) { saved.style.display = 'inline'; setTimeout(function() { saved.style.display = 'none'; }, 2000); }
                        }
                    });
            });
        });
    })();
    </script>
    <?php
}

/* ── New bookings badge count ── */
function jkrc_new_bookings_badge() {
    global $wpdb;
    $table = $wpdb->prefix . JKRC_BOOK_TABLE;
    // Table might not exist yet
    $exists = $wpdb->get_var( "SHOW TABLES LIKE '$table'" );
    if ( ! $exists ) return '';
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'new'" );
    if ( ! $count ) return '';
    return '<span style="background:#e74c3c;color:#fff;border-radius:10px;padding:2px 7px;font-size:11px;margin-left:6px;">' . $count . '</span>';
}

/* ─── Category block ─── */
function jkrc_render_category( $ci, $cat, $template = false ) {
    $tpl    = $template ? ' jkrc-template' : '';
    $id     = esc_attr( $cat['id'] );
    $icon   = $cat['icon'] ?? '';
    $is_svg = ( strpos( $icon, '<svg' ) !== false || strpos( $icon, 'data:image/svg' ) !== false );
    $is_img = ( strpos( $icon, 'data:image/' ) !== false && ! $is_svg );
    $is_emoji = ( ! $is_svg && ! $is_img );
    ?>
    <div class="jkrc-category<?php echo $tpl; ?>" data-ci="<?php echo $ci; ?>">
        <div class="jkrc-category-header">
            <span class="jkrc-drag-handle">⠿</span>
            <div class="jkrc-icon-block">
                <div class="jkrc-icon-preview">
                    <?php if ( $is_svg ) : echo $icon;
                    elseif ( $is_img ) : ?><img src="<?php echo esc_attr( $icon ); ?>" alt="icon">
                    <?php else : ?><span class="jkrc-icon-emoji"><?php echo esc_html( $icon ?: '🔧' ); ?></span>
                    <?php endif; ?>
                </div>
                <input type="hidden" class="jkrc-cat-icon" value="<?php echo esc_attr( $icon ); ?>">
                <div class="jkrc-icon-actions">
                    <label class="jkrc-btn jkrc-btn-sm jkrc-upload-icon-btn" title="Upload SVG or PNG">
                        📁 Upload Icon
                        <input type="file" class="jkrc-icon-file-input" accept=".svg,image/svg+xml,image/png,image/jpeg,image/webp" style="display:none;">
                    </label>
                    <input type="text" class="jkrc-field jkrc-icon-emoji-input" value="<?php echo $is_emoji ? esc_attr( $icon ) : ''; ?>" placeholder="or emoji" style="width:70px;text-align:center;">
                    <button class="jkrc-btn jkrc-btn-xs jkrc-clear-icon" title="Remove icon">✕</button>
                </div>
            </div>
            <input type="text" class="jkrc-field jkrc-cat-label" value="<?php echo esc_attr( $cat['label'] ); ?>" placeholder="Category name (e.g. Smartphone)">
            <input type="text" class="jkrc-field jkrc-cat-id" value="<?php echo $id; ?>" placeholder="ID (no spaces)" style="width:160px;">
            <button class="jkrc-btn jkrc-btn-sm jkrc-toggle-body">▾ Show / Hide</button>
            <button class="jkrc-btn jkrc-btn-danger jkrc-btn-sm jkrc-delete-category">✕ Remove</button>
        </div>
        <div class="jkrc-category-body">
            <div class="jkrc-brands-list">
                <?php foreach ( $cat['brands'] as $bi => $brand ) { jkrc_render_brand( $ci, $bi, $brand ); } ?>
            </div>
            <button class="jkrc-btn jkrc-btn-sm jkrc-add-brand" data-ci="<?php echo $ci; ?>">+ Add Brand</button>
        </div>
    </div>
    <?php
}

/* ─── Brand block ─── */
function jkrc_render_brand( $ci, $bi, $brand, $template = false ) {
    $tpl = $template ? ' jkrc-template' : '';
    ?>
    <div class="jkrc-brand<?php echo $tpl; ?>" data-ci="<?php echo $ci; ?>" data-bi="<?php echo $bi; ?>">
        <div class="jkrc-brand-header">
            <span class="jkrc-drag-handle">⠿</span>
            <input type="text" class="jkrc-field jkrc-brand-name" value="<?php echo esc_attr( $brand['name'] ); ?>" placeholder="Brand name (e.g. Apple)">
            <button class="jkrc-btn jkrc-btn-sm jkrc-toggle-body">▾ Models</button>
            <button class="jkrc-btn jkrc-btn-danger jkrc-btn-sm jkrc-delete-brand">✕</button>
        </div>
        <div class="jkrc-brand-body">
            <div class="jkrc-models-list">
                <?php foreach ( $brand['models'] as $mi => $model ) { jkrc_render_model( $ci, $bi, $mi, $model ); } ?>
            </div>
            <button class="jkrc-btn jkrc-btn-sm jkrc-add-model" data-ci="<?php echo $ci; ?>" data-bi="<?php echo $bi; ?>">+ Add Model</button>
        </div>
    </div>
    <?php
}

/* ─── Model block ─── */
function jkrc_render_model( $ci, $bi, $mi, $model, $template = false ) {
    $tpl = $template ? ' jkrc-template' : '';
    ?>
    <div class="jkrc-model<?php echo $tpl; ?>" data-ci="<?php echo $ci; ?>" data-bi="<?php echo $bi; ?>" data-mi="<?php echo $mi; ?>">
        <div class="jkrc-model-header">
            <span class="jkrc-drag-handle">⠿</span>
            <input type="text" class="jkrc-field jkrc-model-name" value="<?php echo esc_attr( $model['name'] ); ?>" placeholder="Model name (e.g. iPhone 15 Pro)">
            <button class="jkrc-btn jkrc-btn-sm jkrc-toggle-body">▾ Repairs</button>
            <button class="jkrc-btn jkrc-btn-danger jkrc-btn-sm jkrc-delete-model">✕</button>
        </div>
        <div class="jkrc-model-body">
            <table class="jkrc-repairs-table">
                <thead><tr><th>Repair Name <span class="jkrc-tip" data-tip="The repair type shown to customers e.g. Screen Replacement">?</span></th><th>Price <span class="jkrc-tip" data-tip="Enter $99 for a fixed price, or type: Contact us to show a contact link">?</span></th><th>Note <span class="jkrc-tip" data-tip="Optional small text shown below the repair name e.g. OEM parts, 90-day warranty">?</span></th><th style="width:130px;">Tier <span class="jkrc-tip" data-tip="Badge shown on the price table: STD=Standard, OEM=OEM parts, ORIG=Original manufacturer parts">?</span></th><th></th></tr></thead>
                <tbody class="jkrc-repairs-list">
                    <?php foreach ( $model['repairs'] as $ri => $repair ) { jkrc_render_repair( $ci, $bi, $mi, $ri, $repair ); } ?>
                </tbody>
            </table>
            <button class="jkrc-btn jkrc-btn-sm jkrc-add-repair" data-ci="<?php echo $ci; ?>" data-bi="<?php echo $bi; ?>" data-mi="<?php echo $mi; ?>">+ Add Repair</button>
        </div>
    </div>
    <?php
}

/* ─── Repair row ─── */
function jkrc_render_repair( $ci, $bi, $mi, $ri, $repair, $template = false ) {
    $tpl  = $template ? ' jkrc-template' : '';
    $tier = $repair['tier'] ?? '';
    ?>
    <tr class="jkrc-repair<?php echo $tpl; ?>" data-ci="<?php echo $ci; ?>" data-bi="<?php echo $bi; ?>" data-mi="<?php echo $mi; ?>" data-ri="<?php echo $ri; ?>">
        <td><input type="text" class="jkrc-field jkrc-repair-name" value="<?php echo esc_attr( $repair['name'] ); ?>" placeholder="e.g. Screen Replacement"></td>
        <td><input type="text" class="jkrc-field jkrc-repair-price" value="<?php echo esc_attr( $repair['price'] ); ?>" placeholder="e.g. $99"></td>
        <td><input type="text" class="jkrc-field jkrc-repair-note" value="<?php echo esc_attr( $repair['note'] ); ?>" placeholder="e.g. OEM parts included"></td>
        <td>
            <div class="jkrc-tier-picker">
                <button type="button" class="jkrc-tier-btn <?php echo $tier === '' ? 'active' : ''; ?>" data-value="">—</button>
                <?php
                $all_data = jkrc_get_data();
                $tiers    = $all_data['settings']['tiers'] ?? [
                    [ 'slug' => 'standard', 'label' => 'Standard' ],
                    [ 'slug' => 'oem',      'label' => 'OEM'      ],
                    [ 'slug' => 'original', 'label' => 'Original' ],
                ];
                foreach ( $tiers as $ti => $t ) :
                    $slug      = esc_attr( $t['slug'] );
                    $lbl_short = esc_html( strtoupper( substr( $t['label'], 0, 4 ) ) );
                    $active    = $tier === $t['slug'] ? 'active' : '';
                ?>
                <button type="button" class="jkrc-tier-btn jkrc-tier-dyn jkrc-tier-idx-<?php echo $ti; ?> <?php echo $active; ?>" data-value="<?php echo $slug; ?>" title="<?php echo esc_attr( $t['label'] ); ?>"><?php echo $lbl_short; ?></button>
                <?php endforeach; ?>
                <input type="hidden" class="jkrc-repair-tier" value="<?php echo esc_attr( $tier ); ?>">
            </div>
        </td>
        <td><button class="jkrc-btn jkrc-btn-danger jkrc-btn-xs jkrc-delete-repair">✕</button></td>
    </tr>
    <?php
}

/* ─── Docs tab ─── */
function jkrc_render_docs() {
    ?>
    <div class="jkrc-docs-wrap">

        <!-- Sidebar nav -->
        <nav class="jkrc-docs-nav">
            <div class="jkrc-docs-nav-title">Documentation</div>
            <a href="#doc-overview"    class="jkrc-docs-nav-link active">Overview</a>
            <a href="#doc-quickstart"  class="jkrc-docs-nav-link">Quick Start</a>
            <a href="#doc-categories"  class="jkrc-docs-nav-link">Categories &amp; Brands</a>
            <a href="#doc-repairs"     class="jkrc-docs-nav-link">Repairs &amp; Pricing</a>
            <a href="#doc-tiers"       class="jkrc-docs-nav-link">Tier Badges</a>
            <a href="#doc-booking"     class="jkrc-docs-nav-link">Booking Flow</a>
            <a href="#doc-bookings"    class="jkrc-docs-nav-link">Managing Bookings</a>
            <a href="#doc-emails"      class="jkrc-docs-nav-link">Email Notifications</a>
            <a href="#doc-settings"    class="jkrc-docs-nav-link">Settings</a>
            <a href="#doc-shortcode"   class="jkrc-docs-nav-link">Shortcode</a>
            <a href="#doc-faq"         class="jkrc-docs-nav-link">FAQ</a>
        </nav>

        <!-- Content -->
        <div class="jkrc-docs-content">

            <!-- OVERVIEW -->
            <section class="jkrc-doc-section" id="doc-overview">
                <div class="jkrc-doc-badge">v2.0</div>
                <h2>JK Tech Repair Price Checker</h2>
                <p class="jkrc-doc-lead">A fully customizable repair price checker that lets customers check repair prices and book a repair — all without leaving your website.</p>
                <div class="jkrc-doc-cards">
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">🔍</div>
                        <strong>Price Checker</strong>
                        <p>Customers select their device, brand, and model to see a full repair price table.</p>
                    </div>
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">✅</div>
                        <strong>Multi-Select</strong>
                        <p>Customers can select multiple repairs at once and book them all in a single request.</p>
                    </div>
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">📅</div>
                        <strong>Inline Booking</strong>
                        <p>After selecting repairs, customers fill out a booking form with visit type and preferred date.</p>
                    </div>
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">📬</div>
                        <strong>Email Notifications</strong>
                        <p>Both the shop and the customer receive an email when a booking is submitted.</p>
                    </div>
                </div>
            </section>

            <!-- QUICK START -->
            <section class="jkrc-doc-section" id="doc-quickstart">
                <h2>Quick Start</h2>
                <p>Get the price checker live on your site in 4 steps:</p>
                <ol class="jkrc-doc-steps">
                    <li>
                        <span class="jkrc-doc-step-num">1</span>
                        <div>
                            <strong>Add your categories</strong>
                            <p>Go to <em>Categories &amp; Prices</em> and confirm or add device types (Smartphone, Laptop, etc.).</p>
                        </div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">2</span>
                        <div>
                            <strong>Add brands and models</strong>
                            <p>Expand each category and add the brands and models you service.</p>
                        </div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">3</span>
                        <div>
                            <strong>Add repair prices</strong>
                            <p>Expand each model and add repair rows with names and prices.</p>
                        </div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">4</span>
                        <div>
                            <strong>Add the shortcode to a page</strong>
                            <p>Create or edit a page in WordPress and add <code>[repair_price_checker]</code> to the content. Save and you are live.</p>
                        </div>
                    </li>
                </ol>
                <div class="jkrc-doc-tip">
                    <span class="jkrc-doc-tip-icon">💡</span>
                    <span>Always click <strong>Save All Changes</strong> after making edits. Changes are not saved automatically.</span>
                </div>
            </section>

            <!-- CATEGORIES -->
            <section class="jkrc-doc-section" id="doc-categories">
                <h2>Categories, Brands &amp; Models</h2>
                <p>The data is structured in a 3-level hierarchy:</p>
                <div class="jkrc-doc-hierarchy">
                    <div class="jkrc-doc-hier-item jkrc-doc-hier-cat">📱 Category <span>e.g. Smartphone</span></div>
                    <div class="jkrc-doc-hier-arrow">↳</div>
                    <div class="jkrc-doc-hier-item jkrc-doc-hier-brand">🏷 Brand <span>e.g. Apple</span></div>
                    <div class="jkrc-doc-hier-arrow">↳</div>
                    <div class="jkrc-doc-hier-item jkrc-doc-hier-model">📋 Model <span>e.g. iPhone 14</span></div>
                    <div class="jkrc-doc-hier-arrow">↳</div>
                    <div class="jkrc-doc-hier-item jkrc-doc-hier-repair">🔧 Repairs <span>e.g. Screen Replacement — $149</span></div>
                </div>

                <h3>Category fields</h3>
                <table class="jkrc-doc-table">
                    <tr><th>Field</th><th>Description</th><th>Example</th></tr>
                    <tr><td><code>Icon</code></td><td>Upload an SVG/PNG or type an emoji. Shows on the device selection cards.</td><td>📱 or uploaded SVG</td></tr>
                    <tr><td><code>Category Name</code></td><td>The device type shown to customers.</td><td>Smartphone</td></tr>
                    <tr><td><code>ID</code></td><td>Internal slug, no spaces. Used for filtering. Auto-fill recommended.</td><td>smartphone</td></tr>
                </table>

                <div class="jkrc-doc-tip">
                    <span class="jkrc-doc-tip-icon">💡</span>
                    <span>Use <strong>▾ Show / Hide</strong> on any category to collapse it and keep the admin panel tidy.</span>
                </div>
            </section>

            <!-- REPAIRS -->
            <section class="jkrc-doc-section" id="doc-repairs">
                <h2>Repairs &amp; Pricing</h2>
                <p>Each model can have any number of repair rows. Each repair row has four fields:</p>
                <table class="jkrc-doc-table">
                    <tr><th>Field</th><th>Description</th><th>Example</th></tr>
                    <tr>
                        <td><code>Repair Name</code></td>
                        <td>The repair type shown to customers in the price table.</td>
                        <td>Screen Replacement</td>
                    </tr>
                    <tr>
                        <td><code>Price</code></td>
                        <td>Enter a dollar amount. Use <strong>Contact us</strong> (exact text) to show a contact link instead of a price badge.</td>
                        <td>$149 or Contact us</td>
                    </tr>
                    <tr>
                        <td><code>Note</code></td>
                        <td>Optional small text shown below the repair name. Good for warranty or parts info.</td>
                        <td>Includes 90-day warranty</td>
                    </tr>
                    <tr>
                        <td><code>Tier</code></td>
                        <td>Adds a quality badge. See Tier Badges section below.</td>
                        <td>STD / OEM / ORIG</td>
                    </tr>
                </table>

                <div class="jkrc-doc-warning">
                    <span class="jkrc-doc-tip-icon">⚠️</span>
                    <span>Repairs with <strong>Contact us</strong> as the price will not show a "Book this repair" button. Use this for custom quotes only.</span>
                </div>
            </section>

            <!-- TIERS -->
            <section class="jkrc-doc-section" id="doc-tiers">
                <h2>Tier Badges</h2>
                <p>Tier badges appear next to the repair name in the price table. They are most useful for <strong>screen replacements</strong> where you offer multiple part quality levels at different prices.</p>

                <div class="jkrc-doc-tier-demo">
                    <div class="jkrc-doc-tier-row">
                        <span class="jkrc-doc-tier-pill jkrc-doc-tier-none">— None</span>
                        <span>No badge shown. Use for repairs that do not have quality tiers.</span>
                    </div>
                    <div class="jkrc-doc-tier-row">
                        <span class="jkrc-doc-tier-pill jkrc-doc-tier-std">STANDARD</span>
                        <span>Budget-friendly aftermarket parts. Good value, functional quality.</span>
                    </div>
                    <div class="jkrc-doc-tier-row">
                        <span class="jkrc-doc-tier-pill jkrc-doc-tier-oem">OEM</span>
                        <span>Original Equipment Manufacturer parts. Same specs as the original.</span>
                    </div>
                    <div class="jkrc-doc-tier-row">
                        <span class="jkrc-doc-tier-pill jkrc-doc-tier-orig">ORIGINAL</span>
                        <span>Genuine manufacturer parts. Highest quality, pulled from OEM supply chain.</span>
                    </div>
                </div>

                <h3>Example setup for iPhone screen repair:</h3>
                <div class="jkrc-doc-example-box">
                    <div class="jkrc-doc-example-row"><span>Screen Replacement</span><span class="jkrc-doc-tier-pill jkrc-doc-tier-std">STANDARD</span><span>$89</span></div>
                    <div class="jkrc-doc-example-row"><span>Screen Replacement</span><span class="jkrc-doc-tier-pill jkrc-doc-tier-oem">OEM</span><span>$129</span></div>
                    <div class="jkrc-doc-example-row"><span>Screen Replacement</span><span class="jkrc-doc-tier-pill jkrc-doc-tier-orig">ORIGINAL</span><span>$169</span></div>
                </div>

                <div class="jkrc-doc-tip">
                    <span class="jkrc-doc-tip-icon">💡</span>
                    <span>To set the tier, click the <strong>STD / OEM / ORIG</strong> pill buttons in the repair row. The selected pill lights up blue. Click <strong>—</strong> to remove the tier.</span>
                </div>
            </section>

            <!-- BOOKING FLOW -->
            <section class="jkrc-doc-section" id="doc-booking">
                <h2>Booking Flow</h2>
                <p>After viewing the price table, customers can select one or more repairs and submit a booking request.</p>

                <ol class="jkrc-doc-steps">
                    <li>
                        <span class="jkrc-doc-step-num">1</span>
                        <div><strong>Customer selects repairs</strong><p>Checkboxes appear on each row. Clicking a row or the checkbox selects it. The row highlights blue.</p></div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">2</span>
                        <div><strong>Customer clicks "Book N repairs →"</strong><p>The button at the bottom of the table activates once at least one repair is selected.</p></div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">3</span>
                        <div><strong>Customer fills the booking form</strong><p>Name, email, phone, visit type, date, and a description. Mail-in repairs show an address field.</p></div>
                    </li>
                    <li>
                        <span class="jkrc-doc-step-num">4</span>
                        <div><strong>Emails are sent</strong><p>The shop gets a notification. The customer gets a confirmation with a summary and what happens next.</p></div>
                    </li>
                </ol>

                <h3>Visit types</h3>
                <table class="jkrc-doc-table">
                    <tr><th>Type</th><th>Description</th></tr>
                    <tr><td>🏪 In-store drop-off</td><td>Customer brings the device to 11990 Rue Sherbrooke Est.</td></tr>
                    <tr><td>📦 Mail-in repair</td><td>Customer ships the device. An address field appears on the form. You ship it back when done.</td></tr>
                    <tr><td>🏠 At-home visit</td><td>Technician visits the customer location.</td></tr>
                </table>
            </section>

            <!-- MANAGING BOOKINGS -->
            <section class="jkrc-doc-section" id="doc-bookings">
                <h2>Managing Bookings</h2>
                <p>All submitted bookings appear in the <strong>Bookings</strong> tab. A red badge shows the number of new unreviewed bookings.</p>

                <h3>Booking statuses</h3>
                <table class="jkrc-doc-table">
                    <tr><th>Status</th><th>Meaning</th></tr>
                    <tr><td><span class="jkrc-doc-status jkrc-doc-status-new">New</span></td><td>Just submitted, not yet reviewed by the shop.</td></tr>
                    <tr><td><span class="jkrc-doc-status jkrc-doc-status-confirmed">Confirmed</span></td><td>Booking confirmed with the customer.</td></tr>
                    <tr><td><span class="jkrc-doc-status jkrc-doc-status-progress">In Progress</span></td><td>Device is in for repair.</td></tr>
                    <tr><td><span class="jkrc-doc-status jkrc-doc-status-done">Completed</span></td><td>Repair done, device returned.</td></tr>
                    <tr><td><span class="jkrc-doc-status jkrc-doc-status-cancelled">Cancelled</span></td><td>Booking cancelled.</td></tr>
                </table>

                <p>To update a status, use the dropdown in the Status column. Changes save instantly — no page reload needed.</p>
            </section>

            <!-- EMAILS -->
            <section class="jkrc-doc-section" id="doc-emails">
                <h2>Email Notifications</h2>
                <p>Two emails are sent automatically when a customer submits a booking:</p>

                <div class="jkrc-doc-cards" style="grid-template-columns:1fr 1fr;">
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">🏪</div>
                        <strong>Shop notification</strong>
                        <p>Sent to <code>info@jktechsolutions.ca</code>. Includes customer details, device, repairs, visit type, and a link to the Bookings tab.</p>
                    </div>
                    <div class="jkrc-doc-card">
                        <div class="jkrc-doc-card-icon">👤</div>
                        <strong>Customer confirmation</strong>
                        <p>Sent to the customer email. Includes a summary of their booking and the 4-step what happens next" process.</p>
                    </div>
                </div>

                <div class="jkrc-doc-tip">
                    <span class="jkrc-doc-tip-icon">💡</span>
                    <span>To change the notification email address, go to <strong>Settings</strong> and update the <em>Notification Email</em> field.</span>
                </div>
            </section>

            <!-- SETTINGS -->
            <section class="jkrc-doc-section" id="doc-settings">
                <h2>Settings</h2>
                <table class="jkrc-doc-table">
                    <tr><th>Setting</th><th>Description</th></tr>
                    <tr>
                        <td><code>Contact Page URL</code></td>
                        <td>The URL used for "Contact us" links when a repair price is set to <em>Contact us</em>. Default: <code>/contact</code></td>
                    </tr>
                    <tr>
                        <td><code>Contact Link Text</code></td>
                        <td>The label shown for contact-us prices. Default: <em>Contact us →</em></td>
                    </tr>
                    <tr>
                        <td><code>Notification Email</code></td>
                        <td>Where booking notification emails are sent. Default: <code>info@jktechsolutions.ca</code></td>
                    </tr>
                    <tr>
                        <td><code>Footer Note</code></td>
                        <td>Optional text shown below the price table, e.g. "Prices are estimates. Final quote confirmed at drop-off."</td>
                    </tr>
                </table>
            </section>

            <!-- SHORTCODE -->
            <section class="jkrc-doc-section" id="doc-shortcode">
                <h2>Shortcode</h2>
                <p>Place the price checker on any page using:</p>
                <div class="jkrc-doc-code">[repair_price_checker]</div>
                <p>You can use this shortcode in:</p>
                <ul class="jkrc-doc-list">
                    <li>The WordPress block editor (use a Shortcode block)</li>
                    <li>Elementor (use a Shortcode widget)</li>
                    <li>Any theme or plugin that supports shortcodes</li>
                </ul>
                <p>Only one instance per page is supported.</p>
            </section>

            <!-- FAQ -->
            <section class="jkrc-doc-section" id="doc-faq">
                <h2>FAQ</h2>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">Why are my changes not showing on the front end?</div>
                    <div class="jkrc-doc-faq-a">You must click <strong>Save All Changes</strong> after every edit. The widget only loads saved data.</div>
                </div>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">Can I have the same repair listed multiple times on one model?</div>
                    <div class="jkrc-doc-faq-a">Yes. This is the intended way to list screen tiers — add the same repair name three times with different tiers (STD / OEM / ORIG) and different prices.</div>
                </div>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">What happens if a customer books but I am closed?</div>
                    <div class="jkrc-doc-faq-a">The booking is saved and the notification email is sent. You can review it in the Bookings tab when you are back and update the status to Confirmed once you have contacted the customer.</div>
                </div>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">Can I delete old bookings?</div>
                    <div class="jkrc-doc-faq-a">Currently bookings can be marked as Completed or Cancelled but not deleted from the admin UI. To permanently delete, use phpMyAdmin and remove rows from the <code>wp_jkrc_bookings</code> table.</div>
                </div>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">The booking emails are going to spam. What can I do?</div>
                    <div class="jkrc-doc-faq-a">Install a transactional email plugin like <strong>WP Mail SMTP</strong> and connect it to your email provider (Mailgun, SendGrid, Gmail SMTP). This dramatically improves deliverability.</div>
                </div>

                <div class="jkrc-doc-faq-item">
                    <div class="jkrc-doc-faq-q">Can I add a repair that requires a quote instead of a fixed price?</div>
                    <div class="jkrc-doc-faq-a">Yes — type <strong>Contact us</strong> (exact text) in the Price field. The price table will show a "Contact us" link instead of a price badge, and no booking button will appear for that repair.</div>
                </div>
            </section>

        </div><!-- .jkrc-docs-content -->
    </div><!-- .jkrc-docs-wrap -->

    <script>
    (function() {
        // Docs sidebar smooth scroll + active link
        var links = document.querySelectorAll('.jkrc-docs-nav-link');
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    links.forEach(function(l) { l.classList.remove('active'); });
                    link.classList.add('active');
                }
            });
        });

        // Highlight nav on scroll
        var sections = document.querySelectorAll('.jkrc-doc-section');
        var docsContent = document.querySelector('.jkrc-docs-content');
        if (docsContent) {
            docsContent.addEventListener('scroll', function() {
                var scrollTop = docsContent.scrollTop + 60;
                sections.forEach(function(section) {
                    if (section.offsetTop <= scrollTop) {
                        var id = section.getAttribute('id');
                        links.forEach(function(l) { l.classList.remove('active'); });
                        var activeLink = document.querySelector('.jkrc-docs-nav-link[href="#' + id + '"]');
                        if (activeLink) activeLink.classList.add('active');
                    }
                });
            });
        }
    })();
    </script>
    <?php
}
