// ── NAV CONFIG PER ROLE ──────────────────────────────────────────────
const KOSTNEST_NAV = {
  admin: [
    { section: 'Utama' },
    { id: 'dashboard', label: 'Dashboard',     icon: 'fa-gauge-high',  href: 'dashboard.html' },
    { id: 'kamar',     label: 'Data Kamar',    icon: 'fa-door-open',   href: 'kamar.html' },
    { id: 'penghuni',  label: 'Data Penghuni', icon: 'fa-users',       href: 'penghuni.html' },
    { section: 'Laporan' },
    { id: 'laporan',   label: 'Laporan',       icon: 'fa-chart-bar',   href: 'laporan.html' },
  ],
  owner: [
    { section: 'Utama' },
    { id: 'dashboard', label: 'Dashboard',  icon: 'fa-gauge-high', href: 'dashboard-owner.html' },
    { id: 'kamar',     label: 'Kamar',      icon: 'fa-door-open',  href: 'kamar.html' },
    { section: 'Laporan' },
    { id: 'laporan',   label: 'Laporan',    icon: 'fa-chart-bar',  href: 'laporan.html' },
  ],
  user: [
    { section: 'Utama' },
    { id: 'dashboard',  label: 'Dashboard',   icon: 'fa-gauge-high', href: 'dashboard-user.html' },
    { section: 'Akun' },
    { id: 'pengaturan', label: 'Pengaturan',  icon: 'fa-gear',       href: 'pengaturan.html' },
  ],
};

// ── USER INFO PER ROLE ───────────────────────────────────────────────
const KOSTNEST_USER = {
  admin: { initials: 'AD', name: 'Administrator', role: 'Admin', logoutBase: '../../' },
  owner: { initials: 'OW', name: 'Owner',         role: 'Owner', logoutBase: '../../' },
  user:  { initials: 'US', name: 'Penghuni',      role: 'User',  logoutBase: '../../' },
};

// ── ROLE DETECTION ───────────────────────────────────────────────────
function getRole() {
  return sessionStorage.getItem('kn_role') || 'admin';
}

// ── BUILD LAYOUT ─────────────────────────────────────────────────────
function buildLayout(options = {}) {
  const { pageId = '', pageTitle = 'Dashboard' } = options;
  const role = getRole();
  const navItems = KOSTNEST_NAV[role] || KOSTNEST_NAV.admin;
  const userInfo = KOSTNEST_USER[role] || KOSTNEST_USER.admin;

  // Determine correct logout path based on folder depth
  // pages/admin|owner|user/  =>  ../../login.html
  const logoutHref = '../../login.html';

  // ── SIDEBAR NAV ──
  let navHTML = '';
  navItems.forEach(item => {
    if (item.section) {
      navHTML += `<div class="nav-section-label">${item.section}</div>`;
    } else {
      const isActive = item.id === pageId ? 'active' : '';
      const badge    = item.badge ? `<span class="nav-badge">${item.badge}</span>` : '';
      navHTML += `
        <a class="nav-item-link ${isActive}" href="${item.href}">
          <span class="nav-icon"><i class="fa-solid ${item.icon}"></i></span>
          ${item.label}${badge}
        </a>`;
    }
  });

  // Role badge colour
  const roleBadgeColor = { admin: 'var(--clr-accent)', owner: 'var(--clr-info)', user: 'var(--clr-primary)' }[role] || 'var(--clr-primary)';

  const sidebar = `
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon"><i class="fa-solid fa-house-chimney"></i></div>
      <div class="brand-name">KostNest</div>
      <div class="brand-sub">Sistem Informasi Kost</div>
    </div>
    <nav class="sidebar-nav">${navHTML}</nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="avatar" style="width:36px;height:36px;font-size:13px;background:${roleBadgeColor}">${userInfo.initials}</div>
        <div class="user-info">
          <div class="user-name" style="font-size:13px;font-weight:600;color:#fff">${userInfo.name}</div>
          <div class="user-role" style="font-size:11px;color:rgba(255,255,255,.4)">${userInfo.role}</div>
        </div>
      </div>
    </div>
  </aside>`;

  const topbar = `
  <header class="topbar">
    <button class="topbar-btn d-lg-none me-2" id="sidebarToggle" onclick="toggleSidebar()">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="topbar-title">${pageTitle}</div>
    <div class="topbar-search">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input type="text" placeholder="Cari kamar, penghuni..."/>
    </div>
    <div class="topbar-actions">
      <button class="topbar-btn" title="Notifikasi">
        <i class="fa-solid fa-bell"></i><span class="badge-dot"></span>
      </button>
      <div class="topbar-divider"></div>
      <div class="d-flex align-items-center gap-2" style="cursor:pointer">
        <div class="avatar" style="width:34px;height:34px;font-size:13px;background:${roleBadgeColor}">${userInfo.initials}</div>
        <span style="font-size:13px;font-weight:600;color:var(--clr-text)">${userInfo.name}</span>
        <a href="${logoutHref}" title="Logout"
           onclick="sessionStorage.removeItem('kn_role')"
           style="color:var(--clr-muted);font-size:14px;margin-left:4px;text-decoration:none">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>
      </div>
    </div>
  </header>`;

  document.body.insertAdjacentHTML('afterbegin', sidebar + topbar);
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Filter chips helper
function initFilterChips() {
  document.querySelectorAll('.filter-chip').forEach(chip => {
    chip.addEventListener('click', function () {
      this.closest('.filter-bar').querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
    });
  });
}
