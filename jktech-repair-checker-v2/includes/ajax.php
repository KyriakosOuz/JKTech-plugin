<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Save price data (admin) ── */
add_action( 'wp_ajax_jkrc_save', 'jkrc_ajax_save' );
function jkrc_ajax_save() {
    check_ajax_referer( 'jkrc_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

    $raw  = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
    $data = json_decode( $raw, true );

    if ( ! is_array( $data ) ) {
        wp_send_json_error( [ 'message' => 'Invalid data format' ] );
    }

    $clean = jkrc_sanitize_data( $data );
    update_option( JKRC_DB_KEY, $clean );
    wp_send_json_success( [ 'message' => 'Saved successfully' ] );
}

/* ── Book a repair (public) ── */
add_action( 'wp_ajax_jkrc_book',        'jkrc_ajax_book' );
add_action( 'wp_ajax_nopriv_jkrc_book', 'jkrc_ajax_book' );

function jkrc_ajax_book() {
    check_ajax_referer( 'jkrc_book_nonce', 'nonce' );

    $full_name   = sanitize_text_field( $_POST['full_name']   ?? '' );
    $email       = sanitize_email(      $_POST['email']       ?? '' );
    $phone       = sanitize_text_field( $_POST['phone']       ?? '' );
    $device      = sanitize_text_field( $_POST['device']      ?? '' );
    $repair      = sanitize_text_field( $_POST['repair']      ?? '' );
    $price       = sanitize_text_field( $_POST['price']       ?? '' );
    $visit_type  = sanitize_text_field( $_POST['visit_type']  ?? '' );
    $address     = sanitize_textarea_field( $_POST['address'] ?? '' );
    $pref_date   = sanitize_text_field( $_POST['pref_date']   ?? '' );
    $pref_time   = sanitize_text_field( $_POST['pref_time']   ?? '' );
    $description = sanitize_textarea_field( $_POST['description'] ?? '' );

    // Validate required fields
    if ( ! $full_name || ! $email || ! $phone || ! $visit_type ) {
        wp_send_json_error( [ 'message' => 'Please fill in all required fields.' ] );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ] );
    }

    if ( $visit_type === 'mail-in' && ! $address ) {
        wp_send_json_error( [ 'message' => 'Please enter your shipping address for mail-in repairs.' ] );
    }

    // Save to DB
    global $wpdb;
    $table = $wpdb->prefix . JKRC_BOOK_TABLE;

    $inserted = $wpdb->insert( $table, [
        'full_name'   => $full_name,
        'email'       => $email,
        'phone'       => $phone,
        'device'      => $device,
        'repair'      => $repair,
        'visit_type'  => $visit_type,
        'address'     => $address,
        'pref_date'   => $pref_date ?: null,
        'pref_time'   => $pref_time,
        'description' => $description,
        'status'      => 'new',
    ], [
        '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
    ]);

    if ( ! $inserted ) {
        wp_send_json_error( [ 'message' => 'Something went wrong. Please try again or call us at 514-560-6449.' ] );
    }

    $booking_id = $wpdb->insert_id;

    // Format visit type for emails
    $visit_labels = [
        'in-store' => 'In-store drop-off (11990 Rue Sherbrooke Est, Montreal)',
        'mail-in'  => 'Mail-in repair',
        'at-home'  => 'At-home visit',
    ];
    $visit_label = $visit_labels[ $visit_type ] ?? $visit_type;

    $time_labels = [
        'morning'   => 'Morning (9am – 12pm)',
        'afternoon' => 'Afternoon (12pm – 4pm)',
        'evening'   => 'Evening (4pm – 6pm)',
    ];
    $time_label = $pref_time ? ( $time_labels[ $pref_time ] ?? $pref_time ) : 'No preference';

    // ── Notification email to shop ──
    $plugin_data  = jkrc_get_data();
    $plugin_settings = $plugin_data['settings'] ?? [];
    $shop_email   = get_option( 'admin_email' );
    $notify_email = ! empty( $plugin_settings['notify_email'] )
        ? sanitize_email( $plugin_settings['notify_email'] )
        : get_option( 'admin_email' ); // fallback to WP admin email
    $shop_subject = "New Repair Booking #{$booking_id} — {$full_name}";

    $shop_body  = "A new repair booking has been submitted via the website.\n\n";
    $shop_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $shop_body .= "BOOKING #{$booking_id}\n";
    $shop_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $shop_body .= "Customer:    {$full_name}\n";
    $shop_body .= "Email:       {$email}\n";
    $shop_body .= "Phone:       {$phone}\n\n";
    $shop_body .= "Device:      {$device}\n";
    $shop_body .= "Repair:      {$repair}\n";
    if ( $price ) $shop_body .= "Price:       {$price}\n";
    $shop_body .= "\nVisit type:  {$visit_label}\n";
    if ( $visit_type === 'mail-in' && $address ) {
        $shop_body .= "Ship to:     {$address}\n";
    }
    if ( $pref_date ) $shop_body .= "Pref. date:  {$pref_date}\n";
    $shop_body .= "Pref. time:  {$time_label}\n";
    if ( $description ) {
        $shop_body .= "\nIssue description:\n{$description}\n";
    }
    $shop_body .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $shop_body .= "View all bookings: " . admin_url( 'admin.php?page=jkrc-settings&tab=bookings' ) . "\n";

    $shop_html = jkrc_build_shop_email(
        $booking_id, $full_name, $email, $phone, $device, $repair, $price,
        $visit_label, $address, $pref_date, $time_label, $description, $visit_type
    );
    $mail_sent = wp_mail(
        $notify_email,
        $shop_subject,
        $shop_html,
        [ 'Content-Type: text/html; charset=UTF-8', "From: JK Tech Website <{$shop_email}>" ]
    );
    if ( ! $mail_sent ) {
        error_log( '[JKRC] Shop notification email FAILED to: ' . $notify_email );
    } else {
        error_log( '[JKRC] Shop notification email sent to: ' . $notify_email );
    }

    // ── Confirmation email to customer ──
    $cust_subject = 'Your repair request — JK Tech Solutions';
    $cust_body    = "Hi {$full_name},\n\n";
    $cust_body   .= "We have received your repair request and will confirm within 1 business day.\n\n";
    $cust_body   .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $cust_body   .= "YOUR REQUEST SUMMARY\n";
    $cust_body   .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $cust_body   .= "Device:      {$device}\n";
    $cust_body   .= "Repair:      {$repair}\n";
    if ( $price ) $cust_body .= "Est. price:  {$price}\n";
    $cust_body   .= "Visit type:  {$visit_label}\n";
    if ( $visit_type === 'mail-in' && $address ) {
        $cust_body .= "Ship to:     {$address}\n";
    }
    if ( $pref_date ) $cust_body .= "Pref. date:  {$pref_date}\n";
    $cust_body   .= "Pref. time:  {$time_label}\n";
    $cust_body   .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $cust_body   .= "WHAT HAPPENS NEXT\n\n";
    $cust_body   .= "1. We review your request and confirm within 1 business day\n";
    $cust_body   .= "2. We give you a diagnosis and quote before any work starts\n";
    $cust_body   .= "3. We fix it — most repairs done same day or within 48 hours\n";
    if ( $visit_type === 'mail-in' ) {
        $cust_body .= "4. We ship your device back once the repair is complete\n";
    } else {
        $cust_body .= "4. Pick up your device in store\n";
    }
    $cust_body   .= "\nNeed to reach us sooner?\n";
    $cust_body   .= "Call or text: 514-560-6449\n";
    $cust_body   .= "Email: info@jktechsolutions.ca\n\n";
    $cust_body   .= "JK Tech Solutions\n";
    $cust_body   .= "11990 Rue Sherbrooke Est, Montreal, QC H1B 1C5\n";
    $cust_body   .= "Mon–Fri 9am–6pm · Sat 9am–5pm\n";

    $cust_html = jkrc_build_customer_email(
        $full_name, $device, $repair, $price,
        $visit_label, $address, $pref_date, $time_label, $visit_type
    );
    $cust_sent = wp_mail(
        $email,
        $cust_subject,
        $cust_html,
        [ 'Content-Type: text/html; charset=UTF-8', "From: JK Tech Solutions <{$notify_email}>" ]
    );
    if ( ! $cust_sent ) {
        error_log( '[JKRC] Customer confirmation email FAILED to: ' . $email );
    } else {
        error_log( '[JKRC] Customer confirmation email sent to: ' . $email );
    }

    wp_send_json_success( [ 'message' => 'Booking submitted successfully.', 'id' => $booking_id ] );
}

/* ── Update booking status (admin) ── */
add_action( 'wp_ajax_jkrc_update_status', 'jkrc_ajax_update_status' );
function jkrc_ajax_update_status() {
    check_ajax_referer( 'jkrc_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

    $id     = intval( $_POST['id'] ?? 0 );
    $status = sanitize_text_field( $_POST['status'] ?? '' );
    $allowed = [ 'new', 'confirmed', 'in-progress', 'completed', 'cancelled' ];

    if ( ! $id || ! in_array( $status, $allowed ) ) {
        wp_send_json_error( [ 'message' => 'Invalid request.' ] );
    }

    global $wpdb;
    $table = $wpdb->prefix . JKRC_BOOK_TABLE;
    $wpdb->update( $table, [ 'status' => $status ], [ 'id' => $id ], [ '%s' ], [ '%d' ] );

    wp_send_json_success( [ 'message' => 'Status updated.' ] );
}


/* ── Live bookings poll (admin) ── */
add_action( 'wp_ajax_jkrc_poll_bookings', 'jkrc_ajax_poll_bookings' );
function jkrc_ajax_poll_bookings() {
    check_ajax_referer( 'jkrc_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

    global $wpdb;
    $table = $wpdb->prefix . JKRC_BOOK_TABLE;

    // Last known ID sent by client
    $last_id    = intval( $_POST['last_id'] ?? 0 );
    $new_count  = intval( $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'new'" ) );
    $total      = intval( $wpdb->get_var( "SELECT COUNT(*) FROM $table" ) );

    // Any bookings newer than last_id?
    $new_rows = $wpdb->get_results(
        $wpdb->prepare( "SELECT * FROM $table WHERE id > %d ORDER BY created_at DESC", $last_id )
    );

    $rows_html = '';
    if ( ! empty( $new_rows ) ) {
        $status_colors = [
            'new'         => '#2da5de',
            'confirmed'   => '#2369b1',
            'in-progress' => '#f39c12',
            'completed'   => '#27ae60',
            'cancelled'   => '#e74c3c',
        ];
        $statuses   = [ 'new', 'confirmed', 'in-progress', 'completed', 'cancelled' ];
        $visit_labels = [
            'in-store' => '🏪 In-store',
            'mail-in'  => '📦 Mail-in',
            'at-home'  => '🏠 At-home',
        ];
        $time_labels = [ 'morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening' ];

        ob_start();
        foreach ( $new_rows as $b ) :
            $color       = $status_colors[ $b->status ] ?? '#aaa';
            $visit_label = $visit_labels[ $b->visit_type ] ?? esc_html( $b->visit_type );
            $time_label  = $b->pref_time ? ( $time_labels[ $b->pref_time ] ?? $b->pref_time ) : '—';
        ?>
        <tr class="jkrc-booking-new-row" data-id="<?php echo $b->id; ?>">
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
        <?php
        endforeach;
        $rows_html = ob_get_clean();
    }

    // Latest booking ID in DB
    $max_id = intval( $wpdb->get_var( "SELECT MAX(id) FROM $table" ) );

    wp_send_json_success([
        'new_count' => $new_count,
        'total'     => $total,
        'max_id'    => $max_id,
        'has_new'   => ! empty( $new_rows ),
        'rows_html' => $rows_html,
    ]);
}

/* ── HTML Email Builder ── */
function jkrc_build_shop_email( $booking_id, $full_name, $email, $phone, $device, $repair, $price, $visit_label, $address, $pref_date, $pref_time_label, $description, $visit_type ) {
    $admin_url  = admin_url( 'admin.php?page=jkrc-settings&tab=bookings' );
    $price_html = $price ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;width:130px;">Est. Price</td><td style="padding:6px 0;font-size:14px;font-weight:700;color:#1b3f8b;">' . esc_html( $price ) . '</td></tr>' : '';
    $addr_html  = ( $visit_type === 'mail-in' && $address ) ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Ship To</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $address ) . '</td></tr>' : '';
    $date_html  = $pref_date ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Pref. Date</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( date( 'F j, Y', strtotime( $pref_date ) ) ) . '</td></tr>' : '';
    $desc_html  = $description ? '<div style="margin-top:20px;padding:16px;background:#f4f7fb;border-left:4px solid #2da5de;border-radius:0 8px 8px 0;"><p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a6475;">Issue Description</p><p style="margin:0;font-size:14px;color:#1d1d1b;line-height:1.6;">' . nl2br( esc_html( $description ) ) . '</p></div>' : '';

    return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  <!-- Header -->
  <tr><td style="background:linear-gradient(135deg,#1b3f8b 0%,#2369b1 100%);border-radius:12px 12px 0 0;padding:28px 36px;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td><p style="margin:0;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.3px;">JK Tech Solutions</p>
            <p style="margin:4px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">Montreal Repair Experts</p></td>
        <td align="right"><span style="background:rgba(255,255,255,0.15);color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:100px;white-space:nowrap;">New Booking #' . $booking_id . '</span></td>
      </tr>
    </table>
  </td></tr>

  <!-- Body -->
  <tr><td style="background:#ffffff;padding:32px 36px;">
    <p style="margin:0 0 24px;font-size:16px;color:#1d1d1b;line-height:1.6;">A new repair booking has been submitted on the website and is waiting for your review.</p>

    <!-- Customer info -->
    <div style="background:#f4f7fb;border-radius:10px;padding:20px 24px;margin-bottom:20px;">
      <p style="margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a6475;">Customer</p>
      <table cellpadding="0" cellspacing="0">
        <tr><td style="padding:4px 0;color:#5a6475;font-size:14px;width:80px;">Name</td><td style="padding:4px 0;font-size:14px;font-weight:600;color:#1d1d1b;">' . esc_html( $full_name ) . '</td></tr>
        <tr><td style="padding:4px 0;color:#5a6475;font-size:14px;">Email</td><td style="padding:4px 0;font-size:14px;"><a href="mailto:' . esc_attr( $email ) . '" style="color:#2da5de;text-decoration:none;">' . esc_html( $email ) . '</a></td></tr>
        <tr><td style="padding:4px 0;color:#5a6475;font-size:14px;">Phone</td><td style="padding:4px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $phone ) . '</td></tr>
      </table>
    </div>

    <!-- Repair info -->
    <div style="background:#f4f7fb;border-radius:10px;padding:20px 24px;margin-bottom:20px;">
      <p style="margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a6475;">Repair Details</p>
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;width:130px;">Device</td><td style="padding:6px 0;font-size:14px;font-weight:600;color:#1d1d1b;">' . esc_html( $device ) . '</td></tr>
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Repair(s)</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $repair ) . '</td></tr>
        ' . $price_html . '
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;border-top:1px solid #dce4f0;padding-top:12px;margin-top:6px;">Visit Type</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;border-top:1px solid #dce4f0;">' . esc_html( $visit_label ) . '</td></tr>
        ' . $addr_html . '
        ' . $date_html . '
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Pref. Time</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $pref_time_label ) . '</td></tr>
      </table>
    </div>

    ' . $desc_html . '

    <!-- CTA -->
    <div style="text-align:center;margin-top:28px;">
      <a href="' . $admin_url . '" style="display:inline-block;background:linear-gradient(135deg,#2da5de 0%,#1b3f8b 100%);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:14px 32px;border-radius:100px;letter-spacing:0.2px;">View Booking in Dashboard →</a>
    </div>
  </td></tr>

  <!-- Footer -->
  <tr><td style="background:#f4f7fb;border-radius:0 0 12px 12px;padding:20px 36px;border-top:1px solid #dce4f0;">
    <p style="margin:0;font-size:12px;color:#5a6475;text-align:center;line-height:1.6;">JK Tech Solutions · 11990 Rue Sherbrooke Est, Montreal, QC H1B 1C5<br>514-560-6449 · info@jktechsolutions.ca</p>
  </td></tr>

</table>
</td></tr>
</table>
</body></html>';
}

function jkrc_build_customer_email( $full_name, $device, $repair, $price, $visit_label, $address, $pref_date, $pref_time_label, $visit_type ) {
    $steps_html = $visit_type === 'mail-in'
        ? '<li style="padding:8px 0;border-bottom:1px solid #dce4f0;font-size:14px;color:#1d1d1b;">📦 Ship your device to us — we will provide the address when confirming</li>
           <li style="padding:8px 0;border-bottom:1px solid #dce4f0;font-size:14px;color:#1d1d1b;">🔧 We repair it and notify you when done</li>
           <li style="padding:8px 0;font-size:14px;color:#1d1d1b;">✈️ We ship it back to you</li>'
        : '<li style="padding:8px 0;border-bottom:1px solid #dce4f0;font-size:14px;color:#1d1d1b;">📞 We confirm your appointment within 1 business day</li>
           <li style="padding:8px 0;border-bottom:1px solid #dce4f0;font-size:14px;color:#1d1d1b;">🔧 We diagnose and quote before any work starts</li>
           <li style="padding:8px 0;font-size:14px;color:#1d1d1b;">✅ Most repairs are done same day or within 48 hours</li>';

    $price_row = $price ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;width:130px;">Est. Price</td><td style="padding:6px 0;font-size:14px;font-weight:700;color:#1b3f8b;">' . esc_html( $price ) . '</td></tr>' : '';
    $addr_row  = ( $visit_type === 'mail-in' && $address ) ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Ship To</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $address ) . '</td></tr>' : '';
    $date_row  = $pref_date ? '<tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Pref. Date</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( date( 'F j, Y', strtotime( $pref_date ) ) ) . '</td></tr>' : '';

    return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:32px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  <!-- Header -->
  <tr><td style="background:linear-gradient(135deg,#1b3f8b 0%,#2369b1 100%);border-radius:12px 12px 0 0;padding:28px 36px;">
    <p style="margin:0;font-size:22px;font-weight:800;color:#ffffff;">JK Tech Solutions</p>
    <p style="margin:4px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">Repair Request Confirmed</p>
  </td></tr>

  <!-- Body -->
  <tr><td style="background:#ffffff;padding:32px 36px;">
    <p style="margin:0 0 6px;font-size:20px;font-weight:700;color:#1b3f8b;">Hi ' . esc_html( $full_name ) . '!</p>
    <p style="margin:0 0 24px;font-size:15px;color:#5a6475;line-height:1.6;">Your repair request has been received. Heres a summary of what you booked:</p>

    <!-- Summary -->
    <div style="background:#f4f7fb;border-radius:10px;padding:20px 24px;margin-bottom:24px;">
      <p style="margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a6475;">Your Booking</p>
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;width:130px;">Device</td><td style="padding:6px 0;font-size:14px;font-weight:600;color:#1d1d1b;">' . esc_html( $device ) . '</td></tr>
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Repair(s)</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $repair ) . '</td></tr>
        ' . $price_row . '
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;border-top:1px solid #dce4f0;">Visit Type</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;border-top:1px solid #dce4f0;">' . esc_html( $visit_label ) . '</td></tr>
        ' . $addr_row . '
        ' . $date_row . '
        <tr><td style="padding:6px 0;color:#5a6475;font-size:14px;">Pref. Time</td><td style="padding:6px 0;font-size:14px;color:#1d1d1b;">' . esc_html( $pref_time_label ) . '</td></tr>
      </table>
    </div>

    <!-- What happens next -->
    <div style="margin-bottom:24px;">
      <p style="margin:0 0 12px;font-size:15px;font-weight:700;color:#1b3f8b;">What happens next</p>
      <ol style="margin:0;padding-left:20px;">' . $steps_html . '</ol>
    </div>

    <!-- Guarantee box -->
    <div style="background:#e8f0fb;border-radius:10px;padding:16px 20px;margin-bottom:24px;border-left:4px solid #1b3f8b;">
      <p style="margin:0;font-size:14px;color:#1b3f8b;line-height:1.6;"><strong>Our promise:</strong> We give you a diagnosis and quote before any work starts. If we cannot find the issue, you do not pay anything.</p>
    </div>

    <!-- Contact -->
    <p style="margin:0;font-size:14px;color:#5a6475;line-height:1.7;">Need to reach us sooner? Call or text <strong style="color:#1d1d1b;">514-560-6449</strong> or reply to this email.</p>
  </td></tr>

  <!-- Footer -->
  <tr><td style="background:#f4f7fb;border-radius:0 0 12px 12px;padding:20px 36px;border-top:1px solid #dce4f0;">
    <p style="margin:0;font-size:12px;color:#5a6475;text-align:center;line-height:1.6;">JK Tech Solutions · 11990 Rue Sherbrooke Est, Montreal, QC H1B 1C5<br>514-560-6449 · info@jktechsolutions.ca · Mon–Fri 9am–6pm · Sat 9am–5pm</p>
  </td></tr>

</table>
</td></tr>
</table>
</body></html>';
}

/* ── Sanitize data recursively ── */
function jkrc_sanitize_data( $data ) {
    if ( ! is_array( $data ) ) return sanitize_text_field( $data );
    $out = [];
    foreach ( $data as $key => $value ) {
        $clean_key = sanitize_key( $key );
        if ( $clean_key === 'icon' && is_string( $value ) ) {
            if ( strpos( $value, '<svg' ) !== false ) {
                $out[ $clean_key ] = wp_kses( $value, [
                    'svg'      => [ 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true, 'class' => true, 'style' => true ],
                    'path'     => [ 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'fill-rule' => true, 'clip-rule' => true ],
                    'circle'   => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ],
                    'rect'     => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true ],
                    'line'     => [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true ],
                    'polyline' => [ 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ],
                    'polygon'  => [ 'points' => true, 'fill' => true, 'stroke' => true ],
                    'g'        => [ 'fill' => true, 'stroke' => true, 'transform' => true, 'style' => true ],
                    'defs'     => [],
                    'title'    => [],
                ]);
            } elseif ( strpos( $value, 'data:image/' ) === 0 ) {
                $out[ $clean_key ] = $value;
            } else {
                $out[ $clean_key ] = sanitize_text_field( $value );
            }
        } elseif ( $clean_key === 'tiers' && is_array( $value ) ) {
            // Sanitize tier array — each item has slug and label
            $out[ $clean_key ] = array_map( function( $tier ) {
                return [
                    'slug'  => sanitize_key( $tier['slug'] ?? '' ),
                    'label' => sanitize_text_field( $tier['label'] ?? '' ),
                ];
            }, array_values( $value ) );
        } else {
            $out[ $clean_key ] = is_array( $value )
                ? jkrc_sanitize_data( $value )
                : sanitize_text_field( $value );
        }
    }
    return $out;
}
