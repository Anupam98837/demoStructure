<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Register - Attendance System</title>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/images/web/favicon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('/assets/css/main.css') }}"/>

  <style>
    :root{
      --ux-bg:#f3f7fb;
      --ux-surface:#ffffff;
      --ux-ink:#14324c;
      --ux-copy:#6a8093;
      --ux-line:rgba(20,50,76,.10);
      --ux-primary:#0e7ac4;
      --ux-secondary:#0da58f;
      --ux-dark:#0f314d;
      --ux-radius-xl:32px;
      --ux-radius-lg:24px;
      --ux-radius-md:18px;
      --ux-shadow:0 24px 60px rgba(15,49,77,.10);
    }

    *{box-sizing:border-box}

    body.ux-auth-body{
      margin:0;
      min-height:100vh;
      font-family:"DM Sans",sans-serif;
      color:var(--ux-ink);
      background:
        radial-gradient(circle at top left, rgba(13,165,143,.08), transparent 18%),
        radial-gradient(circle at bottom right, rgba(14,122,196,.10), transparent 22%),
        linear-gradient(180deg,#fbfdff 0%, var(--ux-bg) 100%);
    }

    .ux-auth{
      min-height:100vh;
      display:grid;
      grid-template-columns:minmax(320px,.95fr) minmax(320px,.85fr);
    }

    .ux-brand-side{
      background:linear-gradient(160deg, #123a5b 0%, #14557d 58%, #0da58f 100%);
      color:#fff;
      padding:clamp(20px,4vw,42px);
      display:flex;
      align-items:flex-start;
      justify-content:flex-start;
      position:relative;
      overflow:hidden;
    }

    .ux-brand-side::before,
    .ux-brand-side::after{
      content:"";
      position:absolute;
      border-radius:50%;
      opacity:.16;
      pointer-events:none;
    }

    .ux-brand-side::before{
      width:260px;
      height:260px;
      top:-80px;
      right:-70px;
      background:radial-gradient(circle, #fff 0%, transparent 70%);
    }

    .ux-brand-side::after{
      width:240px;
      height:240px;
      left:-70px;
      bottom:-90px;
      background:radial-gradient(circle, #ffe38c 0%, transparent 72%);
    }

    .ux-brand-inner{
      position:relative;
      z-index:1;
      width:min(440px,100%);
      margin-left:auto;
      margin-right:auto;
      margin-top:18px;
    }

    .ux-brand-head{
      display:flex;
      align-items:center;
      gap:14px;
      margin-bottom:28px;
    }

    .ux-brand-mark{
      width:64px;
      height:64px;
      border-radius:20px;
      display:grid;
      place-items:center;
      background:rgba(255,255,255,.14);
      border:1px solid rgba(255,255,255,.18);
      backdrop-filter:blur(10px);
      flex-shrink:0;
    }

    .ux-brand-mark img{
      width:40px;
      height:40px;
      object-fit:contain;
    }

    .ux-brand-copy strong{
      display:block;
      font-family:"Space Grotesk",sans-serif;
      font-size:1.15rem;
      letter-spacing:-.03em;
    }

    .ux-brand-copy span{
      display:block;
      margin-top:4px;
      color:rgba(255,255,255,.72);
      font-size:.92rem;
    }

    .ux-kicker{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 14px;
      border-radius:999px;
      background:rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.14);
      font-size:.78rem;
      font-weight:700;
      letter-spacing:.05em;
      text-transform:uppercase;
    }

    .ux-brand-title{
      margin:18px 0 12px;
      font-family:"Space Grotesk",sans-serif;
      font-size:clamp(2rem,4.6vw,3.4rem);
      line-height:1.02;
      letter-spacing:-.05em;
      max-width:10ch;
    }

    .ux-brand-text{
      margin:0;
      max-width:34ch;
      color:rgba(255,255,255,.78);
      line-height:1.75;
      font-size:1rem;
    }

    .ux-brand-pills{
      margin-top:24px;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .ux-brand-pills span{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 12px;
      border-radius:999px;
      background:rgba(9,24,37,.16);
      border:1px solid rgba(255,255,255,.12);
      font-size:.88rem;
    }

    .ux-form-side{
      padding:clamp(20px,4vw,42px);
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .ux-form-shell{
      width:min(420px,100%);
    }

    .ux-topline{
      display:flex;
      justify-content:flex-end;
      margin-bottom:16px;
    }

    .ux-topline a{
      color:var(--ux-copy);
      text-decoration:none;
      font-weight:700;
    }

    .ux-card{
      padding:30px;
      border-radius:var(--ux-radius-xl);
      background:rgba(255,255,255,.86);
      border:1px solid rgba(255,255,255,.8);
      box-shadow:var(--ux-shadow);
      backdrop-filter:blur(10px);
    }

    .ux-card-title{
      margin:0 0 8px;
      font-family:"Space Grotesk",sans-serif;
      font-size:2rem;
      letter-spacing:-.05em;
      line-height:1.02;
    }

    .ux-card-copy{
      margin:0 0 22px;
      color:var(--ux-copy);
      line-height:1.68;
      font-size:.95rem;
    }

    .ux-alert{
      margin-bottom:16px;
      border-radius:16px;
      border:0;
      font-size:.92rem;
    }

    .ux-field{
      margin-bottom:16px;
    }

    .ux-label{
      display:block;
      margin-bottom:8px;
      font-size:.9rem;
      font-weight:700;
      color:var(--ux-ink);
    }

    .ux-input-wrap{
      position:relative;
    }

    .ux-field-icon{
      position:absolute;
      left:15px;
      top:50%;
      transform:translateY(-50%);
      color:#88a0b2;
      font-size:.92rem;
      pointer-events:none;
    }

    .ux-control{
      width:100%;
      min-height:52px;
      border-radius:18px;
      border:1px solid var(--ux-line);
      background:#fbfdff;
      color:var(--ux-ink);
      padding:14px 16px 14px 44px;
      font-size:.96rem;
      transition:border-color .18s ease, box-shadow .18s ease;
    }

    .ux-control:focus{
      outline:none;
      border-color:rgba(14,122,196,.34);
      box-shadow:0 0 0 4px rgba(14,122,196,.10);
      background:#fff;
    }

    .ux-control::placeholder{
      color:#9ab0bf;
    }

    .ux-control.with-eye{
      padding-right:52px;
    }

    .ux-eye{
      position:absolute;
      top:50%;
      right:10px;
      transform:translateY(-50%);
      width:36px;
      height:36px;
      border:0;
      border-radius:12px;
      background:transparent;
      color:#88a0b2;
      display:grid;
      place-items:center;
      cursor:pointer;
    }

    .ux-register{
      width:100%;
      min-height:54px;
      border:0;
      border-radius:18px;
      background:linear-gradient(135deg,var(--ux-primary) 0%, #1b98ea 55%, var(--ux-secondary) 100%);
      color:#fff;
      font-weight:700;
      font-size:.98rem;
      box-shadow:0 16px 30px rgba(14,122,196,.18);
      transition:transform .18s ease, filter .18s ease;
    }

    .ux-register:hover{
      transform:translateY(-1px);
      filter:brightness(.99);
    }

    .ux-note{
      margin-top:14px;
      color:var(--ux-copy);
      font-size:.85rem;
      line-height:1.6;
    }

    .ux-footer{
      margin-top:18px;
      text-align:center;
      color:var(--ux-copy);
      font-size:.93rem;
    }

    .ux-footer a{
      color:var(--ux-primary);
      text-decoration:none;
      font-weight:700;
    }

    @media (max-width: 920px){
      .ux-auth{
        grid-template-columns:1fr;
      }

      .ux-brand-side{
        padding:24px 20px;
      }

      .ux-brand-inner{
        width:100%;
      }

      .ux-brand-title{
        max-width:none;
        font-size:2rem;
      }

      .ux-brand-text{
        max-width:none;
      }
    }

    @media (max-width: 576px){
      .ux-form-side{
        padding:16px;
      }

      .ux-card{
        padding:22px 18px;
        border-radius:24px;
      }

      .ux-brand-side{
        padding:20px 16px 18px;
      }

      .ux-brand-inner{
        margin-top:8px;
      }

      .ux-brand-head{
        margin-bottom:18px;
      }

      .ux-brand-mark{
        width:54px;
        height:54px;
        border-radius:18px;
      }

      .ux-brand-mark img{
        width:32px;
        height:32px;
      }

      .ux-brand-title{
        font-size:1.72rem;
      }

      .ux-brand-text{
        font-size:.93rem;
      }

      .ux-brand-pills{
        margin-top:18px;
      }
    }
  </style>
</head>
<body class="ux-auth-body">
<div class="ux-auth">
  <aside class="ux-brand-side">
    <div class="ux-brand-inner">
      <div class="ux-brand-head">
        <div class="ux-brand-mark">
          <img src="{{ asset('/assets/media/images/web/logo.png') }}" alt="Attendance System">
        </div>
        <div class="ux-brand-copy">
          <strong>Attendance System</strong>
          <span>Quick account setup</span>
        </div>
      </div>

      <span class="ux-kicker"><i class="fa-solid fa-user-plus"></i> Register</span>
      <h1 class="ux-brand-title">Create your account first.</h1>
      <p class="ux-brand-text">This step is only for user registration. Patient details and booking information can come later.</p>

      <div class="ux-brand-pills">
        <span><i class="fa-solid fa-check"></i> Minimal form</span>
        <span><i class="fa-solid fa-check"></i> Booking later</span>
      </div>
    </div>
  </aside>

  <main class="ux-form-side">
    <div class="ux-form-shell">
      <div class="ux-topline">
        <a href="/">Back to Login</a>
      </div>

      <section class="ux-card">
        <h2 class="ux-card-title">Create account</h2>
        <p class="ux-card-copy">Register your account.</p>

        <form id="ux_register_form" novalidate>
          <input type="hidden" id="ux_source" value="auth_register_page">

          <div id="ux_alert" class="ux-alert alert d-none" role="alert"></div>

          <div class="ux-field">
            <label class="ux-label" for="ux_name">Full Name</label>
            <div class="ux-input-wrap">
              <i class="ux-field-icon fa-solid fa-user"></i>
              <input id="ux_name" type="text" class="ux-control" placeholder="Enter your full name" autocomplete="name" required>
            </div>
          </div>

          <div class="ux-field">
            <label class="ux-label" for="ux_email">Email Address</label>
            <div class="ux-input-wrap">
              <i class="ux-field-icon fa-solid fa-envelope"></i>
              <input id="ux_email" type="email" class="ux-control" placeholder="you@example.com" autocomplete="email" required>
            </div>
          </div>

          <div class="ux-field">
            <label class="ux-label" for="ux_phone">Phone Number</label>
            <div class="ux-input-wrap">
              <i class="ux-field-icon fa-solid fa-phone"></i>
              <input id="ux_phone" type="tel" class="ux-control" placeholder="90000 00000" autocomplete="tel" required>
            </div>
          </div>

          <div class="ux-field">
            <label class="ux-label" for="ux_password">Password</label>
            <div class="ux-input-wrap">
              <i class="ux-field-icon fa-solid fa-lock"></i>
              <input id="ux_password" type="password" class="ux-control with-eye" placeholder="Enter password" minlength="8" autocomplete="new-password" required>
              <button type="button" class="ux-eye" id="ux_togglePw" aria-label="Toggle password visibility">
                <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
              </button>
            </div>
          </div>

          <div class="ux-field">
            <label class="ux-label" for="ux_password_confirm">Confirm Password</label>
            <div class="ux-input-wrap">
              <i class="ux-field-icon fa-solid fa-shield"></i>
              <input id="ux_password_confirm" type="password" class="ux-control with-eye" placeholder="Repeat password" minlength="8" autocomplete="new-password" required>
              <button type="button" class="ux-eye" id="ux_togglePwConfirm" aria-label="Toggle confirm password visibility">
                <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
              </button>
            </div>
          </div>

          <button class="ux-register" id="ux_btn" type="submit">
            <span class="me-2"><i class="fa-solid fa-user-plus"></i></span> Create Account
          </button>

          <div class="ux-note">Patient and appointment details can be added later in the booking process.</div>

          <div class="ux-footer">
            Already have an account?
            <a href="/login" id="ux_login_link">Login</a>
          </div>
        </form>
      </section>
    </div>
  </main>
</div>

<script>
  (function(){
    const REGISTER_API = "/api/auth/register";
    const CHECK_API = "/api/auth/check";

    const form = document.getElementById('ux_register_form');
    const nameIn = document.getElementById('ux_name');
    const emailIn = document.getElementById('ux_email');
    const phoneIn = document.getElementById('ux_phone');
    const passwordIn = document.getElementById('ux_password');
    const passwordConfirmIn = document.getElementById('ux_password_confirm');
    const sourceIn = document.getElementById('ux_source');
    const btn = document.getElementById('ux_btn');
    const alertEl = document.getElementById('ux_alert');
    const loginLink = document.getElementById('ux_login_link');
    const togglePw = document.getElementById('ux_togglePw');
    const togglePwConfirm = document.getElementById('ux_togglePwConfirm');
    const redirectParam = new URLSearchParams(window.location.search).get('redirect') || '';

    function resolveNextPath(fallback){
      if (!redirectParam) return fallback;
      if (!redirectParam.startsWith('/') || redirectParam.startsWith('//')) return fallback;
      return redirectParam;
    }

    if (loginLink && redirectParam && redirectParam.startsWith('/')) {
      loginLink.href = '/login?redirect=' + encodeURIComponent(redirectParam);
    }

    function rolePath(role){
      const r = (role || '').toString().trim().toLowerCase();
      if (!r) return '/dashboard';
      return '/dashboard';
    }

    function authStoreSet(token, role){
      sessionStorage.setItem('token', token);
      sessionStorage.setItem('role', role);
      localStorage.removeItem('token');
      localStorage.removeItem('role');
    }

    function setBusy(isBusy){
      btn.disabled = isBusy;
      btn.innerHTML = isBusy
        ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>Creating account...'
        : '<span class="me-2"><i class="fa-solid fa-user-plus"></i></span> Create Account';
    }

    function showAlert(kind, msg){
      alertEl.classList.remove('d-none', 'alert-danger', 'alert-success', 'alert-warning');
      alertEl.classList.add('alert', kind === 'error' ? 'alert-danger' : (kind === 'warn' ? 'alert-warning' : 'alert-success'));
      alertEl.textContent = msg;
    }

    function clearAlert(){
      alertEl.classList.add('d-none');
      alertEl.textContent = '';
    }

    function toggleField(button, field){
      button?.addEventListener('click', () => {
        const show = field.type === 'password';
        field.type = show ? 'text' : 'password';
        button.innerHTML = show
          ? '<i class="fa-regular fa-eye" aria-hidden="true"></i>'
          : '<i class="fa-regular fa-eye-slash" aria-hidden="true"></i>';
      });
    }

    async function tryAutoLoginFromLocal(){
      const token = sessionStorage.getItem('token') || localStorage.getItem('token');
      const role = sessionStorage.getItem('role') || localStorage.getItem('role');
      if (!token) return;

      try {
        const res = await fetch(CHECK_API, {
          headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data && data.user) {
          const resolvedRole = (data.user.role || role || 'employee').toString().toLowerCase();
          sessionStorage.setItem('token', token);
          sessionStorage.setItem('role', resolvedRole);
          window.location.replace(resolveNextPath(rolePath(resolvedRole)));
        }
      } catch (error) {
      }
    }

    toggleField(togglePw, passwordIn);
    toggleField(togglePwConfirm, passwordConfirmIn);
    document.addEventListener('DOMContentLoaded', tryAutoLoginFromLocal);

    form?.addEventListener('submit', async (event) => {
      event.preventDefault();
      clearAlert();

      const payload = {
        name: (nameIn.value || '').trim(),
        email: (emailIn.value || '').trim(),
        phone_number: (phoneIn.value || '').trim(),
        password: passwordIn.value || '',
        password_confirmation: passwordConfirmIn.value || '',
        source: sourceIn.value || 'auth_register_page',
      };

      if (!payload.name || !payload.email || !payload.phone_number || !payload.password || !payload.password_confirmation) {
        showAlert('error', 'Please complete all fields.');
        return;
      }

      if (payload.password.length < 8) {
        showAlert('error', 'Password must be at least 8 characters.');
        return;
      }

      if (payload.password !== payload.password_confirmation) {
        showAlert('error', 'Password confirmation does not match.');
        return;
      }

      setBusy(true);

      try {
        const res = await fetch(REGISTER_API, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
          const msg = data?.message || data?.error || (data?.errors ? Object.values(data.errors).flat().join(', ') : 'Unable to create account.');
          showAlert('error', msg);
          return;
        }

        const token = data?.access_token || data?.token || '';
        const role = (data?.user?.role || 'employee').toLowerCase();

        if (!token) {
          showAlert('error', 'No token received from server.');
          return;
        }

        authStoreSet(token, role);
        showAlert('success', data?.message || 'Account created successfully. Redirecting...');
        setTimeout(() => {
          window.location.assign(resolveNextPath(rolePath(role)));
        }, 500);
      } catch (error) {
        showAlert('error', 'Network error. Please try again.');
      } finally {
        setBusy(false);
      }
    });
  })();
</script>
</body>
</html>
