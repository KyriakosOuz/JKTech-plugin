# JK Tech — Repair Price Checker

![Plugin Version](https://img.shields.io/badge/version-2.1.3-1b3f8b?style=flat-square)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b?style=flat-square&logo=wordpress&logoColor=white)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B-96588a?style=flat-square&logo=woocommerce&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Proprietary-f2295b?style=flat-square)
![Built By](https://img.shields.io/badge/built%20by-CoreConcepts.design-2da5de?style=flat-square)

> A fully custom WordPress plugin built exclusively for **JK Tech Solutions** — a Montreal-based IT repair shop. Gives customers an interactive, multi-step repair price estimator with real-time quote generation, quality tier comparison, and direct booking integration.

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [How It Works](#-how-it-works)
- [Admin Panel](#-admin-panel)
- [Shortcode](#-shortcode)
- [Email Notifications](#-email-notifications)
- [Installation](#-installation)
- [File Structure](#-file-structure)
- [Changelog](#-changelog)
- [Built By](#-built-by)

---

## 🔍 Overview

The **JK Tech Repair Price Checker** is a bespoke WordPress plugin developed from scratch for JK Tech Solutions. It replaces generic contact forms with an intelligent, guided estimator that helps customers understand repair costs before they walk through the door.

The plugin is fully integrated with the JK Tech admin panel — no coding required to manage pricing, devices, tiers, or bookings. Every booking triggers branded HTML email notifications to both the shop and the customer.

---

## ✨ Features

### Frontend
- 🔢 **5-step interactive estimator** — Device → Brand → Model → Repairs → Booking
- ✅ **Multi-select repair checkboxes** — customers can select multiple repairs at once
- 💰 **Live price calculation** — total updates in real time as repairs are selected
- 🏷️ **Quality tier comparison** — displays pricing across all configured tiers (e.g. Standard, Premium)
- 📋 **"Book N repairs →" action bar** — activates automatically when repairs are selected
- 📱 **Fully responsive** — works across desktop, tablet, and mobile
- 🎨 **Brand-matched styling** — uses JK Tech's design system and colour variables

### Admin Panel (5 tabs)
- 📦 **Categories & Prices** — manage all device types, brands, models, and repair pricing per tier
- 📬 **Bookings** — live booking dashboard with 15-second auto-poll, new entry flash animation, and auto-dismiss banners
- ⚙️ **Settings** — configure notification email, shop name, and booking redirect URL
- 🔧 **Shortcode** — copy-ready embed code with usage instructions
- 📖 **Docs** — full in-admin documentation for non-technical users

---

## 🔄 How It Works

```
Step 1 → Customer selects device type     (e.g. Laptop, Smartphone)
Step 2 → Customer selects brand           (e.g. Apple, Samsung)
Step 3 → Customer selects model           (e.g. MacBook Air M1)
Step 4 → Customer selects repairs         (multi-select with live price total)
Step 5 → Customer fills booking form      (name, email, phone, notes)
         ↓
         Booking saved to database
         ↓
         HTML email sent to shop + customer
         ↓
         Customer redirected to booking confirmation page
```

---

## 🛠️ Admin Panel

Access via **WordPress Admin → JK Repair Checker**

### Categories & Prices Tab
Add and manage the full device tree:
- Create device **categories** (e.g. Computers & Laptops, Cell Phones)
- Add **brands** under each category
- Add **models** under each brand
- Set **repair prices per tier** for each model

### Bookings Tab
- View all incoming booking requests in a live table
- New bookings prepend at the top with a **green flash animation**
- Auto-polls every **15 seconds** — no page refresh needed
- Banner notification auto-dismisses after **8 seconds**
- Each row shows: name, email, phone, device, selected repairs, total, and timestamp

### Settings Tab
| Setting | Description |
|---|---|
| Notification Email | Where booking alerts are sent |
| Shop Name | Used in email templates |
| Booking Redirect URL | Where customers land after submitting |

### Shortcode Tab
Provides the embed shortcode and basic usage instructions.

### Docs Tab
Full in-admin documentation covering setup, pricing management, and booking flow — written for non-technical shop owners.

---

## 🔧 Shortcode

Embed the repair price checker anywhere on your site:

```
[jktech_repair_checker]
```

Paste this into any page, post, or Elementor HTML widget.

---

## 📧 Email Notifications

Both the **shop** and the **customer** receive a branded HTML email on every booking submission.

**Shop email contains:**
- Customer name, email, phone
- Device, brand, model
- Selected repairs and prices per tier
- Booking timestamp

**Customer email contains:**
- Confirmation message
- Summary of selected repairs and estimated total
- Next steps and shop contact details

The notification email address is configurable from the **Settings tab** in the admin panel.

---

## 📦 Installation

1. Download the plugin zip file
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Upload `jktech-repair-checker-v2.zip`
4. Click **Activate**
5. Navigate to **JK Repair Checker** in the sidebar to configure

> ⚠️ This plugin is proprietary and built exclusively for JK Tech Solutions. It is not distributed publicly.

---

## 📁 File Structure

```
jktech-repair-checker-v2/
├── jktech-repair-checker-v2.php       # Main plugin file (v2.1.3)
├── includes/
│   ├── default-data.php               # Default device/pricing seed data
│   ├── admin-page.php                 # 5-tab admin UI
│   ├── shortcode.php                  # 5-step frontend estimator
│   └── ajax.php                       # AJAX handlers — save, book, poll, emails
├── public/
│   ├── css/
│   │   └── checker.css                # Frontend styles
│   └── js/
│       └── checker.js                 # Frontend interaction logic
└── admin/
    ├── css/                           # Admin panel styles
    └── js/                            # Admin panel scripts (tabs, live poll)
```

---

## 📝 Changelog

### v2.1.3 — Current
- Fixed apostrophe/curly quote PHP parse errors in device names

### v2.1.2
- Additional apostrophe scanner pass before packaging

### v2.1.1
- Branded HTML email templates for shop and customer

### v2.1.0
- Fixed duplicate badge rendering (removed PHP static badge, JS-only approach)

### v2.0.9
- Rebuilt `admin.js` as single clean file
- Fixed tab switching and live poll co-existing correctly

### v2.0.8
- Introduced live bookings poll (15s interval)

### v2.0.7
- Fixed hardcoded notification email — now reads from Settings tab
- Added error logging for AJAX failures

### v2.0.6
- Fixed tier column/delete button overlap in admin UI

### v2.0.5
- Tier manager in Settings — add, rename, remove pricing tiers

### v2.0.4
- Tier pill picker on frontend

### v2.0.3
- Added Docs tab to admin panel

### v2.0.2
- Fixed template leak on multi-instance pages

### v2.0.1
- Repair multi-select with live price total

### v2.0.0
- Full rebuild — 5-step flow, dynamic tiers, admin panel, AJAX bookings

---

## 🏗️ Built By

**CoreConcepts.design**
Custom WordPress development for businesses that need something built right.

- 🌐 [coreconcepts.design](https://coreconcepts.design)
- 📧 team@coreconcepts.design

---

> Built with 💙 for JK Tech Solutions — Montreal's honest repair shop.
