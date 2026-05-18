{{-- resources/views/users/assignRolePrivileges.blade.php --}}
@extends('pages.layout.structure')
@section('title', 'Assign Role Privileges')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<style>
/* ===== Assign Role Privileges (same polished shell) ===== */
.arp-scope.ap-wrap{padding:2px 0}

/* Role mini */
.arp-scope .ap-profile{
  display:flex;gap:12px;align-items:center;
  padding:12px 14px;
  border:1px solid var(--line-strong);
  border-radius:16px;
  background:linear-gradient(180deg, var(--background-soft), transparent);
  margin:10px 0 12px;
}
.arp-scope .ap-avatar{
  width:48px;height:48px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg, rgba(158,54,58,.18), rgba(201,75,80,.10));
  border:1px solid var(--line-soft);
  color:var(--primary-color);
  font-weight:900;
  flex:0 0 auto;
}
.arp-scope .ap-profile-meta{display:flex;flex-direction:column;min-width:0}
.arp-scope .ap-profile-name{
  font-weight:900;color:var(--ink);line-height:1.15;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:520px;
}
.arp-scope .ap-profile-sub{
  font-size:0.84rem;color:var(--muted-color);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:640px;
}
.arp-scope .ap-profile-badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
.arp-scope .ap-badge{
  display:inline-flex;align-items:center;gap:6px;
  border:1px solid var(--line-soft);
  background:var(--surface);
  color:var(--muted-color);
  border-radius:999px;
  padding:3px 10px;
  font-size:0.78rem;
}
.arp-scope .ap-badge b{color:var(--ink);font-weight:800}

/* Main card */
.arp-scope .ap-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:18px;
  box-shadow:var(--shadow-2);
  margin-top:10px;
  overflow:visible;
}
.arp-scope .ap-card-head{
  padding:12px 14px;
  border-bottom:1px solid var(--line-strong);
  display:flex;flex-wrap:wrap;gap:10px;
  align-items:center;justify-content:space-between;
}
.arp-scope .ap-card-head-left{display:flex;flex-direction:column;gap:2px;min-width:260px}
.arp-scope .ap-card-head-title{font-weight:800}
.arp-scope .ap-card-head-sub{font-size:0.82rem;color:var(--muted-color)}
.arp-scope .ap-card-head-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.arp-scope .ap-card-body{padding:14px}

/* Top tools */
.arp-scope .ap-tools{
  display:flex;align-items:center;gap:8px;flex-wrap:wrap;
  margin-bottom:10px;
}
.arp-scope .ap-tools .input-group{max-width:520px;min-width:240px}
.arp-scope .ap-tools .input-group-text{background:var(--background-soft);border-color:var(--line-strong);color:var(--muted-color)}
.arp-scope .ap-tools .form-control{border-color:var(--line-strong)}
.arp-scope .ap-tools .form-control:focus{box-shadow:0 0 0 2px rgba(158,54,58,.16);border-color:rgba(158,54,58,.55)}
.arp-scope .ap-kpi{
  margin-left:auto;
  display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:flex-end;
}
.arp-scope .ap-kpi .ap-chip{
  display:inline-flex;align-items:center;gap:8px;
  border:1px solid var(--line-soft);
  background:var(--background-soft);
  color:var(--muted-color);
  border-radius:999px;
  padding:6px 10px;
  font-size:0.82rem;
}
.arp-scope .ap-kpi .ap-chip b{color:var(--ink);font-weight:900}

/* Dropdown (must be able to dropup too) */
.arp-scope .ap-dd{position:relative}
.arp-scope .ap-dd .dd-toggle{border-radius:12px}
.arp-scope .ap-dd .dropdown-menu{
  border-radius:14px;
  border:1px solid var(--line-strong);
  box-shadow:var(--shadow-2);
  min-width:240px;
  z-index:6000;
}
.arp-scope .ap-dd .dropdown-menu.show{display:block !important}
.arp-scope .ap-dd .dropdown-item{display:flex;align-items:center;gap:.65rem}
.arp-scope .ap-dd .dropdown-item i{width:16px;text-align:center}
.arp-scope .ap-dd .dropdown-item.text-danger{color:var(--danger-color) !important}

/* Bootstrap accordion styling */
.arp-scope .ap-accordion .accordion-item{
  border-radius:14px;
  border:1px solid var(--line-soft);
  margin-bottom:10px;
  background:var(--surface);
  overflow:visible;
}
.arp-scope .ap-accordion .accordion-header{position:relative}
.arp-scope .ap-accordion .accordion-button{
  display:flex;align-items:center;
  padding:10px 14px;gap:10px;
  font-weight:800;
  background:var(--background-soft);
  color:var(--ink);
}
.arp-scope .ap-accordion .accordion-button:not(.collapsed){
  background:var(--surface);
  box-shadow:none;
}
.arp-scope .ap-accordion .accordion-button:focus{box-shadow:0 0 0 2px rgba(158,54,58,.18)}
.arp-scope .ap-accordion .accordion-body{overflow:visible}

.arp-scope .ap-module-header-inner{display:flex;align-items:center;justify-content:space-between;width:100%;gap:10px}
.arp-scope .ap-module-title{font-weight:900;font-size:0.98rem}
.arp-scope .ap-module-pill{
  font-size:0.78rem;border-radius:999px;padding:4px 10px;
  background:var(--background-soft);color:var(--muted-color);
  border:1px solid var(--line-soft);
  display:inline-flex;align-items:center;gap:6px;
  white-space:nowrap;
}
.arp-scope .ap-module-pill b{color:var(--ink);font-weight:900}

/* Module tools */
.arp-scope .ap-module-tools{
  display:flex;align-items:center;justify-content:space-between;gap:10px;
  margin-bottom:8px;flex-wrap:wrap;
}
.arp-scope .ap-module-tools-left{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.arp-scope .ap-module-tools-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

/* Privilege table */
.arp-scope .ap-module-body{padding:2px 0}
.arp-scope .ap-priv-table-wrap{margin-top:6px}
.arp-scope .ap-priv-table{width:100%;border-collapse:collapse;font-size:0.88rem}
.arp-scope .ap-priv-table thead th{
  background:var(--background-soft);
  color:var(--muted-color);
  font-weight:800;
  border-bottom:1px solid var(--line-soft);
  padding:8px 10px;
}
.arp-scope .ap-priv-table tbody td{
  border-top:1px solid var(--line-soft);
  padding:8px 10px;
  vertical-align:middle;
}
.arp-scope .ap-priv-row{transition:background .15s ease, box-shadow .15s ease, border-color .15s ease}
.arp-scope .ap-priv-row:hover{background:var(--background-hover);box-shadow:0 3px 10px rgba(0,0,0,0.03)}
.arp-scope .ap-priv-row.active{background:var(--background-soft);box-shadow:0 0 0 2px rgba(201,75,80,.10)}

/* Privilege text */
.arp-scope .ap-priv-title{font-size:0.94rem;font-weight:900;color:var(--ink);margin-bottom:2px}
.arp-scope .ap-priv-desc{font-size:0.82rem;color:var(--muted-color)}

/* Checkbox */
.arp-scope .ap-check{width:18px;height:18px;accent-color:var(--primary-color);cursor:pointer}

/* Empty State */
.arp-scope .ap-empty{padding:28px;text-align:center;color:var(--muted-color)}
.arp-scope .ap-empty i{font-size:2rem;margin-bottom:12px;opacity:.55}

/* Small helper */
.arp-scope .ap-small-muted{font-size:13px;color:var(--muted-color)}

/* Role picker row */
.arp-scope .ap-rolebar{
  display:flex;gap:10px;flex-wrap:wrap;align-items:end;
  margin-top:8px;
}
.arp-scope .ap-rolebar .form-label{font-size:.82rem;color:var(--muted-color);margin-bottom:4px}
.arp-scope .ap-rolebar .form-select,
.arp-scope .ap-rolebar .form-control{
  border-color:var(--line-strong);
  border-radius:12px;
  height:40px;
}
.arp-scope .ap-rolebar .form-select:focus,
.arp-scope .ap-rolebar .form-control:focus{
  box-shadow:0 0 0 2px rgba(158,54,58,.16);
  border-color:rgba(158,54,58,.55);
}
.arp-scope .ap-rolebar .ap-role-actions{display:flex;gap:8px;flex-wrap:wrap}

.arp-scope .ap-rolebar .ap-role-field.is-ready .form-select,
.arp-scope .ap-rolebar .ap-role-field.is-ready .form-control{
  border-color:rgba(158,54,58,.45);
  box-shadow:0 0 0 3px rgba(158,54,58,.08);
  background:linear-gradient(180deg, color-mix(in oklab, var(--surface) 88%, rgba(201,75,80,.12)), var(--surface));
}

.arp-scope .ap-rolebar .ap-load-ready{
  background:var(--primary-color);
  color:#fff;
  border-color:transparent;
}

.arp-scope .ap-rolebar .ap-load-ready:hover,
.arp-scope .ap-rolebar .ap-load-ready:focus{
  background:var(--primary-hover, var(--primary-color));
  color:#fff;
}

.arp-scope .ap-load-state{
  display:none;
  margin-top:8px;
  align-items:center;
  gap:8px;
  font-size:.84rem;
  color:var(--muted-color);
}

.arp-scope .ap-load-state.is-ready{
  display:flex;
}

.arp-scope .ap-load-state .dot{
  width:8px;
  height:8px;
  border-radius:999px;
  background:var(--success-color, #16a34a);
  box-shadow:0 0 0 5px rgba(34,197,94,.12);
}

/* Responsive */
@media (max-width: 576px){
  .arp-scope .ap-card-head{align-items:flex-start}
  .arp-scope .ap-card-head-right{width:100%;justify-content:flex-start}
  .arp-scope .ap-tools .input-group{min-width:100%;max-width:100%}
  .arp-scope .ap-kpi{width:100%;justify-content:flex-start;margin-left:0}
  .arp-scope .ap-priv-table thead{display:none}
  .arp-scope .ap-priv-table tbody td{
    display:block;width:100%;
    border-top:none;border-bottom:1px solid var(--line-soft);
  }
  .arp-scope .ap-priv-table tbody tr:last-child td{border-bottom:none}
  .arp-scope .ap-priv-table tbody td:first-child{padding-top:10px}
  .arp-scope .ap-priv-table tbody td:last-child{padding-bottom:10px}
  .arp-scope .ap-profile-name{max-width:240px}
  .arp-scope .ap-profile-sub{max-width:260px}
  .arp-scope .ap-rolebar .ap-role-actions{width:100%;justify-content:flex-start}
}
</style>
@endpush
@section('content')

<div class="ap-wrap arp-scope">
  {{-- Header Panel --}}
  <div class="row g-2 mb-2 align-items-center panel">
    <div class="col-12 col-lg">
      <h4 class="mb-2">Manage Role Privileges</h4>

      {{-- Role picker --}}
      <div class="ap-rolebar">
        <div id="roleSelectWrap" class="ap-role-field" style="min-width:260px;max-width:420px;flex:1">
          <label class="form-label">Select Role</label>
          <select id="selRole" class="form-select">
            <option value="">-- choose role --</option>
          </select>
        </div>

        <div class="ap-role-actions">
          <button id="btnLoadRole" class="btn btn-light" disabled>
            <i class="fa fa-download me-1"></i>Load
          </button>
          <button id="btnRefreshRole" class="btn btn-light" disabled>
            <i class="fa fa-rotate-right me-1"></i>Refresh
          </button>
          <button id="btnSaveRole" class="btn btn-primary" disabled>
            <i class="fa fa-save me-1"></i>Save
          </button>
        </div>
      </div>

      <div id="roleLoadState" class="ap-load-state">
        <span class="dot"></span>
        <span>Role selected. Click <b>Load</b> to fetch privileges.</span>
      </div>

      <div id="roleSummary" class="ap-small-muted mt-2" style="display:none;"></div>
    </div>
  </div>

  {{-- Mini role card --}}
  <div class="ap-profile">
    <div id="apAvatar" class="ap-avatar">R</div>
    <div class="ap-profile-meta">
      <div id="apProfileName" class="ap-profile-name">Select a role to load…</div>
      <div id="apProfileSub" class="ap-profile-sub">Role privileges will appear below</div>
      <div id="apProfileBadges" class="ap-profile-badges" style="display:none;"></div>
    </div>
  </div>

  {{-- Main card --}}
  <div class="ap-card">
    <div class="ap-card-head">
      <div class="ap-card-head-left">
        <span class="ap-card-head-title">Modules &amp; Privileges</span>
        <span class="ap-card-head-sub">
          Tick the privileges you want this <strong>role</strong> to have, then click <strong>Save</strong>.
        </span>
      </div>

      <div class="ap-card-head-right">
        <label class="form-check mb-0 d-flex align-items-center gap-2">
          <input class="form-check-input" type="checkbox" id="chkGlobalSelectAll" disabled>
          <span class="ap-small-muted">Select all</span>
        </label>
      </div>
    </div>

    <div class="ap-card-body">
      {{-- search + KPIs --}}
      <div class="ap-tools">
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="fa fa-magnifying-glass"></i></span>
          <input id="txtSearch" type="text" class="form-control" placeholder="Search privilege (e.g., create, edit, view)…" disabled>
          <button id="btnClearSearch" class="btn btn-light" type="button" title="Clear search" disabled>
            <i class="fa fa-xmark"></i>
          </button>
        </div>

        <div class="ap-kpi">
          <span class="ap-chip"><i class="fa fa-list-check"></i> Selected: <b id="kpiSelected">0</b></span>
          <span class="ap-chip"><i class="fa fa-shield-halved"></i> Total: <b id="kpiTotal">0</b></span>
        </div>
      </div>

      <div id="modulesContainer" class="accordion ap-accordion">
        <div class="ap-empty">
          <i class="fa fa-arrow-up-right-dots"></i>
          <div>Select a role and click <b>Load</b>.</div>
        </div>
      </div>

      <div id="modulesEmpty" class="ap-empty" style="display:none;">
        <i class="fa fa-folder-open"></i>
        <div>No modules or privileges found.</div>
      </div>
    </div>
  </div>

  <div class="ap-small-muted mt-3">
    Tip: Use <b>Select all</b> for every privilege, or per-module <b>Select all</b> to control one module.
    Changes are only saved after you click <strong>Save</strong>.
  </div>
</div>

{{-- Toasts --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1200">
  <div id="toastOk" class="toast text-bg-success border-0">
    <div class="d-flex">
      <div id="toastOkMsg" class="toast-body">Done</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="toastErr" class="toast text-bg-danger border-0 mt-2">
    <div class="d-flex">
      <div id="toastErrMsg" class="toast-body">Something went wrong</div>
      <button class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', ()=> {

  // ✅ optional: preselect role from query (?role=admin)
  const params = new URLSearchParams(location.search);
  const roleFromQuery = (params.get('role') || '').trim();

  const token = localStorage.getItem('token') || sessionStorage.getItem('token') || '';
  if(!token){
    alert('Login required. Redirecting to home.');
    location.href='/';
    return;
  }

  const authHeaders = (extra={}) =>
    Object.assign({'Authorization':'Bearer '+token, 'Accept':'application/json'}, extra);

  const toastOk  = new bootstrap.Toast(document.getElementById('toastOk'));
  const toastErr = new bootstrap.Toast(document.getElementById('toastErr'));
  const ok  = (m='Done') => { document.getElementById('toastOkMsg').textContent = m; toastOk.show(); };
  const err = (m='Something went wrong') => { document.getElementById('toastErrMsg').textContent = m; toastErr.show(); };

  const modulesContainer   = document.getElementById('modulesContainer');
  const modulesEmpty       = document.getElementById('modulesEmpty');

  const selRole        = document.getElementById('selRole');
  const roleSelectWrap = document.getElementById('roleSelectWrap');
  const roleLoadState  = document.getElementById('roleLoadState');
  const btnLoadRole    = document.getElementById('btnLoadRole');
  const btnRefreshRole = document.getElementById('btnRefreshRole');
  const btnSaveRole    = document.getElementById('btnSaveRole');

  const chkGlobalSelectAll = document.getElementById('chkGlobalSelectAll');
  const txtSearch      = document.getElementById('txtSearch');
  const btnClearSearch = document.getElementById('btnClearSearch');
  const kpiSelected    = document.getElementById('kpiSelected');
  const kpiTotal       = document.getElementById('kpiTotal');

  // role profile nodes
  const apAvatar        = document.getElementById('apAvatar');
  const apProfileName   = document.getElementById('apProfileName');
  const apProfileSub    = document.getElementById('apProfileSub');
  const apProfileBadges = document.getElementById('apProfileBadges');
  const roleSummary     = document.getElementById('roleSummary');

  let modules         = [];
  let assignedPrivIds = new Set();

  let activeRole      = ''; // resolved role string
  let isSaving        = false;
  let isLoadingRole   = false;

  function escapeHtml(s){
    return (s||'').toString().replace(/[&<>"'`]/g, ch => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;','`':'&#96;'
    }[ch]));
  }

  function roleInitials(role){
    const s = (role || '').trim();
    if(!s) return 'R';
    const parts = s.split(/[_\s\-]+/).filter(Boolean);
    if(parts.length === 1) return parts[0].slice(0,2).toUpperCase();
    return (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
  }

  function setUiEnabled(enabled){
    btnRefreshRole.disabled = !enabled;
    btnSaveRole.disabled    = !enabled;
    chkGlobalSelectAll.disabled = !enabled;
    txtSearch.disabled = !enabled;
    btnClearSearch.disabled = !enabled;
  }

  function setLoadButtonState(){
    const readyRole = resolveRoleFromInputs();
    const ready = !!readyRole && !isLoadingRole;

    btnLoadRole.disabled = !ready;
    btnLoadRole.classList.toggle('ap-load-ready', ready);
    roleLoadState?.classList.toggle('is-ready', !!readyRole && !activeRole);

    roleSelectWrap?.classList.toggle('is-ready', !!(selRole.value || '').trim());
  }

  function setRoleLoadingState(on){
    isLoadingRole = !!on;
    btnLoadRole.disabled = !!on || !resolveRoleFromInputs();
    btnLoadRole.innerHTML = on
      ? '<span class="spinner-border spinner-border-sm me-1"></span>Loading'
      : '<i class="fa fa-download me-1"></i>Load';
    setLoadButtonState();
  }

  function renderRoleCard(role){
    const r = (role || '').trim();
    if (!r){
      apAvatar.textContent = 'R';
      apProfileName.textContent = 'Select a role to load…';
      apProfileSub.textContent  = 'Role privileges will appear below';
      apProfileBadges.style.display = 'none';
      apProfileBadges.innerHTML = '';
      return;
    }

    apAvatar.textContent = roleInitials(r);
    apProfileName.textContent = r;
    apProfileSub.textContent  = 'Assign privileges for this role';

    const badges = [
      `<span class="ap-badge"><i class="fa fa-user-shield"></i><b>Role</b> ${escapeHtml(r)}</span>`
    ];

    apProfileBadges.innerHTML = badges.join('');
    apProfileBadges.style.display = '';
  }

  function updateKPIs(){
    const total = modulesContainer.querySelectorAll('.ap-priv-row').length;
    const selected = assignedPrivIds.size;
    kpiTotal.textContent = String(total);
    kpiSelected.textContent = String(selected);

    if (roleSummary){
      roleSummary.style.display = activeRole ? '' : 'none';
      roleSummary.innerHTML = activeRole
        ? `<i class="fa fa-circle-info me-1"></i> Role <b>${escapeHtml(activeRole)}</b>: Selected <b>${selected}</b> of <b>${total}</b> privileges`
        : '';
    }
  }

  // ==========================================================
  // ✅ Dropdown dropup support (auto decide before opening)
  // ==========================================================
  document.addEventListener('show.bs.dropdown', (ev) => {
    const dd = ev.target;
    if (!dd || !dd.classList || !dd.classList.contains('dropdown')) return;

    const toggle = dd.querySelector('[data-bs-toggle="dropdown"]');
    const menu   = dd.querySelector('.dropdown-menu');
    if (!toggle || !menu) return;

    const prevDisplay = menu.style.display;
    const prevVis     = menu.style.visibility;
    const prevPos     = menu.style.position;

    menu.style.visibility = 'hidden';
    menu.style.display    = 'block';
    menu.style.position   = 'absolute';
    const menuHeight      = menu.getBoundingClientRect().height || menu.offsetHeight || 220;

    menu.style.display    = prevDisplay;
    menu.style.visibility = prevVis;
    menu.style.position   = prevPos;

    const rect = toggle.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    if (spaceBelow < menuHeight && spaceAbove > spaceBelow) dd.classList.add('dropup');
    else dd.classList.remove('dropup');
  });

  // ==========================================================
  // ✅ Roles dropdown options (edit as you like)
  // (role is NOT restricted server-side; this is only UI suggestions)
  // ==========================================================
  const ROLE_OPTIONS = [
    'admin','hr','employee'
  ];

  function populateRoleSelect(){
    const frag = document.createDocumentFragment();
    ROLE_OPTIONS.forEach(r=>{
      const opt = document.createElement('option');
      opt.value = r;
      opt.textContent = r;
      frag.appendChild(opt);
    });
    selRole.appendChild(frag);

    if (roleFromQuery){
      // try to set from query
      const exists = Array.from(selRole.options).some(o => o.value === roleFromQuery);
      if (exists) selRole.value = roleFromQuery;
    }
  }

  function resolveRoleFromInputs(){
    const sel = (selRole.value || '').trim();
    return sel;
  }

  // ==========================================================
  // ✅ Load assigned privileges for role
  // expects: GET /api/role-privileges/list?role=ROLE
  // response: { flat_privilege_ids:[...], tree:[...], ... }
  // ==========================================================
  async function loadAssignedPrivilegesForRole(role){
    assignedPrivIds = new Set();
    if (!role) return;

    const res = await fetch(`/api/role-privileges/list?role=${encodeURIComponent(role)}`, {
      headers: authHeaders()
    });
    const js = await res.json().catch(()=>({}));
    if(!res.ok) throw new Error(js.message || js.error || 'Failed to load role privileges');

    const ids = Array.isArray(js.flat_privilege_ids) ? js.flat_privilege_ids : [];
    ids.forEach(id => assignedPrivIds.add(String(id)));
  }

  // ==========================================================
  // ✅ Load modules tree with privileges
  // expects: GET /api/dashboard-menus/all-with-privileges
  // response: { data:[tree...] }
  // ==========================================================
  async function loadModulesTree(){
    const res = await fetch('/api/dashboard-menus/all-with-privileges', { headers: authHeaders() });
    const js = await res.json().catch(()=>({}));
    if(!res.ok) throw new Error(js.message || 'Failed to load modules');
    modules = Array.isArray(js.data) ? js.data : [];
  }

  // ==========================================================
  // ✅ Select-all + pills
  // ==========================================================
  function updateModulePill(moduleEl, checkedCount, totalCount){
    const pillSel = moduleEl.querySelector('.ap-pill-selected');
    const pillTot = moduleEl.querySelector('.ap-pill-total');
    if (pillSel) pillSel.textContent = String(checkedCount);
    if (pillTot) pillTot.textContent = String(totalCount);
  }

  function updateModuleSelectAllState(moduleEl){
    if (!moduleEl) return;
    const moduleCheckbox = moduleEl.querySelector('.ap-mod-select-all');
    const rows = moduleEl.querySelectorAll('.ap-priv-row');
    const total = rows.length;

    let checkedCount = 0;
    rows.forEach(row=>{
      const cb = row.querySelector('.sm-privilege-checkbox');
      if (cb && cb.checked) checkedCount++;
    });

    updateModulePill(moduleEl, checkedCount, total);

    if (!moduleCheckbox) return;
    if (!total){
      moduleCheckbox.checked = false;
      moduleCheckbox.indeterminate = false;
      return;
    }

    if (checkedCount === 0){
      moduleCheckbox.checked = false;
      moduleCheckbox.indeterminate = false;
    } else if (checkedCount === total){
      moduleCheckbox.checked = true;
      moduleCheckbox.indeterminate = false;
    } else {
      moduleCheckbox.checked = false;
      moduleCheckbox.indeterminate = true;
    }
  }

  function updateAllModulesSelectAllState(){
    const moduleEls = modulesContainer.querySelectorAll('.accordion-item');
    moduleEls.forEach(updateModuleSelectAllState);
  }

  function updateGlobalSelectAllState(){
    const checkboxes = modulesContainer.querySelectorAll('.ap-priv-row .sm-privilege-checkbox');
    if (!checkboxes.length){
      chkGlobalSelectAll.checked = false;
      chkGlobalSelectAll.indeterminate = false;
      return;
    }

    let checkedCount = 0;
    checkboxes.forEach(cb=>{ if (cb.checked) checkedCount++; });

    if (checkedCount === 0){
      chkGlobalSelectAll.checked = false;
      chkGlobalSelectAll.indeterminate = false;
    } else if (checkedCount === checkboxes.length){
      chkGlobalSelectAll.checked = true;
      chkGlobalSelectAll.indeterminate = false;
    } else {
      chkGlobalSelectAll.checked = false;
      chkGlobalSelectAll.indeterminate = true;
    }
  }

  // ==========================================================
  // ✅ TREE helpers + builder
  // ==========================================================
  function countPrivilegesInTree(node){
    let c = (node.privileges && node.privileges.length) ? node.privileges.length : 0;
    if (node.children && node.children.length){
      node.children.forEach(ch => { c += countPrivilegesInTree(ch); });
    }
    return c;
  }

  function buildAccordionItem(node, index, depth = 0, rootHeaderId = null){
    const item       = document.createElement('div');
    const collapseId = `ap_mod_${node.id ?? ('i'+index)}_${depth}_${index}`;
    const headerId   = `ap_modh_${node.id ?? ('i'+index)}_${depth}_${index}`;

    const hasChildren      = !!(node.children && node.children.length);
    const hasOwnPrivileges = !!(node.privileges && node.privileges.length);
    const showPrivilegeTable = hasOwnPrivileges || !hasChildren;
    const privCount  = countPrivilegesInTree(node);

    item.className = 'accordion-item';

    const myRootHeaderId = (depth === 0) ? (node.id ?? null) : rootHeaderId;

    item.innerHTML = `
      <h2 class="accordion-header" id="${headerId}">
        <button class="accordion-button ${index ? 'collapsed' : ''}" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#${collapseId}"
                aria-expanded="${index ? 'false':'true'}"
                aria-controls="${collapseId}">
          <div class="ap-module-header-inner">
            <span class="ap-module-title">${escapeHtml(node.name || ('Module #'+(node.id || (index+1))))}</span>
            <span class="ap-module-pill">
              <i class="fa fa-shield-halved"></i>
              Selected <b class="ap-pill-selected">0</b>/<b class="ap-pill-total">${privCount}</b>
            </span>
          </div>
        </button>
      </h2>

      <div id="${collapseId}" class="accordion-collapse collapse ${index ? '' : 'show'}"
           aria-labelledby="${headerId}">
        <div class="accordion-body">
          <div class="ap-module-tools">
            <div class="ap-module-tools-left">
              <span class="ap-small-muted">Quick actions:</span>
            </div>

            <div class="ap-module-tools-right">
              <label class="form-check mb-0 d-flex align-items-center gap-2">
                <input class="form-check-input ap-mod-select-all" type="checkbox">
                <span class="ap-small-muted">Select all (this module)</span>
              </label>

              <div class="dropdown ap-dd">
                <button class="btn btn-light btn-sm dd-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-ellipsis"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><button class="dropdown-item" type="button" data-mod-action="expand"><i class="fa fa-square-plus"></i> Expand subtree</button></li>
                  <li><button class="dropdown-item" type="button" data-mod-action="collapse"><i class="fa fa-square-minus"></i> Collapse subtree</button></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><button class="dropdown-item" type="button" data-mod-action="selectAll"><i class="fa fa-check-double"></i> Select all privileges</button></li>
                  <li><button class="dropdown-item text-danger" type="button" data-mod-action="clear"><i class="fa fa-trash"></i> Clear privileges</button></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="ap-module-body">
            <div class="ap-priv-table-wrap" ${showPrivilegeTable ? '' : 'style="display:none"'} >
              <table class="ap-priv-table">
                <thead>
                  <tr>
                    <th style="width:70px" class="text-center">Select</th>
                    <th>Privilege</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="ap-child-accordion mt-2"></div>
          </div>
        </div>
      </div>
    `;

    const tbody           = item.querySelector('tbody');
    const moduleSelectAll = item.querySelector('.ap-mod-select-all');
    const tableWrap       = item.querySelector('.ap-priv-table-wrap');

    if (showPrivilegeTable){
      if(!hasOwnPrivileges){
        tbody.innerHTML = `
          <tr><td colspan="2">
            <div class="ap-empty ap-small-muted">
              <i class="fa fa-ban"></i>
              <div>No privileges for this module</div>
            </div>
          </td></tr>`;
      } else {
        node.privileges.forEach(p => {
          const pid     = String(p.id ?? '');
          const checked = assignedPrivIds.has(pid);

          const tr = document.createElement('tr');
          tr.className = 'ap-priv-row';

          tr.dataset.privId   = pid;
          tr.dataset.action   = String(p.action || p.name || '').toLowerCase();
          tr.dataset.pageId   = String(node.id ?? '0');
          tr.dataset.headerId = String(myRootHeaderId || '0');

          const title  = p.action || p.name || 'Untitled';
          const desc   = p.description || '';

          tr.innerHTML = `
            <td class="text-center">
              <input type="checkbox" class="ap-check sm-privilege-checkbox" ${checked ? 'checked':''}>
            </td>
            <td>
              <div class="ap-priv-title">${escapeHtml(title)}</div>
              ${desc ? `<div class="ap-priv-desc">${escapeHtml(desc)}</div>` : ''}
            </td>
          `;

          const checkbox = tr.querySelector('.sm-privilege-checkbox');
          if (checked) tr.classList.add('active');

          checkbox.addEventListener('change', (ev) => {
            const nowChecked = ev.target.checked;
            if (nowChecked){
              assignedPrivIds.add(pid);
              tr.classList.add('active');
            } else {
              assignedPrivIds.delete(pid);
              tr.classList.remove('active');
            }
            const moduleEl = tr.closest('.accordion-item');
            updateModuleSelectAllState(moduleEl);
            updateGlobalSelectAllState();
            updateKPIs();
          });

          tr.addEventListener('click', (e)=>{
            if (e.target && (e.target.matches('input'))) return;
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change', { bubbles:true }));
          });

          tbody.appendChild(tr);
        });
      }
    } else {
      if (tbody) tbody.innerHTML = '';
      if (tableWrap) tableWrap.style.display = 'none';
    }

    const childWrap = item.querySelector('.ap-child-accordion');
    if (hasChildren){
      const nested = document.createElement('div');
      nested.className = 'accordion ap-accordion';
      node.children.forEach((ch, i) => nested.appendChild(buildAccordionItem(ch, i, depth + 1, myRootHeaderId)));
      childWrap.appendChild(nested);
    }

    if (moduleSelectAll){
      moduleSelectAll.addEventListener('change', (ev)=>{
        const checked = ev.target.checked;
        const rows = item.querySelectorAll('.ap-priv-row');
        rows.forEach(row=>{
          const cb  = row.querySelector('.sm-privilege-checkbox');
          const key = row.dataset.privId;
          if (!cb || !key) return;

          cb.checked = checked;
          if (checked){
            assignedPrivIds.add(key);
            row.classList.add('active');
          } else {
            assignedPrivIds.delete(key);
            row.classList.remove('active');
          }
        });

        updateModuleSelectAllState(item);
        updateGlobalSelectAllState();
        updateKPIs();
      });
    }

    return item;
  }

  function renderModules(){
    modulesContainer.innerHTML = '';
    if(!modules.length){
      modulesEmpty.style.display = '';
      chkGlobalSelectAll.checked = false;
      chkGlobalSelectAll.indeterminate = false;
      updateKPIs();
      return;
    }
    modulesEmpty.style.display = 'none';

    modules.forEach((rootNode, index) => {
      modulesContainer.appendChild(buildAccordionItem(rootNode, index, 0, null));
    });

    updateAllModulesSelectAllState();
    updateGlobalSelectAllState();
    updateKPIs();
    applySearchFilter();
  }

  // ==========================================================
  // ✅ Global select all
  // ==========================================================
  chkGlobalSelectAll?.addEventListener('change', (e)=>{
    const checked = e.target.checked;
    const rows = modulesContainer.querySelectorAll('.ap-priv-row');
    rows.forEach(row=>{
      const cb  = row.querySelector('.sm-privilege-checkbox');
      const key = row.dataset.privId;
      if (!cb || !key) return;

      cb.checked = checked;
      if (checked){
        assignedPrivIds.add(String(key));
        row.classList.add('active');
      } else {
        assignedPrivIds.delete(String(key));
        row.classList.remove('active');
      }
    });

    updateAllModulesSelectAllState();
    updateGlobalSelectAllState();
    updateKPIs();
    ok(checked ? 'All selected (not yet saved)' : 'All deselected (not yet saved)');
  });

  // ==========================================================
  // ✅ Build TREE payload from UI
  // payload: [{id:headerId,type:'header',children:[{id:pageId,type:'page',privileges:[{id,action}]}]}]
  // ==========================================================
  function buildTreePayloadFromUI(){
    const headersMap = new Map();

    const rows = modulesContainer.querySelectorAll('.ap-priv-row');
    rows.forEach(row=>{
      const pid = row.dataset.privId;
      if (!pid) return;
      if (!assignedPrivIds.has(String(pid))) return;

      const pageIdRaw   = Number(row.dataset.pageId || 0);
      const headerIdRaw = Number(row.dataset.headerId || 0);

      const pageId   = pageIdRaw || headerIdRaw || 0;
      const headerId = headerIdRaw || pageId || 0;

      const action = String(row.dataset.action || '').toLowerCase() || null;

      if (!pageId || !headerId) return;

      if (!headersMap.has(headerId)){
        headersMap.set(headerId, { id: headerId, type: "header", children: [] });
      }

      const headerNode = headersMap.get(headerId);

      let pageNode = headerNode.children.find(x => Number(x.id) === pageId);
      if (!pageNode){
        pageNode = { id: pageId, type: "page", privileges: [] };
        headerNode.children.push(pageNode);
      }

      if (!pageNode.privileges.some(x => Number(x.id) === Number(pid))){
        pageNode.privileges.push({ id: Number(pid), action });
      }
    });

    const tree = Array.from(headersMap.values());
    tree.sort((a,b)=>a.id-b.id);
    tree.forEach(h=>{
      h.children.sort((a,b)=>a.id-b.id);
      h.children.forEach(p=>{
        if (Array.isArray(p.privileges)) p.privileges.sort((x,y)=>x.id-y.id);
      });
    });

    return tree;
  }

  // ==========================================================
  // ✅ Search filter
  // ==========================================================
  function applySearchFilter(){
    const q = (txtSearch?.value || '').trim().toLowerCase();
    const rows = modulesContainer.querySelectorAll('.ap-priv-row');

    if(!q){
      rows.forEach(r=> r.style.display = '');
      return;
    }

    rows.forEach(r=>{
      const title = (r.querySelector('.ap-priv-title')?.textContent || '').toLowerCase();
      const desc  = (r.querySelector('.ap-priv-desc')?.textContent || '').toLowerCase();
      const hit = title.includes(q) || desc.includes(q) || (r.dataset.action || '').includes(q);
      r.style.display = hit ? '' : 'none';
    });
  }

  txtSearch?.addEventListener('input', applySearchFilter);
  btnClearSearch?.addEventListener('click', ()=>{
    if (txtSearch) txtSearch.value = '';
    applySearchFilter();
    txtSearch?.focus();
  });

  // ==========================================================
  // ✅ Global actions dropdown
  // ==========================================================
  function setAllCollapses(expand){
    const colls = modulesContainer.querySelectorAll('.accordion-collapse');
    colls.forEach(c=>{
      const inst = bootstrap.Collapse.getOrCreateInstance(c, { toggle:false });
      expand ? inst.show() : inst.hide();
    });
  }

  function clearAllSelections(){
    assignedPrivIds.clear();
    const rows = modulesContainer.querySelectorAll('.ap-priv-row');
    rows.forEach(row=>{
      const cb = row.querySelector('.sm-privilege-checkbox');
      if (cb) cb.checked = false;
      row.classList.remove('active');
    });
    updateAllModulesSelectAllState();
    updateGlobalSelectAllState();
    updateKPIs();
  }

  function selectAllSelections(){
    const rows = modulesContainer.querySelectorAll('.ap-priv-row');
    rows.forEach(row=>{
      const cb = row.querySelector('.sm-privilege-checkbox');
      const key = row.dataset.privId;
      if (!cb || !key) return;
      cb.checked = true;
      assignedPrivIds.add(String(key));
      row.classList.add('active');
    });
    updateAllModulesSelectAllState();
    updateGlobalSelectAllState();
    updateKPIs();
  }

  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-ap-action]');
    if(!btn) return;
    e.preventDefault();
    if (!activeRole) { err('Select a role first'); return; }

    const action = btn.dataset.apAction;
    if (action === 'expandAll') { setAllCollapses(true); return; }
    if (action === 'collapseAll') { setAllCollapses(false); return; }
    if (action === 'clearAll') { clearAllSelections(); ok('Cleared (not yet saved)'); return; }
    if (action === 'selectAll') { selectAllSelections(); ok('Selected all (not yet saved)'); return; }
  });

  // module dropdown actions
  modulesContainer.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-mod-action]');
    if(!btn) return;
    e.preventDefault();

    const moduleEl = btn.closest('.accordion-item');
    if(!moduleEl) return;

    const act = btn.dataset.modAction;

    if (act === 'expand' || act === 'collapse'){
      const colls = moduleEl.querySelectorAll('.accordion-collapse');
      colls.forEach(c=>{
        const inst = bootstrap.Collapse.getOrCreateInstance(c, { toggle:false });
        act === 'expand' ? inst.show() : inst.hide();
      });
      return;
    }

    const modSelect = moduleEl.querySelector('.ap-mod-select-all');
    if (act === 'selectAll'){
      if (modSelect){ modSelect.checked = true; modSelect.indeterminate = false; modSelect.dispatchEvent(new Event('change', {bubbles:true})); }
      return;
    }
    if (act === 'clear'){
      if (modSelect){ modSelect.checked = false; modSelect.indeterminate = false; modSelect.dispatchEvent(new Event('change', {bubbles:true})); }
      return;
    }
  });

  // ==========================================================
  // ✅ Load role flow
  // ==========================================================
  async function loadForRole(role){
    activeRole = (role || '').trim();
    renderRoleCard(activeRole);

    if (!activeRole){
      setUiEnabled(false);
      modulesContainer.innerHTML = `<div class="ap-empty"><i class="fa fa-arrow-up-right-dots"></i><div>Select a role and click <b>Load</b>.</div></div>`;
      updateKPIs();
      return;
    }

    setUiEnabled(true);

    modulesContainer.innerHTML = `<div class="ap-empty">Loading modules &amp; role privileges…</div>`;
    modulesEmpty.style.display = 'none';

    try{
      await loadModulesTree();
      await loadAssignedPrivilegesForRole(activeRole);
      renderModules();
      ok('Loaded role privileges');
    }catch(e){
      console.error(e);
      modulesContainer.innerHTML = `<div class="ap-empty text-danger">Failed to load: ${escapeHtml(e.message || '')}</div>`;
      assignedPrivIds = new Set();
      updateKPIs();
      err(e.message || 'Load failed');
    }
  }

  btnLoadRole.addEventListener('click', async ()=>{
    const role = resolveRoleFromInputs();
    setRoleLoadingState(true);
    await loadForRole(role);
    setRoleLoadingState(false);
  });

  btnRefreshRole.addEventListener('click', async ()=>{
    if (!activeRole) { err('Select a role first'); return; }
    setRoleLoadingState(true);
    await loadForRole(activeRole);
    setRoleLoadingState(false);
  });

  // Save (sync)
  btnSaveRole.addEventListener('click', async ()=>{
    if (isSaving) return;
    if (!activeRole) { err('Select a role first'); return; }

    isSaving = true;
    btnSaveRole.disabled = true;
    btnSaveRole.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving';

    try{
      const tree = buildTreePayloadFromUI();
      const flat_privileges = Array.from(assignedPrivIds).map(v => Number(v)).filter(Boolean);

      const payload = {
        role: String(activeRole),              // ✅ role string (varchar)
        tree: Array.isArray(tree) ? tree : [],
        privileges: flat_privileges
      };

      const res = await fetch('/api/role-privileges/sync', {
        method:'POST',
        headers: authHeaders({'Content-Type':'application/json'}),
        body: JSON.stringify(payload)
      });

      const js = await res.json().catch(()=>({}));
      if(!res.ok) throw new Error(js.message || js.error || 'Sync failed');

      ok('Role privileges saved');
      await loadAssignedPrivilegesForRole(activeRole);
      renderModules();
    }catch(e){
      console.error(e);
      err(e.message || 'Save failed');
    }finally{
      isSaving = false;
      btnSaveRole.disabled = false;
      btnSaveRole.innerHTML = '<i class="fa fa-save me-1"></i>Save';
    }
  });

  // ==========================================================
  // Boot
  // ==========================================================
  populateRoleSelect();
  setLoadButtonState();

  selRole?.addEventListener('change', ()=>{
    renderRoleCard(resolveRoleFromInputs());
    setLoadButtonState();
  });

  // auto-load if role present
  if (roleFromQuery){
    const role = resolveRoleFromInputs();
    if (role) {
      setRoleLoadingState(true);
      loadForRole(role).finally(()=> setRoleLoadingState(false));
    }
  } else {
    renderRoleCard('');
    setUiEnabled(false);
    updateKPIs();
    setLoadButtonState();
  }
});
</script>
@endpush
