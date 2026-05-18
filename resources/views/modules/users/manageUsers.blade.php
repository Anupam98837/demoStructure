{{-- resources/views/modules/users/manageUsers.blade.php --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>

<style>
  .usr-wrap{
    max-width:1140px;
    margin:16px auto 40px;
    overflow:visible;
  }
  .panel{
    background:var(--surface);
    border:1px solid var(--line-strong);
    border-radius:16px;
    box-shadow:var(--shadow-2);
    padding:14px;
  }

  .table-wrap.card{
    position:relative;
    border-radius:16px;
    border:1px solid var(--line-strong);
    box-shadow:var(--shadow-2);
    background:var(--surface);
  }
  .table-wrap .card-body{ overflow:visible; }
  .table-responsive{ overflow:visible !important; }
  .table{ --bs-table-bg:transparent; }
  .table thead th{
    font-weight:600;
    color:var(--muted-color);
    font-size:13px;
    border-bottom:1px solid var(--line-strong);
    background:var(--surface);
    white-space:nowrap;
  }
  .table thead.sticky-top{ z-index:3; }
  .table tbody tr{ border-top:1px solid var(--line-soft); }
  .table tbody tr:hover{ background:var(--page-hover); }

  .empty-state{
    padding:38px;
    text-align:center;
    color:var(--muted-color);
  }

  .dropdown-menu{
    border-radius:12px;
    border:1px solid var(--line-strong);
    box-shadow:var(--shadow-2);
    min-width:220px;
    z-index:1040;
  }
  .dropdown-item{
    display:flex;
    align-items:center;
    gap:.6rem;
  }
  .dropdown-item i{
    width:16px;
    text-align:center;
  }
  .dropdown-item.text-danger{ color:var(--danger-color)!important; }

  .usr-action-cell{
    overflow:visible !important;
    position:relative;
  }
  .usr-dd-btn{
    min-width:32px;
    width:32px;
    height:32px;
    padding:0;
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }
  .usr-dd-btn.is-open{
    color:var(--accent-color);
    border-color:var(--accent-color);
    background:color-mix(in oklab, var(--accent-color) 10%, transparent);
  }
  .table tbody tr.is-selected{
    background:var(--page-hover);
  }

  #usersFloatingMenu{
    position:fixed;
    top:0;
    left:0;
    min-width:220px;
    max-width:min(260px, calc(100vw - 20px));
    padding:6px;
    background:var(--surface);
    border:1px solid var(--line-strong);
    border-radius:14px;
    box-shadow:0 16px 40px rgba(15,23,42,.16);
    z-index:1080;
    display:none;
  }
  #usersFloatingMenu .usr-menu-item{
    width:100%;
    border:0;
    background:transparent;
    border-radius:10px;
    padding:9px 10px;
    display:flex;
    align-items:center;
    gap:10px;
    text-align:left;
    color:var(--text-color);
    font-size:13px;
    line-height:1.2;
  }
  #usersFloatingMenu .usr-menu-item:hover{
    background:var(--page);
  }
  #usersFloatingMenu .usr-menu-item.text-danger:hover{
    background:color-mix(in oklab, var(--danger-color) 10%, transparent);
  }
  #usersFloatingMenu .usr-menu-item i{
    width:16px;
    text-align:center;
    opacity:.85;
  }
  #usersFloatingMenu .dropdown-divider{
    margin:6px 0;
    border-top:1px solid var(--line-strong);
  }

  .u-avatar{
    width:40px;
    height:40px;
    border-radius:10px;
    object-fit:cover;
    border:1px solid var(--line-strong);
    background:#fff;
  }
  .u-avatar-fallback{
    width:40px;
    height:40px;
    border-radius:10px;
    border:1px solid var(--line-strong);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#9aa3b2;
    font-size:12px;
    background:var(--page);
  }

  .badge-role{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    font-weight:600;
    padding:6px 12px;
    border:1px solid var(--line-strong);
    background:var(--page);
    color:var(--ink);
  }
  .badge-soft-active{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:5px 10px;
    background:color-mix(in oklab, var(--success-color) 16%, transparent);
    color:var(--ink);
    font-weight:600;
  }
  .badge-soft-inactive{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:5px 10px;
    background:color-mix(in oklab, var(--danger-color) 12%, transparent);
    color:var(--ink);
    font-weight:600;
  }

  .folder-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border:1px dashed var(--line-strong);
    color:var(--muted-color);
    border-radius:999px;
    padding:4px 10px;
    font-size:12px;
    background:transparent;
  }

  .u-pw-wrap{ position:relative; }
  .u-pw-wrap .u-eye{
    position:absolute;
    top:50%;
    right:10px;
    transform:translateY(-50%);
    width:32px;
    height:32px;
    border:none;
    background:transparent;
    display:grid;
    place-items:center;
    border-radius:8px;
    color:#9aa3b2;
    cursor:pointer;
  }
  .u-pw-wrap .u-eye:focus-visible{
    outline:none;
    box-shadow:var(--ring);
  }

  .modal-content{
    border-radius:16px;
    border:1px solid var(--line-strong);
    background:var(--surface);
  }
  .modal-header{ border-bottom:1px solid var(--line-strong); }
  .modal-footer{ border-top:1px solid var(--line-strong); }

  .form-control,.form-select,textarea{
    border-radius:12px;
    border:1px solid var(--line-strong);
    background:#fff;
  }
  .form-control:focus,.form-select:focus{
    box-shadow:0 0 0 3px color-mix(in oklab, var(--accent-color) 20%, transparent);
    border-color:var(--accent-color);
  }

  .csv-upload-area{
    border:3px dashed var(--line-strong);
    border-radius:16px;
    padding:48px 24px;
    text-align:center;
    transition:border-color .2s ease;
    background:var(--page);
    cursor:pointer;
  }
  .csv-upload-area:hover{ border-color:var(--accent-color); }
  .csv-upload-area.dragover{
    border-color:var(--accent-color);
    background:color-mix(in oklab, var(--accent-color) 8%, transparent);
  }
  .csv-icon{
    font-size:48px;
    margin-bottom:16px;
    color:var(--accent-color);
  }
  .csv-help{
    font-size:12px;
    color:var(--muted-color);
    margin-top:12px;
  }
  .csv-template-link{
    display:inline-flex;
    align-items:center;
    gap:6px;
    color:var(--accent-color);
    text-decoration:none;
    font-weight:500;
  }
  .csv-template-link:hover{ text-decoration:underline; }

  .import-progress{
    height:6px;
    border-radius:3px;
    background:var(--line-soft);
    overflow:hidden;
    margin:16px 0;
  }
  .import-progress-bar{
    height:100%;
    background:var(--accent-color);
    transition:width .3s ease;
    border-radius:3px;
  }
  .import-results{
    max-height:220px;
    overflow-y:auto;
    border:1px solid var(--line-strong);
    border-radius:12px;
    padding:12px;
    background:var(--page);
  }
  .import-result-item{
    padding:6px 8px;
    border-radius:8px;
    margin-bottom:6px;
    font-size:13px;
  }
  .import-result-item.success{
    background:color-mix(in oklab, var(--success-color) 10%, transparent);
    border-left:3px solid var(--success-color);
  }
  .import-result-item.error{
    background:color-mix(in oklab, var(--danger-color) 10%, transparent);
    border-left:3px solid var(--danger-color);
  }

  .detail-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:14px;
  }
  .detail-card{
    border:1px solid var(--line-strong);
    border-radius:14px;
    padding:14px;
    background:var(--page);
  }
  .detail-label{
    font-size:12px;
    color:var(--muted-color);
    margin-bottom:6px;
  }
  .detail-value{
    font-weight:600;
    color:var(--ink);
    word-break:break-word;
  }

  @media (max-width: 767.98px){
    .detail-grid{ grid-template-columns:1fr; }
  }

  html.theme-dark .panel,
  html.theme-dark .table-wrap.card,
  html.theme-dark .modal-content{
    background:#0f172a;
    border-color:var(--line-strong);
  }
  html.theme-dark .table thead th{
    background:#0f172a;
    border-color:var(--line-strong);
    color:#94a3b8;
  }
  html.theme-dark .form-control,
  html.theme-dark .form-select,
  html.theme-dark textarea{
    background:#0f172a;
    color:#e5e7eb;
    border-color:var(--line-strong);
  }
  html.theme-dark .dropdown-menu{
    background:#0f172a;
    border-color:var(--line-strong);
  }
</style>

<div class="usr-wrap">

  <div class="row align-items-center g-2 mb-3 mfa-toolbar panel">
    <div class="col-12 col-lg d-flex align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <label class="text-muted small mb-0">Per Page</label>
        <select id="perPage" class="form-select" style="width:96px;">
          <option>10</option>
          <option selected>20</option>
          <option>50</option>
          <option>100</option>
        </select>
      </div>

      <div class="position-relative" style="min-width:300px;">
        <input id="searchInput" type="search" class="form-control ps-5" placeholder="Search by name, email or phone…">
        <i class="fa fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); opacity:.6;"></i>
      </div>

      <button id="btnFilter" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fa fa-filter me-1"></i> Filter
      </button>

      <button id="btnReset" class="btn btn-primary">
        <i class="fa fa-rotate-left me-1"></i> Reset
      </button>
    </div>

    <div class="col-12 col-lg-auto ms-lg-auto d-flex justify-content-lg-end">
      <div id="writeControls" style="display:none;">
        <button type="button" class="btn btn-outline-primary me-2" id="btnImportCsv" data-bs-toggle="modal" data-bs-target="#importCsvModal">
          <i class="fa fa-file-import me-1"></i> Import CSV
        </button>

        <button type="button" class="btn btn-primary" id="btnAddUser">
          <i class="fa fa-plus me-1"></i> Add User
        </button>
      </div>
    </div>
  </div>

  <div class="card table-wrap">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-borderless align-middle mb-0">
          <thead class="sticky-top">
            <tr>
              <th style="width:100px;">Status</th>
              <th style="width:84px;">Avatar</th>
              <th>Name</th>
              <th>Email</th>
              <th style="width:150px;">Phone</th>
              <th style="width:150px;">Role</th>
              <th style="width:110px;" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="usersTbody">
            <tr>
              <td colspan="7" class="empty-state">
                <i class="fa fa-circle-notch fa-spin mb-2" style="font-size:20px;"></i>
                <div>Loading users…</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-2">
        <div class="text-muted small" id="resultsInfo">—</div>
        <nav><ul id="pager" class="pagination mb-0"></ul></nav>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-filter me-2"></i>Filter Users</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Status</label>
            <select id="modal_status" class="form-select">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Role</label>
            <select id="modal_role" class="form-select">
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="hr">HR</option>
              <option value="employee">Employee</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Sort By</label>
            <select id="modal_sort" class="form-select">
              <option value="name">Name A-Z</option>
              <option value="-name">Name Z-A</option>
              <option value="email">Email A-Z</option>
              <option value="-email">Email Z-A</option>
              <option value="role">Role A-Z</option>
              <option value="-role">Role Z-A</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="btnApplyFilters" class="btn btn-primary">
          <i class="fa fa-check me-1"></i> Apply Filters
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importCsvModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-import me-2"></i>Import Users from CSV</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="importCsvForm" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="importStep1">
            <div class="csv-upload-area" id="csvDropZone">
              <div class="csv-icon"><i class="fa fa-file-csv"></i></div>
              <h5 class="mb-2">Drag & drop your CSV file here</h5>
              <p class="text-muted mb-3">or click to browse</p>
              <input type="file" id="csvFile" name="file" accept=".csv,text/csv" class="d-none" required>
              <button type="button" class="btn btn-primary" id="csvBrowseBtn">
                <i class="fa fa-folder-open me-1"></i> Browse Files
              </button>
              <div class="csv-help mt-3">
                <div><strong>CSV Format:</strong> name, email, password, role</div>
                <div class="mt-1 text-muted">You can also add: phone_number, alternative_email, alternative_phone_number, whatsapp_number, address</div>
                <div class="mt-1">First row must contain header. Max file size: 10MB</div>
              </div>
            </div>

            <div id="fileInfo" class="mt-3 d-none">
              <div class="alert alert-light d-flex align-items-center justify-content-between">
                <div>
                  <i class="fa fa-file-csv text-primary me-2"></i>
                  <span id="fileName" class="fw-semibold"></span>
                  <span id="fileSize" class="text-muted ms-2"></span>
                </div>
                <button type="button" class="btn btn-sm btn-light" id="clearFileBtn">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>

            <div class="mt-4">
              <div class="alert alert-info">
                <div class="d-flex align-items-center">
                  <i class="fa fa-circle-info me-2"></i>
                  <div>
                    <strong>Download CSV template</strong>
                    <div class="mt-1">
                      <a href="javascript:void(0)" id="downloadTemplateBtn" class="csv-template-link">
                        <i class="fa fa-download"></i> Download Template.csv
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div id="importStep2" class="d-none">
            <div class="text-center py-4">
              <div id="importSpinnerWrap">
                <div class="spinner-border text-primary mb-3" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>

              <div id="importSuccessWrap" class="d-none">
                <div style="font-size:72px; line-height:1; margin-bottom:10px;">
                  <i class="fa fa-circle-check text-success"></i>
                </div>
              </div>

              <h5 id="importStatusText">Processing CSV file...</h5>
              <div class="import-progress mt-4">
                <div class="import-progress-bar" id="importProgressBar" style="width: 0%"></div>
              </div>
              <div id="importStats" class="mt-3 text-muted small">Processing...</div>
            </div>

            <div id="importResults" class="mt-4 d-none">
              <h6 class="mb-3">Import Results</h6>
              <div class="import-results" id="importResultsList"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="importCancelBtn">Cancel</button>
          <button type="submit" class="btn btn-primary" id="importSubmitBtn" disabled>
            <i class="fa fa-upload me-1"></i> Start Import
          </button>
          <button type="button" class="btn btn-primary d-none" id="importDoneBtn" data-bs-dismiss="modal">
            <i class="fa fa-check me-1"></i> Done
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" id="userForm" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalTitle">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="userId"/>

        <div class="row g-3">
          <div class="col-md-12">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input class="form-control" id="userName" required maxlength="150" placeholder="John Doe">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="userEmail" required maxlength="255" placeholder="john.doe@example.com">
          </div>

          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input class="form-control" id="userPhone" maxlength="32" placeholder="+91 99999 99999">
          </div>

          <div class="col-md-4">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select" id="userRole" required>
              <option value="">Select Role</option>
              <option value="admin">Admin</option>
              <option value="hr">HR</option>
              <option value="employee">Employee</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select class="form-select" id="userStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="col-md-6 js-pw-section">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="u-pw-wrap">
              <input type="password" class="form-control pe-5" id="userPassword" placeholder="••••••••">
              <button type="button" class="u-eye js-eye-toggle" data-target="userPassword" aria-label="Toggle password visibility">
                <i class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
            <div class="form-text">Password for new user (min 8 characters).</div>
          </div>

          <div class="col-md-6 js-pw-section">
            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="u-pw-wrap">
              <input type="password" class="form-control pe-5" id="userPasswordConfirmation" placeholder="••••••••">
              <button type="button" class="u-eye js-eye-toggle" data-target="userPasswordConfirmation" aria-label="Toggle confirm password visibility">
                <i class="fa-regular fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Alt. Email</label>
            <input type="email" class="form-control" id="userAltEmail" maxlength="255" placeholder="alt@example.com">
          </div>

          <div class="col-md-6">
            <label class="form-label">Alt. Phone</label>
            <input class="form-control" id="userAltPhone" maxlength="32" placeholder="+91 88888 88888">
          </div>

          <div class="col-md-6">
            <label class="form-label">WhatsApp</label>
            <input class="form-control" id="userWhatsApp" maxlength="32" placeholder="+91 77777 77777">
          </div>

          <div class="col-md-6">
            <label class="form-label">Address</label>
            <textarea class="form-control" id="userAddress" rows="2" placeholder="Street, City, State, ZIP"></textarea>
          </div>

          <div class="col-md-12">
            <label class="form-label">Avatar (optional)</label>
            <div class="d-flex align-items-center gap-2">
              <img id="imagePreview" alt="Preview" style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid var(--line-strong);display:none;">
              <input type="file" id="userImage" accept="image/*" class="form-control">
            </div>
            <div class="form-text">PNG, JPG, WEBP, GIF, SVG up to 5MB.</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="saveUserBtn">
          <i class="fa fa-floppy-disk me-1"></i> Save
        </button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-user me-2"></i>User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="userViewBody">Loading…</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="uploadCvModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <form class="modal-content" id="uploadCvForm" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa fa-file-arrow-up me-2"></i> Upload CV —
          <span id="cv_user_name" class="fw-semibold">User</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="cv_user_uuid">

        <div class="mb-2">
          <label class="form-label">Select CV File <span class="text-danger">*</span></label>
          <input type="file" id="cvFileInput" class="form-control" accept=".pdf,.doc,.docx" required>
          <div class="form-text">Allowed: PDF, DOC, DOCX • Max: 10MB</div>
        </div>

        <div class="alert alert-light small mb-0">
          <i class="fa fa-circle-info me-1"></i>
          This will replace the previous CV (if any).
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="cvUploadBtn">
          <i class="fa fa-upload me-1"></i> Upload
        </button>
      </div>
    </form>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2100;">
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

<div id="usersFloatingMenu" aria-hidden="true"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const TOKEN = sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  if (!TOKEN){
    Swal.fire('Login needed','Your session expired. Please login again.','warning')
      .then(()=> location.href = '/');
    return;
  }

  const API = {
    meRole: '/api/auth/me-role',
    users: '/api/users',
    usersAll: '/api/users/all?limit=1000',
    importCsv: '/api/users/import-csv',
  };

  const state = {
    role: '',
    canWrite: false,
    canDelete: false,
    canView: false,
    canAssignPrivileges: false,
    rows: [],
    filteredRows: [],
    page: 1,
    perPage: 20,
    search: '',
    permissionsByPath: new Map(),
    allAccess: false,
    filters: {
      status: 'all',
      role: '',
      sort: 'name',
    },
  };

  const usersTbody    = document.getElementById('usersTbody');
  const pager         = document.getElementById('pager');
  const resultsInfo   = document.getElementById('resultsInfo');
  const perPageSel    = document.getElementById('perPage');
  const searchInput   = document.getElementById('searchInput');
  const btnReset      = document.getElementById('btnReset');
  const btnAddUser    = document.getElementById('btnAddUser');
  const writeControls = document.getElementById('writeControls');

  const filterModalEl   = document.getElementById('filterModal');
  const filterModal     = bootstrap.Modal.getOrCreateInstance(filterModalEl);
  const modalStatus     = document.getElementById('modal_status');
  const modalRole       = document.getElementById('modal_role');
  const modalSort       = document.getElementById('modal_sort');
  const btnApplyFilters = document.getElementById('btnApplyFilters');

  const userModalEl    = document.getElementById('userModal');
  const userModal      = new bootstrap.Modal(userModalEl);
  const userForm       = document.getElementById('userForm');
  const userModalTitle = document.getElementById('userModalTitle');
  const saveUserBtn    = document.getElementById('saveUserBtn');

  const userIdInput    = document.getElementById('userId');
  const userNameInput  = document.getElementById('userName');
  const userEmailInput = document.getElementById('userEmail');
  const userPhoneInput = document.getElementById('userPhone');
  const userRoleInput  = document.getElementById('userRole');
  const userStatusInput= document.getElementById('userStatus');
  const userPasswordInput  = document.getElementById('userPassword');
  const userPassword2Input = document.getElementById('userPasswordConfirmation');
  const userAltEmailInput  = document.getElementById('userAltEmail');
  const userAltPhoneInput  = document.getElementById('userAltPhone');
  const userWhatsAppInput  = document.getElementById('userWhatsApp');
  const userAddressInput   = document.getElementById('userAddress');
  const userImageInput     = document.getElementById('userImage');
  const imagePreview       = document.getElementById('imagePreview');
  const pwSections         = document.querySelectorAll('.js-pw-section');

  const userViewModalEl = document.getElementById('userViewModal');
  const userViewModal   = new bootstrap.Modal(userViewModalEl);
  const userViewBody    = document.getElementById('userViewBody');

  const uploadCvModalEl = document.getElementById('uploadCvModal');
  const uploadCvModal   = new bootstrap.Modal(uploadCvModalEl);
  const uploadCvForm    = document.getElementById('uploadCvForm');
  const cvUserName      = document.getElementById('cv_user_name');
  const cvUserUuidInput = document.getElementById('cv_user_uuid');
  const cvFileInput     = document.getElementById('cvFileInput');
  const cvUploadBtn     = document.getElementById('cvUploadBtn');
  const usersFloatingMenu = document.getElementById('usersFloatingMenu');

  const importCsvModalEl   = document.getElementById('importCsvModal');
  const importCsvModal     = new bootstrap.Modal(importCsvModalEl);
  const importCsvForm      = document.getElementById('importCsvForm');
  const csvDropZone        = document.getElementById('csvDropZone');
  const csvBrowseBtn       = document.getElementById('csvBrowseBtn');
  const csvFileInput       = document.getElementById('csvFile');
  const fileInfo           = document.getElementById('fileInfo');
  const fileName           = document.getElementById('fileName');
  const fileSize           = document.getElementById('fileSize');
  const clearFileBtn       = document.getElementById('clearFileBtn');
  const downloadTemplateBtn= document.getElementById('downloadTemplateBtn');
  const importStep1        = document.getElementById('importStep1');
  const importStep2        = document.getElementById('importStep2');
  const importStatusText   = document.getElementById('importStatusText');
  const importProgressBar  = document.getElementById('importProgressBar');
  const importStats        = document.getElementById('importStats');
  const importResults      = document.getElementById('importResults');
  const importResultsList  = document.getElementById('importResultsList');
  const importCancelBtn    = document.getElementById('importCancelBtn');
  const importSubmitBtn    = document.getElementById('importSubmitBtn');
  const importDoneBtn      = document.getElementById('importDoneBtn');
  const importSpinnerWrap  = document.getElementById('importSpinnerWrap');
  const importSuccessWrap  = document.getElementById('importSuccessWrap');

  const toastOk  = new bootstrap.Toast(document.getElementById('toastSuccess'));
  const toastErr = new bootstrap.Toast(document.getElementById('toastError'));
  const okTxt    = document.getElementById('toastSuccessText');
  const errTxt   = document.getElementById('toastErrorText');

  function ok(msg){
    okTxt.textContent = msg || 'Done';
    toastOk.show();
  }

  function err(msg){
    errTxt.textContent = msg || 'Something went wrong';
    toastErr.show();
  }

  function authHeaders(extra = {}){
    return Object.assign({
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + TOKEN,
    }, extra);
  }

  async function fetchJson(url, options = {}){
    const opts = Object.assign({}, options);
    opts.headers = authHeaders(opts.headers || {});
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));

    if (!res.ok){
      throw new Error(data.message || data.error || 'Request failed');
    }

    return data;
  }

  function esc(v){
    return String(v ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function debounce(fn, wait = 350){
    let t = null;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), wait);
    };
  }

  function normalizePath(path){
    const raw = String(path || '').trim();
    if (!raw) return '';
    try{
      const u = new URL(raw, window.location.origin);
      return (u.pathname || '/').replace(/\/+$/, '') || '/';
    }catch(e){
      return ('/' + raw.replace(/^\/+/, '')).replace(/\/+$/, '') || '/';
    }
  }

  function hasPageAction(path, ...actions){
    if (state.allAccess) return true;
    const set = state.permissionsByPath.get(normalizePath(path));
    if (!set) return false;
    return actions.some(action => set.has(String(action).toLowerCase()));
  }

  async function loadPermissions(){
    state.permissionsByPath = new Map();
    state.allAccess = false;

    try{
      const res = await fetch('/api/my/sidebar-menus?with_actions=1', { headers: authHeaders() });
      const js = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(js.message || 'Failed to load permissions');

      if (js.tree === 'all'){
        state.allAccess = true;
        return;
      }

      (Array.isArray(js.tree) ? js.tree : []).forEach(header => {
        (Array.isArray(header.children) ? header.children : []).forEach(page => {
          const href = normalizePath(page.href || '');
          if (!href) return;
          state.permissionsByPath.set(
            href,
            new Set((Array.isArray(page.actions) ? page.actions : []).map(v => String(v || '').toLowerCase()).filter(Boolean))
          );
        });
      });
    }catch(e){
      state.permissionsByPath = new Map();
    }
  }

  function normalizeRoleToken(v){
    let r = String(v || '').trim().toLowerCase();
    if (!r) return '';
    r = r.replace(/[\s-]+/g, '_');

    const map = {
      administrator: 'admin',
      human_resources: 'hr',
      human_resource: 'hr',
      people_ops: 'hr',
      recruiter: 'hr',
      staff: 'employee',
      team_member: 'employee',
      associate: 'employee',
      executive: 'employee',
      manager: 'employee',
      supervisor: 'employee',
      member: 'employee',
      user: 'employee',
    };

    return map[r] || r;
  }

  function roleLabel(role){
    const map = {
      admin: 'Admin',
      hr: 'HR',
      employee: 'Employee',
    };
    return map[normalizeRoleToken(role)] || (role ? String(role) : '—');
  }

  function statusBadge(status){
    const s = String(status || '').toLowerCase();
    if (s === 'active'){
      return '<span class="badge-soft-active"><i class="fa fa-circle-check"></i> Active</span>';
    }
    return '<span class="badge-soft-inactive"><i class="fa fa-circle-xmark"></i> Inactive</span>';
  }

  function avatarHtml(user){
    const img = String(user.image || '').trim();
    if (img){
      return '<img class="u-avatar" src="' + esc(img) + '" alt="Avatar">';
    }
    const name = String(user.name || '').trim();
    const initials = name
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, 2)
      .map(x => x.charAt(0).toUpperCase())
      .join('') || 'NA';

    return '<div class="u-avatar-fallback">' + esc(initials) + '</div>';
  }

  function buildFloatingMenuHtml(user){
    const items = [];

    if (state.canAssignPrivileges){
      items.push('<button type="button" class="usr-menu-item" data-action="assign_privilege"><i class="fa fa-key"></i><span>Assign Privilege</span></button>');
    }

    if (state.canView){
      items.push('<button type="button" class="usr-menu-item" data-action="view"><i class="fa fa-eye"></i><span>View</span></button>');
    }

    if (state.canWrite){
      items.push('<button type="button" class="usr-menu-item" data-action="edit"><i class="fa fa-pen-to-square"></i><span>Edit</span></button>');
      items.push('<button type="button" class="usr-menu-item" data-action="password"><i class="fa fa-key"></i><span>Change Password</span></button>');
      items.push('<button type="button" class="usr-menu-item" data-action="cv"><i class="fa fa-file-arrow-up"></i><span>Upload CV</span></button>');
    }

    if (state.canDelete){
      items.push('<hr class="dropdown-divider">');
      items.push('<button type="button" class="usr-menu-item text-danger" data-action="delete"><i class="fa fa-trash"></i><span>Delete</span></button>');
    }

    if (!items.length){
      items.push('<div class="px-2 py-2 text-muted small">No actions available</div>');
    }

    return items.join('');
  }

  function clearSelectedRows(){
    document.querySelectorAll('.usr-dd-btn.is-open').forEach(btn => {
      btn.classList.remove('is-open');
      btn.setAttribute('aria-expanded', 'false');
    });
    document.querySelectorAll('#usersTbody tr.is-selected').forEach(row => row.classList.remove('is-selected'));
  }

  function hideFloatingMenu(){
    if (!usersFloatingMenu) return;
    usersFloatingMenu.style.display = 'none';
    usersFloatingMenu.style.visibility = 'hidden';
    usersFloatingMenu.innerHTML = '';
    usersFloatingMenu.setAttribute('aria-hidden', 'true');
    delete usersFloatingMenu.dataset.userId;
    delete usersFloatingMenu.dataset.userUuid;
    delete usersFloatingMenu.dataset.userName;
    clearSelectedRows();
  }

  function positionFloatingMenu(button){
    if (!usersFloatingMenu || !button) return;

    const rect = button.getBoundingClientRect();
    const gap = 8;

    let left = rect.right - usersFloatingMenu.offsetWidth;
    let top = rect.bottom + gap;

    if (left < 10) left = 10;
    if (left + usersFloatingMenu.offsetWidth > window.innerWidth - 10){
      left = Math.max(10, window.innerWidth - usersFloatingMenu.offsetWidth - 10);
    }

    if (top + usersFloatingMenu.offsetHeight > window.innerHeight - 10){
      const aboveTop = rect.top - usersFloatingMenu.offsetHeight - gap;
      top = aboveTop >= 10 ? aboveTop : Math.max(10, window.innerHeight - usersFloatingMenu.offsetHeight - 10);
    }

    usersFloatingMenu.style.top = top + 'px';
    usersFloatingMenu.style.left = left + 'px';
  }

  function getFilteredRows(){
    let rows = [...state.rows];
    const q = state.search.trim().toLowerCase();

    if (q){
      rows = rows.filter(row => {
        return [
          row.name,
          row.email,
          row.phone_number,
          roleLabel(row.role),
        ].some(v => String(v || '').toLowerCase().includes(q));
      });
    }

    if (state.filters.status !== 'all'){
      rows = rows.filter(row => String(row.status || '').toLowerCase() === state.filters.status);
    }

    if (state.filters.role){
      rows = rows.filter(row => normalizeRoleToken(row.role) === state.filters.role);
    }

    const sort = state.filters.sort || 'name';
    rows.sort((a, b) => {
      const desc = sort.startsWith('-');
      const key = desc ? sort.slice(1) : sort;

      const av = String(a[key] ?? '').toLowerCase();
      const bv = String(b[key] ?? '').toLowerCase();

      if (av < bv) return desc ? 1 : -1;
      if (av > bv) return desc ? -1 : 1;
      return 0;
    });

    return rows;
  }

  function renderPager(totalPages){
    pager.innerHTML = '';
    if (totalPages <= 1) return;

    const addItem = (label, page, disabled = false, active = false) => {
      const li = document.createElement('li');
      li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'page-link';
      btn.textContent = label;

      if (!disabled){
        btn.addEventListener('click', () => {
          state.page = page;
          render();
        });
      }

      li.appendChild(btn);
      pager.appendChild(li);
    };

    addItem('Prev', Math.max(1, state.page - 1), state.page === 1);

    const start = Math.max(1, state.page - 2);
    const end = Math.min(totalPages, start + 4);
    for (let i = start; i <= end; i++){
      addItem(String(i), i, false, i === state.page);
    }

    addItem('Next', Math.min(totalPages, state.page + 1), state.page === totalPages);
  }

  function render(){
    hideFloatingMenu();
    state.filteredRows = getFilteredRows();

    const perPage = Number(state.perPage) || 20;
    const total = state.filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    if (state.page > totalPages) state.page = totalPages;

    const start = (state.page - 1) * perPage;
    const end = start + perPage;
    const pageRows = state.filteredRows.slice(start, end);

    if (!pageRows.length){
      usersTbody.innerHTML = `
        <tr>
          <td colspan="7" class="empty-state">
            <i class="fa fa-users-slash mb-2" style="font-size:20px;"></i>
            <div>No users found.</div>
          </td>
        </tr>
      `;
    } else {
      usersTbody.innerHTML = pageRows.map(user => {
        return `
          <tr data-id="${esc(user.id)}" data-uuid="${esc(user.uuid || '')}" data-name="${esc(user.name || '')}" data-role="${esc(normalizeRoleToken(user.role))}">
            <td>${statusBadge(user.status)}</td>
            <td>${avatarHtml(user)}</td>
            <td>
              <div class="fw-semibold">${esc(user.name || '—')}</div>
              <div class="small text-muted">#${esc(user.id)}</div>
            </td>
            <td>
              <div>${esc(user.email || '—')}</div>
              <div class="small text-muted">${esc(user.uuid || '')}</div>
            </td>
            <td>${esc(user.phone_number || '—')}</td>
            <td><span class="badge-role">${esc(roleLabel(user.role))}</span></td>
            <td class="text-end usr-action-cell">
              <button type="button" class="btn btn-light btn-sm usr-dd-btn js-action-toggle" aria-label="Actions" aria-expanded="false" title="Actions">
                <i class="fa fa-ellipsis-vertical"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    if (!total){
      resultsInfo.textContent = '0 results';
    } else {
      resultsInfo.textContent = `Showing ${start + 1}-${Math.min(end, total)} of ${total} results`;
    }

    renderPager(totalPages);
  }

  async function loadUsers(){
    usersTbody.innerHTML = `
      <tr>
        <td colspan="7" class="empty-state">
          <i class="fa fa-circle-notch fa-spin mb-2" style="font-size:20px;"></i>
          <div>Loading users…</div>
        </td>
      </tr>
    `;

    try{
      const data = await fetchJson(API.usersAll);
      const rows = Array.isArray(data.data) ? data.data : [];
      state.rows = rows.map(row => ({
        ...row,
        role: normalizeRoleToken(row.role),
      }));
      render();
    }catch(ex){
      usersTbody.innerHTML = `
        <tr>
          <td colspan="7" class="empty-state">
            <i class="fa fa-triangle-exclamation mb-2" style="font-size:20px;"></i>
            <div>${esc(ex.message || 'Failed to load users')}</div>
          </td>
        </tr>
      `;
      resultsInfo.textContent = 'Failed to load results';
      err(ex.message || 'Failed to load users');
    }
  }

  async function ensureRole(){
    let role = normalizeRoleToken(sessionStorage.getItem('role') || localStorage.getItem('role') || '');

    if (!role){
      try{
        const data = await fetchJson(API.meRole);
        role = normalizeRoleToken(data.role || data.user?.role || '');
        if (role){
          try{
            sessionStorage.setItem('role', role);
            localStorage.setItem('role', role);
          }catch(_){}
        }
      }catch(ex){
        role = '';
      }
    }

    state.role = role;
    state.canView = hasPageAction('/user/manage', 'view') || hasPageAction('/users/manage', 'view') || role === 'admin';
    state.canWrite =
      hasPageAction('/user/manage', 'create', 'store', 'update', 'edit') ||
      hasPageAction('/users/manage', 'create', 'store', 'update', 'edit') ||
      role === 'admin';
    state.canDelete =
      hasPageAction('/user/manage', 'delete', 'destroy') ||
      hasPageAction('/users/manage', 'delete', 'destroy') ||
      role === 'admin';
    state.canAssignPrivileges = role === 'admin';

    if (state.canWrite && writeControls){
      writeControls.style.display = 'flex';
    } else if (writeControls){
      writeControls.style.display = 'none';
    }
  }

  function resetForm(){
    userForm.reset();
    userIdInput.value = '';
    imagePreview.style.display = 'none';
    imagePreview.removeAttribute('src');
    userImageInput.value = '';
    pwSections.forEach(el => el.style.display = '');
  }

  function openCreateModal(){
    resetForm();
    userModalTitle.textContent = 'Add User';
    saveUserBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i> Save';
    userPasswordInput.required = true;
    userPassword2Input.required = true;
    userModal.show();
  }

  async function openEditModal(id){
    try{
      const data = await fetchJson(`${API.users}/${id}`);
      const user = data.user || {};
      resetForm();

      userModalTitle.textContent = 'Edit User';
      saveUserBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i> Update';

      userIdInput.value = user.id || '';
      userNameInput.value = user.name || '';
      userEmailInput.value = user.email || '';
      userPhoneInput.value = user.phone_number || '';
      userRoleInput.value = normalizeRoleToken(user.role || '');
      userStatusInput.value = user.status || 'active';
      userAltEmailInput.value = user.alternative_email || '';
      userAltPhoneInput.value = user.alternative_phone_number || '';
      userWhatsAppInput.value = user.whatsapp_number || '';
      userAddressInput.value = user.address || '';

      if (user.image){
        imagePreview.src = user.image;
        imagePreview.style.display = 'block';
      }

      pwSections.forEach(el => el.style.display = 'none');
      userPasswordInput.required = false;
      userPassword2Input.required = false;

      userModal.show();
    }catch(ex){
      err(ex.message || 'Failed to load user');
    }
  }

  async function openViewModal(id){
    hideFloatingMenu();
    userViewBody.innerHTML = 'Loading…';
    userViewModal.show();

    try{
      const data = await fetchJson(`${API.users}/${id}`);
      const user = data.user || {};
      const image = user.image ? `<img src="${esc(user.image)}" alt="Avatar" class="u-avatar" style="width:72px;height:72px;border-radius:18px;">` : `<div class="u-avatar-fallback" style="width:72px;height:72px;border-radius:18px;">NA</div>`;
      const cv = user.cv ? `<a class="btn btn-sm btn-outline-primary" href="${esc(user.cv)}" target="_blank"><i class="fa fa-file-lines me-1"></i> View CV</a>` : `<span class="text-muted">No CV uploaded</span>`;

      userViewBody.innerHTML = `
        <div class="d-flex align-items-center gap-3 mb-4">
          ${image}
          <div>
            <div class="h5 mb-1">${esc(user.name || '—')}</div>
            <div class="text-muted">${esc(user.email || '—')}</div>
            <div class="mt-2 d-flex flex-wrap gap-2">
              <span class="badge-role">${esc(roleLabel(user.role))}</span>
              ${statusBadge(user.status)}
            </div>
          </div>
        </div>

        <div class="detail-grid">
          <div class="detail-card">
            <div class="detail-label">Phone</div>
            <div class="detail-value">${esc(user.phone_number || '—')}</div>
          </div>
          <div class="detail-card">
            <div class="detail-label">Alternative Email</div>
            <div class="detail-value">${esc(user.alternative_email || '—')}</div>
          </div>
          <div class="detail-card">
            <div class="detail-label">Alternative Phone</div>
            <div class="detail-value">${esc(user.alternative_phone_number || '—')}</div>
          </div>
          <div class="detail-card">
            <div class="detail-label">WhatsApp</div>
            <div class="detail-value">${esc(user.whatsapp_number || '—')}</div>
          </div>
          <div class="detail-card">
            <div class="detail-label">Slug</div>
            <div class="detail-value">${esc(user.slug || '—')}</div>
          </div>
          <div class="detail-card" style="grid-column:1 / -1;">
            <div class="detail-label">Address</div>
            <div class="detail-value">${esc(user.address || '—')}</div>
          </div>
          <div class="detail-card" style="grid-column:1 / -1;">
            <div class="detail-label">CV</div>
            <div class="detail-value">${cv}</div>
          </div>
        </div>
      `;
    }catch(ex){
      userViewBody.innerHTML = `<div class="alert alert-danger mb-0">${esc(ex.message || 'Failed to load user')}</div>`;
    }
  }

  async function confirmDelete(id, name){
    hideFloatingMenu();
    const res = await Swal.fire({
      title: 'Delete user?',
      text: `This will soft-delete ${name || 'this user'}.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#d33',
    });

    if (!res.isConfirmed) return;

    try{
      await fetchJson(`${API.users}/${id}`, { method: 'DELETE' });
      ok('User deleted');
      await loadUsers();
    }catch(ex){
      err(ex.message || 'Failed to delete user');
    }
  }

  async function promptPasswordChange(id, name){
    hideFloatingMenu();
    const result = await Swal.fire({
      title: `Change password${name ? ' — ' + name : ''}`,
      html: `
        <input id="swalPassword" type="password" class="swal2-input" placeholder="New password">
        <input id="swalPassword2" type="password" class="swal2-input" placeholder="Confirm password">
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Update',
      preConfirm: () => {
        const p1 = document.getElementById('swalPassword').value.trim();
        const p2 = document.getElementById('swalPassword2').value.trim();

        if (!p1 || p1.length < 8){
          Swal.showValidationMessage('Password must be at least 8 characters');
          return false;
        }
        if (p1 !== p2){
          Swal.showValidationMessage('Passwords do not match');
          return false;
        }

        return { password: p1 };
      }
    });

    if (!result.isConfirmed || !result.value) return;

    try{
      await fetchJson(`${API.users}/${id}/password`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(result.value),
      });
      ok('Password updated');
    }catch(ex){
      err(ex.message || 'Failed to update password');
    }
  }

  function openCvModal(uuid, name){
    hideFloatingMenu();
    cvUserUuidInput.value = uuid || '';
    cvUserName.textContent = name || 'User';
    cvFileInput.value = '';
    uploadCvModal.show();
  }

  function setImportStep(step){
    if (step === 1){
      importStep1.classList.remove('d-none');
      importStep2.classList.add('d-none');
      importCancelBtn.classList.remove('d-none');
      importSubmitBtn.classList.remove('d-none');
      importDoneBtn.classList.add('d-none');
    } else {
      importStep1.classList.add('d-none');
      importStep2.classList.remove('d-none');
      importCancelBtn.classList.add('d-none');
      importSubmitBtn.classList.add('d-none');
      importDoneBtn.classList.remove('d-none');
    }
  }

  function clearSelectedCsv(){
    csvFileInput.value = '';
    fileInfo.classList.add('d-none');
    fileName.textContent = '';
    fileSize.textContent = '';
    importSubmitBtn.disabled = true;
  }

  function setSelectedCsv(file){
    if (!file) return clearSelectedCsv();
    fileName.textContent = file.name;
    fileSize.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
    fileInfo.classList.remove('d-none');
    importSubmitBtn.disabled = false;
  }

  function resetImportModal(){
    setImportStep(1);
    clearSelectedCsv();
    importSpinnerWrap.classList.remove('d-none');
    importSuccessWrap.classList.add('d-none');
    importStatusText.textContent = 'Processing CSV file...';
    importProgressBar.style.width = '0%';
    importStats.textContent = 'Processing...';
    importResults.classList.add('d-none');
    importResultsList.innerHTML = '';
  }

  function downloadCsvTemplate(){
    const content = [
      'name,email,password,role,phone_number,alternative_email,alternative_phone_number,whatsapp_number,address',
      'Aarav Sen,aarav@example.com,Employee@123,employee,,9876543210,,,,Kolkata',
      'Riya Sharma,riya@example.com,HrTeam@123,hr,,9123456780,,,,Mumbai',
    ].join('\n');

    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'users_import_template.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  }

  async function submitUserForm(e){
    e.preventDefault();

    const id = userIdInput.value.trim();
    const isEdit = !!id;
    const fd = new FormData();

    fd.append('name', userNameInput.value.trim());
    fd.append('email', userEmailInput.value.trim());
    if (userPhoneInput.value.trim()) fd.append('phone_number', userPhoneInput.value.trim());
    if (userAltEmailInput.value.trim()) fd.append('alternative_email', userAltEmailInput.value.trim());
    if (userAltPhoneInput.value.trim()) fd.append('alternative_phone_number', userAltPhoneInput.value.trim());
    if (userWhatsAppInput.value.trim()) fd.append('whatsapp_number', userWhatsAppInput.value.trim());
    if (userAddressInput.value.trim()) fd.append('address', userAddressInput.value.trim());
    if (userRoleInput.value) fd.append('role', userRoleInput.value);
    if (userStatusInput.value) fd.append('status', userStatusInput.value);
    if (!isEdit){
      if (!userPasswordInput.value.trim() || userPasswordInput.value.trim().length < 8){
        return err('Password must be at least 8 characters');
      }
      if (userPasswordInput.value !== userPassword2Input.value){
        return err('Passwords do not match');
      }
      fd.append('password', userPasswordInput.value);
    }

    if (userImageInput.files && userImageInput.files[0]){
      fd.append('image', userImageInput.files[0]);
    }

    saveUserBtn.disabled = true;
    const original = saveUserBtn.innerHTML;
    saveUserBtn.innerHTML = '<i class="fa fa-circle-notch fa-spin me-1"></i> Saving';

    try{
      let url = API.users;
      let method = 'POST';

      if (isEdit){
        url = `${API.users}/${id}`;
        fd.append('_method', 'PATCH');
      }

      const res = await fetch(url, {
        method,
        headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' },
        body: fd,
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok){
        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Failed to save user'));
      }

      ok(isEdit ? 'User updated' : 'User created');
      userModal.hide();
      await loadUsers();
    }catch(ex){
      err(ex.message || 'Failed to save user');
    }finally{
      saveUserBtn.disabled = false;
      saveUserBtn.innerHTML = original;
    }
  }

  async function submitCvForm(e){
    e.preventDefault();

    const uuid = cvUserUuidInput.value.trim();
    const file = cvFileInput.files && cvFileInput.files[0];
    if (!uuid || !file){
      return err('Please select a CV file');
    }

    const fd = new FormData();
    fd.append('cv', file);

    cvUploadBtn.disabled = true;
    const original = cvUploadBtn.innerHTML;
    cvUploadBtn.innerHTML = '<i class="fa fa-circle-notch fa-spin me-1"></i> Uploading';

    try{
      const res = await fetch(`${API.users}/${uuid}/cv`, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' },
        body: fd,
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok){
        throw new Error(data.message || 'Failed to upload CV');
      }

      ok('CV uploaded successfully');
      uploadCvModal.hide();
      await loadUsers();
    }catch(ex){
      err(ex.message || 'Failed to upload CV');
    }finally{
      cvUploadBtn.disabled = false;
      cvUploadBtn.innerHTML = original;
    }
  }

  async function submitImportCsv(e){
    e.preventDefault();

    const file = csvFileInput.files && csvFileInput.files[0];
    if (!file){
      return err('Please select a CSV file');
    }

    setImportStep(2);
    importProgressBar.style.width = '30%';
    importStats.textContent = 'Uploading CSV...';
    importResults.classList.add('d-none');
    importResultsList.innerHTML = '';

    const fd = new FormData();
    fd.append('file', file);

    try{
      const res = await fetch(API.importCsv, {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' },
        body: fd,
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok){
        throw new Error(data.message || 'Import failed');
      }

      importProgressBar.style.width = '100%';
      importSpinnerWrap.classList.add('d-none');
      importSuccessWrap.classList.remove('d-none');
      importStatusText.textContent = 'Import completed successfully';

      const meta = data.meta || {};
      const imported = Number(meta.imported || 0);
      const skipped = Number(meta.skipped || 0);
      const errors = Array.isArray(meta.errors) ? meta.errors : [];

      importStats.textContent = `Imported: ${imported} • Skipped: ${skipped}`;

      importResults.classList.remove('d-none');
      const items = [];
      items.push(`<div class="import-result-item success">Imported users: ${esc(imported)}</div>`);
      items.push(`<div class="import-result-item ${skipped ? 'error' : 'success'}">Skipped rows: ${esc(skipped)}</div>`);
      errors.forEach(msg => {
        items.push(`<div class="import-result-item error">${esc(msg)}</div>`);
      });
      importResultsList.innerHTML = items.join('');

      ok('CSV imported');
      await loadUsers();
    }catch(ex){
      importProgressBar.style.width = '100%';
      importSpinnerWrap.classList.add('d-none');
      importSuccessWrap.classList.add('d-none');
      importStatusText.textContent = 'Import failed';
      importStats.textContent = ex.message || 'Something went wrong';
      importResults.classList.remove('d-none');
      importResultsList.innerHTML = `<div class="import-result-item error">${esc(ex.message || 'Import failed')}</div>`;
      err(ex.message || 'CSV import failed');
    }
  }

  function bindEvents(){
    perPageSel.addEventListener('change', () => {
      state.perPage = Number(perPageSel.value) || 20;
      state.page = 1;
      render();
    });

    searchInput.addEventListener('input', debounce(() => {
      state.search = searchInput.value || '';
      state.page = 1;
      render();
    }, 250));

    btnApplyFilters.addEventListener('click', () => {
      state.filters.status = modalStatus.value || 'all';
      state.filters.role = modalRole.value || '';
      state.filters.sort = modalSort.value || 'name';
      state.page = 1;
      filterModal.hide();
      render();
    });

    btnReset.addEventListener('click', () => {
      state.search = '';
      state.page = 1;
      state.perPage = 20;
      state.filters = {
        status: 'all',
        role: '',
        sort: 'name',
      };

      perPageSel.value = '20';
      searchInput.value = '';
      modalStatus.value = 'all';
      modalRole.value = '';
      modalSort.value = 'name';
      render();
    });

    if (btnAddUser){
      btnAddUser.addEventListener('click', openCreateModal);
    }

    userForm.addEventListener('submit', submitUserForm);
    uploadCvForm.addEventListener('submit', submitCvForm);
    importCsvForm.addEventListener('submit', submitImportCsv);

    userImageInput.addEventListener('change', () => {
      const file = userImageInput.files && userImageInput.files[0];
      if (!file){
        imagePreview.style.display = 'none';
        imagePreview.removeAttribute('src');
        return;
      }

      const reader = new FileReader();
      reader.onload = ev => {
        imagePreview.src = ev.target.result;
        imagePreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });

    document.querySelectorAll('.js-eye-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;

        input.type = input.type === 'password' ? 'text' : 'password';
        btn.innerHTML = input.type === 'password'
          ? '<i class="fa-regular fa-eye-slash"></i>'
          : '<i class="fa-regular fa-eye"></i>';
      });
    });

    usersTbody.addEventListener('click', (e) => {
      const actionToggle = e.target.closest('.js-action-toggle');
      if (!actionToggle) return;

      e.preventDefault();
      e.stopPropagation();

      const row = actionToggle.closest('tr');
      if (!row || !usersFloatingMenu) return;

      const rowId = String(row.dataset.id || '');
      if (!rowId) return;

      if (usersFloatingMenu.style.display === 'block' && usersFloatingMenu.dataset.userId === rowId){
        hideFloatingMenu();
        return;
      }

      clearSelectedRows();
      row.classList.add('is-selected');
      actionToggle.classList.add('is-open');
      actionToggle.setAttribute('aria-expanded', 'true');

      usersFloatingMenu.innerHTML = buildFloatingMenuHtml({
        id: rowId,
        uuid: row.dataset.uuid || '',
        name: row.dataset.name || '',
        role: row.dataset.role || '',
      });
      usersFloatingMenu.dataset.userId = rowId;
      usersFloatingMenu.dataset.userUuid = row.dataset.uuid || '';
      usersFloatingMenu.dataset.userName = row.dataset.name || '';
      usersFloatingMenu.style.display = 'block';
      usersFloatingMenu.style.visibility = 'hidden';

      requestAnimationFrame(() => {
        positionFloatingMenu(actionToggle);
        usersFloatingMenu.style.visibility = 'visible';
        usersFloatingMenu.setAttribute('aria-hidden', 'false');
      });
    });

    document.addEventListener('click', async (e) => {
      const actionBtn = e.target.closest('#usersFloatingMenu [data-action]');
      if (!actionBtn) return;

      e.preventDefault();
      e.stopPropagation();

      const action = actionBtn.dataset.action || '';
      const userId = usersFloatingMenu?.dataset.userId || '';
      const userUuid = usersFloatingMenu?.dataset.userUuid || '';
      const userName = usersFloatingMenu?.dataset.userName || '';

      if (!userId && !userUuid) return;

      hideFloatingMenu();

      if (action === 'assign_privilege'){
        const query = new URLSearchParams();
        if (userUuid) query.set('user_uuid', userUuid);
        if (userId) query.set('user_id', userId);
        window.location.href = '/user-privileges/manage?' + query.toString();
        return;
      }

      if (action === 'view' && state.canView) return openViewModal(userId);
      if (action === 'edit') return openEditModal(userId);
      if (action === 'password') return promptPasswordChange(userId, userName);
      if (action === 'cv') return openCvModal(userUuid, userName);
      if (action === 'delete') return confirmDelete(userId, userName);
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('#usersFloatingMenu, .js-action-toggle')){
        hideFloatingMenu();
      }
    });

    window.addEventListener('scroll', hideFloatingMenu, true);
    window.addEventListener('resize', hideFloatingMenu);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') hideFloatingMenu();
    });

    csvBrowseBtn.addEventListener('click', () => csvFileInput.click());
    clearFileBtn.addEventListener('click', clearSelectedCsv);
    downloadTemplateBtn.addEventListener('click', downloadCsvTemplate);

    csvFileInput.addEventListener('change', () => {
      const file = csvFileInput.files && csvFileInput.files[0];
      setSelectedCsv(file);
    });

    csvDropZone.addEventListener('click', () => csvFileInput.click());
    ['dragenter', 'dragover'].forEach(evt => {
      csvDropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        csvDropZone.classList.add('dragover');
      });
    });
    ['dragleave', 'drop'].forEach(evt => {
      csvDropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        csvDropZone.classList.remove('dragover');
      });
    });
    csvDropZone.addEventListener('drop', (e) => {
      const file = e.dataTransfer.files && e.dataTransfer.files[0];
      if (!file) return;
      const dt = new DataTransfer();
      dt.items.add(file);
      csvFileInput.files = dt.files;
      setSelectedCsv(file);
    });

    importCsvModalEl.addEventListener('hidden.bs.modal', resetImportModal);
  }

  (async function init(){
    bindEvents();
    await loadPermissions();
    await ensureRole();
    await loadUsers();
  })();
});
</script>
