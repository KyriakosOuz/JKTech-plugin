<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'repair_price_checker', 'jkrc_render_shortcode' );

function jkrc_render_shortcode() {
    $settings = ( jkrc_get_data()['settings'] ?? [] );
    ob_start();
    ?>
    <div class="jkrc-checker-wrapper" id="jkrc-app">

        <!-- STEPS INDICATOR -->
        <div class="jkrc-steps-header">
            <div class="jkrc-step-item">
                <div class="jkrc-step-circle active" id="jkrc-sc1">1</div>
                <span class="jkrc-step-label active" id="jkrc-sl1"><?php _e( 'Device', 'jktech-repair-checker' ); ?></span>
            </div>
            <div class="jkrc-step-connector" id="jkrc-con1"></div>
            <div class="jkrc-step-item">
                <div class="jkrc-step-circle" id="jkrc-sc2">2</div>
                <span class="jkrc-step-label" id="jkrc-sl2"><?php _e( 'Brand', 'jktech-repair-checker' ); ?></span>
            </div>
            <div class="jkrc-step-connector" id="jkrc-con2"></div>
            <div class="jkrc-step-item">
                <div class="jkrc-step-circle" id="jkrc-sc3">3</div>
                <span class="jkrc-step-label" id="jkrc-sl3"><?php _e( 'Model', 'jktech-repair-checker' ); ?></span>
            </div>
            <div class="jkrc-step-connector" id="jkrc-con3"></div>
            <div class="jkrc-step-item">
                <div class="jkrc-step-circle" id="jkrc-sc4">4</div>
                <span class="jkrc-step-label" id="jkrc-sl4"><?php _e( 'Prices', 'jktech-repair-checker' ); ?></span>
            </div>
            <div class="jkrc-step-connector" id="jkrc-con4"></div>
            <div class="jkrc-step-item">
                <div class="jkrc-step-circle" id="jkrc-sc5">5</div>
                <span class="jkrc-step-label" id="jkrc-sl5"><?php _e( 'Book', 'jktech-repair-checker' ); ?></span>
            </div>
        </div>

        <!-- STEP 1: DEVICE -->
        <div class="jkrc-step-section jkrc-visible" id="jkrc-step1">
            <div class="jkrc-section-label"><?php _e( 'Step 1 of 4', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-selection-title"><?php _e( 'What type of device do you need repaired?', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-cards-grid" id="jkrc-device-grid"></div>
        </div>

        <!-- STEP 2: BRAND -->
        <div class="jkrc-step-section" id="jkrc-step2">
            <button class="jkrc-back-btn" id="jkrc-back2">← <?php _e( 'Back', 'jktech-repair-checker' ); ?></button>
            <div class="jkrc-section-label"><?php _e( 'Step 2 of 4', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-selection-title" id="jkrc-step2-title"><?php _e( 'Select your brand', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-pill-grid" id="jkrc-brand-grid"></div>
        </div>

        <!-- STEP 3: MODEL -->
        <div class="jkrc-step-section" id="jkrc-step3">
            <button class="jkrc-back-btn" id="jkrc-back3">← <?php _e( 'Back', 'jktech-repair-checker' ); ?></button>
            <div class="jkrc-section-label"><?php _e( 'Step 3 of 4', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-selection-title" id="jkrc-step3-title"><?php _e( 'Select your model', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-pill-grid" id="jkrc-model-grid"></div>
        </div>

        <!-- STEP 4: PRICES -->
        <div class="jkrc-step-section" id="jkrc-step4">
            <button class="jkrc-back-btn" id="jkrc-back4">← <?php _e( 'Back', 'jktech-repair-checker' ); ?></button>
            <div class="jkrc-price-table-wrapper">
                <div class="jkrc-price-table-header">
                    <div>
                        <h3 id="jkrc-table-title"><?php _e( 'Repair Prices', 'jktech-repair-checker' ); ?></h3>
                        <span><?php _e( 'Select one or more repairs below, then click Book to request an appointment', 'jktech-repair-checker' ); ?></span>
                    </div>
                </div>
                <table class="jkrc-price-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th><?php _e( 'Repair Type', 'jktech-repair-checker' ); ?></th>
                            <th><?php _e( 'Price', 'jktech-repair-checker' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="jkrc-price-tbody"></tbody>
                </table>
            </div>
            <div class="jkrc-book-selected-bar">
                <button class="jkrc-book-selected-btn" id="jkrc-book-selected-btn" disabled>
                    <?php _e( 'Select repairs to book', 'jktech-repair-checker' ); ?>
                </button>
                <span class="jkrc-select-hint"><?php _e( 'Click rows to select', 'jktech-repair-checker' ); ?></span>
            </div>
            <div class="jkrc-bottom-note" id="jkrc-footer-note"></div>
            <div class="jkrc-step4-actions">
                <button class="jkrc-reset-btn" id="jkrc-reset">↩ <?php _e( 'Start Over', 'jktech-repair-checker' ); ?></button>
                <a href="<?php echo esc_url( $settings['contact_url'] ?? '/contact' ); ?>" class="jkrc-contact-link">
                    <?php _e( 'Have a question? Contact us →', 'jktech-repair-checker' ); ?>
                </a>
            </div>
        </div>

        <!-- STEP 5: BOOKING FORM -->
        <div class="jkrc-step-section" id="jkrc-step5">
            <button class="jkrc-back-btn" id="jkrc-back5">← <?php _e( 'Back to Prices', 'jktech-repair-checker' ); ?></button>
            <div class="jkrc-section-label"><?php _e( 'Step 4 of 4', 'jktech-repair-checker' ); ?></div>
            <div class="jkrc-selection-title"><?php _e( 'Book your repair', 'jktech-repair-checker' ); ?></div>

            <!-- Selected repairs summary -->
            <div class="jkrc-booking-summary" id="jkrc-booking-summary">
                <div class="jkrc-summary-label"><?php _e( 'Device', 'jktech-repair-checker' ); ?></div>
                <div class="jkrc-summary-device-row">
                    <span id="jkrc-summary-device"></span>
                </div>
                <div class="jkrc-summary-label" style="margin-top:10px;"><?php _e( 'Selected repairs', 'jktech-repair-checker' ); ?></div>
                <div id="jkrc-summary-repairs-list" class="jkrc-summary-repairs-list"></div>
                <input type="hidden" id="jkrc-summary-repair">
                <input type="hidden" id="jkrc-summary-price">
            </div>

            <form class="jkrc-booking-form" id="jkrc-booking-form" novalidate>

                <!-- Row: Name + Email -->
                <div class="jkrc-form-row">
                    <div class="jkrc-form-group">
                        <label for="jkrc-name"><?php _e( 'Full name', 'jktech-repair-checker' ); ?> <span class="jkrc-required">*</span></label>
                        <input type="text" id="jkrc-name" name="full_name" placeholder="John Smith" required>
                    </div>
                    <div class="jkrc-form-group">
                        <label for="jkrc-email"><?php _e( 'Email address', 'jktech-repair-checker' ); ?> <span class="jkrc-required">*</span></label>
                        <input type="email" id="jkrc-email" name="email" placeholder="john@example.com" required>
                    </div>
                </div>

                <!-- Row: Phone + Visit type -->
                <div class="jkrc-form-row">
                    <div class="jkrc-form-group">
                        <label for="jkrc-phone"><?php _e( 'Phone number', 'jktech-repair-checker' ); ?> <span class="jkrc-required">*</span></label>
                        <input type="tel" id="jkrc-phone" name="phone" placeholder="514-555-0000" required>
                    </div>
                    <div class="jkrc-form-group">
                        <label for="jkrc-visit-type"><?php _e( 'Visit type', 'jktech-repair-checker' ); ?> <span class="jkrc-required">*</span></label>
                        <select id="jkrc-visit-type" name="visit_type" required>
                            <option value=""><?php _e( 'Select…', 'jktech-repair-checker' ); ?></option>
                            <option value="in-store"><?php _e( 'In-store drop-off — 11990 Rue Sherbrooke Est', 'jktech-repair-checker' ); ?></option>
                            <option value="mail-in"><?php _e( 'Mail-in repair (shipped back when done)', 'jktech-repair-checker' ); ?></option>
                            <option value="at-home"><?php _e( 'At-home visit (Montreal area)', 'jktech-repair-checker' ); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Mail-in address (shown conditionally) -->
                <div class="jkrc-form-group jkrc-address-group" id="jkrc-address-group" style="display:none;">
                    <label for="jkrc-address"><?php _e( 'Shipping address', 'jktech-repair-checker' ); ?> <span class="jkrc-required">*</span></label>
                    <input type="text" id="jkrc-address" name="address" placeholder="123 Main St, City, Province, Postal Code">
                </div>

                <!-- Row: Date + Time -->
                <div class="jkrc-form-row">
                    <div class="jkrc-form-group">
                        <label for="jkrc-date"><?php _e( 'Preferred date', 'jktech-repair-checker' ); ?></label>
                        <input type="date" id="jkrc-date" name="pref_date">
                    </div>
                    <div class="jkrc-form-group">
                        <label for="jkrc-time"><?php _e( 'Preferred time', 'jktech-repair-checker' ); ?></label>
                        <select id="jkrc-time" name="pref_time">
                            <option value=""><?php _e( 'No preference', 'jktech-repair-checker' ); ?></option>
                            <option value="morning"><?php _e( 'Morning (9am – 12pm)', 'jktech-repair-checker' ); ?></option>
                            <option value="afternoon"><?php _e( 'Afternoon (12pm – 4pm)', 'jktech-repair-checker' ); ?></option>
                            <option value="evening"><?php _e( 'Evening (4pm – 6pm)', 'jktech-repair-checker' ); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="jkrc-form-group">
                    <label for="jkrc-description"><?php _e( 'Describe the issue (optional)', 'jktech-repair-checker' ); ?></label>
                    <textarea id="jkrc-description" name="description" rows="3" placeholder="<?php esc_attr_e( 'Any additional details about the problem…', 'jktech-repair-checker' ); ?>"></textarea>
                </div>

                <!-- Hidden fields for device/repair context -->
                <input type="hidden" id="jkrc-hidden-device" name="device">
                <input type="hidden" id="jkrc-hidden-repair" name="repair">
                <input type="hidden" id="jkrc-hidden-price" name="price">

                <!-- Error message -->
                <div class="jkrc-form-error" id="jkrc-form-error" style="display:none;"></div>

                <!-- Submit -->
                <button type="submit" class="jkrc-submit-btn" id="jkrc-submit-btn">
                    <span class="jkrc-submit-text"><?php _e( 'Request Appointment', 'jktech-repair-checker' ); ?></span>
                    <span class="jkrc-submit-loading" style="display:none;"><?php _e( 'Sending…', 'jktech-repair-checker' ); ?></span>
                </button>

                <p class="jkrc-form-disclaimer">
                    <?php _e( "We will confirm within 1 business day. If we cannot find the issue, you will not be charged.", 'jktech-repair-checker' ); ?>
                </p>

            </form>

            <!-- Success message (shown after submit) -->
            <div class="jkrc-booking-success" id="jkrc-booking-success" style="display:none;">
                <div class="jkrc-success-icon">✓</div>
                <h3><?php _e( 'Request received!', 'jktech-repair-checker' ); ?></h3>
                <p><?php _e( "We have received your request and will confirm within 1 business day. If you need to reach us sooner, call or text 514-560-6449.", 'jktech-repair-checker' ); ?></p>
                <button class="jkrc-reset-btn" id="jkrc-reset-success">↩ <?php _e( 'Start Over', 'jktech-repair-checker' ); ?></button>
            </div>
        </div>

    </div>
    <?php
    return ob_get_clean();
}
