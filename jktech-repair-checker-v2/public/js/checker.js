/* JK Tech Repair Price Checker V2 — Public JS */
(function() {
    'use strict';

    const data     = typeof jkrcData  !== 'undefined' ? jkrcData  : { categories: [], settings: {} };
    const ajaxCfg  = typeof jkrcAjax !== 'undefined' ? jkrcAjax  : { ajaxurl: '', nonce: '' };
    const settings = data.settings || {};

    let selCatIndex    = null;
    let selBrandIndex  = null;
    let selModelIndex  = null;
    var selRepairIndexes = []; // array of selected repair indexes

    /* ── INIT ── */
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('jkrc-app')) return;
        setMinDate();
        renderDeviceCards();
        bindEvents();
    });

    /* ── Set minimum date to today ── */
    function setMinDate() {
        var dateInput = document.getElementById('jkrc-date');
        if (dateInput) {
            var today = new Date();
            var yyyy  = today.getFullYear();
            var mm    = String(today.getMonth() + 1).padStart(2, '0');
            var dd    = String(today.getDate()).padStart(2, '0');
            dateInput.min = yyyy + '-' + mm + '-' + dd;
        }
    }

    /* ── RENDER DEVICE CARDS ── */
    function renderDeviceCards() {
        var grid = document.getElementById('jkrc-device-grid');
        if (!grid) return;
        grid.innerHTML = '';
        data.categories.forEach(function(cat, i) {
            var card = document.createElement('div');
            card.className = 'jkrc-option-card';
            card.innerHTML = '<div class="jkrc-icon">' + renderIcon(cat.icon) + '</div>'
                           + '<div class="jkrc-card-label">' + escHtml(cat.label) + '</div>';
            card.addEventListener('click', function() { selectDevice(i); });
            grid.appendChild(card);
        });
    }

    /* ── RENDER ICON ── */
    function renderIcon(icon) {
        if (!icon) return '🔧';
        if (icon.indexOf('<svg') !== -1) return icon;
        if (icon.indexOf('data:image/') !== -1) return '<img src="' + escHtml(icon) + '" alt="" style="width:32px;height:32px;object-fit:contain;">';
        return escHtml(icon);
    }

    /* ── SELECT DEVICE ── */
    function selectDevice(catIndex) {
        selCatIndex      = catIndex;
        selBrandIndex    = null;
        selModelIndex    = null;
        selRepairIndexes = [];

        var cat = data.categories[catIndex];
        document.getElementById('jkrc-step2-title').textContent = 'Select your brand — ' + cat.label;
        renderBrands(cat);
        showStep(2);
        updateStepIndicator(2);
    }

    /* ── RENDER BRANDS ── */
    function renderBrands(cat) {
        var grid = document.getElementById('jkrc-brand-grid');
        grid.innerHTML = '';
        (cat.brands || []).forEach(function(brand, i) {
            var pill = document.createElement('div');
            pill.className = 'jkrc-pill';
            pill.textContent = brand.name;
            pill.addEventListener('click', function() { selectBrand(i); });
            grid.appendChild(pill);
        });
    }

    /* ── SELECT BRAND ── */
    function selectBrand(brandIndex) {
        selBrandIndex    = brandIndex;
        selModelIndex    = null;
        selRepairIndexes = [];

        var brand = data.categories[selCatIndex].brands[brandIndex];
        document.getElementById('jkrc-step3-title').textContent = 'Select your model — ' + brand.name;
        renderModels(brand);
        showStep(3);
        updateStepIndicator(3);
    }

    /* ── RENDER MODELS ── */
    function renderModels(brand) {
        var grid = document.getElementById('jkrc-model-grid');
        grid.innerHTML = '';
        (brand.models || []).forEach(function(model, i) {
            var pill = document.createElement('div');
            pill.className = 'jkrc-pill';
            pill.textContent = model.name;
            pill.addEventListener('click', function() { selectModel(i); });
            grid.appendChild(pill);
        });
    }

    /* ── SELECT MODEL ── */
    function selectModel(modelIndex) {
        selModelIndex    = modelIndex;
        selRepairIndexes = [];

        var model = data.categories[selCatIndex].brands[selBrandIndex].models[modelIndex];
        renderPrices(model);
        showStep(4);
        updateStepIndicator(4);
    }

    /* ── RENDER PRICES ── */
    function renderPrices(model) {
        document.getElementById('jkrc-table-title').textContent = model.name + ' — Repair Prices';

        var tbody = document.getElementById('jkrc-price-tbody');
        tbody.innerHTML = '';
        selRepairIndexes = [];
        updateBookSelectedBtn();

        var contactText = settings.contact_text || 'Contact us →';
        var contactUrl  = settings.contact_url  || '/contact';

        (model.repairs || []).forEach(function(repair, ri) {
            var tr   = document.createElement('tr');
            var tier = repair.tier || '';

            // Build tier label map from settings (dynamic tiers)
            var tierMap = {};
            var tiers   = (settings.tiers || [
                { slug: 'standard', label: 'Standard' },
                { slug: 'oem',      label: 'OEM'      },
                { slug: 'original', label: 'Original' },
            ]);
            tiers.forEach(function(t, idx) {
                tierMap[t.slug] = { label: t.label.toUpperCase(), idx: idx };
            });
            var tierInfo = tier ? tierMap[tier] : null;
            var tierHtml = tierInfo
                ? '<span class="jkrc-tier-badge jkrc-tier-dyn jkrc-tier-idx-' + tierInfo.idx + '">' + escHtml(tierInfo.label) + '</span>'
                : (tier ? '<span class="jkrc-tier-badge">' + escHtml(tier.toUpperCase()) + '</span>' : '');

            var noteHtml = repair.note
                ? '<div class="jkrc-repair-note">' + escHtml(repair.note) + '</div>'
                : '';

            var priceHtml = repair.price === 'Contact us'
                ? '<a href="' + escHtml(contactUrl) + '" class="jkrc-contact-price">' + escHtml(contactText) + '</a>'
                : '<span class="jkrc-price-badge">' + escHtml(repair.price) + '</span>';

            // Checkbox cell — only for bookable repairs
            var checkHtml = repair.price !== 'Contact us'
                ? '<label class="jkrc-check-label"><input type="checkbox" class="jkrc-repair-check" data-ri="' + ri + '"><span class="jkrc-check-box"></span></label>'
                : '';

            tr.innerHTML = '<td class="jkrc-check-cell">' + checkHtml + '</td>'
                         + '<td><div class="jkrc-repair-name">' + escHtml(repair.name) + tierHtml + '</div>' + noteHtml + '</td>'
                         + '<td>' + priceHtml + '</td>';

            // Click whole row to toggle checkbox (except contact-us rows)
            if (repair.price !== 'Contact us') {
                tr.style.cursor = 'pointer';
                tr.addEventListener('click', function(e) {
                    if (e.target.tagName === 'INPUT') return;
                    var cb = this.querySelector('.jkrc-repair-check');
                    if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
                });
            }

            tbody.appendChild(tr);
        });

        // Bind checkboxes
        tbody.querySelectorAll('.jkrc-repair-check').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var ri = parseInt(this.getAttribute('data-ri'), 10);
                var tr = this.closest('tr');
                if (this.checked) {
                    if (selRepairIndexes.indexOf(ri) === -1) selRepairIndexes.push(ri);
                    tr.classList.add('jkrc-row-selected');
                } else {
                    selRepairIndexes = selRepairIndexes.filter(function(i) { return i !== ri; });
                    tr.classList.remove('jkrc-row-selected');
                }
                updateBookSelectedBtn();
            });
        });

        // Footer note
        var note   = settings.footer_note || '';
        var noteEl = document.getElementById('jkrc-footer-note');
        if (note && noteEl) {
            noteEl.innerHTML = escHtml(note);
            noteEl.style.display = '';
        } else if (noteEl) {
            noteEl.style.display = 'none';
        }
    }

    /* ── UPDATE BOOK SELECTED BUTTON ── */
    function updateBookSelectedBtn() {
        var btn = document.getElementById('jkrc-book-selected-btn');
        if (!btn) return;
        var count = selRepairIndexes.length;
        if (count === 0) {
            btn.disabled = true;
            btn.textContent = 'Select repairs to book';
        } else {
            btn.disabled = false;
            btn.textContent = count === 1 ? 'Book 1 repair →' : 'Book ' + count + ' repairs →';
        }
    }

    /* ── BOOK SELECTED REPAIRS → go to booking ── */
    function bookSelected() {
        if (selRepairIndexes.length === 0) return;

        var cat   = data.categories[selCatIndex];
        var brand = cat.brands[selBrandIndex];
        var model = brand.models[selModelIndex];

        var deviceLabel  = cat.label + ' - ' + brand.name + ' ' + model.name;
        var repairLabels = selRepairIndexes.map(function(ri) {
            var r = model.repairs[ri];
            return r.name + (r.tier ? ' (' + r.tier + ')' : '');
        });
        var priceLabels = selRepairIndexes.map(function(ri) {
            return model.repairs[ri].price || '';
        }).filter(Boolean);

        var repairStr = repairLabels.join(', ');
        var priceStr  = priceLabels.join(' + ');

        // Update summary strip
        document.getElementById('jkrc-summary-device').textContent = deviceLabel;
        document.getElementById('jkrc-summary-repair').textContent = repairStr;
        var priceEl = document.getElementById('jkrc-summary-price');
        priceEl.textContent = priceStr;
        priceEl.style.display = priceStr ? '' : 'none';

        // Render selected repairs list in summary
        var listEl = document.getElementById('jkrc-summary-repairs-list');
        if (listEl) {
            listEl.innerHTML = '';
            selRepairIndexes.forEach(function(ri) {
                var r  = model.repairs[ri];
                var li = document.createElement('div');
                li.className = 'jkrc-summary-repair-item';
                li.innerHTML = '<span class="jkrc-summary-repair-name">' + escHtml(r.name) + (r.tier ? ' <em>(' + escHtml(r.tier) + ')</em>' : '') + '</span>'
                             + (r.price ? '<span class="jkrc-summary-repair-price">' + escHtml(r.price) + '</span>' : '');
                listEl.appendChild(li);
            });
        }

        // Pre-fill hidden fields
        document.getElementById('jkrc-hidden-device').value = deviceLabel;
        document.getElementById('jkrc-hidden-repair').value = repairStr;
        document.getElementById('jkrc-hidden-price').value  = priceStr;

        // Reset form state
        document.getElementById('jkrc-booking-form').style.display = '';
        document.getElementById('jkrc-booking-success').style.display = 'none';
        hideFormError();

        showStep(5);
        updateStepIndicator(5);

        var app = document.getElementById('jkrc-app');
        if (app) app.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ── BIND EVENTS ── */
    function bindEvents() {
        document.getElementById('jkrc-back2')?.addEventListener('click', function() { goBack(1); });
        document.getElementById('jkrc-back3')?.addEventListener('click', function() { goBack(2); });
        document.getElementById('jkrc-back4')?.addEventListener('click', function() { goBack(3); });
        document.getElementById('jkrc-back5')?.addEventListener('click', function() { goBack(4); });
        document.getElementById('jkrc-reset')?.addEventListener('click', function() { resetAll(); });
        document.getElementById('jkrc-reset-success')?.addEventListener('click', function() { resetAll(); });
        document.getElementById('jkrc-book-selected-btn')?.addEventListener('click', function() { bookSelected(); });

        // Visit type toggle for address field
        document.getElementById('jkrc-visit-type')?.addEventListener('change', function() {
            var addrGroup = document.getElementById('jkrc-address-group');
            var addrInput = document.getElementById('jkrc-address');
            if (this.value === 'mail-in') {
                addrGroup.style.display = '';
                addrInput.setAttribute('required', 'required');
            } else {
                addrGroup.style.display = 'none';
                addrInput.removeAttribute('required');
                addrInput.value = '';
            }
        });

        // Booking form submit
        document.getElementById('jkrc-booking-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBooking(this);
        });
    }

    /* ── SUBMIT BOOKING ── */
    function submitBooking(form) {
        hideFormError();

        var name      = form.querySelector('#jkrc-name').value.trim();
        var email     = form.querySelector('#jkrc-email').value.trim();
        var phone     = form.querySelector('#jkrc-phone').value.trim();
        var visitType = form.querySelector('#jkrc-visit-type').value;
        var address   = form.querySelector('#jkrc-address').value.trim();

        // Basic validation
        if (!name || !email || !phone || !visitType) {
            showFormError('Please fill in all required fields.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFormError('Please enter a valid email address.');
            return;
        }

        if (visitType === 'mail-in' && !address) {
            showFormError('Please enter your shipping address for mail-in repairs.');
            return;
        }

        // Disable button + show loading
        var submitBtn  = document.getElementById('jkrc-submit-btn');
        var submitText = submitBtn.querySelector('.jkrc-submit-text');
        var submitLoad = submitBtn.querySelector('.jkrc-submit-loading');
        submitBtn.disabled  = true;
        submitText.style.display = 'none';
        submitLoad.style.display = '';

        // Build form data
        var fd = new FormData(form);
        fd.append('action', 'jkrc_book');
        fd.append('nonce',  ajaxCfg.nonce);

        fetch(ajaxCfg.ajaxurl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                submitBtn.disabled  = false;
                submitText.style.display = '';
                submitLoad.style.display = 'none';

                if (res.success) {
                    form.style.display = 'none';
                    document.getElementById('jkrc-booking-success').style.display = '';
                } else {
                    showFormError(res.data?.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(function() {
                submitBtn.disabled  = false;
                submitText.style.display = '';
                submitLoad.style.display = 'none';
                showFormError('Connection error. Please try again or call 514-560-6449.');
            });
    }

    /* ── FORM ERROR HELPERS ── */
    function showFormError(msg) {
        var el = document.getElementById('jkrc-form-error');
        if (el) { el.textContent = msg; el.style.display = ''; }
    }

    function hideFormError() {
        var el = document.getElementById('jkrc-form-error');
        if (el) { el.textContent = ''; el.style.display = 'none'; }
    }

    /* ── GO BACK ── */
    function goBack(toStep) {
        showStep(toStep);
        updateStepIndicator(toStep);
        if (toStep <= 1) { selCatIndex = null; selBrandIndex = null; selModelIndex = null; selRepairIndexes = []; }
        if (toStep <= 2) { selBrandIndex = null; selModelIndex = null; selRepairIndexes = []; }
        if (toStep <= 3) { selModelIndex = null; selRepairIndexes = []; }
        if (toStep <= 4) { selRepairIndexes = []; }
    }

    /* ── RESET ── */
    function resetAll() {
        selCatIndex = null; selBrandIndex = null; selModelIndex = null; selRepairIndexes = [];
        // Reset form
        var form = document.getElementById('jkrc-booking-form');
        if (form) { form.reset(); form.style.display = ''; }
        document.getElementById('jkrc-booking-success').style.display = 'none';
        document.getElementById('jkrc-address-group').style.display = 'none';
        hideFormError();
        showStep(1);
        updateStepIndicator(1);
    }

    /* ── SHOW STEP ── */
    function showStep(n) {
        [1, 2, 3, 4, 5].forEach(function(i) {
            var el = document.getElementById('jkrc-step' + i);
            if (el) el.classList.toggle('jkrc-visible', i === n);
        });
    }

    /* ── UPDATE STEP INDICATOR ── */
    function updateStepIndicator(active) {
        // Steps 1-4 visible in indicator, step 5 maps to indicator step 5
        [1, 2, 3, 4, 5].forEach(function(i) {
            var circle = document.getElementById('jkrc-sc' + i);
            var label  = document.getElementById('jkrc-sl' + i);
            if (!circle || !label) return;

            circle.classList.remove('active', 'done');
            label.classList.remove('active', 'done');

            if (i < active) {
                circle.classList.add('done');
                label.classList.add('done');
                circle.textContent = '✓';
            } else if (i === active) {
                circle.classList.add('active');
                label.classList.add('active');
                circle.textContent = i;
            } else {
                circle.textContent = i;
            }

            if (i < 5) {
                var con = document.getElementById('jkrc-con' + i);
                if (con) con.classList.toggle('done', i < active);
            }
        });
    }

    /* ── ESCAPE HTML ── */
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

})();
