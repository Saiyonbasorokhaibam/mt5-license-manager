const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');
const querystring = require('querystring');

const PORT = process.env.PORT || 3000;
const DB_FILE = path.join(__dirname, 'licenses_data.json');
const SETTINGS_FILE = path.join(__dirname, 'settings_data.json');

// --- SETTINGS STORE ---
function getSettings() {
  if (!fs.existsSync(SETTINGS_FILE)) {
    const defaultSettings = {
      admin_password: "admin123",
      key_multiplier: 4,
      key_offset: 550,
      grace_period: true
    };
    fs.writeFileSync(SETTINGS_FILE, JSON.stringify(defaultSettings, null, 2));
    return defaultSettings;
  }
  try {
    return JSON.parse(fs.readFileSync(SETTINGS_FILE, 'utf8'));
  } catch (e) {
    return { admin_password: "admin123", key_multiplier: 4, key_offset: 550, grace_period: true };
  }
}

function saveSettings(settings) {
  fs.writeFileSync(SETTINGS_FILE, JSON.stringify(settings, null, 2));
}

// --- LICENSES STORE ---
function getLicenses() {
  if (!fs.existsSync(DB_FILE)) {
    const settings = getSettings();
    const mult = settings.key_multiplier;
    const offset = settings.key_offset;
    const initial = [
      {
        id: 1,
        customer_name: "Alex Morgan",
        customer_email: "alex.morgan@example.com",
        account_number: 88991122,
        plan: "1year",
        license_key: String((88991122 * mult) + offset + 365),
        expiry_days: 365,
        expiry_date: new Date(Date.now() + 365 * 86400000).toISOString().split('T')[0],
        status: "active",
        created_at: new Date().toISOString().replace('T', ' ').split('.')[0]
      },
      {
        id: 2,
        customer_name: "Sarah Jenkins",
        customer_email: "sarah@tradingbot.io",
        account_number: 55443322,
        plan: "3month",
        license_key: String((55443322 * mult) + offset + 90),
        expiry_days: 90,
        expiry_date: new Date(Date.now() + 90 * 86400000).toISOString().split('T')[0],
        status: "active",
        created_at: new Date().toISOString().replace('T', ' ').split('.')[0]
      },
      {
        id: 3,
        customer_name: "David Smith",
        customer_email: "david@fxmaster.com",
        account_number: 99112233,
        plan: "1month",
        license_key: String((99112233 * mult) + offset + 30),
        expiry_days: 30,
        expiry_date: new Date(Date.now() - 5 * 86400000).toISOString().split('T')[0],
        status: "active",
        created_at: new Date(Date.now() - 35 * 86400000).toISOString().replace('T', ' ').split('.')[0]
      }
    ];
    fs.writeFileSync(DB_FILE, JSON.stringify(initial, null, 2));
    return initial;
  }
  try {
    return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
  } catch (e) {
    return [];
  }
}

function saveLicenses(licenses) {
  fs.writeFileSync(DB_FILE, JSON.stringify(licenses, null, 2));
}

function parseCookies(req) {
  const list = {};
  const rc = req.headers.cookie;
  if (rc) {
    rc.split(';').forEach(cookie => {
      const parts = cookie.split('=');
      list[parts.shift().trim()] = decodeURI(parts.join('='));
    });
  }
  return list;
}

// Master Stylesheet & Visual Design System
const GLOBAL_CSS = `
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-dark: #07090e;
        --card-bg: rgba(15, 23, 42, 0.75);
        --card-solid: #0f172a;
        --border-glass: rgba(255, 255, 255, 0.08);
        --border-hover: rgba(56, 189, 248, 0.3);
        --accent-cyan: #38bdf8;
        --accent-blue: #3b82f6;
        --accent-emerald: #10b981;
        --accent-amber: #f59e0b;
        --accent-rose: #f43f5e;
        --accent-purple: #818cf8;
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --text-muted: #64748b;
        --input-bg: rgba(2, 6, 23, 0.6);
        --glow-cyan: 0 0 25px rgba(56, 189, 248, 0.2);
        --glow-emerald: 0 0 25px rgba(16, 185, 129, 0.25);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

    body {
        background-color: var(--bg-dark);
        color: var(--text-primary);
        min-height: 100vh;
        padding: 1.5rem 1rem;
        background-image: 
            radial-gradient(at 10% 10%, rgba(56, 189, 248, 0.12) 0px, transparent 45%),
            radial-gradient(at 90% 20%, rgba(129, 140, 248, 0.1) 0px, transparent 50%),
            radial-gradient(at 50% 90%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
        background-attachment: fixed;
    }

    /* Floating Glass Navigation Header */
    .navbar {
        max-width: 1000px;
        margin: 0 auto 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        padding: 0.85rem 1.5rem;
        border-radius: 999px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .brand {
        font-size: 1.15rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--text-primary);
        text-decoration: none;
        letter-spacing: -0.02em;
    }

    .brand-logo {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.1rem;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
    }

    .nav-links { display: flex; gap: 0.5rem; align-items: center; }

    .nav-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        transition: all 0.2s ease;
    }

    .nav-link:hover { color: var(--text-primary); background: rgba(255, 255, 255, 0.05); }

    .nav-link.active {
        color: #ffffff;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(59, 130, 246, 0.2));
        border: 1px solid rgba(56, 189, 248, 0.3);
        box-shadow: 0 0 12px rgba(56, 189, 248, 0.2);
    }

    .nav-logout { color: var(--accent-rose) !important; }
    .nav-logout:hover { background: rgba(244, 63, 94, 0.15) !important; }

    /* Glass Cards */
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .gradient-text {
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 60%, var(--accent-cyan) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Buttons */
    .btn-primary {
        width: 100%;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25 ease;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(56, 189, 248, 0.5);
    }

    /* Inputs */
    .form-group { margin-bottom: 1.5rem; }
    label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-secondary); letter-spacing: 0.02em; }
    
    input[type="text"], input[type="email"], input[type="number"], input[type="password"] {
        width: 100%;
        padding: 0.9rem 1.1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-glass);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    input:focus {
        outline: none;
        border-color: var(--accent-cyan);
        box-shadow: var(--glow-cyan);
        background: rgba(15, 23, 42, 0.9);
    }

    /* Status Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.8rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .badge-active {
        background: rgba(16, 185, 129, 0.12);
        color: var(--accent-emerald);
        border: 1px solid rgba(16, 185, 129, 0.3);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.15);
    }

    .badge-expired {
        background: rgba(245, 158, 11, 0.12);
        color: var(--accent-amber);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-revoked {
        background: rgba(244, 63, 94, 0.12);
        color: var(--accent-rose);
        border: 1px solid rgba(244, 63, 94, 0.3);
    }

    /* Monospace Code Styling */
    code, .code-font {
        font-family: 'JetBrains Mono', monospace;
    }
</style>
`;

function getHeaderHTML(activePage, isAdmin = false) {
  if (isAdmin) {
    return `
    <div class="navbar">
        <a href="/admin.php" class="brand">
            <div class="brand-logo">🛡️</div>
            <span>MT5 Admin Portal</span>
        </a>
        <div class="nav-links">
            <a href="/admin.php" class="nav-link ${activePage === 'admin' ? 'active' : ''}">Dashboard</a>
            <a href="/analytics.php" class="nav-link ${activePage === 'analytics' ? 'active' : ''}">Analytics</a>
            <a href="/settings.php" class="nav-link ${activePage === 'settings' ? 'active' : ''}">Settings</a>
            <a href="/index.php" class="nav-link">Public Site</a>
            <a href="/admin.php?action=logout" class="nav-link nav-logout">Logout</a>
        </div>
    </div>`;
  }
  return `
    <div class="navbar">
        <a href="/manage" class="brand">
            <div class="brand-logo">⚡</div>
            <span>MT5 EA Suite</span>
        </a>
        <div class="nav-links">
            <a href="/manage" class="nav-link ${activePage === 'generator' ? 'active' : ''}">Generator</a>
            <a href="/lookup.php" class="nav-link ${activePage === 'lookup' ? 'active' : ''}">Check License</a>
            <a href="/setup.php" class="nav-link ${activePage === 'setup' ? 'active' : ''}">Setup Guide</a>
            <a href="/admin.php" class="nav-link">Admin Portal</a>
        </div>
    </div>`;
}

// MIME types for static files
const MIME_TYPES = {
  '.html': 'text/html',
  '.css':  'text/css',
  '.js':   'application/javascript',
  '.json': 'application/json',
  '.png':  'image/png',
  '.jpg':  'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif':  'image/gif',
  '.svg':  'image/svg+xml',
  '.ico':  'image/x-icon',
  '.woff': 'font/woff',
  '.woff2':'font/woff2',
  '.ttf':  'font/ttf',
};

// Path to the SAIYON EA website
const EA_SITE_DIR = path.join(__dirname, '..', '..', '..', 'antigravity', 'scratch', 'saiyon-ea');

const server = http.createServer((req, res) => {
  const parsedUrl = url.parse(req.url, true);
  const pathname = parsedUrl.pathname;
  const query = parsedUrl.query;
  const cookies = parseCookies(req);
  const settings = getSettings();

  // --- SAIYON EA STATIC ASSETS (src/css, src/js) ---
  if (pathname.startsWith('/src/')) {
    const filePath = path.join(EA_SITE_DIR, pathname);
    const ext = path.extname(filePath);
    if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      res.writeHead(200, { 'Content-Type': MIME_TYPES[ext] || 'text/plain' });
      return fs.createReadStream(filePath).pipe(res);
    }
    res.writeHead(404, { 'Content-Type': 'text/plain' });
    return res.end('Asset not found');
  }

  // Downloads static files
  if (pathname.startsWith('/downloads/')) {
    const filePath = path.join(__dirname, pathname);
    if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      res.writeHead(200, {
        'Content-Type': 'application/octet-stream',
        'Content-Disposition': `attachment; filename="${path.basename(filePath)}"`
      });
      return fs.createReadStream(filePath).pipe(res);
    } else {
      res.writeHead(404, { 'Content-Type': 'text/plain' });
      return res.end('File not found');
    }
  }

  // --- CHECK.PHP API ---
  if (pathname === '/check.php') {
    res.writeHead(200, { 'Content-Type': 'text/plain; charset=utf-8' });
    const key = (parsedUrl.query.key || '').trim();
    if (!key) return res.end('INVALID');
    
    const licenses = getLicenses();
    const found = licenses.find(l => String(l.license_key) === String(key));
    if (!found) return res.end('INVALID');

    const today = new Date().toISOString().split('T')[0];
    if (found.status === 'revoked') {
      return res.end('REVOKED');
    } else if (today > found.expiry_date) {
      return res.end('EXPIRED');
    } else if (found.status === 'active') {
      return res.end('ACTIVE');
    } else {
      return res.end('INVALID');
    }
  }

  // --- ROOT: Redirect to License Portal ---
  if (pathname === '/' || pathname === '/index.html') {
    res.writeHead(302, { 'Location': '/index.php' });
    return res.end();
  }

  // --- LICENSE MANAGER GENERATOR (/manage) ---
  if (pathname === '/manage' || pathname === '/index.php') {
    if (req.method === 'POST') {
      let body = '';
      req.on('data', chunk => { body += chunk.toString(); });
      req.on('end', () => {
        const post = querystring.parse(body);
        const name = (post.customer_name || '').trim();
        const email = (post.customer_email || '').trim();
        const accountStr = (post.account_number || '').trim();
        const plan = (post.plan || '').trim();

        const errors = [];
        if (!name) errors.push("Customer Name is required.");
        if (!email || !email.includes('@')) errors.push("A valid Customer Email address is required.");
        const accountNumber = parseInt(accountStr, 10);
        if (!accountStr || isNaN(accountNumber) || accountNumber <= 0) {
          errors.push("MetaTrader Account Number must be a positive integer.");
        }

        const planDaysMap = { '1month': 30, '3month': 90, '1year': 365, 'lifetime': 9999 };
        if (!planDaysMap[plan]) errors.push("Please select a valid subscription plan.");

        let generated_key = null;
        let license_details = null;

        if (errors.length === 0) {
          const expiry_days = planDaysMap[plan];
          const mult = settings.key_multiplier;
          const offset = settings.key_offset;
          const calculated_key = String((accountNumber * mult) + offset + expiry_days);

          const expDate = new Date();
          expDate.setDate(expDate.getDate() + expiry_days);
          const expiry_date = expDate.toISOString().split('T')[0];

          const licenses = getLicenses();
          const existingIdx = licenses.findIndex(l => String(l.account_number) === String(accountNumber));
          if (existingIdx >= 0) {
            licenses[existingIdx].customer_name = name;
            licenses[existingIdx].customer_email = email;
            licenses[existingIdx].plan = plan;
            licenses[existingIdx].license_key = calculated_key;
            licenses[existingIdx].expiry_days = expiry_days;
            licenses[existingIdx].expiry_date = expiry_date;
            licenses[existingIdx].status = 'active';
          } else {
            licenses.push({
              id: licenses.length ? Math.max(...licenses.map(l => l.id)) + 1 : 1,
              customer_name: name,
              customer_email: email,
              account_number: accountNumber,
              plan: plan,
              license_key: calculated_key,
              expiry_days: expiry_days,
              expiry_date: expiry_date,
              status: 'active',
              created_at: new Date().toISOString().replace('T', ' ').split('.')[0]
            });
          }
          saveLicenses(licenses);

          generated_key = calculated_key;
          license_details = {
            name, email, account: accountNumber, plan, key: calculated_key, expiry_date
          };
        }

        renderIndexPage(res, { errors, generated_key, license_details, post });
      });
    } else {
      renderIndexPage(res, { errors: [], generated_key: null, license_details: null, post: {} });
    }
    return;
  }


  // --- LOOKUP.PHP ---
  if (pathname === '/lookup.php') {
    const q = (query.query || '').trim().toLowerCase();
    const licenses = getLicenses();
    let result = null;
    let searched = false;

    if (q) {
      searched = true;
      result = licenses.find(l => String(l.account_number) === q || String(l.license_key) === q || l.customer_email.toLowerCase() === q);
    }

    renderLookupPage(res, { query: query.query || '', searched, result });
    return;
  }

  // --- SETUP.PHP ---
  if (pathname === '/setup.php') {
    renderSetupPage(res);
    return;
  }

  // --- ADMIN DASHBOARD (ADMIN.PHP) ---
  if (pathname === '/admin.php') {
    if (req.method === 'POST') {
      let body = '';
      req.on('data', chunk => { body += chunk.toString(); });
      req.on('end', () => {
        const post = querystring.parse(body);
        if (post.login_password === settings.admin_password) {
          res.writeHead(302, {
            'Set-Cookie': 'admin_session=1; Path=/; HttpOnly',
            'Location': '/admin.php'
          });
          return res.end();
        } else {
          renderAdminLoginPage(res, "Invalid administrator password.");
        }
      });
      return;
    }

    if (query.action === 'logout') {
      res.writeHead(302, {
        'Set-Cookie': 'admin_session=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT',
        'Location': '/admin.php'
      });
      return res.end();
    }

    if (!cookies.admin_session) {
      return renderAdminLoginPage(res, null);
    }

    const licenses = getLicenses();
    let actionMessage = '';

    if (query.action === 'revoke' && query.id) {
      const id = parseInt(query.id, 10);
      const target = licenses.find(l => l.id === id);
      if (target) {
        target.status = 'revoked';
        saveLicenses(licenses);
        actionMessage = `License #${id} successfully revoked.`;
      }
    } else if (query.action === 'delete' && query.id) {
      const id = parseInt(query.id, 10);
      const idx = licenses.findIndex(l => l.id === id);
      if (idx >= 0) {
        licenses.splice(idx, 1);
        saveLicenses(licenses);
        actionMessage = `License #${id} permanently deleted.`;
      }
    }

    return renderAdminDashboardPage(res, licenses, actionMessage, query);
  }

  // --- ANALYTICS.PHP ---
  if (pathname === '/analytics.php') {
    if (!cookies.admin_session) {
      return renderAdminLoginPage(res, "Authentication required.");
    }
    const licenses = getLicenses();
    return renderAnalyticsPage(res, licenses);
  }

  // --- SETTINGS.PHP ---
  if (pathname === '/settings.php') {
    if (!cookies.admin_session) {
      return renderAdminLoginPage(res, "Authentication required.");
    }

    let message = '';
    if (req.method === 'POST') {
      let body = '';
      req.on('data', chunk => { body += chunk.toString(); });
      req.on('end', () => {
        const post = querystring.parse(body);
        if (post.action === 'update_auth' && post.admin_password) {
          settings.admin_password = post.admin_password.trim();
          saveSettings(settings);
          message = "Admin password updated successfully!";
        } else if (post.action === 'update_formula') {
          settings.key_multiplier = parseInt(post.key_multiplier, 10) || 4;
          settings.key_offset = parseInt(post.key_offset, 10) || 550;
          saveSettings(settings);
          message = "License key generation algorithm settings saved!";
        }
        renderSettingsPage(res, settings, message);
      });
      return;
    }

    renderSettingsPage(res, settings, message);
    return;
  }

  // --- API: Validate License Key (used by SAIYON EA website) ---
  if (pathname === '/api/validate') {
    const corsHeaders = {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'GET, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type',
      'Content-Type': 'application/json'
    };

    if (req.method === 'OPTIONS') {
      res.writeHead(204, corsHeaders);
      res.end();
      return;
    }

    const key = query.key ? query.key.trim() : '';
    if (!key) {
      res.writeHead(400, corsHeaders);
      res.end(JSON.stringify({ valid: false, message: 'No license key provided.' }));
      return;
    }

    const licenses = getLicenses();
    const license = licenses.find(l => l.license_key === key);

    if (!license) {
      res.writeHead(200, corsHeaders);
      res.end(JSON.stringify({ valid: false, message: 'License key not found. Please check and try again.' }));
      return;
    }

    const today = new Date().toISOString().split('T')[0];
    const isExpired = today > license.expiry_date;
    const isRevoked = license.status === 'revoked';

    if (isRevoked) {
      res.writeHead(200, corsHeaders);
      res.end(JSON.stringify({ valid: false, message: 'This license key has been revoked.' }));
      return;
    }

    if (isExpired) {
      res.writeHead(200, corsHeaders);
      res.end(JSON.stringify({ valid: false, message: 'This license key has expired.' }));
      return;
    }

    if (license.status !== 'active') {
      res.writeHead(200, corsHeaders);
      res.end(JSON.stringify({ valid: false, message: 'This license key is not active.' }));
      return;
    }

    const expiryMs = new Date(license.expiry_date).getTime() - new Date(today).getTime();
    const daysLeft = Math.ceil(expiryMs / (1000 * 60 * 60 * 24));

    res.writeHead(200, corsHeaders);
    res.end(JSON.stringify({
      valid: true,
      customer_name: license.customer_name,
      customer_email: license.customer_email,
      plan: license.plan,
      license_key: license.license_key,
      expiry_date: license.expiry_date,
      days_left: daysLeft
    }));
    return;
  }

  res.writeHead(404, { 'Content-Type': 'text/plain' });
  res.end('Not Found');
});


// --- RENDER FUNCTIONS ---

function renderIndexPage(res, { errors, generated_key, license_details, post }) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaTrader 5 EA License Generator</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 680px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 0.5rem; }
        .header p { color: var(--text-secondary); font-size: 1rem; }

        .alert { background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); color: #fda4af; padding: 1rem; border-radius: 14px; margin-bottom: 1.5rem; font-size: 0.9rem; }

        .plans-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 0.5rem; }
        @media(max-width: 500px) { .plans-grid { grid-template-columns: 1fr; } }

        .plan-option input[type="radio"] { display: none; }
        .plan-box {
            display: flex; flex-direction: column; padding: 1.25rem;
            background: var(--input-bg); border: 1px solid var(--border-glass); border-radius: 16px;
            cursor: pointer; transition: all 0.25s ease; position: relative; overflow: hidden;
        }

        .plan-option input[type="radio"]:checked + .plan-box {
            border-color: var(--accent-cyan);
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.12), rgba(59, 130, 246, 0.08));
            box-shadow: var(--glow-cyan);
            transform: translateY(-2px);
        }

        .plan-name { font-weight: 700; font-size: 1rem; color: var(--text-primary); }
        .plan-price { font-size: 1.5rem; font-weight: 800; color: var(--accent-cyan); margin-top: 0.35rem; }
        .plan-duration { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }

        .result-box {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(5, 150, 105, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px; padding: 2rem; margin-top: 2rem; text-align: center;
            box-shadow: var(--glow-emerald);
        }

        .key-display {
            background: #020617; border: 1px dashed var(--accent-emerald);
            padding: 1.25rem; border-radius: 14px; font-family: 'JetBrains Mono', monospace;
            font-size: 1.75rem; font-weight: 700; color: #34d399; letter-spacing: 3px;
            margin: 1rem 0; word-break: break-all;
        }

        .actions-group { display: flex; gap: 0.85rem; justify-content: center; flex-wrap: wrap; margin-top: 1.25rem; }

        .btn-secondary {
            padding: 0.85rem 1.4rem; background: rgba(30, 41, 59, 0.8); color: var(--text-primary);
            border: 1px solid var(--border-glass); border-radius: 12px; font-weight: 600;
            cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-secondary:hover { background: rgba(51, 65, 85, 1); border-color: rgba(255, 255, 255, 0.2); }

        .btn-download-ea {
            padding: 0.85rem 1.4rem; background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            border: none; border-radius: 12px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-download-ea:hover { transform: translateY(-2px); box-shadow: var(--glow-emerald); }
    </style>
</head>
<body>

${getHeaderHTML('generator')}

<div class="container">
    <div class="glass-card">
        <div class="header">
            <h1 class="gradient-text">License Key Generator</h1>
            <p>Generate activation keys for your MetaTrader 5 Expert Advisors</p>
        </div>

        ${errors.length ? `<div class="alert"><ul>${errors.map(e => `<li>${e}</li>`).join('')}</ul></div>` : ''}

        <form method="POST" action="">
            <div class="form-group">
                <label for="customer_name">Customer Full Name</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="John Doe" value="${post.customer_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="customer_email">Email Address</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="john@example.com" value="${post.customer_email || ''}" required>
            </div>
            <div class="form-group">
                <label for="account_number">MetaTrader 5 Account Number</label>
                <input type="number" id="account_number" name="account_number" placeholder="e.g. 88991122" value="${post.account_number || ''}" required>
            </div>

            <div class="form-group">
                <label>Select Subscription Plan</label>
                <div class="plans-grid">
                    <label class="plan-option">
                        <input type="radio" name="plan" value="1month" ${(!post.plan || post.plan === '1month') ? 'checked' : ''}>
                        <div class="plan-box">
                            <span class="plan-name">1 Month</span>
                            <span class="plan-price">$29</span>
                            <span class="plan-duration">30 Days License</span>
                        </div>
                    </label>
                    <label class="plan-option">
                        <input type="radio" name="plan" value="3month" ${post.plan === '3month' ? 'checked' : ''}>
                        <div class="plan-box">
                            <span class="plan-name">3 Months</span>
                            <span class="plan-price">$79</span>
                            <span class="plan-duration">90 Days License</span>
                        </div>
                    </label>
                    <label class="plan-option">
                        <input type="radio" name="plan" value="1year" ${post.plan === '1year' ? 'checked' : ''}>
                        <div class="plan-box">
                            <span class="plan-name">1 Year</span>
                            <span class="plan-price">$199</span>
                            <span class="plan-duration">365 Days License</span>
                        </div>
                    </label>
                    <label class="plan-option">
                        <input type="radio" name="plan" value="lifetime" ${post.plan === 'lifetime' ? 'checked' : ''}>
                        <div class="plan-box">
                            <span class="plan-name">Lifetime</span>
                            <span class="plan-price">$499</span>
                            <span class="plan-duration">9999 Days License</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 1.5rem;">
                ⚡ Generate License Activation Key
            </button>
        </form>

        ${generated_key && license_details ? `
            <div class="result-box">
                <h3 style="color: #34d399; font-size: 1.2rem;">License Key Generated Successfully!</h3>
                <div class="key-display" id="keyText">${generated_key}</div>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                    Expires on <strong>${license_details.expiry_date}</strong> for MT5 Account #<strong>${license_details.account}</strong>
                </p>
                <div class="actions-group">
                    <button type="button" class="btn-secondary" onclick="copyKey()">📋 Copy Key</button>
                    <a href="downloads/MetaTrader5_EA.ex5" class="btn-download-ea" download>📥 Download EA (.ex5)</a>
                </div>
            </div>
        ` : ''}
    </div>
</div>

<script>
function copyKey() {
    const keyText = document.getElementById('keyText').innerText;
    navigator.clipboard.writeText(keyText).then(() => { alert('Key copied to clipboard!'); });
}
</script>
</body>
</html>`;
  res.end(html);
}

function renderLookupPage(res, { query, searched, result }) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const today = new Date().toISOString().split('T')[0];

  let statusBadge = '';
  let daysLeft = 0;
  if (result) {
    const expDate = new Date(result.expiry_date);
    const now = new Date();
    daysLeft = Math.ceil((expDate - now) / (1000 * 60 * 60 * 24));

    if (result.status === 'revoked') {
      statusBadge = '<span class="badge badge-revoked">REVOKED</span>';
    } else if (result.expiry_date < today) {
      statusBadge = '<span class="badge badge-expired">EXPIRED</span>';
    } else {
      statusBadge = '<span class="badge badge-active">ACTIVE</span>';
    }
  }

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check EA License Status</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 650px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 0.5rem; }
        
        .result-card {
            background: #020617; border: 1px solid var(--border-glass); border-radius: 18px; padding: 1.75rem; margin-top: 2rem;
        }

        .info-row { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border-glass); font-size: 0.95rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-secondary); }
        .info-value { font-weight: 700; color: var(--text-primary); }

        .btn-download-ea {
            display: block; text-align: center; margin-top: 1.5rem; padding: 0.9rem;
            background: linear-gradient(135deg, #10b981, #059669); color: #fff; text-decoration: none;
            border-radius: 12px; font-weight: 700; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>

${getHeaderHTML('lookup')}

<div class="container">
    <div class="glass-card">
        <div class="header">
            <h1 class="gradient-text">License Lookup</h1>
            <p>Verify subscription validity and remaining active days</p>
        </div>

        <form method="GET" action="/lookup.php">
            <div class="form-group">
                <label for="query">MT5 Account Number, Email, or Key</label>
                <input type="text" id="query" name="query" placeholder="e.g. 88991122 or alex.morgan@example.com" value="${query}" required>
            </div>
            <button type="submit" class="btn-primary">🔍 Lookup Status</button>
        </form>

        ${searched ? (result ? `
            <div class="result-card">
                <div style="text-align: center; margin-bottom: 1.25rem;">${statusBadge}</div>
                <div class="info-row"><span class="info-label">Customer:</span><span class="info-value">${result.customer_name}</span></div>
                <div class="info-row"><span class="info-label">Email:</span><span class="info-value">${result.customer_email}</span></div>
                <div class="info-row"><span class="info-label">MT5 Account #:</span><span class="info-value"><code style="color: var(--accent-cyan);">${result.account_number}</code></span></div>
                <div class="info-row"><span class="info-label">Subscription Tier:</span><span class="info-value">${result.plan}</span></div>
                <div class="info-row"><span class="info-label">License Key:</span><span class="info-value"><code style="color: #34d399;">${result.license_key}</code></span></div>
                <div class="info-row"><span class="info-label">Expiry Date:</span><span class="info-value">${result.expiry_date}</span></div>
                <div class="info-row"><span class="info-label">Time Remaining:</span><span class="info-value" style="color: var(--accent-cyan);">${daysLeft > 0 ? daysLeft + ' days' : 'Expired'}</span></div>

                <a href="/downloads/MetaTrader5_EA.ex5" class="btn-download-ea" download>📥 Download Expert Advisor (.ex5)</a>
            </div>
        ` : `
            <div class="result-card" style="text-align: center; color: var(--accent-rose);">
                ⚠️ No matching active license record found for "<strong>${query}</strong>".
            </div>
        `) : ''}
    </div>
</div>

</body>
</html>`;
  res.end(html);
}

function renderSetupPage(res) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT5 EA Setup & Installation Guide</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 900px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 2.5rem; }
        .header h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 0.5rem; }

        .step-card {
            background: var(--card-bg); border: 1px solid var(--border-glass); border-radius: 20px;
            padding: 2rem; margin-bottom: 1.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .step-num {
            display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); color: #fff;
            border-radius: 12px; font-weight: 800; font-size: 1.1rem; margin-bottom: 1rem;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
        }

        .step-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-primary); }
        .step-desc { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.25rem; }

        .url-box {
            background: #020617; border: 1px dashed var(--accent-cyan); border-radius: 12px;
            padding: 1rem 1.25rem; font-family: 'JetBrains Mono', monospace; font-size: 1rem; color: #34d399;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;
        }

        .btn-copy {
            padding: 0.45rem 0.9rem; background: rgba(30, 41, 59, 0.8); border: 1px solid var(--border-glass);
            color: var(--text-primary); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
        }

        .tip-box {
            background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px; padding: 1rem; color: #fbbf24; font-size: 0.9rem; line-height: 1.5;
        }

        ol { padding-left: 1.5rem; color: var(--text-primary); margin-bottom: 1rem; }
        li { margin-bottom: 0.5rem; font-size: 0.95rem; }
    </style>
</head>
<body>

${getHeaderHTML('setup')}

<div class="container">
    <div class="header">
        <h1 class="gradient-text">MT5 EA Setup & Installation Guide</h1>
        <p>Follow these 3 steps to connect your Expert Advisor to the licensing server</p>
    </div>

    <div class="step-card">
        <div class="step-num">1</div>
        <h2 class="step-title">Enable WebRequest in MetaTrader 5 Settings</h2>
        <p class="step-desc">To allow the EA to verify its license status dynamically in real-time, authorize our server URL in MetaTrader 5 settings.</p>
        <ol>
            <li>Open MetaTrader 5 on your computer.</li>
            <li>Go to top menu: <strong>Tools</strong> ➔ <strong>Options</strong> (or press <code>Ctrl + O</code>).</li>
            <li>Select the <strong>Expert Advisors</strong> tab.</li>
            <li>Check the box <strong>"Allow WebRequest for listed URL"</strong>.</li>
            <li>Double-click <strong>"Add new URL..."</strong> and paste the endpoint URL:</li>
        </ol>

        <div class="url-box">
            <span id="apiUrl">const LICENSE_SERVER = '';  // same origin — served from port 3000/check.php</span>
            <button class="btn-copy" onclick="copyUrl()">Clipboard Copy</button>
        </div>

        <div class="tip-box">
            ⚠️ <strong>Important:</strong> Without WebRequest enabled, MetaTrader 5 will block network calls and your EA will report a <code>WebRequest Error</code>.
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">2</div>
        <h2 class="step-title">Install EA File into MQL5 Directory</h2>
        <p class="step-desc">Copy your downloaded <code>MetaTrader5_EA.ex5</code> binary into the MetaTrader Experts folder.</p>
        <ol>
            <li>In MT5, click <strong>File</strong> ➔ <strong>Open Data Folder</strong>.</li>
            <li>Navigate to <code>MQL5</code> ➔ <code>Experts</code>.</li>
            <li>Paste your <code>MetaTrader5_EA.ex5</code> file into this directory.</li>
            <li>Return to MT5, right-click <strong>Expert Advisors</strong> in the Navigator panel, and click <strong>Refresh</strong>.</li>
        </ol>
    </div>

    <div class="step-card">
        <div class="step-num">3</div>
        <h2 class="step-title">Attach EA to Chart & Enter License Key</h2>
        <p class="step-desc">Drag the EA onto your chart and enter your activation key.</p>
        <ol>
            <li>Drag <code>MetaTrader5_EA</code> from Navigator onto any currency chart.</li>
            <li>In the <strong>Inputs</strong> tab, set <code>InpLicenseKey</code> to your generated key.</li>
            <li>Check <strong>"Allow Algo Trading"</strong> in the Common tab.</li>
            <li>Click <strong>OK</strong>. Confirm activation in the MT5 Experts log tab!</li>
        </ol>
    </div>
</div>

<script>
function copyUrl() {
    const urlText = document.getElementById('apiUrl').innerText;
    navigator.clipboard.writeText(urlText).then(() => { alert('URL copied!'); });
}
</script>
</body>
</html>`;
  res.end(html);
}

function renderAdminLoginPage(res, errorMsg) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EA License Manager</title>
    ${GLOBAL_CSS}
    <style>
        body { display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 420px; text-align: center; }
        .login-icon {
            width: 56px; height: 56px; background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 16px; color: var(--accent-cyan);
            display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem;
        }
        .alert-error { background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); color: #fda4af; padding: 0.75rem; border-radius: 10px; font-size: 0.85rem; margin-bottom: 1.25rem; }
    </style>
</head>
<body>

<div class="glass-card login-card">
    <div class="login-icon">🛡️</div>
    <h2 class="gradient-text" style="font-size: 1.75rem; margin-bottom: 0.5rem;">Admin Portal</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;">Authenticate to access license management</p>

    ${errorMsg ? `<div class="alert-error">${errorMsg}</div>` : ''}

    <form method="POST" action="/admin.php">
        <div class="form-group" style="text-align: left;">
            <label for="login_password">Password</label>
            <input type="password" id="login_password" name="login_password" placeholder="Enter admin password" value="admin123" required>
        </div>
        <button type="submit" class="btn-primary">Authenticate</button>
    </form>
    <a href="/index.php" style="display: block; margin-top: 1.5rem; color: var(--text-secondary); text-decoration: none; font-size: 0.85rem;">← Return to Public Site</a>
</div>

</body>
</html>`;
  res.end(html);
}

function renderAdminDashboardPage(res, licenses, actionMessage, queryParams) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const today = new Date().toISOString().split('T')[0];

  const search = (queryParams.search || '').trim().toLowerCase();
  const filter_status = queryParams.status || 'all';

  const total_count = licenses.length;
  const active_count = licenses.filter(l => l.status === 'active' && l.expiry_date >= today).length;
  const expired_count = licenses.filter(l => l.status !== 'revoked' && l.expiry_date < today).length;
  const revoked_count = licenses.filter(l => l.status === 'revoked').length;

  let filtered = licenses.filter(l => {
    if (search) {
      const match = (l.customer_name || '').toLowerCase().includes(search) ||
                    (l.customer_email || '').toLowerCase().includes(search) ||
                    String(l.account_number).includes(search) ||
                    String(l.license_key).includes(search);
      if (!match) return false;
    }
    if (filter_status === 'active') return l.status === 'active' && l.expiry_date >= today;
    if (filter_status === 'expired') return l.status !== 'revoked' && l.expiry_date < today;
    if (filter_status === 'revoked') return l.status === 'revoked';
    return true;
  });

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MetaTrader 5 License Manager</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 1200px; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        
        .stat-card {
            background: var(--card-bg); border: 1px solid var(--border-glass); border-radius: 18px; padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .stat-label { font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; }
        .stat-value { font-size: 2.2rem; font-weight: 800; margin-top: 0.4rem; color: var(--text-primary); }

        .stat-active .stat-value { color: var(--accent-emerald); }
        .stat-expired .stat-value { color: var(--accent-amber); }
        .stat-revoked .stat-value { color: var(--accent-rose); }

        .msg-banner {
            padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem;
            background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;
        }

        .controls-card {
            background: var(--card-bg); border: 1px solid var(--border-glass); border-radius: 18px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
            display: flex; gap: 1rem; justify-content: space-between; align-items: center; flex-wrap: wrap;
        }

        .search-form { display: flex; gap: 0.75rem; flex: 1; min-width: 280px; }
        .search-input { flex: 1; padding: 0.7rem 1.1rem; background: var(--input-bg); border: 1px solid var(--border-glass); border-radius: 12px; color: var(--text-primary); font-size: 0.9rem; }
        .btn-search { padding: 0.7rem 1.4rem; background: var(--accent-blue); color: #fff; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; }

        .table-card { background: var(--card-bg); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(2, 6, 23, 0.8); color: var(--text-secondary); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1.1rem 1.25rem; font-weight: 700; border-bottom: 1px solid var(--border-glass); }
        td { padding: 1.1rem 1.25rem; border-bottom: 1px solid var(--border-glass); font-size: 0.9rem; }
        
        .btn-sm { padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-revoke { background: rgba(244, 63, 94, 0.12); color: var(--accent-rose); border: 1px solid rgba(244, 63, 94, 0.3); }
        .btn-revoke:hover { background: var(--accent-rose); color: #fff; }
        .btn-del { background: rgba(30, 41, 59, 0.8); color: var(--text-secondary); border: 1px solid var(--border-glass); margin-left: 0.4rem; }
        .btn-del:hover { background: rgba(51, 65, 85, 1); color: #fff; }
    </style>
</head>
<body>

${getHeaderHTML('admin', true)}

<div class="container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Issued Licenses</div>
            <div class="stat-value">${total_count}</div>
        </div>
        <div class="stat-card stat-active">
            <div class="stat-label">Active Subscriptions</div>
            <div class="stat-value">${active_count}</div>
        </div>
        <div class="stat-card stat-expired">
            <div class="stat-label">Expired Licenses</div>
            <div class="stat-value">${expired_count}</div>
        </div>
        <div class="stat-card stat-revoked">
            <div class="stat-label">Revoked Keys</div>
            <div class="stat-value">${revoked_count}</div>
        </div>
    </div>

    ${actionMessage ? `<div class="msg-banner">${actionMessage}</div>` : ''}

    <div class="controls-card">
        <form class="search-form" method="GET" action="/admin.php">
            <input type="text" name="search" class="search-input" placeholder="Search customer, email, account or key..." value="${queryParams.search || ''}">
            <button type="submit" class="btn-search">Search</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>MT5 Account</th>
                    <th>Plan</th>
                    <th>License Key</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${filtered.length === 0 ? `<tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">No matching license records.</td></tr>` : ''}
                ${filtered.map(l => {
                    let badgeClass = 'badge-active';
                    let statusLabel = 'ACTIVE';
                    if (l.status === 'revoked') {
                      badgeClass = 'badge-revoked'; statusLabel = 'REVOKED';
                    } else if (l.expiry_date < today) {
                      badgeClass = 'badge-expired'; statusLabel = 'EXPIRED';
                    }
                    return `
                    <tr>
                        <td style="color: var(--text-muted);">#${l.id}</td>
                        <td><strong>${l.customer_name}</strong></td>
                        <td style="color: var(--text-secondary);">${l.customer_email}</td>
                        <td><code style="color: var(--accent-cyan);">${l.account_number}</code></td>
                        <td><span style="font-weight: 600;">${l.plan}</span></td>
                        <td><code style="color: #34d399;">${l.license_key}</code></td>
                        <td>${l.expiry_date}</td>
                        <td><span class="badge ${badgeClass}">${statusLabel}</span></td>
                        <td>
                            ${l.status !== 'revoked' ? `<a href="/admin.php?action=revoke&id=${l.id}" class="btn-sm btn-revoke" onclick="return confirm('Revoke key?');">Revoke</a>` : ''}
                            <a href="/admin.php?action=delete&id=${l.id}" class="btn-sm btn-del" onclick="return confirm('Delete permanently?');">Delete</a>
                        </td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>
    </div>
</div>

</body>
</html>`;
  res.end(html);
}

function renderAnalyticsPage(res, licenses) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const today = new Date().toISOString().split('T')[0];

  const planPrices = { '1month': 29, '3month': 79, '1year': 199, 'lifetime': 499 };
  const planCounts = { '1month': 0, '3month': 0, '1year': 0, 'lifetime': 0 };

  let totalRevenue = 0;
  licenses.forEach(l => {
    const price = planPrices[l.plan] || 0;
    totalRevenue += price;
    if (planCounts[l.plan] !== undefined) planCounts[l.plan]++;
  });

  const aov = licenses.length ? Math.round(totalRevenue / licenses.length) : 0;
  const activeCount = licenses.filter(l => l.status === 'active' && l.expiry_date >= today).length;

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Revenue Analytics - MT5 EA License Manager</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 1100px; margin: 0 auto; }
        .header { margin-bottom: 2rem; }
        .header h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.03em; }

        .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }

        .stat-card {
            background: var(--card-bg); border: 1px solid var(--border-glass); border-radius: 18px; padding: 1.5rem;
        }

        .stat-label { font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; }
        .stat-val { font-size: 2.2rem; font-weight: 800; margin-top: 0.5rem; color: #34d399; }

        .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        @media(max-width: 768px) { .chart-grid { grid-template-columns: 1fr; } }

        .progress-bar-bg { background: #020617; border-radius: 10px; height: 14px; overflow: hidden; margin-top: 0.6rem; }
        .progress-fill { height: 100%; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); border-radius: 10px; }

        .plan-row { margin-bottom: 1.4rem; }
        .plan-info { display: flex; justify-content: space-between; font-size: 0.95rem; font-weight: 600; }
    </style>
</head>
<body>

${getHeaderHTML('analytics', true)}

<div class="container">
    <div class="header">
        <h1 class="gradient-text">Sales & Revenue Analytics</h1>
        <p style="color: var(--text-secondary);">Financial dashboard & subscription plan metrics</p>
    </div>

    <div class="grid-4">
        <div class="stat-card">
            <div class="stat-label">Total Gross Revenue</div>
            <div class="stat-val">$${totalRevenue.toLocaleString()}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Average Order Value (AOV)</div>
            <div class="stat-val" style="color: var(--accent-cyan);">$${aov}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Issued Licenses</div>
            <div class="stat-val" style="color: var(--text-primary);">${licenses.length}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Paying Members</div>
            <div class="stat-val" style="color: #34d399;">${activeCount}</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="glass-card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Subscription Plan Volume</h3>
            ${Object.keys(planCounts).map(plan => {
                const count = planCounts[plan];
                const pct = licenses.length ? Math.round((count / licenses.length) * 100) : 0;
                const labels = { '1month': '1 Month ($29)', '3month': '3 Months ($79)', '1year': '1 Year ($199)', 'lifetime': 'Lifetime ($499)' };
                return `
                <div class="plan-row">
                    <div class="plan-info">
                        <span>${labels[plan]}</span>
                        <span style="color: var(--accent-cyan);">${count} licenses (${pct}%)</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-fill" style="width: ${pct}%;"></div>
                    </div>
                </div>`;
            }).join('')}
        </div>

        <div class="glass-card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Recent Sales Activity</h3>
            <div>
                ${licenses.slice(0, 5).map(l => `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 0; border-bottom: 1px solid var(--border-glass);">
                        <div>
                            <strong style="color: var(--text-primary);">${l.customer_name}</strong>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">${l.plan} tier • MT5 #${l.account_number}</div>
                        </div>
                        <div style="font-weight: 800; font-size: 1.1rem; color: #34d399;">+$${planPrices[l.plan] || 0}</div>
                    </div>
                `).join('')}
            </div>
        </div>
    </div>
</div>

</body>
</html>`;
  res.end(html);
}

function renderSettingsPage(res, settings, message) {
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
  const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin System Settings - MT5 License Manager</title>
    ${GLOBAL_CSS}
    <style>
        .container { max-width: 800px; margin: 0 auto; }
        .card-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-glass); padding-bottom: 0.75rem; }
        .help-text { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.35rem; }
        .msg-banner { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
    </style>
</head>
<body>

${getHeaderHTML('settings', true)}

<div class="container">
    ${message ? `<div class="msg-banner">${message}</div>` : ''}

    <div class="glass-card" style="margin-bottom: 2rem;">
        <h2 class="card-title">🔐 Administrator Credentials</h2>
        <form method="POST" action="/settings.php">
            <input type="hidden" name="action" value="update_auth">
            <div class="form-group">
                <label for="admin_password">Admin Dashboard Password</label>
                <input type="password" id="admin_password" name="admin_password" value="${settings.admin_password}" required>
                <div class="help-text">Password required to log into administrative portals.</div>
            </div>
            <button type="submit" class="btn-primary" style="width: auto;">Save Password</button>
        </form>
    </div>

    <div class="glass-card">
        <h2 class="card-title">🧮 License Algorithm Parameters</h2>
        <form method="POST" action="/settings.php">
            <input type="hidden" name="action" value="update_formula">
            <div class="form-group">
                <label for="key_multiplier">Account Multiplier Factor</label>
                <input type="number" id="key_multiplier" name="key_multiplier" value="${settings.key_multiplier}" required>
                <div class="help-text">Formula: <code>(Account Number * Multiplier) + Offset + Expiry Days</code></div>
            </div>
            <div class="form-group">
                <label for="key_offset">Fixed Offset</label>
                <input type="number" id="key_offset" name="key_offset" value="${settings.key_offset}" required>
            </div>
            <button type="submit" class="btn-primary" style="width: auto;">Save Algorithm Settings</button>
        </form>
    </div>
</div>

</body>
</html>`;
  res.end(html);
}

server.listen(PORT, () => {
  console.log(`
  ✅ SAIYON EA Suite running on http://localhost:${PORT}/
  📊 License Manager   → http://localhost:${PORT}/manage
  🔐 Admin Dashboard   → http://localhost:${PORT}/admin.php
  🔍 License Lookup    → http://localhost:${PORT}/lookup.php
  🔑 API Validate      → http://localhost:${PORT}/api/validate?key=...
  `);
});
