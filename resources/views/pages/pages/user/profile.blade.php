@extends('pages.layout.structure')

@section('title','My Profile')

@push('styles')
<style>
.mprof-wrap{
  padding:2px 0;
}

.mprof-head{
  padding:10px 12px;
  margin-bottom:12px;
}

.mprof-head-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}

.mprof-head-copy{
  display:flex;
  align-items:center;
  min-height:30px;
}

.mprof-head-copy h1{
  margin:0;
  font-size:var(--fs-15);
  line-height:1.15;
  display:flex;
  align-items:center;
  gap:6px;
  flex-wrap:wrap;
}

.mprof-head-copy .seg-muted{
  color:var(--muted-color);
  font-weight:500;
}

.mprof-head-copy .seg-sep{
  color:var(--muted-color);
  opacity:.7;
}

.mprof-head-sub{
  margin-top:5px;
  color:var(--muted-color);
  font-size:12px;
}

.mprof-head-actions{
  display:flex;
  align-items:center;
  gap:8px;
}

.mprof-refresh-btn{
  min-width:30px;
  width:30px;
  height:30px;
  padding:0;
  display:inline-flex;
  align-items:center;
  justify-content:center;
}

.mprof-refresh-btn.is-spinning i{
  animation:mprofSpin .8s linear infinite;
}

@keyframes mprofSpin{
  to{ transform:rotate(360deg); }
}

.mprof-card,
.mprof-panel{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:16px;
  box-shadow:var(--shadow-1);
}

.mprof-panel{
  padding:14px;
  margin-bottom:12px;
}

.mprof-hero{
  padding:26px 18px 20px;
  text-align:center;
  margin-bottom:12px;
}

.mprof-avatar-wrap{
  position:relative;
  width:126px;
  height:126px;
  margin:0 auto 14px;
}

.mprof-avatar-trigger{
  position:relative;
  width:126px;
  height:126px;
  border:0;
  background:transparent;
  padding:0;
  cursor:pointer;
}

.mprof-avatar{
  width:126px;
  height:126px;
  border-radius:28px;
  object-fit:cover;
  border:1px solid var(--line-strong);
  background:var(--surface-2);
  display:block;
}

.mprof-avatar-fallback{
  width:126px;
  height:126px;
  border-radius:28px;
  border:1px solid var(--line-strong);
  background:var(--surface-2);
  color:var(--muted-color);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:38px;
  font-weight:800;
}

.mprof-avatar-camera{
  position:absolute;
  right:-6px;
  bottom:-6px;
  width:38px;
  height:38px;
  border-radius:14px;
  border:1px solid var(--line-strong);
  background:var(--surface);
  color:var(--primary-color);
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 12px 28px rgba(15,23,42,.14);
}

.mprof-name{
  margin:0;
  font-size:22px;
  font-weight:800;
  color:var(--ink);
}

.mprof-hero-badges{
  margin-top:10px;
  display:flex;
  justify-content:center;
  gap:8px;
  flex-wrap:wrap;
}

.mprof-pill{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 12px;
  border-radius:999px;
  border:1px solid var(--line-strong);
  background:var(--surface-2);
  font-size:12px;
  font-weight:700;
}

.mprof-pill.role{
  color:var(--primary-color);
}

.mprof-pill.status.active{
  color:#15803d;
  border-color:rgba(22,163,74,.22);
  background:rgba(22,163,74,.08);
}

.mprof-pill.status.inactive{
  color:#a16207;
  border-color:rgba(234,179,8,.22);
  background:rgba(234,179,8,.08);
}

.mprof-email{
  margin-top:10px;
  color:var(--muted-color);
  font-size:14px;
  word-break:break-word;
}

.mprof-avatar-note{
  margin-top:10px;
  color:var(--muted-color);
  font-size:12px;
}

.mprof-tabs-panel{
  padding:12px;
}

.mprof-tabs.nav-tabs{
  border-bottom:1px solid var(--line-strong);
  gap:8px;
}

.mprof-tabs .nav-link{
  border:1px solid transparent;
  border-radius:12px 12px 0 0;
  color:var(--muted-color);
  font-weight:700;
  padding:.75rem 1rem;
}

.mprof-tabs .nav-link.active{
  color:var(--primary-color);
  border-color:var(--line-strong) var(--line-strong) var(--surface);
  background:var(--surface);
}

.mprof-tab-pane{
  padding:16px 4px 4px;
}

.mprof-form .form-label{
  font-weight:500;
}

.mprof-form .form-control,
.mprof-form textarea{
  border:1px solid var(--line-strong);
  background:#fff;
}

.mprof-form .form-control:focus,
.mprof-form textarea:focus{
  border-color:var(--primary-color);
  box-shadow:0 0 0 .2rem rgba(149,30,170,.18);
}

.mprof-help{
  color:var(--muted-color);
  font-size:12px;
}

.mprof-actions{
  display:flex;
  align-items:center;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:wrap;
  margin-top:8px;
}

.mprof-strength{
  display:flex;
  gap:6px;
  margin-top:8px;
}

.mprof-strength span{
  flex:1 1 0;
  height:6px;
  border-radius:999px;
  background:var(--line-soft);
}

.mprof-strength span.is-on:nth-child(1){ background:#ef4444; }
.mprof-strength span.is-on:nth-child(2){ background:#f59e0b; }
.mprof-strength span.is-on:nth-child(3){ background:#22c55e; }

.mprof-img-drop{
  border:1px dashed var(--line-strong);
  border-radius:16px;
  background:var(--surface-2);
  padding:16px;
}

.mprof-img-preview-wrap{
  display:none;
  align-items:center;
  gap:14px;
  padding:12px;
  border:1px solid var(--line-strong);
  border-radius:14px;
  background:var(--surface);
  margin-top:12px;
}

.mprof-img-preview-wrap img{
  width:84px;
  height:84px;
  border-radius:18px;
  object-fit:cover;
  border:1px solid var(--line-strong);
  background:var(--surface-2);
}

.mprof-img-empty{
  color:var(--muted-color);
  font-size:13px;
}

.modal-content{
  border-radius:16px;
  border:1px solid var(--line-strong);
  background:var(--surface);
}

.modal-header{
  border-bottom:1px solid var(--line-strong);
}

.modal-footer{
  border-top:1px solid var(--line-strong);
}

#globalLoading{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.4);
  backdrop-filter:blur(2px);
  z-index:1085;
  display:none;
  align-items:center;
  justify-content:center;
}

.btn-loading{
  position:relative;
  pointer-events:none;
}

.btn-loading .btn-label{
  visibility:hidden;
}

.btn-loading::after{
  content:'';
  position:absolute;
  width:16px;
  height:16px;
  top:50%;
  left:50%;
  margin-left:-8px;
  margin-top:-8px;
  border:2px solid rgba(255,255,255,.35);
  border-top-color:#fff;
  border-radius:50%;
  animation:mprofSpin .8s linear infinite;
}

.btn-light.btn-loading::after,
.btn-outline-primary.btn-loading::after,
.btn-outline-secondary.btn-loading::after{
  border:2px solid rgba(15,23,42,.18);
  border-top-color:var(--primary-color);
}

html.theme-dark .mprof-card,
html.theme-dark .mprof-panel,
html.theme-dark .modal-content{
  background:#0f172a;
  border-color:var(--line-strong);
}

html.theme-dark .mprof-form .form-control,
html.theme-dark .mprof-form textarea,
html.theme-dark .mprof-avatar-fallback,
html.theme-dark .mprof-pill,
html.theme-dark .mprof-img-drop,
html.theme-dark .mprof-img-preview-wrap{
  background:#0f172a;
  color:#e5e7eb;
  border-color:#233146;
}

html.theme-dark .mprof-tabs .nav-link.active{
  background:#0f172a;
  border-color:#233146 #233146 #0f172a;
}

@media (max-width:767.98px){
  .mprof-head-row,
  .mprof-actions{
    flex-direction:column;
    align-items:flex-start !important;
  }

  .mprof-head-actions{
    width:100%;
    justify-content:flex-end;
  }

  .mprof-actions .btn{
    width:100%;
  }

  .mprof-hero{
    padding:22px 14px 18px;
  }

  .mprof-img-preview-wrap{
    flex-direction:column;
    align-items:flex-start;
  }
}
</style>
@endpush

@section('content')
<div class="mprof-wrap">

  <div id="globalLoading">
    @include('partials.overlay')
  </div>

  <div class="panel mprof-head">
    <div class="mprof-head-row">
      <div>
        <div class="mprof-head-copy">
          <h1>
            <span class="seg-muted">Account</span>
            <span class="seg-sep">/</span>
            <span>Profile</span>
          </h1>
        </div>
        <div class="mprof-head-sub">Update your basic details and security settings.</div>
      </div>

      <div class="mprof-head-actions">
        <button type="button" class="w3-icon-btn mprof-refresh-btn" id="btnRefreshProfile" title="Refresh profile" aria-label="Refresh profile">
          <i class="fa fa-rotate-right"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="mprof-card mprof-hero">
    <div class="mprof-avatar-wrap">
      <button type="button" class="mprof-avatar-trigger" id="btnAvatarTrigger" title="Change profile image">
        <img id="profileAvatar" class="mprof-avatar" alt="Profile avatar" style="display:none;">
        <div id="profileAvatarFallback" class="mprof-avatar-fallback"><i class="fa-regular fa-user"></i></div>
        <span class="mprof-avatar-camera"><i class="fa fa-camera"></i></span>
      </button>
    </div>

    <h2 class="mprof-name" id="profileNameCard">—</h2>

    <div class="mprof-hero-badges">
      <span class="mprof-pill role" id="profileRoleBadge"><i class="fa fa-user-shield"></i> —</span>
      <span class="mprof-pill status active" id="profileStatusBadge"><i class="fa fa-circle-check"></i> —</span>
    </div>

    <div class="mprof-email" id="profileEmailCard">—</div>
    <div class="mprof-avatar-note">Click the camera icon to upload a new profile image.</div>
  </div>

  <div class="mprof-panel mprof-tabs-panel">
    <ul class="nav nav-tabs mprof-tabs" id="profileTabNav" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-pane" type="button" role="tab" aria-controls="basic-pane" aria-selected="true">
          <i class="fa fa-id-card me-2"></i>Basic Details
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab" aria-controls="security-pane" aria-selected="false">
          <i class="fa fa-lock me-2"></i>Security
        </button>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane fade show active mprof-tab-pane" id="basic-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">
        <form id="profileForm" class="mprof-form" novalidate>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" id="profileName" class="form-control" maxlength="150" required placeholder="Your full name">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" id="profileEmail" class="form-control" maxlength="255" required placeholder="you@example.com">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="text" id="profilePhone" class="form-control" maxlength="32" placeholder="Primary phone">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">WhatsApp Number</label>
              <input type="text" id="profileWhatsapp" class="form-control" maxlength="32" placeholder="WhatsApp number">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Alternative Email</label>
              <input type="email" id="profileAltEmail" class="form-control" maxlength="255" placeholder="Alternative email">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Alternative Phone</label>
              <input type="text" id="profileAltPhone" class="form-control" maxlength="32" placeholder="Alternative phone">
            </div>

            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea id="profileAddress" rows="3" class="form-control" placeholder="Street, city, state, ZIP"></textarea>
            </div>

            <div class="col-12">
              <div class="mprof-help">Profile image upload is handled separately from the camera icon on the avatar.</div>
            </div>

            <div class="col-12">
              <div class="mprof-actions">
                <button type="button" class="btn btn-light" id="btnResetProfileForm">
                  <i class="fa fa-rotate-left me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="btnSaveProfile">
                  <i class="fa fa-floppy-disk me-1"></i><span class="btn-label">Save Basic Details</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="tab-pane fade mprof-tab-pane" id="security-pane" role="tabpanel" aria-labelledby="security-tab" tabindex="0">
        <form id="passwordForm" class="mprof-form" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Current Password <span class="text-danger">*</span></label>
              <input type="password" id="currentPassword" class="form-control" required placeholder="Current password">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <input type="password" id="newPassword" class="form-control" required placeholder="New password">
              <div class="mprof-strength" id="passwordStrengthBars">
                <span></span><span></span><span></span>
              </div>
              <div class="mprof-help mt-2">Minimum 8 characters and should be different from current password.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
              <input type="password" id="confirmPassword" class="form-control" required placeholder="Confirm new password">
            </div>

            <div class="col-12">
              <div class="mprof-actions">
                <button type="button" class="btn btn-light" id="btnResetPasswordForm">
                  <i class="fa fa-rotate-left me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="btnSavePassword">
                  <i class="fa fa-key me-1"></i><span class="btn-label">Update Password</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="imageUploadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0">
            <i class="fa fa-camera me-2"></i>Upload Profile Image
          </h5>
          <div class="small text-muted">Choose an image and upload it separately.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mprof-img-drop">
          <input type="file" id="profileImageModalInput" accept="image/*" hidden>

          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <div class="fw-semibold">Choose image</div>
              <div class="mprof-help">JPG, PNG, WEBP, GIF or SVG up to 5 MB.</div>
            </div>
            <button type="button" class="btn btn-outline-primary" id="btnChooseModalImage">
              <i class="fa fa-image me-1"></i> Choose Image
            </button>
          </div>

          <div class="mprof-img-preview-wrap" id="modalImagePreviewWrap">
            <img id="modalImagePreview" alt="Selected profile preview">
            <div>
              <div class="fw-semibold">Preview ready</div>
              <div class="mprof-help">Click Upload Image to save this image as your profile picture.</div>
            </div>
          </div>

          <div class="mprof-img-empty mt-3" id="modalImageEmpty">
            No image selected yet.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="btnCloseImageModal">
          Close
        </button>
        <button type="button" class="btn btn-primary" id="btnUploadImage">
          <i class="fa fa-upload me-1"></i><span class="btn-label">Upload Image</span>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
  <div id="toastSuccess" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastSuccessText">Done</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>

  <div id="toastError" class="toast align-items-center text-bg-danger border-0 mt-2" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastErrorText">Something went wrong</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (!window.bootstrap) {
    console.error('Bootstrap is missing.');
    return;
  }

  const TOKEN = sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  if (!TOKEN) {
    window.location.href = '/';
    return;
  }

  const API_PROFILE = '/api/profile';
  const API_PASSWORD = '/api/profile/password';

  const toastOk = new bootstrap.Toast(document.getElementById('toastSuccess'));
  const toastErr = new bootstrap.Toast(document.getElementById('toastError'));
  const imageModalEl = document.getElementById('imageUploadModal');
  const imageModal = new bootstrap.Modal(imageModalEl);

  const okTxt = document.getElementById('toastSuccessText');
  const errTxt = document.getElementById('toastErrorText');

  const els = {
    globalLoading: document.getElementById('globalLoading'),
    btnRefresh: document.getElementById('btnRefreshProfile'),

    profileForm: document.getElementById('profileForm'),
    passwordForm: document.getElementById('passwordForm'),

    btnSaveProfile: document.getElementById('btnSaveProfile'),
    btnSavePassword: document.getElementById('btnSavePassword'),
    btnResetProfileForm: document.getElementById('btnResetProfileForm'),
    btnResetPasswordForm: document.getElementById('btnResetPasswordForm'),

    profileName: document.getElementById('profileName'),
    profileEmail: document.getElementById('profileEmail'),
    profilePhone: document.getElementById('profilePhone'),
    profileWhatsapp: document.getElementById('profileWhatsapp'),
    profileAltEmail: document.getElementById('profileAltEmail'),
    profileAltPhone: document.getElementById('profileAltPhone'),
    profileAddress: document.getElementById('profileAddress'),

    currentPassword: document.getElementById('currentPassword'),
    newPassword: document.getElementById('newPassword'),
    confirmPassword: document.getElementById('confirmPassword'),
    strengthBars: Array.from(document.querySelectorAll('#passwordStrengthBars span')),

    profileAvatar: document.getElementById('profileAvatar'),
    profileAvatarFallback: document.getElementById('profileAvatarFallback'),
    profileNameCard: document.getElementById('profileNameCard'),
    profileRoleBadge: document.getElementById('profileRoleBadge'),
    profileStatusBadge: document.getElementById('profileStatusBadge'),
    profileEmailCard: document.getElementById('profileEmailCard'),
    btnAvatarTrigger: document.getElementById('btnAvatarTrigger'),

    profileImageModalInput: document.getElementById('profileImageModalInput'),
    btnChooseModalImage: document.getElementById('btnChooseModalImage'),
    btnUploadImage: document.getElementById('btnUploadImage'),
    modalImagePreview: document.getElementById('modalImagePreview'),
    modalImagePreviewWrap: document.getElementById('modalImagePreviewWrap'),
    modalImageEmpty: document.getElementById('modalImageEmpty'),

    headerImage: document.getElementById('profileCircleImage'),
    headerLetter: document.getElementById('profileCircleLetter')
  };

  let PROFILE_CACHE = null;

  function authHeaders(extra = {}) {
    return Object.assign({
      'Authorization': 'Bearer ' + TOKEN,
      'Accept': 'application/json'
    }, extra);
  }

  function showGlobalLoading(show) {
    if (!els.globalLoading) return;
    els.globalLoading.style.display = show ? 'flex' : 'none';
  }

  function ok(message) {
    okTxt.textContent = message || 'Done';
    toastOk.show();
  }

  function err(message) {
    errTxt.textContent = message || 'Something went wrong';
    toastErr.show();
  }

  function setButtonLoading(button, loading) {
    if (!button) return;
    if (loading) {
      button.disabled = true;
      button.classList.add('btn-loading');
    } else {
      button.disabled = false;
      button.classList.remove('btn-loading');
    }
  }

  function formatRole(role) {
    const map = {
      admin: 'Admin',
      hr: 'HR',
      employee: 'Employee'
    };
    return map[String(role || '').toLowerCase()] || (role || '—');
  }

  function setStatusBadge(status) {
    const s = String(status || '').toLowerCase();
    const active = s === 'active';
    els.profileStatusBadge.className = 'mprof-pill status ' + (active ? 'active' : 'inactive');
    els.profileStatusBadge.innerHTML = active
      ? '<i class="fa fa-circle-check"></i> Active'
      : '<i class="fa fa-circle-pause"></i> ' + (status || 'Inactive');
  }

  function syncHeaderAvatar(url, name) {
    if (!els.headerImage || !els.headerLetter) return;

    if (url) {
      els.headerImage.src = url;
      els.headerImage.style.display = 'block';
      els.headerLetter.style.display = 'none';
      els.headerImage.onerror = function () {
        els.headerImage.style.display = 'none';
        els.headerLetter.style.display = 'flex';
        els.headerLetter.textContent = String(name || '').trim().charAt(0).toUpperCase() || 'U';
        els.headerImage.onerror = null;
      };
      return;
    }

    els.headerImage.removeAttribute('src');
    els.headerImage.style.display = 'none';
    els.headerLetter.style.display = 'flex';
    els.headerLetter.textContent = String(name || '').trim().charAt(0).toUpperCase() || 'U';
  }

  function setAvatar(url, name) {
    const imageUrl = url || '';
    if (imageUrl) {
      els.profileAvatar.src = imageUrl;
      els.profileAvatar.style.display = 'block';
      els.profileAvatarFallback.style.display = 'none';
      els.profileAvatar.onerror = function () {
        els.profileAvatar.style.display = 'none';
        els.profileAvatarFallback.style.display = 'flex';
        els.profileAvatarFallback.innerHTML = '<i class="fa-regular fa-user"></i>';
        els.profileAvatar.onerror = null;
      };
    } else {
      els.profileAvatar.style.display = 'none';
      els.profileAvatarFallback.style.display = 'flex';
      const first = String(name || '').trim().charAt(0).toUpperCase();
      els.profileAvatarFallback.innerHTML = first ? first : '<i class="fa-regular fa-user"></i>';
    }

    syncHeaderAvatar(imageUrl, name);
  }

  function fillProfileForm(user) {
    els.profileName.value = user.name || '';
    els.profileEmail.value = user.email || '';
    els.profilePhone.value = user.phone_number || '';
    els.profileWhatsapp.value = user.whatsapp_number || '';
    els.profileAltEmail.value = user.alternative_email || '';
    els.profileAltPhone.value = user.alternative_phone_number || '';
    els.profileAddress.value = user.address || '';
  }

  function fillProfileHero(user) {
    els.profileNameCard.textContent = user.name || '—';
    els.profileRoleBadge.innerHTML = '<i class="fa fa-user-shield"></i> ' + formatRole(user.role);
    els.profileEmailCard.textContent = user.email || '—';
    setStatusBadge(user.status || '');
    setAvatar(user.image || '', user.name || '');
  }

  function fillAll(user) {
    PROFILE_CACHE = user;
    fillProfileForm(user);
    fillProfileHero(user);
  }

  function resetImageModal() {
    els.profileImageModalInput.value = '';
    els.modalImagePreview.src = '';
    els.modalImagePreviewWrap.style.display = 'none';
    els.modalImageEmpty.style.display = '';
  }

  async function readJsonSafe(res) {
    return await res.json().catch(() => ({}));
  }

  async function fetchProfile(showOverlay = true) {
    try {
      if (showOverlay) showGlobalLoading(true);

      const res = await fetch(API_PROFILE, { headers: authHeaders() });

      if (res.status === 401 || res.status === 403) {
        window.location.href = '/';
        return;
      }

      const js = await readJsonSafe(res);
      if (!res.ok) throw new Error(js.message || js.error || 'Failed to load profile');

      const user = js.user || js.data || {};
      fillAll(user);
    } catch (e) {
      err(e.message || 'Failed to load profile');
    } finally {
      if (showOverlay) showGlobalLoading(false);
    }
  }

  function resetProfileForm() {
    if (PROFILE_CACHE) fillProfileForm(PROFILE_CACHE);
  }

  function resetPasswordForm() {
    els.currentPassword.value = '';
    els.newPassword.value = '';
    els.confirmPassword.value = '';
    updatePasswordStrength('');
  }

  function updatePasswordStrength(value) {
    const password = String(value || '');
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password) || /[a-z]/.test(password)) score++;
    if (/\d/.test(password) || /[^A-Za-z0-9]/.test(password)) score++;

    els.strengthBars.forEach((bar, idx) => {
      bar.classList.toggle('is-on', idx < score);
    });
  }

  function getProfilePayload() {
    return {
      name: els.profileName.value.trim(),
      email: els.profileEmail.value.trim(),
      phone_number: els.profilePhone.value.trim(),
      whatsapp_number: els.profileWhatsapp.value.trim(),
      alternative_email: els.profileAltEmail.value.trim(),
      alternative_phone_number: els.profileAltPhone.value.trim(),
      address: els.profileAddress.value.trim()
    };
  }

  function validateProfilePayload(data) {
    if (!data.name) return 'Name is required';
    if (!data.email) return 'Email is required';

    if (data.alternative_email && !/^\S+@\S+\.\S+$/.test(data.alternative_email)) {
      return 'Alternative email is invalid';
    }

    return '';
  }

  async function updateProfile(e) {
    e.preventDefault();

    const payload = getProfilePayload();
    const validationError = validateProfilePayload(payload);
    if (validationError) {
      err(validationError);
      return;
    }

    try {
      setButtonLoading(els.btnSaveProfile, true);
      showGlobalLoading(true);

      const res = await fetch(API_PROFILE, {
        method: 'POST',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(payload)
      });

      if (res.status === 401 || res.status === 403) {
        window.location.href = '/';
        return;
      }

      const js = await readJsonSafe(res);
      if (!res.ok) {
        const msg = js.message || js.error || (js.errors ? Object.values(js.errors).flat().join(' ') : 'Failed to update profile');
        throw new Error(msg);
      }

      const user = js.user || js.data || PROFILE_CACHE || {};
      fillAll(user);
      ok(js.message || 'Profile updated successfully');
    } catch (e2) {
      err(e2.message || 'Failed to update profile');
    } finally {
      setButtonLoading(els.btnSaveProfile, false);
      showGlobalLoading(false);
    }
  }

  async function uploadProfileImage() {
    const file = els.profileImageModalInput.files && els.profileImageModalInput.files[0]
      ? els.profileImageModalInput.files[0]
      : null;

    if (!file) {
      err('Please choose an image first');
      return;
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
      setButtonLoading(els.btnUploadImage, true);
      showGlobalLoading(true);

      const res = await fetch(API_PROFILE, {
        method: 'POST',
        headers: {
          'Authorization': 'Bearer ' + TOKEN,
          'Accept': 'application/json'
        },
        body: formData
      });

      if (res.status === 401 || res.status === 403) {
        window.location.href = '/';
        return;
      }

      const js = await readJsonSafe(res);
      if (!res.ok) {
        const msg = js.message || js.error || (js.errors ? Object.values(js.errors).flat().join(' ') : 'Failed to upload image');
        throw new Error(msg);
      }

      const user = js.user || js.data || PROFILE_CACHE || {};
      fillAll(user);
      resetImageModal();
      imageModal.hide();
      ok(js.message || 'Profile image uploaded successfully');
    } catch (e2) {
      err(e2.message || 'Failed to upload image');
    } finally {
      setButtonLoading(els.btnUploadImage, false);
      showGlobalLoading(false);
    }
  }

  async function updatePassword(e) {
    e.preventDefault();

    const currentPassword = els.currentPassword.value;
    const newPassword = els.newPassword.value;
    const confirmPassword = els.confirmPassword.value;

    if (!currentPassword) {
      err('Current password is required');
      return;
    }
    if (!newPassword || newPassword.length < 8) {
      err('New password must be at least 8 characters');
      return;
    }
    if (newPassword !== confirmPassword) {
      err('Password confirmation does not match');
      return;
    }

    try {
      setButtonLoading(els.btnSavePassword, true);
      showGlobalLoading(true);

      const res = await fetch(API_PASSWORD, {
        method: 'PATCH',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({
          current_password: currentPassword,
          new_password: newPassword,
          new_password_confirmation: confirmPassword
        })
      });

      if (res.status === 401 || res.status === 403) {
        window.location.href = '/';
        return;
      }

      const js = await readJsonSafe(res);
      if (!res.ok) {
        const msg = js.message || js.error || (js.errors ? Object.values(js.errors).flat().join(' ') : 'Failed to update password');
        throw new Error(msg);
      }

      resetPasswordForm();
      ok(js.message || 'Password updated successfully');
    } catch (e2) {
      err(e2.message || 'Failed to update password');
    } finally {
      setButtonLoading(els.btnSavePassword, false);
      showGlobalLoading(false);
    }
  }

  els.btnAvatarTrigger.addEventListener('click', function () {
    resetImageModal();
    imageModal.show();
  });

  els.btnChooseModalImage.addEventListener('click', function () {
    els.profileImageModalInput.click();
  });

  els.profileImageModalInput.addEventListener('change', function () {
    const file = this.files && this.files[0] ? this.files[0] : null;
    if (!file) {
      resetImageModal();
      return;
    }

    const reader = new FileReader();
    reader.onload = function (ev) {
      const src = ev.target.result || '';
      els.modalImagePreview.src = src;
      els.modalImagePreviewWrap.style.display = 'flex';
      els.modalImageEmpty.style.display = 'none';
    };
    reader.readAsDataURL(file);
  });

  imageModalEl.addEventListener('hidden.bs.modal', resetImageModal);

  els.newPassword.addEventListener('input', function () {
    updatePasswordStrength(this.value);
  });

  els.btnResetProfileForm.addEventListener('click', resetProfileForm);
  els.btnResetPasswordForm.addEventListener('click', resetPasswordForm);
  els.btnUploadImage.addEventListener('click', uploadProfileImage);

  els.profileForm.addEventListener('submit', updateProfile);
  els.passwordForm.addEventListener('submit', updatePassword);

  els.btnRefresh.addEventListener('click', function () {
    this.classList.add('is-spinning');
    showGlobalLoading(true);
    Promise.resolve(fetchProfile(false)).finally(() => {
      showGlobalLoading(false);
      this.classList.remove('is-spinning');
    });
  });

  fetchProfile();
});
</script>
@endpush
