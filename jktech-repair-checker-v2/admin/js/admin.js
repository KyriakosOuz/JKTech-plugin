/* JK Tech Repair Checker — Admin JS */
(function($) {
    'use strict';

    let data = jkrcAdmin.data;

    /* ── TABS ── */
    $(document).on('click', '.jkrc-tab', function() {
        $('.jkrc-tab').removeClass('active');
        $('.jkrc-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#tab-' + $(this).data('tab')).addClass('active');

        // Start/stop poll based on active tab
        if ($(this).data('tab') === 'bookings') {
            startPoll();
        } else {
            stopPoll();
        }
    });

    /* ── TOGGLE BODY ── */
    $(document).on('click', '.jkrc-toggle-body', function(e) {
        e.preventDefault();
        $(this).closest('.jkrc-category, .jkrc-brand, .jkrc-model').toggleClass('jkrc-collapsed');
    });

    /* ── DELETE CATEGORY ── */
    $(document).on('click', '.jkrc-delete-category', function(e) {
        e.preventDefault();
        if (!confirm('Remove this entire category and all its data?')) return;
        $(this).closest('.jkrc-category').remove();
    });

    /* ── DELETE BRAND ── */
    $(document).on('click', '.jkrc-delete-brand', function(e) {
        e.preventDefault();
        if (!confirm('Remove this brand and all its models?')) return;
        $(this).closest('.jkrc-brand').remove();
    });

    /* ── DELETE MODEL ── */
    $(document).on('click', '.jkrc-delete-model', function(e) {
        e.preventDefault();
        if (!confirm('Remove this model and all its repairs?')) return;
        $(this).closest('.jkrc-model').remove();
    });

    /* ── DELETE REPAIR ── */
    $(document).on('click', '.jkrc-delete-repair', function(e) {
        e.preventDefault();
        $(this).closest('.jkrc-repair').remove();
    });

    /* ── ICON FILE UPLOAD ── */
    $(document).on('change', '.jkrc-icon-file-input', function() {
        const file    = this.files[0];
        if (!file) return;
        const $block  = $(this).closest('.jkrc-icon-block');
        const $hidden = $block.find('.jkrc-cat-icon');
        const $preview= $block.find('.jkrc-icon-preview');
        const isSVG   = file.type === 'image/svg+xml' || file.name.endsWith('.svg');

        const reader = new FileReader();

        if (isSVG) {
            reader.onload = function(e) {
                let svgText = e.target.result;
                svgText = svgText.replace(/<\?xml[^?]*\?>/gi, '').trim();
                svgText = svgText.replace(/<svg([^>]*)>/, function(match, attrs) {
                    attrs = attrs.replace(/\s*(width|height)="[^"]*"/gi, '');
                    return '<svg' + attrs + ' width="100%" height="100%">';
                });
                $hidden.val(svgText);
                $preview.html(svgText);
                $block.find('.jkrc-icon-emoji-input').val('');
            };
            reader.readAsText(file);
        } else {
            reader.onload = function(e) {
                const dataUri = e.target.result;
                $hidden.val(dataUri);
                $preview.html('<img src="' + dataUri + '" alt="icon">');
                $block.find('.jkrc-icon-emoji-input').val('');
            };
            reader.readAsDataURL(file);
        }
        this.value = '';
    });

    /* ── EMOJI INPUT SYNC ── */
    $(document).on('input', '.jkrc-icon-emoji-input', function() {
        const val     = $(this).val().trim();
        const $block  = $(this).closest('.jkrc-icon-block');
        const $hidden = $block.find('.jkrc-cat-icon');
        const $preview= $block.find('.jkrc-icon-preview');
        if (val) {
            $hidden.val(val);
            $preview.html('<span class="jkrc-icon-emoji">' + val + '</span>');
        }
    });

    /* ── CLEAR ICON ── */
    $(document).on('click', '.jkrc-clear-icon', function(e) {
        e.preventDefault();
        const $block  = $(this).closest('.jkrc-icon-block');
        $block.find('.jkrc-cat-icon').val('');
        $block.find('.jkrc-icon-preview').html('<span class="jkrc-icon-emoji">🔧</span>');
        $block.find('.jkrc-icon-emoji-input').val('');
    });

    /* ── TIER PILL PICKER ── */
    $(document).on('click', '.jkrc-tier-btn', function(e) {
        e.preventDefault();
        const $row = $(this).closest('.jkrc-tier-picker');
        $row.find('.jkrc-tier-btn').removeClass('active');
        $(this).addClass('active');
        $row.find('.jkrc-repair-tier').val($(this).data('value'));
    });

    /* ── ADD CATEGORY ── */
    $('#jkrc-add-category').on('click', function() {
        const $tpl = $('.jkrc-category.jkrc-template').clone().removeClass('jkrc-template');
        $tpl.attr('data-ci', Date.now());
        $('#jkrc-categories-list').append($tpl);
        $tpl.find('.jkrc-cat-label').focus();
    });

    /* ── ADD BRAND ── */
    $(document).on('click', '.jkrc-add-brand', function(e) {
        e.preventDefault();
        const $tpl = $('.jkrc-brand.jkrc-template').clone().removeClass('jkrc-template');
        $tpl.attr('data-bi', Date.now());
        $(this).closest('.jkrc-category-body').find('.jkrc-brands-list').append($tpl);
        $tpl.find('.jkrc-brand-name').focus();
    });

    /* ── ADD MODEL ── */
    $(document).on('click', '.jkrc-add-model', function(e) {
        e.preventDefault();
        const $tpl = $('.jkrc-model.jkrc-template').clone().removeClass('jkrc-template');
        $tpl.attr('data-mi', Date.now());
        $(this).closest('.jkrc-brand-body').find('.jkrc-models-list').append($tpl);
        $tpl.find('.jkrc-model-name').focus();
    });

    /* ── ADD REPAIR ── */
    $(document).on('click', '.jkrc-add-repair', function(e) {
        e.preventDefault();
        const $tpl = $('.jkrc-repair.jkrc-template').clone().removeClass('jkrc-template');
        $tpl.attr('data-ri', Date.now());
        $(this).closest('.jkrc-model-body').find('.jkrc-repairs-list').append($tpl);
        $tpl.find('.jkrc-repair-name').focus();
    });

    /* ── TIER MANAGER ── */
    $(document).on('click', '#jkrc-add-tier', function(e) {
        e.preventDefault();
        const ti   = $('#jkrc-tiers-list .jkrc-tier-row-admin').length;
        const $row = $('<div class="jkrc-tier-row-admin" data-ti="' + ti + '">' +
            '<span class="jkrc-drag-handle">⠿</span>' +
            '<div class="jkrc-tier-color-dot jkrc-tier-dot-' + ti + '"></div>' +
            '<input type="text" class="jkrc-field jkrc-tier-slug" placeholder="slug (e.g. premium)" style="width:130px;">' +
            '<input type="text" class="jkrc-field jkrc-tier-label" placeholder="Label shown to customers">' +
            '<button class="jkrc-btn jkrc-btn-danger jkrc-btn-sm jkrc-delete-tier">✕ Remove</button>' +
            '</div>');
        $('#jkrc-tiers-list').append($row);
        $row.find('.jkrc-tier-slug').focus();
    });

    $(document).on('click', '.jkrc-delete-tier', function(e) {
        e.preventDefault();
        if ($('#jkrc-tiers-list .jkrc-tier-row-admin').length <= 1) {
            alert('You must keep at least one tier.');
            return;
        }
        $(this).closest('.jkrc-tier-row-admin').remove();
    });

    $(document).on('click', '#jkrc-save-tiers', function(e) {
        e.preventDefault();
        const $btn    = $(this);
        const $status = $('#jkrc-save-tiers-status');
        const tiers   = [];

        $('#jkrc-tiers-list .jkrc-tier-row-admin').each(function() {
            const slug  = $(this).find('.jkrc-tier-slug').val().trim().replace(/\s+/g, '-').toLowerCase();
            const label = $(this).find('.jkrc-tier-label').val().trim();
            if (slug && label) tiers.push({ slug: slug, label: label });
        });

        if (tiers.length === 0) {
            $status.addClass('error').text('Add at least one tier.');
            return;
        }

        const current = readData();
        current.settings = current.settings || {};
        current.settings.contact_url  = $('#setting-contact_url').val().trim();
        current.settings.contact_text = $('#setting-contact_text').val().trim();
        current.settings.notify_email = $('#setting-notify_email').val().trim();
        current.settings.footer_note  = $('#setting-footer_note').val().trim();
        current.settings.tiers        = tiers;

        $btn.text('Saving…').prop('disabled', true);
        $status.text('').removeClass('error');

        $.post(jkrcAdmin.ajaxurl, {
            action: 'jkrc_save',
            nonce:  jkrcAdmin.nonce,
            data:   JSON.stringify(current)
        })
        .done(function(res) {
            if (res.success) {
                $status.text('✓ Tiers saved! Reload to see updated tier buttons.');
                data = current;
                setTimeout(() => $status.text(''), 5000);
            } else {
                $status.addClass('error').text('Error: ' + (res.data?.message || 'Unknown'));
            }
        })
        .fail(function() {
            $status.addClass('error').text('Save failed.');
        })
        .always(function() {
            $btn.text('💾 Save Tiers').prop('disabled', false);
        });
    });

    $(document).on('input', '.jkrc-tier-label', function() {
        const $row  = $(this).closest('.jkrc-tier-row-admin');
        const $slug = $row.find('.jkrc-tier-slug');
        if (!$slug.val()) {
            $slug.val($(this).val().trim().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, ''));
        }
    });

    /* ── READ CURRENT DOM STATE INTO JS OBJECT ── */
    function readData() {
        const out = { categories: [], settings: data.settings || {} };

        $('#jkrc-categories-list .jkrc-category:not(.jkrc-template)').each(function() {
            const cat = {
                id:     $(this).find('> .jkrc-category-header .jkrc-cat-id').val().trim(),
                label:  $(this).find('> .jkrc-category-header .jkrc-cat-label').val().trim(),
                icon:   $(this).find('> .jkrc-category-header .jkrc-cat-icon').val().trim(),
                brands: []
            };
            $(this).find('> .jkrc-category-body .jkrc-brand:not(.jkrc-template)').each(function() {
                const brand = {
                    name:   $(this).find('> .jkrc-brand-header .jkrc-brand-name').val().trim(),
                    models: []
                };
                $(this).find('> .jkrc-brand-body .jkrc-model:not(.jkrc-template)').each(function() {
                    const model = {
                        name:    $(this).find('> .jkrc-model-header .jkrc-model-name').val().trim(),
                        repairs: []
                    };
                    $(this).find('> .jkrc-model-body .jkrc-repair:not(.jkrc-template)').each(function() {
                        model.repairs.push({
                            name:  $(this).find('.jkrc-repair-name').val().trim(),
                            price: $(this).find('.jkrc-repair-price').val().trim(),
                            note:  $(this).find('.jkrc-repair-note').val().trim(),
                            tier:  $(this).find('.jkrc-repair-tier').val()
                        });
                    });
                    if (model.name) brand.models.push(model);
                });
                if (brand.name) cat.brands.push(brand);
            });
            if (cat.label) out.categories.push(cat);
        });

        return out;
    }

    /* ── SAVE ALL ── */
    $('#jkrc-save-all').on('click', function() {
        const $btn    = $(this);
        const $status = $('#jkrc-save-status');
        const payload = readData();

        $btn.text('Saving…').prop('disabled', true);
        $status.text('').removeClass('error');

        $.post(jkrcAdmin.ajaxurl, {
            action: 'jkrc_save',
            nonce:  jkrcAdmin.nonce,
            data:   JSON.stringify(payload)
        })
        .done(function(res) {
            if (res.success) {
                $status.text('✓ Saved!');
                data = payload;
                setTimeout(() => $status.text(''), 3000);
            } else {
                $status.addClass('error').text('Error: ' + (res.data?.message || 'Unknown error'));
            }
        })
        .fail(function() {
            $status.addClass('error').text('Save failed. Please try again.');
        })
        .always(function() {
            $btn.text('💾 Save All Changes').prop('disabled', false);
        });
    });

    /* ── SAVE SETTINGS ── */
    $('#jkrc-save-settings').on('click', function() {
        const $btn    = $(this);
        const $status = $('#jkrc-save-settings-status');
        const current = readData();

        current.settings = {
            contact_url:  $('#setting-contact_url').val().trim(),
            contact_text: $('#setting-contact_text').val().trim(),
            notify_email: $('#setting-notify_email').val().trim(),
            footer_note:  $('#setting-footer_note').val().trim(),
            tiers:        data.settings.tiers || []
        };

        $btn.text('Saving…').prop('disabled', true);
        $status.text('').removeClass('error');

        $.post(jkrcAdmin.ajaxurl, {
            action: 'jkrc_save',
            nonce:  jkrcAdmin.nonce,
            data:   JSON.stringify(current)
        })
        .done(function(res) {
            if (res.success) {
                $status.text('✓ Saved!');
                data = current;
                setTimeout(() => $status.text(''), 3000);
            } else {
                $status.addClass('error').text('Error: ' + (res.data?.message || 'Unknown error'));
            }
        })
        .fail(function() {
            $status.addClass('error').text('Save failed.');
        })
        .always(function() {
            $btn.text('💾 Save Settings').prop('disabled', false);
        });
    });

    /* ── BOOKING STATUS UPDATE ── */
    $(document).on('change', '.jkrc-status-select', function() {
        var id     = $(this).data('id');
        var status = $(this).val();
        var fd     = { action: 'jkrc_update_status', nonce: jkrcAdmin.nonce, id: id, status: status };
        $.post(jkrcAdmin.ajaxurl, fd, function(res) {
            if (res.success) {
                var $saved = $('#jkrc-saved-' + id);
                $saved.show();
                setTimeout(function() { $saved.hide(); }, 2000);
            }
        });
    });

    /* ══════════════════════════════════════
       LIVE BOOKINGS POLL
    ══════════════════════════════════════ */

    var pollInterval = null;
    var lastKnownId  = 0;
    var pollActive   = false;
    var POLL_MS      = 15000;

    function isBookingsTabActive() {
        return $('#tab-bookings').hasClass('active');
    }

    function startPoll() {
        if (pollInterval) return;

        // Seed lastKnownId from highest ID in current table
        var maxId = 0;
        $('#tab-bookings .jkrc-bookings-table tbody tr').each(function() {
            var id = parseInt($(this).find('td:first strong').text(), 10);
            if (id > maxId) maxId = id;
        });
        lastKnownId = maxId;

        doPoll(); // immediate first check
        pollInterval = setInterval(doPoll, POLL_MS);
    }

    function stopPoll() {
        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
    }

    function doPoll() {
        if (pollActive) return;
        pollActive = true;

        $.post(jkrcAdmin.ajaxurl, {
            action:  'jkrc_poll_bookings',
            nonce:   jkrcAdmin.nonce,
            last_id: lastKnownId
        })
        .done(function(res) {
            if (!res.success) return;
            var d = res.data;

            // Update new count badge on tab
            updateBookingsBadge(d.new_count);

            // Prepend new rows if any
            if (d.has_new && d.rows_html) {
                var $tbody = $('#tab-bookings .jkrc-bookings-table tbody');
                if ($tbody.length) {
                    var $new = $(d.rows_html);
                    $new.addClass('jkrc-live-new-row');
                    $tbody.prepend($new);
                    showLiveBanner($new.length);
                    setTimeout(function() { $new.removeClass('jkrc-live-new-row'); }, 4000);
                } else {
                    location.reload();
                }
                lastKnownId = d.max_id;
            }

            // Update total count
            var $heading = $('#tab-bookings .jkrc-card h2');
            if ($heading.length) {
                $heading.html('Repair Bookings <span style="font-size:14px;font-weight:400;color:#5a6475;">(' + d.total + ' total)</span>');
            }
        })
        .always(function() { pollActive = false; });
    }

    function updateBookingsBadge(count) {
        var $tab = $('[data-tab="bookings"]');
        $tab.find('.jkrc-live-badge').remove();
        if (count > 0) {
            $tab.append('<span class="jkrc-live-badge" style="background:#e74c3c;color:#fff;border-radius:10px;padding:2px 7px;font-size:11px;margin-left:4px;">' + count + '</span>');
        }
    }

    function showLiveBanner(count) {
        $('#jkrc-live-banner').remove();
        var $banner = $('<div id="jkrc-live-banner">' +
            '<span class="jkrc-live-dot"></span>' +
            (count === 1 ? '1 new booking just arrived' : count + ' new bookings just arrived') +
            '<button id="jkrc-live-banner-close">✕</button>' +
            '</div>');
        $('#tab-bookings .jkrc-card').prepend($banner);
        $banner.hide().slideDown(300);
        setTimeout(function() { $banner.slideUp(300, function() { $(this).remove(); }); }, 8000);
    }

    $(document).on('click', '#jkrc-live-banner-close', function() {
        $('#jkrc-live-banner').slideUp(200, function() { $(this).remove(); });
    });

    // On page load: always do one immediate poll to seed the badge count
    // Then start continuous polling only if bookings tab is active
    $(document).ready(function() {
        // Seed badge immediately via one silent poll
        $.post(jkrcAdmin.ajaxurl, {
            action:  'jkrc_poll_bookings',
            nonce:   jkrcAdmin.nonce,
            last_id: 999999999 // large number so no rows are returned, just counts
        }).done(function(res) {
            if (res.success) updateBookingsBadge(res.data.new_count);
        });

        if (isBookingsTabActive()) startPoll();
    });

})(jQuery);
